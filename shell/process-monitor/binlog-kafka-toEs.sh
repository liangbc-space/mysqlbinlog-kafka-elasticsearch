#!/bin/bash

PROCESS_NAME="task-mysql-binlog-canal-toEs"

PHP_BIN_PATH="/usr/local/php/bin/php"

START_COMMAND="$PHP_BIN_PATH /home/wwwroot/es_sync/cmd es/task/run -batchNum=100"


if [ $(ps -ef | grep "$PROCESS_NAME" | grep -v "grep" | wc -l) -eq 0 ]
then
    $START_COMMAND
else
    echo "script is running..."
fi