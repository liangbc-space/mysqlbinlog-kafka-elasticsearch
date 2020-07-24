#!/bin/bash

PROCESS_NAME="canal"

CANAL_BIN_PATH="/usr/local/canal/bin"


if [ $(ps -ef | grep "$PROCESS_NAME" | grep -v "grep" | wc -l) -eq 0 ]
then
    cd "$CANAL_BIN_PATH"
    echo $(sh ./startup.sh)
else
    echo "service is running..."
fi