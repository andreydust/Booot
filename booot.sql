-- MySQL dump 10.13  Distrib 5.1.41, for pc-linux-gnu (i686)
--
-- Host: localhost    Database: booot
-- ------------------------------------------------------
-- Server version	5.1.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bm_admin_users`
--

DROP TABLE IF EXISTS `bm_admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(32) NOT NULL,
  `password` varchar(128) NOT NULL,
  `type` enum('a','m') NOT NULL DEFAULT 'm',
  `access` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `post` varchar(255) NOT NULL,
  `email` varchar(128) NOT NULL,
  `lastenter` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_admin_users`
--

LOCK TABLES `bm_admin_users` WRITE;
/*!40000 ALTER TABLE `bm_admin_users` DISABLE KEYS */;
INSERT INTO `bm_admin_users` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3','a','','Андрей Антонов','Администратор','andreydust@gmail.com','2011-02-28 00:00:00','2010-12-15 11:44:56','2011-02-28 14:38:57');
/*!40000 ALTER TABLE `bm_admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_blocks`
--

DROP TABLE IF EXISTS `bm_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_blocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `callname` varchar(128) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `callname` (`callname`),
  KEY `show` (`show`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_blocks`
--

LOCK TABLES `bm_blocks` WRITE;
/*!40000 ALTER TABLE `bm_blocks` DISABLE KEYS */;
INSERT INTO `bm_blocks` VALUES (1,0,'Текст под логотипом','sub_logo','Y','N','2010-12-15 16:32:25','2011-02-18 13:41:40','Уникальные товары массового потребления'),(2,1,'Телефон','phone','Y','N','2010-12-15 16:32:46','2011-03-07 15:04:34','+7 (906) 809-01-04'),(3,2,'icq','icq','Y','N','2010-12-15 16:33:06','2011-03-07 15:04:56','54741024'),(4,3,'skype','skype','N','Y','2010-12-15 16:33:47','0000-00-00 00:00:00','andreydust'),(5,4,'Текст на главной','main_text','Y','N','2010-12-15 16:37:40','2011-02-17 13:18:01','<h2>О нас</h2>\r\n<p>Как ключевой поставщик для многих российских и мировых компаний мы удовлетворяем запросы наших клиентов как с точки зрения экономической выгоды, так и оптимального применения продукции в трудных и критических условий эксплуатации. Наша компания полностью посвящает себя клиенту и его пожеланиям по приобретению качественной продукции.</p>\r\n<p>Мы стремимся повысить качество жизни людей, работая на благо их здоровья и благополучия.  Проще говоря, мы хотим помочь людям вести здоровую, полноценную жизнь. Говоря о здоровье, мы имеем в виду не только его медицинские аспекты, но и поддержание хорошей формы, здоровое питание и в целом умение вести здоровый образ жизни.</p>'),(12,10,'Информация о сроке доставки в товарах','product_delivery_time','Y','N','2011-03-05 13:26:05','2011-03-05 13:26:37','Сможем доставить завтра с 9-00 до 23-00'),(7,6,'Адрес','address','Y','N','2010-12-15 16:40:50','2011-03-07 15:05:41','620075, г. Екатеринбург, ул. Малышева, 85 а'),(8,7,'О компании в футере (внизу)','about_footer','Y','N','2010-12-15 16:42:28','2011-03-07 15:06:16','<p>Интернет-магазин уникальных товаров массового потребления «Booot»: Шины, диски, ложки, промышленные печи, мягкие игрушки.</p>'),(9,8,'Реквизиты в футере (внизу)','details_footer','Y','N','2010-12-15 16:43:50','2011-03-07 15:06:55','<p>ООО «Веб аутсорс»</p>\r\n<p>ОГРН 1000000000000</p>'),(10,9,'Текст после оформления заказа','basket_thanks','Y','N','2011-02-02 18:48:44','0000-00-00 00:00:00','<p>Наш менеджер скоро свяжется с вами. Пожалуйста, не выключайте телефон некоторое время.</p>'),(11,5,'Баннер на главной','main_promo','Y','N','2011-02-17 12:50:30','2011-03-07 15:19:20','<p><a href=\"http://game66.ru/\"><img src=\"http://game66.ru/templates/skin/habra/img/logo.gif\" alt=\"Flash игры онлайн\" /></a></p>\r\n<p><a href=\"http://game66.ru/\">Flash игры онлайн</a></p>\r\n<p>&nbsp;</p>\r\n<p><a href=\"http://weboutsource.ru/create-and-sell-ishop\"><img src=\"/data/textimages/14a8165c46c50fb3a607adf508ee4ea2.png\" alt=\"weboutsource_logo_\" width=\"208\" height=\"111\" /></a></p>\r\n<p><a href=\"http://weboutsource.ru/create-and-sell-ishop\">Разработка и продажа готовых интернет-магазинов</a></p>');
/*!40000 ALTER TABLE `bm_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_comments`
--

DROP TABLE IF EXISTS `bm_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `hash` char(32) NOT NULL,
  `author` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `text` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_comments`
--

LOCK TABLES `bm_comments` WRITE;
/*!40000 ALTER TABLE `bm_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_content`
--

DROP TABLE IF EXISTS `bm_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `top` int(10) unsigned NOT NULL,
  `order` int(10) NOT NULL,
  `nav` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `module` varchar(32) NOT NULL,
  `template` varchar(63) NOT NULL DEFAULT 'page.php',
  `showmenu` enum('Y','N') NOT NULL DEFAULT 'N',
  `show` enum('Y','N') NOT NULL DEFAULT 'N',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_content`
--

LOCK TABLES `bm_content` WRITE;
/*!40000 ALTER TABLE `bm_content` DISABLE KEYS */;
INSERT INTO `bm_content` VALUES (1,0,0,'about-us','О магазине','<p>{form:write-to-us}</p>\r\n<p>{block:details_footer}</p>\r\n<p>{block:address}</p>\r\n<p><strong>{block:phone}</strong></p>\r\n<p>Мы работаем для тех, кто не может себе позволить тратить время на пробки по пути в магазин, на стояние в очередях, на хождение между бесконечными полками с товарами. Мы поможем вам сделать единственно верный выбор, и поможем быстро.</p>','','page.php','Y','Y','N','2010-08-19 16:52:31','2010-09-13 18:36:33'),(2,0,1,'payment','Способы оплаты','<p>Если вы хотите заплатить наличными, это можно сделать при покупке - как в офисе, так и при курьерской доставке. <strong>Оплата принимается в рублях</strong>.</p>\r\n<p><strong>При доставке курьером</strong> вы передаете деньги нашему сотруднику, а он выдает вам товарную накладную и заполняет гарантийный талон. Подписав товарный чек, вы подтверждаете факт оплаты и доставки товара.</p>','','page.php','Y','Y','N','2010-08-19 16:59:43','2010-09-13 18:25:18'),(3,0,2,'delivery','Доставка','<p>По Лондону &ndash; в день заказа или на следующий. Стоимость&nbsp; 40 рублей. При заказах от 800 рублей доставка бесплатна.</p>\r\n<p>В пригороды Лондона &ndash; в день заказа или на следующий. 5 рублей за километр от границ Лондона. Доставка в Исламабад, Днепропетровск, Карачи, Астрахань, Токио и Торонто производится бесплатно.</p>\r\n<p>В города Нигерии в течение 3-4 дней без предоплаты.</p>','','page.php','Y','Y','N','2010-08-19 16:59:59','2010-09-15 16:56:46'),(4,0,5,'catalog','Каталог','','Catalog','page.php','N','Y','N','2010-08-19 17:00:36','2010-09-13 18:36:20'),(5,0,7,'basket','Корзина','','Basket','page.php','N','Y','N','2010-08-19 18:10:09','2011-01-25 20:28:58'),(6,4,0,'hi','Привет','','','page.php','N','N','Y','2010-09-15 18:37:04','0000-00-00 00:00:00'),(7,1,0,'new','Новый','','','page.php','Y','Y','Y','2010-09-15 19:03:59','2010-12-15 13:27:51'),(8,2,0,'cash-payment','Оплата наличными','','','page.php','Y','Y','N','2010-09-15 19:46:48','0000-00-00 00:00:00'),(9,2,1,'payment-by-credit-card','Оплата банковской картой','','','page.php','Y','Y','N','2010-09-15 19:47:10','2010-09-15 19:48:56'),(10,2,2,'clearing','Безналичный расчет','','','page.php','Y','Y','N','2010-09-15 19:47:26','2011-03-07 15:11:08'),(13,1,1,'geography-delivery','География доставки','','','page.php','Y','Y','Y','2010-12-15 13:28:05','2010-12-15 13:44:49'),(14,3,0,'ekaterinburg','Екатеринбург','','','page.php','Y','Y','N','2010-12-15 14:38:21','2011-03-07 15:11:32'),(15,3,0,'moscow','Москва','','','page.php','Y','Y','N','2010-12-15 14:39:39','0000-00-00 00:00:00'),(16,0,4,'help','Помощь','','','page.php','Y','Y','N','2010-12-15 14:40:50','0000-00-00 00:00:00'),(17,16,0,'how-to-buy','Как купить?','','','page.php','Y','Y','N','2010-12-15 14:43:47','2010-12-15 14:43:57'),(18,16,0,'how-to-choose-a-gift-','Как выбрать подарок?','','','page.php','Y','Y','N','2010-12-15 14:44:13','2011-03-07 15:12:34'),(19,16,0,'maintenance-technology','Обслуживание техники','','','page.php','Y','Y','N','2010-12-15 14:44:24','2011-03-07 15:12:58'),(20,0,3,'news','Новости','','News','page.php','Y','Y','N','2010-12-20 15:56:21','2010-12-20 16:28:31'),(21,0,6,'more','Дополнительно','','','page.php','N','Y','N','2011-01-15 19:09:00','0000-00-00 00:00:00'),(22,21,0,'sizing','Таблицы размеров','','','page.php','Y','Y','N','2011-01-15 19:09:39','2011-03-07 15:14:31'),(23,21,1,'shipping-returns','Оплата и доставка','','','page.php','Y','Y','N','2011-01-15 19:09:58','2011-03-07 15:14:46'),(24,21,2,'exchange-refund-waiver','Обмен, возврат, отказ','','','page.php','Y','Y','N','2011-01-15 19:10:16','2011-03-07 15:15:03'),(25,21,3,'care-and-cleaning','Уход и чистка','','','page.php','Y','Y','N','2011-01-15 19:10:26','2011-03-07 15:15:21'),(26,21,0,'water-heaters','Водонагреватели','','','page.php','N','N','Y','2011-01-15 19:10:35','0000-00-00 00:00:00'),(27,0,8,'feedback','Напишите нам','<p>Будем рады вашим положительным отзывам, а отрицательные возьмем на контроль и доложим в трехдневный срок.</p>\r\n<p>{form:1}</p>','','page.php','N','Y','N','2011-01-17 14:11:05','2011-01-17 14:12:07'),(28,2,3,'payment-webmoney','Оплата WebMoney','','','page.php','Y','Y','N','2011-03-07 15:09:33','0000-00-00 00:00:00'),(29,16,0,'warranty-support','Гарантийная поддержка','','','page.php','N','N','N','2011-03-07 15:13:16','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `bm_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_data`
--

DROP TABLE IF EXISTS `bm_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_data`
--

LOCK TABLES `bm_data` WRITE;
/*!40000 ALTER TABLE `bm_data` DISABLE KEYS */;
INSERT INTO `bm_data` VALUES (2,'CheckFiles',''),(3,'cronJobs','a:6:{s:6:\"Yearly\";i:1295879032;s:7:\"Monthly\";i:1295879094;s:6:\"Weekly\";i:1295879122;s:5:\"Daily\";i:1296027108;s:6:\"Hourly\";i:1296027194;s:8:\"Minutely\";i:1295881942;}'),(4,'currency','a:35:{s:3:\"AUD\";s:5:\"29.68\";s:3:\"AZN\";s:5:\"37.36\";s:3:\"GBP\";s:5:\"47.46\";s:3:\"AMD\";s:5:\"81.74\";s:3:\"BYR\";s:4:\"9.88\";s:3:\"BGN\";s:5:\"20.81\";s:3:\"BRL\";s:5:\"17.83\";s:3:\"HUF\";s:5:\"14.84\";s:3:\"DKK\";s:5:\"54.61\";s:3:\"USD\";s:5:\"29.79\";s:3:\"EUR\";s:5:\"40.70\";s:3:\"INR\";s:5:\"65.32\";s:3:\"KZT\";s:5:\"20.30\";s:3:\"CAD\";s:5:\"30.00\";s:3:\"KGS\";s:5:\"62.80\";s:3:\"CNY\";s:5:\"45.26\";s:3:\"LVL\";s:5:\"57.90\";s:3:\"LTL\";s:5:\"11.79\";s:3:\"MDL\";s:5:\"24.46\";s:3:\"NOK\";s:5:\"51.68\";s:3:\"PLN\";s:5:\"10.51\";s:3:\"RON\";s:5:\"95.47\";s:3:\"XDR\";s:5:\"46.37\";s:3:\"SGD\";s:5:\"23.27\";s:3:\"TJS\";s:5:\"67.67\";s:3:\"TRY\";s:5:\"19.11\";s:3:\"TMT\";s:5:\"10.46\";s:3:\"UZS\";s:5:\"18.05\";s:3:\"UAH\";s:5:\"37.54\";s:3:\"CZK\";s:5:\"16.84\";s:3:\"SEK\";s:5:\"45.52\";s:3:\"CHF\";s:5:\"31.37\";s:3:\"ZAR\";s:5:\"42.45\";s:3:\"KRW\";s:5:\"26.65\";s:3:\"JPY\";s:5:\"36.14\";}');
/*!40000 ALTER TABLE `bm_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_forms`
--

DROP TABLE IF EXISTS `bm_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `callname` varchar(128) NOT NULL,
  `email` varchar(255) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_forms`
--

LOCK TABLES `bm_forms` WRITE;
/*!40000 ALTER TABLE `bm_forms` DISABLE KEYS */;
INSERT INTO `bm_forms` VALUES (1,0,'Напишите нам','write-to-us','andreydust@gmail.com','Y','N','2010-12-16 13:55:25','2010-12-21 16:06:20');
/*!40000 ALTER TABLE `bm_forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_forms_fields`
--

DROP TABLE IF EXISTS `bm_forms_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_forms_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form` int(10) unsigned NOT NULL,
  `type` enum('text','textarea','checkbox') NOT NULL DEFAULT 'text',
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `regex` varchar(255) NOT NULL,
  `regex_error` varchar(255) NOT NULL,
  `default` varchar(255) NOT NULL,
  `required` enum('Y','N') NOT NULL DEFAULT 'N',
  `order` int(11) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `form` (`form`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_forms_fields`
--

LOCK TABLES `bm_forms_fields` WRITE;
/*!40000 ALTER TABLE `bm_forms_fields` DISABLE KEYS */;
INSERT INTO `bm_forms_fields` VALUES (1,1,'text','Ваше имя','your-name','','','','N',0,'Y','2010-12-16 15:30:38','2011-01-19 09:51:20'),(2,1,'text','Телефон','phone','','','','Y',1,'Y','2010-12-16 15:31:31','2011-01-19 09:51:23'),(3,1,'textarea','Сообщение','message','','','','Y',2,'Y','2010-12-16 15:31:51','2011-01-19 09:51:04'),(4,1,'checkbox','Ответье мне срочно!','answer-me-quickly-','','','','N',3,'Y','2010-12-16 15:57:26','2011-01-19 09:47:44');
/*!40000 ALTER TABLE `bm_forms_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_images`
--

DROP TABLE IF EXISTS `bm_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `src` varchar(255) NOT NULL,
  `md5` varchar(32) NOT NULL,
  `module` varchar(64) NOT NULL,
  `module_id` int(11) NOT NULL,
  `alter_key` varchar(64) NOT NULL,
  `main` enum('Y','N') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_images`
--

LOCK TABLES `bm_images` WRITE;
/*!40000 ALTER TABLE `bm_images` DISABLE KEYS */;
INSERT INTO `bm_images` VALUES (1,'/data/moduleImages/Catalog/1/f3557cec3f39d7d9417395705ec76b13.jpg','f3557cec3f39d7d9417395705ec76b13','Catalog',1,'','Y'),(2,'/data/moduleImages/Catalog/1/e9d5a6bf4fe07fed541037bae92b4893.jpg','e9d5a6bf4fe07fed541037bae92b4893','Catalog',1,'','N'),(3,'/data/moduleImages/Catalog/1/ce3795f40ffd44fff0e0d39a765f1277.jpg','ce3795f40ffd44fff0e0d39a765f1277','Catalog',1,'','N'),(4,'/data/moduleImages/Catalog/1/404c58033e439b0d37ddd51767546068.jpg','404c58033e439b0d37ddd51767546068','Catalog',1,'','N'),(5,'/data/moduleImages/Catalog/2/976a9d48ddefdae66d37f46b50385b09.jpg','976a9d48ddefdae66d37f46b50385b09','Catalog',2,'','Y'),(6,'/data/moduleImages/Catalog/2/2155050665605bf622d077e8699cbb41.jpg','2155050665605bf622d077e8699cbb41','Catalog',2,'','N'),(7,'/data/moduleImages/Catalog/3/941412cd93f674c329a4dcd741501a5d.jpg','941412cd93f674c329a4dcd741501a5d','Catalog',3,'','Y'),(8,'/data/moduleImages/Catalog/3/510ff74b97fa69642391e125e113fcb4.jpg','510ff74b97fa69642391e125e113fcb4','Catalog',3,'','N'),(9,'/data/moduleImages/Catalog/4/7767622fe2d275b84a8e04a4313f8723.jpg','7767622fe2d275b84a8e04a4313f8723','Catalog',4,'','Y'),(10,'/data/moduleImages/Catalog/4/58e91a5e155c43d97e80e2750aa23638.jpg','58e91a5e155c43d97e80e2750aa23638','Catalog',4,'','N'),(11,'/data/moduleImages/Catalog/4/0a4ca67a7d3e267d1aa320f73c8264c3.jpg','0a4ca67a7d3e267d1aa320f73c8264c3','Catalog',4,'','N'),(12,'/data/moduleImages/Catalog/5/ef09f0a8a58bce0f899e6d17c7f0c8ce.jpg','ef09f0a8a58bce0f899e6d17c7f0c8ce','Catalog',5,'','Y'),(13,'/data/moduleImages/Catalog/5/fc0cbb8e7554c16cd6b50b95acc91f37.jpg','fc0cbb8e7554c16cd6b50b95acc91f37','Catalog',5,'','N'),(14,'/data/moduleImages/Catalog/5/e5cd2a1c28b013766115228efac61be3.jpg','e5cd2a1c28b013766115228efac61be3','Catalog',5,'','N'),(15,'/data/moduleImages/Catalog/6/7f2d99c4c66b58a70d7125b6ebdfcc86.jpg','7f2d99c4c66b58a70d7125b6ebdfcc86','Catalog',6,'','Y'),(16,'/data/moduleImages/Catalog/6/9d63c304c6e005cb84f4bb0bd1a3bc9d.jpg','9d63c304c6e005cb84f4bb0bd1a3bc9d','Catalog',6,'','N'),(17,'/data/moduleImages/Catalog/7/4e194a844407c2299526fc091cd1ead9.jpg','4e194a844407c2299526fc091cd1ead9','Catalog',7,'','Y'),(18,'/data/moduleImages/Catalog/8/5b0c34d9763d4bceeba0ced90f27f6f0.jpg','5b0c34d9763d4bceeba0ced90f27f6f0','Catalog',8,'','Y'),(19,'/data/moduleImages/Catalog/9/abb2888a5229b547e623c0b90a7a678a.jpg','abb2888a5229b547e623c0b90a7a678a','Catalog',9,'','Y'),(20,'/data/moduleImages/Catalog/10/530211305c9ea88eaacdea2af76c7430.jpg','530211305c9ea88eaacdea2af76c7430','Catalog',10,'','Y');
/*!40000 ALTER TABLE `bm_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_mediafiles`
--

DROP TABLE IF EXISTS `bm_mediafiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_mediafiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `src` varchar(255) NOT NULL,
  `md5` varchar(32) NOT NULL,
  `filetype` varchar(4) NOT NULL,
  `fileinfo` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `module` varchar(64) NOT NULL,
  `module_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_mediafiles`
--

LOCK TABLES `bm_mediafiles` WRITE;
/*!40000 ALTER TABLE `bm_mediafiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_mediafiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_news`
--

DROP TABLE IF EXISTS `bm_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `anons` text NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_news`
--

LOCK TABLES `bm_news` WRITE;
/*!40000 ALTER TABLE `bm_news` DISABLE KEYS */;
INSERT INTO `bm_news` VALUES (1,'Ubuntu 11.10 будет иметь кодовое имя Oneiric Ocelot','2011-03-07 00:00:00','Y','N','2011-03-07 16:35:32','2011-03-07 16:39:50','Марк Шаттлворт объявил, что следующая после Natty Narwhal версия Ubuntu будет называться Oneiric Ocelot.','<h2 class=\"entry-title single-entry-title\"><span class=\"topic\" title=\"http://habrahabr.ru/blogs/ubuntu/115060/\">&nbsp;</span><img src=\"http://habreffect.ru/files/14b/17788217e/ubuntu.png\" alt=\"image\" align=\"left\" />Марк Шаттлворт объявил, что следующая после Natty Narwhal версия Ubuntu будет называться Oneiric Ocelot.</h2>\r\n<div class=\"content\"><br /> В сообщении \"<a href=\"http://www.markshuttleworth.com/archives/646\">Next After Natty?</a>\"  в своем блоге Шаттлворт описывает цели следующей итерации Ubuntu и  объясняет, почему она должна быть названа именно так. Марк говорит:  &laquo;Oneiric значит &ldquo;мечтательный&rdquo;, и вместе с оцелотом это напоминает мне о  том, как происходят инновации: отчасти мечты, отчасти дисциплина&raquo;<br /> <br /> Вместе с Ubuntu 11.10 на CD также будет поставляться Unity 2D для тех  людей, железо которых не позволяет использовать 3D версию, а также Qt,  что, как утверждает Шаттлворт, &ldquo;даст разработчикам еще больше  возможностей для создания интерфейсов, которые как функциональны, так и  эстетически привлекательны&rdquo;</div>'),(2,'Debian или Ubuntu: кому помогать? ','2011-03-07 00:00:00','Y','N','2011-03-07 16:35:41','0000-00-00 00:00:00','','<div class=\"content\"><img src=\"http://habreffect.ru/files/a3b/6282b425f/road-sign-debian-ubuntu-300x199.png\" alt=\"\" align=\"left\" />С  точки зрения пользователя относительно просто сделать выбор между  Debian и Ubuntu. У каждого есть свои личные предпочтения, и попробовать  обе ОС не займет слишком много времени. Но когда дело доходит до вклада в  разработку, времени для этого потребуется гораздо больше, и вам  наверняка захочется подумать об этом дважды, прежде чем начинать. Так на  какую систему лучше потратить свое время?<br /> <br /> Это непростой вопрос, на который нет ответа, который удовлетворил бы  каждого. Все зависит от того, каков ваш стимул для участия в разработке.<br /> <a name=\"habracut\"></a><br />\r\n<h4>Ubuntu: лучше для новичков?</h4>\r\n<br /> С одной стороны, вы наверняка начинали с более дружественной к  пользователю системы &mdash; Ubuntu. Вам она нравится и вы хотели бы  чем-нибудь отплатить проекту, например своим вкладом в разработку.  Отличное решение!<br /> <br /> Кроме того, если вы не из тех людей, кто любит учиться (в основном) в  одиночестве, Ubuntu, скорее всего, будет являться лучшим местом для  вклада (по крайней мере, в начале). С <a href=\"https://wiki.ubuntu.com/UbuntuDeveloperWeek\">Неделей разработчика Ubuntu</a>, а также работой менеджеров сообщества Ubuntu, вы найдете больше помощи новым участникам, чем у Debian.<br /> <br />\r\n<h4>Debian: высокие принципы?</h4>\r\n<br /> С другой же стороны, как только вы станете постоянным вкладчиком, вы им и  останетесь навсегда, благодаря сообществу и тем принципам, которые вас  объединяют.<br /> <br /> Лукас Нуссбаум (который является разработчиком как Ubuntu, так и Debian) в своей <a href=\"http://www.loria.fr/%7Elnussbau/files/minidebconfparis2010-debian-ubuntu.pdf\">речи на mini-debconf в Париже</a> сказал, что сообщество Debian имеет более высокие принципы, поскольку  это работа исключительно добровольцев, в то время как на Ubuntu  оказывает существенное влияние Canonical.<br /> <br /> Это было снова продемонстрировано несколько дней назад историей с <a href=\"http://www.vuntz.net/journal/post/2011/02/28/Canonical%2C-you-re-breaking-my-heart\">Banshee и связанных с ним доходов филиалов Amazon.</a> Мне понравились <a href=\"http://www.markshuttleworth.com/archives/611#comment-345695\">прояснения Марка Шаттлворта</a> на этот счет, но эта история, тем не менее, является доказательством, что власть сообщества Ubuntu имеет свои пределы.<br /> <br /> Возвращаясь к теме статьи, на более фундаментальном уровне во многих  случаях Debian &mdash; верная система для вклада, даже когда вам действительно  хочется помочь Ubuntu. Всякий раз, когда вы работаете над 75% пакетов,  которые пришли непосредственно из Debian, в интересах Ubuntu не  создавать никакого расхождения с Debian. То есть любой багфикс, который  вы бы хотели сделать, в идеале должен быть включен в официальный пакет  Debian (или напрямую в upstream).<br /> <br /> И делая работу для Debian, вы работаете в интересах большего количества  людей, так как ваша работа попадет во все производные от Debian  дистрибутивы (а не только в Ubuntu и её потомков).<br /> <br />\r\n<h4>Зачем вносить вклад?</h4>\r\n<br /> У Debian есть ясный ответ: <a href=\"http://www.debian.org/social_contract\">Общественный договор Debian</a>.  Если вы вносите вклад в развитие Debian, это обычно помогает достигнуть  высокой цели: принести пользователям универсальную ОС высокого  качества.<br /> <br /> Если говорить об Ubuntu, здесь все выглядит не настолько понятно. Где документ, связывающий людей вместе? <a href=\"https://bugs.launchpad.net/ubuntu/+bug/1\">Баг #1</a>, в котором сказано, что Microsoft не должна иметь преимущество на рынке? Или <a href=\"http://www.ubuntu.com/community/conduct\">кодекс корпоративной этики</a>?<br /> <br />\r\n<h4>Вкладывайте в обе</h4>\r\n<br /> В качестве заключения я хотел бы указать на очевидное. Нет никакой  необходимости помогать только одной системе. Вы можете разрабатывать для  обеих систем, как многие и делают. Просто вносите вклад, когда это  имеет смысл.<br /> <br /> Содействуйте разработке Debian, когда требуются глубокие изменения  инфраструктуры, где нужно избегать расхождений, или когда вы планируете  изменить пакет, который еще не изменен в Ubuntu.<br /> <br /> Содействуйте разработке Ubuntu, когда вы работаете над проектами,  которые уже серьезно кастомизированы (или даже форкнуты), или когда вы  работаете над новыми экспериментальными проектами, которые не могут быть  включены в Debian.<br /> <br /> Но надо помнить о том, что выбор существует и спрашивать себя, всякий раз когда вы собираетесь чем-то посодействовать этим ОС.<br /> <br /> <em><strong>Об авторе</strong>: Рафаэль Герцог разработчик Debian. Он работает над пакетным менеджером (dpkg). Также он ведет ежемесячную <a href=\"http://raphaelhertzog.com/email-newsletter/\">информационную рассылку</a>, где делится своими мыслями по поводу новостей о Debian и Ubuntu.</em><br /> <br /> <em>От переводчика: Это мой первый перевод статьи, исправления и критика приветствуются</em></div>'),(3,'Переводим мобильный Firefox в полноэкранный режим под Android ','2011-03-06 00:00:00','Y','N','2011-03-07 16:38:34','0000-00-00 00:00:00','','<p>Тот&nbsp;браузер, который поставляется с&nbsp;системою Android, показывает  системную строку&nbsp;статуса (в&nbsp;которой часы, индикаторы батареи  и&nbsp;будильника, значки соединений и&nbsp;уведомлений) только на&nbsp;время закачки  очередной страницы, а&nbsp;в&nbsp;остальное&nbsp;время развёртывает читаемую&nbsp;страницу  на&nbsp;весь&nbsp;экран, чтобы ничего не&nbsp;мешало чтению. А&nbsp;вот браузер Firefox  не&nbsp;обладает этим немаловажным достоинством. И&nbsp;чем&nbsp;меньше разрешение  экрана по&nbsp;высоте (особенно в&nbsp;альбомном положении), тем&nbsp;досаднее выглядит  строка&nbsp;статуса, тем&nbsp;сильнее мешает&nbsp;она чтению.<br /> <br /> <a href=\"https://addons.mozilla.org/mobile/addon/full-screen-252573/\"><img src=\"http://i55.tinypic.com/jigkcx.png\" alt=\"[скриншот Full Screen]\" align=\"right\" /></a>К&nbsp;счастью, как&nbsp;только</p>\r\n<div class=\"content\">в каком-то  другом браузере обнаруживается полезная особенность, которой недостаёт  Файерфоксу, так&nbsp;сразу и&nbsp;появляется предприимчивый программист  да&nbsp;выпускает такое расширение для&nbsp;Файерфокса, установкою которого можно  невозбранно достигнуть желаемого. Так&nbsp;вышло и&nbsp;на&nbsp;сей&nbsp;раз: Matt&nbsp;Brubeck  выпустил расширение <a href=\"https://addons.mozilla.org/mobile/addon/full-screen-252573/\">Full&nbsp;Screen</a>,  переводящее мобильный Firefox в&nbsp;полноэкранный режим. В&nbsp;меню  предусмотрен переключатель, включающий и&nbsp;отключающий полноэкранность (см. скриншот).<br /> <br /> У&nbsp;этого&nbsp;расширения я&nbsp;подметил два&nbsp;достоинства: во-первых, оно&nbsp;весит всего&nbsp;ничего (5&nbsp;килобайтов с&nbsp;небольшим), а во-вторых, оно&nbsp;применяется мгновенно (не&nbsp;требует перезагрузки мобильного Файерфокса). Так&nbsp;что всем&nbsp;вам его&nbsp;рекомендую.</div>');
/*!40000 ALTER TABLE `bm_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_products`
--

DROP TABLE IF EXISTS `bm_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `top` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nav` varchar(100) NOT NULL,
  `brand` int(11) NOT NULL,
  `price` float(11,2) NOT NULL,
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `anons` text NOT NULL,
  `text` text NOT NULL,
  `types` longtext NOT NULL,
  `is_action` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_featured` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_lider` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_exist` enum('Y','N') NOT NULL DEFAULT 'Y',
  `availability` text NOT NULL,
  `relations` varchar(255) NOT NULL,
  `rate` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `show` (`show`),
  KEY `deleted` (`deleted`),
  KEY `top` (`top`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_products`
--

LOCK TABLES `bm_products` WRITE;
/*!40000 ALTER TABLE `bm_products` DISABLE KEYS */;
INSERT INTO `bm_products` VALUES (1,2,0,'LE32C530F1W','',1,15500.00,'0000-00-00 00:00:00','Y','N','2011-03-07 15:55:02','0000-00-00 00:00:00','LCD телевизор Samsung серии 5','','','N','Y','N','N','','',1,0),(2,2,0,'PS-42C430','',1,18370.00,'0000-00-00 00:00:00','Y','N','2011-03-07 15:59:03','0000-00-00 00:00:00','Телевизор Samsung PS-42C430 подлежит гарантийному обслуживанию в авторизованных сервисных центрах Екатеринбурга','','','N','Y','N','Y','','',1,0),(3,2,0,'42PFL5405','',3,24500.00,'0000-00-00 00:00:00','Y','N','2011-03-07 16:00:48','2011-03-07 16:22:54','Переживайте любой сюжет как часть своей жизни с Pixel Plus HD и мощным объемным звуком','','','N','N','N','Y','','',2,0),(4,5,0,'Legend','',11,15840.00,'0000-00-00 00:00:00','Y','N','2011-03-07 16:06:45','0000-00-00 00:00:00','Металлический корпус!!! Пару раз падал на кафель - ЖИВОЙ!!! экран multitouch.','<h1>HTC Legend &ndash; первый взгляд</h1>\r\n<h3>Фотографии HTC Legend в интерьере</h3>\r\n<table>\r\n<tbody>\r\n<tr>\r\n<td><a href=\"http://www.mobile-review.com/pda/review/image/htc/legend/live/high/1.jpg\" target=\"_blank\"><img src=\"http://www.mobile-review.com/pda/review/image/htc/legend/live/low/1.jpg\" border=\"0\" alt=\"\" width=\"150\" height=\"100\" /></a></td>\r\n<td><a href=\"http://www.mobile-review.com/pda/review/image/htc/legend/live/high/2.jpg\" target=\"_blank\"><img src=\"http://www.mobile-review.com/pda/review/image/htc/legend/live/low/2.jpg\" border=\"0\" alt=\"\" width=\"150\" height=\"100\" /></a></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>Я не зря привел пример с сиквелами. Мне кажется, это самое удачное и  наглядное сравнение, с помощью которого можно объяснить, что же такое  HTC Legend. Представьте, что вы сходили на отличную кинопремьеру или  поиграли в фантастически интересную игру, и вот вы узнаете, что  снимается продолжение фильма или разрабатывается продолжение этой игры.  Первая мысль? Главное, чтобы не испортили! И именно по этому пути идут  многие разработчики и режиссеры, он выигрышный, сложно испортить  успешный фильм или хорошую игру, если в продолжении добавить только то,  что пользовалось успехом в первой части, убрать минусы и представить  готовый продукт публике.</p>\r\n<p>Коммуникатор HTC Legend &ndash; это сиквел HTC Hero, в нем собраны все  плюсы &laquo;героя&raquo;, убраны некоторые недостатки и добавлена пара новшеств.  Продолжение готово. Успешно или нет, и стоит ли переходить на Legend  тем, кто сейчас пользуется Hero?</p>\r\n<p><img src=\"http://www.mobile-review.com/pda/review/image/htc/legend/htc-4-01.jpg\" alt=\"\" width=\"500\" height=\"458\" /></p>','','N','Y','N','Y','','',2,0),(5,5,0,'iPhone 4 16Gb','',9,34900.00,'0000-00-00 00:00:00','Y','N','2011-03-07 16:10:31','0000-00-00 00:00:00','Достоинства:\r\n    * Экран\r\n    * Камера\r\n    * Скорость\r\n    * Удобство использования\r\n    * Дизайн\r\n    * Конструкция','<h3>Дисплей</h3>\r\n<p>Так называемый Retina-дисплей, разрешение 960х640 точек, диагональ  3,5 дюйма. Если Super AMOLED берет яркостью, то &laquo;ретина&raquo; подкупает некой  собранностью. Такое впечатление, что картинка нарисована. Углы обзора  невероятные. Как и насыщенность. Естественно, новый дисплей не только  выглядит лучше, но и в какой-то степени расширяет функциональность.  Удобнее стал просмотр сайтов, работа с почтой, YouTube, ну и для видео  он подходит отлично. Как и во всех прошлых iPhone, работа с сенсорным  дисплеем на интуитивном уровне, multitouch работает быстро и точно, на  данном образце я не заметил желтых пятен - это вроде как еще один  известный &laquo;баг&raquo; iPhone 4. Клей у них там не высох какой-то.</p>\r\n<p><img src=\"http://www.mobile-review.com/review/image/apple/iphone-4/pic/pic42.jpg\" alt=\"\" width=\"500\" height=\"237\" /> <img src=\"http://www.mobile-review.com/review/image/apple/iphone-4/pic/pic43.jpg\" alt=\"\" width=\"500\" height=\"270\" /></p>','','N','Y','N','Y','','',1,0),(6,5,0,'Gratia','',11,15630.00,'0000-00-00 00:00:00','Y','N','2011-03-07 16:14:35','0000-00-00 00:00:00','Хороший аппарат!','','','N','N','N','Y','','',0,0),(7,7,0,'Planet NV-112 175/70 R13 82H','',12,1354.00,'0000-00-00 00:00:00','Y','N','2011-03-07 16:19:00','2011-03-07 16:22:04','Управляемость, Защита от аквапланирования, Бесшумность и комфортабельность','','','N','N','N','Y','','',1,0),(8,7,0,'AA-01 175/70 R13 82T','',13,1713.00,'0000-00-00 00:00:00','Y','N','2011-03-07 16:20:23','2011-03-07 16:22:14','Летняя шина с ненаправленным рисунком протектора для легковых автомобилей','<h3>Функциональные характеристики</h3>\r\n<p>Летняя шина с ненаправленным рисунком протектора для легковых автомобилей. <strong>А.drive</strong> - новый стандарт в производстве шин класса Эконом. Производство этой модели началось в феврале 2005 года. <strong>A.driv</strong><strong>e</strong> выпускается в 60/65/70/80 сериях.<br /><br /><strong>Основные преимущества </strong><br />В  основу успеха положены&nbsp;&nbsp; новейшие технологии компании Yokohama,  улучшенный состав смеси, новый рисунок протектора. В результате  получилась шина, превосходящая своих предшественников и ближайших  конкурентов по уровню сцепления с сухой и мокрой дорогой, боковому  скольжению, при этом, повысилась комфортность и безопасность вождения.<br /><br />Сочетание спортивного характера Yokohama с комфортом и бесшумностью &ndash; это именно то, что вы ждете от качественной шины.</p>','','N','N','N','Y','','',1,0),(9,12,0,'Трактор Massey Ferguson 7480 BRUDER','',0,830.00,'0000-00-00 00:00:00','Y','N','2011-03-07 16:27:45','0000-00-00 00:00:00','Точная копия реальной техники в масштабе 1:16','','','N','N','N','Y','','',1,0),(10,12,0,'Экскаватор Caterpillar BRUDER','',0,650.00,'0000-00-00 00:00:00','Y','N','2011-03-07 16:29:18','0000-00-00 00:00:00','Подвижный ковш; Гусеничный ход','','','N','Y','N','Y','','',1,0);
/*!40000 ALTER TABLE `bm_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_products_brands`
--

DROP TABLE IF EXISTS `bm_products_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_products_brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `nav` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav` (`nav`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_products_brands`
--

LOCK TABLES `bm_products_brands` WRITE;
/*!40000 ALTER TABLE `bm_products_brands` DISABLE KEYS */;
INSERT INTO `bm_products_brands` VALUES (1,0,'Samsung','samsung','','Y','N','2011-03-07 15:47:03','0000-00-00 00:00:00'),(2,0,'Sony','sony','','Y','N','2011-03-07 15:47:09','0000-00-00 00:00:00'),(3,0,'Philips','philips','','Y','N','2011-03-07 15:47:23','0000-00-00 00:00:00'),(4,0,'BBK','bbk','','Y','N','2011-03-07 15:47:46','0000-00-00 00:00:00'),(5,0,'LG','lg','','Y','N','2011-03-07 15:47:54','0000-00-00 00:00:00'),(6,0,'Canon','canon','','Y','N','2011-03-07 15:48:45','0000-00-00 00:00:00'),(7,0,'Nikon','nikon','','Y','N','2011-03-07 15:49:02','0000-00-00 00:00:00'),(8,0,'Nokia','nokia','','Y','N','2011-03-07 15:50:51','0000-00-00 00:00:00'),(9,0,'Apple','apple','','Y','N','2011-03-07 15:52:03','0000-00-00 00:00:00'),(10,0,'Sony-Ericsson','sony-ericsson','','Y','N','2011-03-07 15:52:25','0000-00-00 00:00:00'),(11,0,'HTC','htc','','Y','N','2011-03-07 16:05:40','0000-00-00 00:00:00'),(12,0,'Amtel','amtel','','Y','N','2011-03-07 16:17:41','0000-00-00 00:00:00'),(13,0,'Yokohama','yokohama','','Y','N','2011-03-07 16:19:55','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `bm_products_brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_products_topics`
--

DROP TABLE IF EXISTS `bm_products_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_products_topics` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `top` int(8) unsigned NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `nav` varchar(100) NOT NULL DEFAULT '',
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `text` text NOT NULL,
  `types` longtext NOT NULL,
  `cases` text NOT NULL,
  `rate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `top` (`top`),
  KEY `order` (`order`),
  KEY `deleted` (`deleted`),
  KEY `show` (`show`,`deleted`),
  KEY `show_2` (`show`),
  KEY `rate` (`rate`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_products_topics`
--

LOCK TABLES `bm_products_topics` WRITE;
/*!40000 ALTER TABLE `bm_products_topics` DISABLE KEYS */;
INSERT INTO `bm_products_topics` VALUES (1,0,0,'Электроника и фото','electronics-and-photo','Y','N','2011-03-07 15:42:45','0000-00-00 00:00:00','','','',0),(2,1,0,'Телевизоры','televisions','Y','N','2011-03-07 15:43:02','0000-00-00 00:00:00','','','',4),(3,1,2,'DVD-плееры','dvd-players','Y','N','2011-03-07 15:43:16','0000-00-00 00:00:00','','','',0),(4,1,3,'Фотокамеры','cameras','Y','N','2011-03-07 15:43:28','2011-03-07 15:43:48','','','',0),(5,1,1,'Сотовые телефоны','cell-phones','Y','N','2011-03-07 15:43:59','2011-03-07 15:49:39','','','',3),(6,0,1,'Авто','auto','Y','N','2011-03-07 15:44:16','0000-00-00 00:00:00','','','',0),(10,0,3,'Сантехника','sanitary-engineering','Y','N','2011-03-07 15:45:03','0000-00-00 00:00:00','','','',0),(7,6,0,'Шины','bus','Y','N','2011-03-07 15:44:19','0000-00-00 00:00:00','','','',2),(8,6,2,'Диски','discs','Y','N','2011-03-07 15:44:25','0000-00-00 00:00:00','','','',0),(9,6,3,'Магнитолы','radios','Y','N','2011-03-07 15:44:32','0000-00-00 00:00:00','','','',0),(11,0,4,'Детские товары','children-s-products','Y','N','2011-03-07 15:45:11','0000-00-00 00:00:00','','','',0),(12,11,0,'Игрушки','toys','Y','N','2011-03-07 15:45:23','0000-00-00 00:00:00','','','',2),(13,11,2,'Коляски','strollers','Y','N','2011-03-07 15:45:41','0000-00-00 00:00:00','','','',0),(14,11,3,'Автокресла','car-seats','Y','N','2011-03-07 15:45:51','0000-00-00 00:00:00','','','',0),(15,10,0,'Ванны','baths','Y','N','2011-03-07 15:46:02','0000-00-00 00:00:00','','','',0),(16,10,2,'Душевые кабины','shower-stalls','Y','N','2011-03-07 15:46:08','0000-00-00 00:00:00','','','',0),(17,10,3,'Смесители','mixers','Y','N','2011-03-07 15:46:25','0000-00-00 00:00:00','','','',0);
/*!40000 ALTER TABLE `bm_products_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_seo`
--

DROP TABLE IF EXISTS `bm_seo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_seo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `module_id` int(11) NOT NULL,
  `module_table` varchar(64) NOT NULL,
  `title` varchar(1024) NOT NULL,
  `keywords` varchar(2048) NOT NULL,
  `description` varchar(2048) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_seo`
--

LOCK TABLES `bm_seo` WRITE;
/*!40000 ALTER TABLE `bm_seo` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_seo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_settings`
--

DROP TABLE IF EXISTS `bm_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `callname` varchar(128) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_settings`
--

LOCK TABLES `bm_settings` WRITE;
/*!40000 ALTER TABLE `bm_settings` DISABLE KEYS */;
INSERT INTO `bm_settings` VALUES (2,'Catalog','Товаров на страницу','onpage','10','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'Catalog','Внутренняя валюта каталога','inner_currency','RUR','0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'Catalog','Наценка на конвертацию валюты, %','currency_margin','0','0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,'Catalog','Округление цен','price_round','-1','0000-00-00 00:00:00','0000-00-00 00:00:00'),(7,'Shop','Почта для уведомления о заказе','notify_mail','andreydust@gmail.com','0000-00-00 00:00:00','0000-00-00 00:00:00'),(8,'Blocks','Название сайта','site_title','Booot','0000-00-00 00:00:00','0000-00-00 00:00:00'),(9,'Blocks','Почта администратора','admin_mail','andreydust@gmail.com','0000-00-00 00:00:00','0000-00-00 00:00:00'),(10,'Blocks','Тема оформления','site_theme','sunrise','0000-00-00 00:00:00','0000-00-00 00:00:00'),(11,'Catalog','Включить комментарии у товаров, Y или N','comments','Y','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `bm_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_shop_orders`
--

DROP TABLE IF EXISTS `bm_shop_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_shop_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `paymethod` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_shop_orders`
--

LOCK TABLES `bm_shop_orders` WRITE;
/*!40000 ALTER TABLE `bm_shop_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_shop_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_shop_orders_items`
--

DROP TABLE IF EXISTS `bm_shop_orders_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_shop_orders_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product` int(11) NOT NULL,
  `brand` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `top` int(11) NOT NULL,
  `price` float(11,2) NOT NULL,
  `count` int(11) NOT NULL,
  `total_fake` float(11,2) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_shop_orders_items`
--

LOCK TABLES `bm_shop_orders_items` WRITE;
/*!40000 ALTER TABLE `bm_shop_orders_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_shop_orders_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_shop_paymethods`
--

DROP TABLE IF EXISTS `bm_shop_paymethods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_shop_paymethods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_shop_paymethods`
--

LOCK TABLES `bm_shop_paymethods` WRITE;
/*!40000 ALTER TABLE `bm_shop_paymethods` DISABLE KEYS */;
INSERT INTO `bm_shop_paymethods` VALUES (1,0,'Manual','Наличными','Y','<p>По Москве мы доставляем заказы собственной курьерской службой, поэтому возможна оплата при получении заказа. Оплата производится в рублях по курсу, указанному на сайте.</p>'),(2,1,'Manual','Пластиковой картой','Y',''),(3,2,'Manual','Безналичный расчет','Y',''),(4,3,'WM','WebMoney','Y','');
/*!40000 ALTER TABLE `bm_shop_paymethods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_shop_statuses`
--

DROP TABLE IF EXISTS `bm_shop_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_shop_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_shop_statuses`
--

LOCK TABLES `bm_shop_statuses` WRITE;
/*!40000 ALTER TABLE `bm_shop_statuses` DISABLE KEYS */;
INSERT INTO `bm_shop_statuses` VALUES (1,0,'New','Новый'),(2,0,'Profit','Доставлен'),(3,0,'Noprofit','Отказ');
/*!40000 ALTER TABLE `bm_shop_statuses` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-03-07 16:56:34
