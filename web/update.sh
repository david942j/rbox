#!/bin/bash
ip=`cat ../socket/IP`
scp -r application/ index.php .htaccess root@$ip:/www/pages/rbox
