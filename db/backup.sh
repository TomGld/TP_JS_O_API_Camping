#!/usr/bin/sh
mariadb-dump symfony -uroot -psuperAdmin > /root/init.sql
echo "Sauvegarde terminée"