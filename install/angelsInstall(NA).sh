#!/bin/sh

# -dbslocalhost -dbubooot -dbpmysecretpass -dbbbooot -btuadmin -btpadmin -btmtest@test.ru

tar xzf booot.tar.gz
chmod -R 0777 admin/.htpasswd admin/.htaccess css/ data/ js/ themes/ config.php install.php yandex.xml

php ./angelsInstall.php $*

rm css/allcss.css css/allcss.css.gz js/alljs.js js/alljs.js.gz booot.tar.gz install.php angelsInstall.sh angelsInstall.php booot.sql
