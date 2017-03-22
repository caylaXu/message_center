#!/bin/bash
ps -ef|grep -E send_message|grep -v grep|awk '{print $2}'|xargs kill -9
cd /home/wwwroot/message_new/application/cli
nohup php send_message.php &