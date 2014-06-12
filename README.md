wgetLogFilter
=============

Crawl your site with wget an create a nice an short report

wget --spider -o wget.log -e robots=off -w 1 -r -p
php main.php wget.log