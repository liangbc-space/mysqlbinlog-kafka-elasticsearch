# binlog监听同步数据到elasticsearch

## 中间件依赖

###### 注意：考虑效率问题，一下中间件最好安装在mysql和elasticsearch的同一个局域网内安装，不然设计网路请求问题会导致性能降低

* 安装java jdk 须大于1.8版本

* 安装zookeeper   版本无特殊要求，不要太低

* 安装kafka		版本无特殊要求，不要太低

* 安装canal服务端	最新版1.4.4
```
    https://github.com/alibaba/canal/wiki/Canal-Kafka-RocketMQ-QuickStart

    https://github.com/alibaba/canal/wiki/aliyun-RDS-QuickStart
```

* 配置canal、kafka、zookeeper



## PHP扩展依赖

###### 备注：”php --ri 扩展名称“ 可查看扩展是否安装和启动信息

* pcntl多进程扩展
```
    pecl install pcntl
 ```
	
* sysvshm共享内存扩展
```
    1.  进入PHP源码安装包
    2.  phpize
    3.  ./configure --with-php-config=/usr/local/php/bin/php-config
    4.  make && make install
 ```
	
* librdkafka库
```
    git clone https://github.com/edenhill/librdkafka.git
    ./configure
    make && make install
 ```
	
* rdkafka扩展
```
    pecl install rdkafka
 ```

