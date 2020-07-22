<?php

namespace app\controllers\es;


use app\controllers\BaseTask;
use app\models\elasticsearch\GoodsBase;
use commons\Commons;
use utils\DingTalk;

class Es extends BaseTask
{


    /** @var string 分表hash值 */
    private $tableHash;

    public function __construct($tableHash)
    {

        $this->tableHash = $tableHash;
    }


    public function __destruct()
    {
        unset($this->tableHash);
    }

    public function run(array $goodsData)
    {
        $saveIds = [];
        foreach ($goodsData as $item) {
            $item['operation_type'] != 'DELETE' && $saveIds[] = $item['goods_id'];
        }

        $goodsInfos = [];
        if ($saveIds) {
            //  获取需要保存的商品数据
            try {
                $goodsInfos = $this->getGoodsInfos($saveIds);
                $goodsInfos = array_combine(array_column($goodsInfos, 'uniqueeid'), $goodsInfos);
            } catch (\Exception $e) {
                $e->desc = 'mysql数据同步到ES数据组装异常';

                $dingTalk = new DingTalk(DingTalk::MYSQL_SYNC_TO_ES);
                $dingTalk->formatErrorSendDingTalkMsg($e);

                $this->getLogger($_SERVER['argv'][1])->info($e);
                return false;
            }

        }

        $body = [];

        foreach ($goodsData as $item) {
            $uniqueId = GoodsBase::_id($item['store_id'], $item['goods_id']);

            //  删除
            if ($item['operation_type'] == 'DELETE') {
                $body[] = [
                    'delete' => [
                        '_index' => GoodsBase::getIndex(),
                        '_id' => $uniqueId,
                    ]
                ];
            } else {
                if (isset($goodsInfos[$uniqueId]) && $goodsInfo = $goodsInfos[$uniqueId]) {
                    $body[] = [
                        'index' => [
                            '_index' => GoodsBase::getIndex(),
                            '_id' => $uniqueId,
                        ]
                    ];

                    $body[] = GoodsBase::format($goodsInfo);
                }
            }
        }

        $res = GoodsBase::getDb()->bulk(['body' => $body]);
        if ($res['errors']) {
            foreach ($res['items'] as $item) {
                foreach ($item as $value) {
                    if (isset($value['error'])) {
                        $this->getLogger($_SERVER['argv'][1])->info($item);
                    }
                }
            }
        }

    }


    public function migrate(array $goodsIds)
    {
        $goodsInfos = $this->getGoodsInfos($goodsIds);

        $body = [];
        foreach ($goodsInfos as $goodsInfo) {
            $goodsInfo = GoodsBase::format($goodsInfo);

            $body[] = [
                'create' => [
                    '_index' => GoodsBase::getIndex(),
                    '_id' => $goodsInfo['uniqueeid'],
                ]
            ];

            $body[] = $goodsInfo;
        }

        $res = GoodsBase::getDb()->bulk(['body' => $body]);
        if ($res['errors']) {
            foreach ($res['items'] as $item) {
                foreach ($item as $value) {
                    if (isset($value['error'])) {
                        $this->getLogger($_SERVER['argv'][1])->info($item);
                    }
                }
            }
        }
    }


    private function getGoodsInfos(array $goodsIds)
    {

        $goodsInfos = $this->goodsBaseLists($goodsIds);

        $goodsIds = array_filter(array_unique(array_column($goodsInfos, 'id')));
        //  获取商品tag信息
        $tags = $this->goodsTags($goodsIds);

        //  获取商品推荐信息
        $recommends = $this->goodsRecommends($goodsIds);

        //  获取商品分类信息
        $categoryIds = [];
        foreach (array_column($goodsInfos, 'category_path') as $item) {
            $categoryIds = array_merge($categoryIds, array_filter(array_unique(explode(',', $item))));
        }
        $categoryIds = array_unique($categoryIds);
        $categories = $this->goodsCategories($categoryIds);
        $categories = array_combine(array_column($categories, 'goods_category_id'), $categories);

        //  获取商品附属分类信息
        $categoriesRel = $this->goodsCategoriesRel($goodsIds);
        $categoriesNew = [];
        foreach ($categoriesRel as $item) {
            $categoriesNew[$item['goods_id']][] = $item;
        }

        //  获取商品图片信息
        $otherImages = $this->goodsOtherImages($goodsIds);
        $otherImages = array_combine(array_column($otherImages, 'goods_id'), $otherImages);

        //  获取商品销量属性信息
        $saleProps = $this->goodsSaleProp($goodsIds);

        //  获取商品属性信息
        $props = $this->goodsProps($goodsIds);


        foreach ($goodsInfos as $key => &$goods) {
            $goods['mysql_table_name'] = 'z_goods_' . $this->tableHash;

            if (isset($goods['user_group_id_values'])) {
                if ($goods['user_group_id_values']) {
                    $userGroupIdValues = explode(',', trim($goods['user_group_id_values'], ','));
                    $goods['user_group_ids'] = array_unique($userGroupIdValues);
                }
                unset($goodsInfos[$key]['user_group_id_values']);
            }

            $goodsId = intval($goods['id']);

            //  商品tag信息
            foreach ($tags as $tag) {
                if ($goodsId == $tag['goods_id']) {
                    $goods['tag_ids'][] = $tag['tag_id'];
                    $goods['tag_names'][] = $tag['tag_name'];
                }
            }

            //  商品推荐信息
            foreach ($recommends as $recommend) {
                if ($goodsId == $recommend['goods_id']) {
                    $goods['rec_ids'][] = $recommend['rec_id'];
                    $goods['rec_names'][] = $recommend['rec_name'];
                    $goods['up_time_' . $recommend['rec_index']] = $recommend['rec_up_time'];
                }
            }

            //  商品分类信息
            $categoryIds = array_filter(array_unique(explode(',', $goods['category_path'])));
            foreach ($categoryIds as $categoryId) {
                if (isset($categories[$categoryId])) {
                    $goods['category_ids'][] = $categories[$categoryId]['goods_category_id'];
                    $goods['category_names'][] = $categories[$categoryId]['goods_category_name'];
                }
            }

            //  商品category_rel分类信息
            if (isset($categoriesNew[$goodsId]) && $categoriesNew[$goodsId]) {

                foreach ($categoriesNew[$goodsId] as $item) {
                    $categoryId = $item['goods_category_id'];
                    $categoryName = $item['goods_category_name'];

                    if (!isset($goods['category_ids'][$categoryId])) {
                        $goods['category_ids'][] = $categoryId;
                        $goods['category_names'][] = $categoryName;
                    }

                }

            }

            //  商品图片
            if (isset($otherImages[$goodsId])) {
                $goods['images_other'] = array_column($otherImages[$goodsId], 'image');
            }

            //  商品sale_prop信息
            foreach ($saleProps as $saleProp) {
                if ($goodsId == $saleProp['goods_id']) {
                    $goods['main_prop_name'][] = $saleProp['base_name'];
                    $goods['main_prop_image'][] = $saleProp['image'];
                }
            }

            //  商品prop信息
            foreach ($props as $prop) {
                if ($goodsId == $prop['goods_id']) {
                    $goods['property_ids'][] = $prop['value_id'];

                    $goods['prop_ids_' . $prop['property_id']] = $prop['value_id'];
                    $goods['prop_names_' . $prop['property_id']] = $prop['value_name'];
                }
            }

        }

        return $goodsInfos;
    }


    private function goodsBaseLists(array $goodsIds)
    {

        $goodsIds = array_unique($goodsIds);
        $goodsIds = implode(',', Commons::stringToInteger($goodsIds));
        if (!$goodsIds)
            return [];

        $sql = "
SELECT
	CONCAT( CAST( g.store_id AS CHAR ), '-', CAST( g.id AS CHAR ) ) AS uniqueeid,
	g.*,
IF
	( g.stock_nums > 0, 1, IF ( g.is_bookable, 1, 0 ) ) AS is_instock,
	FROM_UNIXTIME( g.create_time, '%Y%m%d' ) AS create_day,
	b.base_name AS brand_name,
	c.base_name AS category_name 
FROM
	z_goods_{$this->tableHash} g
	LEFT JOIN z_brand AS b ON g.brand_id = b.id
	LEFT JOIN z_goods_category_{$this->tableHash} AS c ON g.category_id = c.id 
WHERE
    g.id IN({$goodsIds}) 
    AND g.store_id > 0
	AND g.STATUS != -1";

        return $this->getDb()->fetchAll($sql);
    }

    private function goodsTags(array $goodsIds)
    {
        $goodsIds = array_unique($goodsIds);
        $goodsIds = implode(',', Commons::stringToInteger($goodsIds));
        if (!$goodsIds)
            return [];

        $sql = "
SELECT
	tag_id AS tag_id,
	base_name AS tag_name,
	r.goods_id
FROM
	z_goods_tag AS t
	LEFT JOIN z_goods_tag_rel AS r ON t.id = r.tag_id 
WHERE
    r.goods_id in({$goodsIds})";

        return $this->getDb()->fetchAll($sql);
    }


    private function goodsRecommends(array $goodsIds)
    {
        $goodsIds = array_unique($goodsIds);
        $goodsIds = implode(',', Commons::stringToInteger($goodsIds));
        if (!$goodsIds)
            return [];

        $sql = "
SELECT
	r.id AS rec_id,
	r.rec_index AS rec_index,
	r.base_name AS rec_name,
	rr.up_time AS rec_up_time,
	rr.goods_id
FROM
	z_goods_recommend AS r
	LEFT JOIN z_goods_recommend_rel AS rr ON r.id = rr.goods_recommend_id 
WHERE
	rr.goods_id IN ({$goodsIds})";

        return $this->getDb()->fetchAll($sql);
    }


    private function goodsCategories($categoryIds)
    {
        $categoryIds = array_unique($categoryIds);
        $categoryIds = implode(',', Commons::stringToInteger($categoryIds));
        if (!$categoryIds)
            return [];

        $sql = "
SELECT
	id AS goods_category_id,
	base_name AS goods_category_name
FROM
	z_goods_category_{$this->tableHash}
WHERE
	id IN ( {$categoryIds} ) ";

        return $this->getDb()->fetchAll($sql);
    }


    private function goodsCategoriesRel(array $goodsIds)
    {
        $goodsIds = array_unique($goodsIds);
        $goodsIds = implode(',', Commons::stringToInteger($goodsIds));
        if (!$goodsIds)
            return [];

        $sql = "SELECT
    r.goods_id,
	c.id AS goods_category_id,
	c.base_name AS goods_category_name 
FROM
	z_goods_category_{$this->tableHash} c
	LEFT JOIN z_goods_category_rel_{$this->tableHash} r ON c.id = r.category_id 
WHERE
	r.goods_id IN({$goodsIds})";

        return $this->getDb()->fetchAll($sql);
    }


    private function goodsOtherImages(array $goodsIds)
    {
        $goodsIds = array_unique($goodsIds);
        $goodsIds = implode(',', Commons::stringToInteger($goodsIds));
        if (!$goodsIds)
            return [];

        $sql = "SELECT
	goods_id,
	image 
FROM
	z_image_{$this->tableHash} 
WHERE
	goods_id IN ( {$goodsIds} ) 
	AND category = 'goods' 
	AND obj_id = 0 
ORDER BY
	listorder ASC";

        return $this->getDb()->fetchAll($sql);
    }


    private function goodsSaleProp(array $goodsIds)
    {
        $goodsIds = array_unique($goodsIds);
        $goodsIds = implode(',', Commons::stringToInteger($goodsIds));
        if (!$goodsIds)
            return [];

        $sql = "SELECT
	a.base_name,
	a.image ,
	a.goods_id
FROM
	z_goods_sale_prop_{$this->tableHash} a
	LEFT JOIN z_goods_sale_prop_{$this->tableHash} b ON a.parent_id = b.id 
WHERE
	b.goods_id IN ( {$goodsIds} ) 
	AND b.multi_image = 1
ORDER BY
	a.listorder ASC";
        return $this->getDb()->fetchAll($sql);
    }


    private function goodsProps(array $goodsIds)
    {
        $goodsIds = array_unique($goodsIds);
        $goodsIds = implode(',', Commons::stringToInteger($goodsIds));
        if (!$goodsIds)
            return [];

        $sql = "SELECT
	goods_id,
	property_id AS property_id,
	value_id AS value_id,
	value_name AS value_name 
FROM
	z_goods_property_rel_{$this->tableHash} 
WHERE
	goods_id IN ( {$goodsIds} ) 
	AND value_id != 0";

        return $this->getDb()->fetchAll($sql);
    }


}