# mysql(binlog)监听同步数据到elasticsearch

## 服务依赖



###### 注意：考虑效率问题，以下中间件最好安装在mysql和elasticsearch的同一个局域网内安装，否则可能会因为网路请求问题导致性能降低

* 安装java jdk 须大于1.8版本

* 安装zookeeper   版本无特殊要求，不要太低

* 安装kafka		版本无特殊要求，不要太低

* 安装canal服务端	最新版1.4.4
```
    https://github.com/alibaba/canal/wiki/Canal-Kafka-RocketMQ-QuickStart

    https://github.com/alibaba/canal/wiki/aliyun-RDS-QuickStart
```

* 配置canal、kafka、zookeeper


## 服务启动

* 启动zookeeper服务

* 启动kafka服务

* 启动canal服务



## PHP扩展依赖

###### 备注：”php --ri 扩展名称“ 可查看扩展是否安装和启动信息

* pcntl多进程扩展
```
    pecl install pcntl
 ```
	
* sysvshm共享内存扩展
```
    1.  进入PHP源码安装包下的ext/sysvshm,如:/root/php-5.6.40/ext/sysvshm
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


## 项目介绍


###### 本项目是基于满足`执行多进程命令及多进程任务，并支持参数传递`场景进行的简单封装，为性能考虑也不会加载过多的无意义的包



### 注意事项
1.当前项目仅支持`CLI`模式，其入口文件为`cmd`

2.本项目须严格遵循`psr4`规范以保证项目文件的正常加载

3.建议采用`composer`进行管理包,已经内置了elasticsearch、mysql、kafka等包，`其中kafka包为自行开发，如存在问题请反馈`



### 目录结构

    ├── app                         脚本代码目录，可通常量APP_PATH自定义路径和文件名
    │   ├── controllers             控制器目录，可通常量CONTROLLER_PATH自定义路径和文件名
    │   └── models                  模型目录，支持自定义
    │       ├── elasticsearch       es模型
    │       └── ...                 其他模型，如mysql，mongodb等
    │── commons                     公共函数类文件夹，支持自定义
    ├── config                      配置文件，可通常量CONFIG_PATH自定义路径和文件名
    ├── framework                   框架核心，通过反射的方式进行路由查找执行
    │   └── elasticsearch           es相关模型及接口抽象
    ├── lockFile                    文件锁目录
    ├── logs                        框架日志文件目录
    ├── shell                       shell脚本目录
    ├── utils                       项目组件目录，支持自定义
    └── cmd                         框架入口文件



### 运行脚本

####### 脚本采用命名空间的方式加载，支持参数传递

```
    如：  php cmd es/task/run -batchNum=100       执行mysql-binlog监控同步数据到elasticsearch的脚本
    如：  php cmd es/task/migrate -psize=1000     执行mysql数据全量同步到elasticsearch脚本
```