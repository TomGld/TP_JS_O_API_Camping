#!/usr/bin/sh

mariadb symfony -uroot -psuperAdmin < /root/init.sql
echo "Restauration terminée"
