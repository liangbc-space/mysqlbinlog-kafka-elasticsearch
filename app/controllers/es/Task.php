<?php


namespace app\controllers\es;


use app\controllers\BaseTask;
use app\models\elasticsearch\GoodsBase;
use commons\Commons;
use kafka\ConsumeConfig;
use kafka\SeniorConsumer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use RdKafka\Metadata;
use RdKafka\Metadata\Collection;
use utils\FileLock;
use utils\SharedMemory;

class Task extends BaseTask
{

    private $topicNames = [];

    private $kafkaConsumeBatchNum = 500;


    /**
     *
     * 消费binlog监听kafka队列，同步数据到es
     *
     * @param int $batchNum
     * @throws \Exception
     */

    public function run($batchNum = 500)
    {
        $lock = new FileLock($this);
        if (!$lock->createLock())
            exit('当前脚本正在运行，请勿重复运行' . PHP_EOL);

        $batchNum && $this->kafkaConsumeBatchNum = $batchNum;

        //  设置进程名称
        cli_set_process_title('task-mysql-binlog-canal-toEs');

        //  初始化获取可订阅的kafka中topic列表
        $this->initTopicNames();

        $this->pcntlLoop(count($this->topicNames), function ($pid, $index) {

            $topicNames = array_values($this->topicNames);
            $topicName = $topicNames[--$index];

            //  获取数据表的hash值
            $topicNames = array_flip($this->topicNames);
            $tableHash = $topicNames[$topicName];

            //  创建一个kafka消费者
            $consumer = $this->createConsumerInstance($topicName);

            while (true) {
                try {
                    $this->consumer($consumer, [$topicName], $tableHash);
                } catch (\Exception $e) {
                    $this->getLogger($_SERVER['argv'][1])->info($e);
                }

                sleep(2);
            }

        });

    }


    /**
     *
     * 数据迁移
     *
     * @param int $psize 单次处理数据数量
     * @return bool
     * @throws \Exception
     */

    public function migrate($psize = 500)
    {
        $lock = new FileLock($this);
        if (!$lock->createLock())
            exit('当前脚本正在运行，请勿重复运行' . PHP_EOL);

        //  获取表hash值
        $conn = clone self::$mysql;
        $query = $conn->createQueryBuilder();
        $query->select(['value'])->from('z_core_config')
            //->where('name = :name')->setParameter('name', 'tbl_hash')
            ->where('name = "tbl_hash"')
            ->orderBy('id', 'desc');

        $res = $query->execute()->fetchAll();
        if (!$res)
            return false;
        $maxTableHash = intval($res[0]['value']);

        $tableHashes = [];
        for ($i = 0; $i <= $maxTableHash; $i++) {
            $tableHashes[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        $this->pcntlLoop(count($tableHashes), function ($pid, $index) use ($tableHashes, $psize) {
            $tableHash = $tableHashes[--$index];
            $tableName = 'z_goods_' . $tableHash;

            $page = 1;
            while (true) {
                $offset = ($page++ - 1) * $psize;

                $sql = "SELECT id FROM {$tableName} WHERE store_id > 0 ORDER BY id ASC LIMIT {$offset},{$psize}";
                if ($ids = self::$mysql->fetchAll($sql)) {
                    $ids = Commons::stringToInteger(array_column($ids, 'id'));

                    //  获取es中是否已经存在数据
                    $body = [
                        'size' => $psize,
                        '_source' => ['id'],
                        'query' => [
                            'bool' => [
                                'filter' => [
                                    [
                                        'terms' => [
                                            'id' => $ids
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'mysql_table_name' => $tableName
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ];
                    $result = GoodsBase::getDb()->search(['index' => GoodsBase::getIndex(), 'body' => $body]);
                    $hits = array_column($result['hits']['hits'], '_source');
                    $existsIds = array_column($hits, 'id');

                    if ($insertIds = array_diff($ids, $existsIds)) {
                        $es = new Es($tableHash);
                        $es->migrate($insertIds);

                        echo "成功插入" . count($insertIds) . "条数据" . PHP_EOL;
                    } else {
                        echo "暂无数据需要插入" . PHP_EOL;
                    }


                } else
                    break;
            }

        });
    }


    /**
     * @param SeniorConsumer $consumer
     * @param array $topicNames
     * @param $tableHash
     * @throws \RdKafka\Exception
     */

    private function consumer(SeniorConsumer $consumer, array $topicNames, $tableHash)
    {
        //  创建一个日志记录器
        $logger = new Logger($_SERVER['argv'][1]);
        $handler = new StreamHandler(LOG_PATH . 'binlog-kafka-consumer/' . date('Ymd') . '.log', Logger::INFO);
        $logger->pushHandler($handler);

        $goodsData = [];
        $consumer->consumer($topicNames, function (Message $message, KafkaConsumer $instance) use (&$goodsData, $tableHash, $logger) {

            if ($message->err) {
                $this->toEs($tableHash, $goodsData);
                return;
            }

            //  记录收到的订阅消息
            $logger->info($message->payload);
            //  处理消息
            $data = json_decode($message->payload, true);
            foreach ($data['data'] as $item) {
                $goodsData[] = [
                    'goods_id' => $item['id'],
                    'store_id' => $item['store_id'],
                    'operation_type' => strtoupper($data['type']),
                ];
            }
            $instance->commitAsync($message);

            if (count($goodsData) >= $this->kafkaConsumeBatchNum) {
                $this->toEs($tableHash, $goodsData);
            }

        });

    }


    private function toEs($tableHash, array &$goodsData)
    {
        if ($goodsData) {
            $es = new Es($tableHash);
            $es->run($goodsData);

            echo '【' . $tableHash . '】成功同步' . count($goodsData) . '条数据' . PHP_EOL;

            $goodsData = [];

            //sleep(0.5);
        }

    }


    private function createConsumerInstance($index = null)
    {
        $kafkaConnOptions = require CONFIG_PATH . 'kafka.config.php';
        $consumerConfig = new ConsumeConfig($kafkaConnOptions['brokers']);
        $consumerConfig->consumeTimeout = 3 * 1000;

        $consumer = new SeniorConsumer($consumerConfig);

        $consumer->autoCommit = true;
        $consumer->autoCommitIntervalMs = 10 * 1000;

        $consumer->getInstance('task-mysql-canal-es' . $index);

        return $consumer;
    }


    private function initTopicNames()
    {
        $shmKey = 'mysql-binlog-monitor-topicNames';
        $shm = new SharedMemory();
        //  获取kafka的topic列表
        $this->pcntlLoop(1, function ($pid, $index) use ($shm, $shmKey) {
            $consumer = $this->createConsumerInstance();
            $topicNames = $this->setTopicNames($consumer->topics());

            $shm->set($shmKey, $topicNames);
        });

        $this->topicNames = $shm->get($shmKey);
        $shm->remove($shmKey);
    }


    private function setTopicNames(Collection $topics)
    {

        $regx = '/^cn01_db.z_goods_(\d{2})$/';
        $topNames = [];

        /** @var Metadata\Topic $topic */
        foreach ($topics as $topic) {

            $topicName = $topic->getTopic();
            if (preg_match($regx, $topicName, $tableHash) && isset($tableHash[1])) {
                $topNames[$tableHash[1]] = $topicName;
            }

        }

        return $topNames;

    }


}