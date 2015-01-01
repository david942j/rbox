#!/bin/bash
ip=`cat IP`
scp -r server/ root@$ip:~
