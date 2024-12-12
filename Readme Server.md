TODO:
Зафиксировать источник подключения

ЗАПУСК КЛИЕНТА
https://docs.vultr.com/how-to-install-nginx-mysql-php-lemp-stack-on-ubuntu-24-04

sudo su
cd /

apt list --installed
apt-get update
apt-get upgrade

service --status-all
systemctl list-units
service apache2 stop

apt-get install nginx -y
nginx -v
service nginx stop

php -v
add-apt-repository ppa:ondrej/php
apt update
apt install php8.3 php8.3-common php8.3-cli php8.3-fpm php8.3-pgsql -y
update-alternatives --config php
systemctl enable php8.3-fpm
systemctl start php8.3-fpm
systemctl status php8.3-fpm
ss -pl | grep php
// cd /etc/php/8.3/fpm/pool.d/
nano /etc/php/8.3/fpm/pool.d/www.conf
?

////
////
////

cd /var/www/
ls -F
rm -R html
mkdir dsts-catalog/
cd dsts-catalog/
nano index.php

<?php
	phpinfo();
?>

nano index.html

<!DOCTYPE html>
<html>
<body>

<h1>Welcome to Catalog!</h1>

</body>
</html>

chmod 755 /var/www/dsts-catalog
chown -R www-data:www-data /var/www/dsts-catalog
ls -all

////
////
////

rm -rf /etc/nginx/sites-enabled/default && rm -rf /etc/nginx/sites-available/default

mkdir /etc/nginx/sites-available
mkdir /etc/nginx/sites-enabled
nano /etc/nginx/sites-available/dsts-catalog.conf

server {
    listen 80;
    server_name dsts-catalog.global;

    root /var/www/dsts-catalog;
    index index.php index.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
}

ln -s /etc/nginx/sites-available/dsts-catalog.conf /etc/nginx/sites-enabled/dsts-catalog.conf

Добавить папку sites-available в nginx.conf


nginx -t
systemctl restart nginx
service nginx status

ufw allow 80/tcp
nano /etc/hosts
dsts-catalog.global 176.108.249.107

systemctl restart php8.3-fpm


////
////
////

https://www.datadoghq.com/blog/nginx-502-bad-gateway-errors-php-fpm/
https://www.linux.org.ru/forum/admin/13071282
https://stackoverflow.com/questions/4252368/nginx-502-bad-gateway

Дргуие гайды:
https://htmlacademy.ru/blog/php/installation-php-on-different-os
https://www.theserverside.com/blog/Coffee-Talk-Java-News-Stories-and-Opinions/Nginx-PHP-FPM-config-example
https://habr.com/ru/articles/351402/
https://habr.com/ru/articles/320036/
https://docs.vultr.com/how-to-install-nginx-mysql-php-lemp-stack-on-ubuntu-24-04

systemctl status nginx
systemctl status php8.3-fpm

Ограничение подключений:
https://selectel.ru/blog/tutorials/how-to-configure-firewall-with-ufw-on-ubuntu-20/

Установка PostgreSQL:
https://selectel.ru/blog/tutorials/how-to-install-and-use-postgresql-on-ubuntu-20-04/

////
////
////

apt install postgresql postgresql-contrib -y
systemctl status postgresql
systemctl is-enabled postgresql
pg_isready
su - postgres
psql

echo "ServerName 176.108.249.107" >> /etc/apache2/apache2.conf
nano /etc/apache2/ports.conf
https://ruvds.com/ru/helpcenter/postgresql-pgadmin-ubuntu/
https://www.geeksforgeeks.org/how-to-change-apache-http-port-in-linux/
https://stackoverflow.com/questions/77718378/setting-up-pgadmin4-on-a-vps-running-ubuntu-configuring-it-from-nginx
https://wiki.archlinux.org/title/PhpPgAdmin



////
////
////

IN LEMP

Создаём папку в dsts-catalog
Стопаем apache2
Запускаем nginx
Создаем dsts-catalog.conf
Создаем ссылку на него в sites-enabled
Удаляем ссылку на default
ufw allow 80/tcp
ps aux | grep 'php'
Скачиваем php8.1-fpm
Разблокируем
Запускаем

ufw allow OpenSSH
ufw enable
ufw status
apt install postgresql postgresql-contrib
systemctl start postgresql.service
systemctl status postgresql.service
sudo -u postgres psql

apt-get install phppgadmin
nano /etc/apache2/conf-available
Allow From all
systemctl restart apache2

apt install git
git clone https://github.com/phppgadmin/phppgadmin.git /var/www/phppgadmin
chown www-data:www-data -R /var/www/phppgadmin
cp /var/www/phppgadmin/conf/config.inc.php-dist /var/www/phppgadmin/conf/config.inc.php
nano /var/www/phppgadmin/conf/config.inc.php
$conf['servers'][0]['host'] = '127.0.0.1';
apt install php8.1-mbstring
systemctl stop apache2

nano /etc/php/8.1/fpm/php.ini
extension=pgsql
open_basedir = /var/www

touch /etc/nginx/sites-available/phppgadmin.conf

server {
    listen 80;
    server_name phppgadmin;

    root /var/www/phppgadmin;

    index index.php;
    include php.conf;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }
}

mkdir /var/log/phppgadmin
touch /var/log/phppgadmin/access.log
touch /var/log/phppgadmin/error.log

ln -s /etc/nginx/sites-available/phppgadmin.conf /etc/nginx/sites-enabled/phppgadmin


////
////
////

pgadmin4
https://ruvds.com/ru/helpcenter/postgresql-pgadmin-ubuntu/
https://www.pgadmin.org/download/pgadmin-4-apt/
https://github.com/LessonDump/DockerPostgresPgAdmin/blob/master/docker-compose.yaml
https://github.com/LessonDump/DockerPostgresPgAdmin
