#!/bin/bash
ip=`cat ../socket/IP`
scp -r system/ application/ index.php .htaccess root@$ip:/www/pages/rbox
