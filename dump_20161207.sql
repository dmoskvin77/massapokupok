/*
SQLyog Ultimate v8.5 
MySQL - 5.5.35-33.0-log : Database - host1320388_spm
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `allMail` */

DROP TABLE IF EXISTS `allMail`;

CREATE TABLE `allMail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subj` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `toMail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mailFrom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mailFromName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ccId` int(11) DEFAULT NULL,
  `zcId` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `allMail` */

LOCK TABLES `allMail` WRITE;

UNLOCK TABLES;

/*Table structure for table `allvkNotice` */

DROP TABLE IF EXISTS `allvkNotice`;

CREATE TABLE `allvkNotice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vkId` int(11) DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `ccId` int(11) DEFAULT NULL,
  `zcId` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `allvkNotice` */

LOCK TABLES `allvkNotice` WRITE;

UNLOCK TABLES;

/*Table structure for table `boardAd` */

DROP TABLE IF EXISTS `boardAd`;

CREATE TABLE `boardAd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `sourceType` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sourceId` int(11) DEFAULT NULL,
  `typeId` int(11) DEFAULT NULL,
  `catId` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8_unicode_ci,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateCreate` int(11) DEFAULT NULL,
  `dateUpdate` int(11) DEFAULT NULL,
  `datePublish` int(11) DEFAULT NULL,
  `picFile1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picSrv1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picVer1` int(3) NOT NULL DEFAULT '1',
  `option1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option4` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option5` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option6` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option7` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option8` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option9` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `viewCnt` int(11) NOT NULL DEFAULT '0',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`sourceType`,`sourceId`,`status`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `boardAd` */

LOCK TABLES `boardAd` WRITE;

UNLOCK TABLES;

/*Table structure for table `boardCategory` */

DROP TABLE IF EXISTS `boardCategory`;

CREATE TABLE `boardCategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `boardTypeId` int(11) DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alias` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cnt` int(11) NOT NULL DEFAULT '0',
  `position` int(3) NOT NULL DEFAULT '0',
  `status` int(3) NOT NULL DEFAULT '1',
  `optionType1` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionName1` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionType2` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionName2` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionType3` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionName3` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionType4` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionName4` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionType5` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionName5` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionType6` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionName6` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionType7` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionName7` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionType8` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionName8` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionType9` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optionName9` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `bt_idx` (`boardTypeId`),
  KEY `alias` (`alias`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `boardCategory` */

LOCK TABLES `boardCategory` WRITE;

UNLOCK TABLES;

/*Table structure for table `boardType` */

DROP TABLE IF EXISTS `boardType`;

CREATE TABLE `boardType` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alias` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cnt` int(11) NOT NULL DEFAULT '0',
  `position` int(3) NOT NULL DEFAULT '0',
  `status` int(3) NOT NULL DEFAULT '1',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `boardType` */

LOCK TABLES `boardType` WRITE;

insert  into `boardType`(`id`,`name`,`alias`,`cnt`,`position`,`status`,`ownerSiteId`,`ownerOrgId`) values ('1','Пристраиваю','pristraivayu','1','1','1','1','0'),('2','Продаю','prodayu','2','2','1','1','0'),('3','Обменяю','menyayu','0','3','1','1','0'),('4','Отдам за шоколадку','otdam-za-shokoladku','0','4','1','1','0'),('5','Куплю','kuplyu','0','5','1','1','0'),('6','Услуги','okaju-uslugu','0','6','0','1','0'),('7','Скидки','dayu-skidku','0','7','0','1','0'),('8','Пристраиваю','pristraivayu','5','1','1','2','0'),('9','Продаю','prodayu','0','2','1','2','0'),('10','Обменяю','menyayu','0','3','1','2','0'),('11','Отдам за шоколадку','otdam-za-shokoladku','0','4','1','2','0'),('12','Куплю','kuplyu','0','5','1','2','0'),('13','Услуги','okaju-uslugu','0','6','0','2','0'),('14','Скидки','dayu-skidku','0','7','0','2','0'),('15','Пристраиваю','pristraivayu','3','1','1','3','0'),('16','Продаю','prodayu','0','2','1','3','0'),('17','Обменяю','menyayu','0','3','1','3','0'),('18','Отдам за шоколадку','otdam-za-shokoladku','0','4','1','3','0'),('19','Куплю','kuplyu','0','5','1','3','0'),('20','Услуги','okaju-uslugu','0','6','0','3','0'),('21','Скидки','dayu-skidku','0','7','0','3','0'),('22','Пристраиваю','pristraivayu','0','1','1','4','0'),('23','Продаю','prodayu','0','2','1','4','0'),('24','Обменяю','menyayu','0','3','1','4','0'),('25','Отдам за шоколадку','otdam-za-shokoladku','0','4','1','4','0'),('26','Куплю','kuplyu','0','5','1','4','0'),('27','Услуги','okaju-uslugu','0','6','0','4','0'),('28','Скидки','dayu-skidku','0','7','0','4','0');

UNLOCK TABLES;

/*Table structure for table `broadcast` */

DROP TABLE IF EXISTS `broadcast`;

CREATE TABLE `broadcast` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orgId` int(11) NOT NULL,
  `dateCreate` int(11) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `orgId` (`orgId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `broadcast` */

LOCK TABLES `broadcast` WRITE;

UNLOCK TABLES;

/*Table structure for table `category` */

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` int(3) NOT NULL DEFAULT '1',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='категории товара - например: для мужчин, для женщин и т.д.';

/*Data for the table `category` */

LOCK TABLES `category` WRITE;

insert  into `category`(`id`,`parentId`,`name`,`path`,`level`,`ownerSiteId`,`ownerOrgId`) values ('1','0','Для женщин','|1|','1','1','0'),('2','0','Для мужчин','|2|','1','1','0'),('3','0','Для детей','|3|','1','1','0'),('4','0','Для дома','|4|','1','1','0'),('5','0','Для авто','|5|','1','1','0'),('6','0','Техника','|6|','1','1','0'),('12','0','Для детей','|12|','1','2','0'),('13','12','Детская верхняя одежда','|12|13|','2','2','0'),('14','12','Детская обувь','|12|14|','2','2','0'),('15','12','Товары для детей','|12|15|','2','2','0'),('16','12','Школьный базар','|12|16|','2','2','0'),('17','12','Игрушки','|12|17|','2','2','0'),('20','0','Электроника и бытовая техника','|20|','1','2','0'),('23','0','Все для беременных и кормящих мам','|23|','1','2','0'),('24','0','Головные уборы и акссесуары','|24|','1','2','0'),('25','0','Белье,колготки','|25|','1','2','0'),('26','0','Все для красоты','|26|','1','2','0'),('27','0','Текстиль','|27|','1','2','0'),('28','0','Продукты','|28|','1','2','0'),('29','0','Товары для дома','|29|','1','2','0'),('31','0','Досуг: спорт, отдых, чтение и др.','|31|','1','2','0'),('32','0','Товары для сада, огорода','|32|','1','2','0'),('35','12','Детская одежда','|12|35|','2','2','0'),('40','0','Домашняя одежда','|40|','1','2','0'),('41','0','Женская одежда','|41|','1','2','0'),('42','0','Мужская одежда','|42|','1','2','0'),('43','0','Обувь для взрослых','|43|','1','2','0'),('44','0','Верхняя одежда для взрослых','|44|','1','2','0'),('86','0','Мужская одежда','|86|','1','3','0'),('87','0','Женская одежда','|87|','1','3','0'),('88','0','Детская одежда и обувь','|88|','1','3','0'),('89','0','Игрушки, товары для детей','|89|','1','3','0'),('90','0','Белье, колготки, купальники','|90|','1','3','0'),('91','0','Обувь, сумки, аксессуары','|91|','1','3','0'),('92','0','Косметика, парфюмерия','|92|','1','3','0'),('93','0','Товары для дома','|93|','1','3','0'),('94','0','Продукты','|94|','1','3','0'),('95','0','ПК, электроника, быт.техника','|95|','1','3','0'),('96','0','Прочие товары','|96|','1','3','0'),('99','105','Верхняя одежда','|105|99|','2','4','0'),('100','0','Для мужчин','|2|','1','4','0'),('101','0','Для детей','|3|','1','4','0'),('102','0','Для дома','|4|','1','4','0'),('103','0','Для авто','|5|','1','4','0'),('104','0','Техника','|6|','1','4','0'),('105','0','Для женщин','|105|','1','4','0'),('106','100','Верхняя одежда','|100|106|','2','4','0'),('107','101','Верхняя одежда','|101|107|','2','4','0'),('108','0','Книги, канцтовары','|108|','1','3','0'),('109','0','Обувь','|109|','1','6','0'),('110','0','Товары для детей','|110|','1','6','0'),('111','0','Одежда','|111|','1','6','0'),('113','0','Книги','|113|','1','6','0'),('114','0','Товары с иностранных сайтов','|114|','1','6','0'),('115','0','Аксессуары','|115|','1','6','0'),('116','0','Парфюмерия и косметика','|116|','1','6','0'),('117','110','Игрушки','|110|117|','2','6','0'),('118','110','Верхняя одежда','|110|118|','2','6','0'),('119','110','Одежда','|110|119|','2','6','0'),('120','0','Детская одежда','|120|','1','1','1'),('121','0','Детская обувь','|121|','1','1','1'),('122','0','Одежда для взрослых','|122|','1','1','1'),('123','0','Обувь для взрослых','|123|','1','1','1'),('124','0','Товары для дома','|124|','1','1','1'),('125','0','Косметика и аксессуары','|125|','1','1','1'),('126','0','Продукты питания','|126|','1','1','1'),('127','0','Детские товары и игрушки','|127|','1','1','1'),('128','0','Детские книги','|128|','1','1','1'),('129','0','Одежда для женщин','|129|','1','7','0'),('130','0','Одежда для детей','|130|','1','7','0');

UNLOCK TABLES;

/*Table structure for table `city` */

DROP TABLE IF EXISTS `city`;

CREATE TABLE `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `predName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vkCityId` int(11) DEFAULT NULL,
  `vkCountryId` int(11) DEFAULT NULL,
  `entityStatus` int(3) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=636 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `city` */

LOCK TABLES `city` WRITE;

insert  into `city`(`id`,`name`,`predName`,`vkCityId`,`vkCountryId`,`entityStatus`,`ownerSiteId`,`ownerOrgId`) values ('1','Санкт-Петербург','SanktPeterburg','2','1','1','1','0'),('2','Стерлитамак','Sterlitamak','1134837','1','1','1','0'),('3','Ростов-на-Дону','RostovnaDonu','119','1','1','1','0'),('4','Петрозаводск','Petrozavodsk','112','1','1','1','0'),('5','Стерлитамак','Sterlitamak','135','1','1','1','0'),('6','Мелеуз','Meleuz','2093','1','1','1','0'),('7','Москва','Moskva','1','1','1','1','0'),('8','Карачев','Karachev','10406','1','1','1','0'),('9','Ишимбай','Ishimbay','360','1','1','1','0'),('10','Челябинск','CHeliabinsk','158','1','1','1','0'),('11','Таганрогский','Taganrogskiy','1078183','1','1','1','0'),('12','Волгоград','Volgograd','10','1','1','1','0'),('13','Омск','Omsk','104','1','1','1','0'),('14','Чамзинка','CHamzinka','19612','1','1','1','0'),('15','Архангельск','Arhangelsk','22','1','1','1','0'),('16','Уфа','Ufa','151','1','1','1','0'),('17','Новосибирск','Novosibirsk','99','1','1','1','0'),('18','Ижевск','Izhevsk','56','1','1','1','0'),('19','Сургут','Surgut','136','1','1','1','0'),('20','Мурманск','Murmansk','87','1','1','1','0'),('21','Дюртюли','Djurtjuli','1660','1','1','1','0'),('22','Северодвинск','Severodvinsk','11','1','1','1','0'),('23','Новодвинск','Novodvinsk','338','1','1','1','0'),('24','Салават','Salavat','264','1','1','1','0'),('25','Нижний Тагил','NizhniyTagil','96','1','1','1','0'),('26','Сафакулево','Safakulevo','14362','1','1','1','0'),('27','Великий Новгород','VelikiyNovgorod','35','1','1','1','0'),('28','Аняс','Anias','1007993','1','1','1','0'),('29','Октябрьский','Oktiabrskiy','9196','1','1','1','0'),('30','Октябрьский','Oktiabrskiy','1008689','1','1','1','0'),('31','Орел','Orel','105','1','1','1','0'),('32','Орехово-Зуево','OrehovoZuevo','107','1','1','1','0'),('33','Мегион','Megion','294','1','1','1','0'),('34','Тюмень','Tjumen','147','1','1','1','0'),('35','Архангел','Arhangel','1126232','1','1','1','0'),('36','Туймазы','Tuymazy','21244','1','1','1','0'),('37','Саранск','Saransk','124','1','1','1','0'),('38','Саратов','Saratov','125','1','1','1','0'),('39','Сыктывкар','Syktyvkar','138','1','1','1','0'),('40','Кантемировка','Kantemirovka','6732','1','1','1','0'),('41','Курск','Kursk','75','1','1','1','0'),('42','Набережные Челны','NaberezhnyeCHelny','88','1','1','1','0'),('43','Краснодар','Krasnodar','72','1','1','1','0'),('44','Нижний Новгород','NizhniyNovgorod','95','1','1','1','0'),('45','Королев','Korolev','859','1','1','1','0'),('46','Казань','Kazan','60','1','1','1','0'),('47','Владивосток','Vladivostok','37','1','1','1','0'),('48','Бердск','Berdsk','1133','1','1','1','0'),('49','Балашиха','Balashiha','24','1','1','1','0'),('50','Кумертау','Kumertau','1245','1','1','1','0'),('51','Отрадное','Otradnoe','5','1','1','1','0'),('52','Мыски','Myski','3248','1','1','1','0'),('53','Калининград','Kaliningrad','61','1','1','1','0'),('54','Чита','CHita','161','1','1','1','0'),('55','Духовщина','Duhovshhina','4822','1','1','1','0'),('56','Юрга','JUrga','258','1','1','1','0'),('57','Воронеж','Voronezh','42','1','1','1','0'),('58','Самара','Samara','123','1','1','1','0'),('59','Ульяновск','Ulianovsk','149','1','1','1','0'),('60','Дивногорск','Divnogorsk','641','1','1','1','0'),('61','Факел','Fakel','1109904','1','1','1','0'),('62','Электросталь','JElektrostal','580','1','1','1','0'),('63','Урай','Uray','15714','1','1','1','0'),('64','Красноярск','Krasnoiarsk','73','1','1','1','0'),('65','Ревда','Revda','22299','1','1','1','0'),('66','Кварцитный','Kvartsitnyy','1036268','1','1','1','0'),('67','Архангельск','Arhangelsk','1025244','1','1','1','0'),('68','Радужный','Raduzhnyy','1426','1','1','1','0'),('69','Камские Поляны','KamskiePoliany','18475','1','1','1','0'),('70','Уфа','Ufa','1157522','1','1','1','0'),('71','Дмитриев-Льговский','DmitrievLgovskiy','15713','1','1','1','0'),('72','Иглино','Iglino','5999','1','1','1','0'),('73','Давлеканово','Davlekanovo','1006083','1','1','1','0'),('74','Давлеканово','Davlekanovo','14210','1','1','1','0'),('75','Назарово','Nazarovo','2611','1','1','1','0'),('76','Ярославль','IAroslavl','169','1','1','1','0'),('77','Троицко-Печорск','TroitskoPechorsk','1676','1','1','1','0'),('78','Екатеринбург','Ekaterinburg','49','1','1','1','0'),('79','Пермь','Perm','110','1','1','1','0'),('80','Марс','Mars','11688','1','1','1','0'),('81','Арзамас','Arzamas','20','1','1','1','0'),('82','Гулькевичи','Gulkevichi','2895','1','1','1','0'),('83','Пенза','Penza','109','1','1','1','0'),('84','Нижневартовск','Nizhnevartovsk','93','1','1','1','0'),('85','Псков','Pskov','114','1','1','1','0'),('86','Вятские Поляны','ViatskiePoliany','13307','1','1','1','0'),('87','Иркутск','Irkutsk','57','1','1','1','0'),('88','Моршанск','Morshansk','2211','1','1','1','0'),('89','Альметьевск','Almetevsk','18','1','1','1','0'),('90','Благовещенск','Blagoveshhensk','21365','1','1','1','0'),('91','Ижморский','Izhmorskiy','1129174','1','1','1','0'),('92','Мценск','Mtsensk','609','1','1','1','0'),('93','Уфа','Ufa','1150693','1','1','1','0'),('94','Полевской','Polevskoy','19977','1','1','1','0'),('95','Стерлибашево','Sterlibashevo','1008285','1','1','1','0'),('96','Тольятти','Toliatti','143','1','1','1','0'),('97','Крестцы','Kresttsy','1061561','1','1','1','0'),('98','Волхов','Volhov','199','1','1','1','0'),('99','Оренбург','Orenburg','106','1','1','1','0'),('100','Ставрополь','Stavropol','134','1','1','1','0'),('101','Орск','Orsk','108','1','1','1','0'),('102','Чистополь','CHistopol','5103','1','1','1','0'),('103','Балаково','Balakovo','9930','1','1','1','0'),('104','Липецк','Lipetsk','78','1','1','1','0'),('105','Солнечногорск','Solnechnogorsk','352','1','1','1','0'),('106','Кировск','Kirovsk','248','1','1','1','0'),('107','Шерегеш','SHeregesh','1129878','1','1','1','0'),('108','Нефтекамск','Neftekamsk','243','1','1','1','0'),('109','Тюмень','Tjumen','1152691','1','1','1','0'),('110','Азнакаево','Aznakaevo','1496','1','1','1','0'),('111','Чебоксары','CHeboksary','157','1','1','1','0'),('112','Рубцовск','Rubtsovsk','424','1','1','1','0'),('113','Тамбов','Tambov','140','1','1','1','0'),('114','Южно-Сахалинск','JUzhnoSahalinsk','167','1','1','1','0'),('115','Подольск','Podolsk','270','1','1','1','0'),('116','Новокузнецк','Novokuznetsk','97','1','1','1','0'),('117','Зеленодольск','Zelenodolsk','545','1','1','1','0'),('118','Волжский','Volzhskiy','40','1','1','1','0'),('119','Белебей','Belebey','1167','1','1','1','0'),('120','Раевский','Raevskiy','14867','1','1','1','0'),('121','Ирбит','Irbit','1722','1','1','1','0'),('122','Киселевск','Kiselevsk','5915','1','1','1','0'),('123','Ялуторовск','IAlutorovsk','6178','1','1','1','0'),('124','Снежногорск','Snezhnogorsk','20281','1','1','1','0'),('125','Барнаул','Barnaul','25','1','1','1','0'),('126','Заозерск','Zaozersk','7406','1','1','1','0'),('127','Полярный','Poliarnyy','7139','1','1','1','0'),('128','Вологда','Vologda','41','1','1','1','0'),('129','Елабуга','Elabuga','50','1','1','1','0'),('130','Гаджиево','Gadzhievo','1062','1','1','1','0'),('131','Ломоносово','Lomonosovo','1003673','1','1','1','0'),('132','Апатиты','Apatity','175','1','1','1','0'),('133','Боровичи','Borovichi','353','1','1','1','0'),('134','Рыбинск','Rybinsk','121','1','1','1','0'),('135','Кашира','Kashira','1014','1','1','1','0'),('136','Исянгулово','Isiangulovo','10832','1','1','1','0'),('137','Плесецк','Plesetsk','14737','1','1','1','0'),('138','Североморск','Severomorsk','172','1','1','1','0'),('139','Балахна','Balahna','6111','1','1','1','0'),('140','Шатура','SHatura','848','1','1','1','0'),('141','Кемерово','Kemerovo','64','1','1','1','0'),('142',NULL,NULL,'0',NULL,'1','1','0'),('143','Первомайский','Pervomayskiy','1941819',NULL,'1','1','0'),('144','Нерюнгри','Nerjungri','321',NULL,'1','1','0'),('145','Ульяновское','Ulianovskoe','1702127',NULL,'1','1','0'),('146','Лосино-Петровский','LosinoPetrovskiy','7785',NULL,'1','1','0'),('147','Мраково','Mrakovo','16368',NULL,'1','1','0'),('148','Кандалакша','Kandalaksha','326',NULL,'1','1','0'),('149','Сочи','Sochi','133',NULL,'1','1','0'),('150','Тосно','Tosno','20691',NULL,'1','1','0'),('151','Кривой Рог','KrivoyRog','916',NULL,'1','1','0'),('152','Тирлянский','Tirlianskiy','1005577',NULL,'1','1','0'),('153','Стародуб','Starodub','1137',NULL,'1','1','0'),('154','Белгород','Belgorod','26',NULL,'1','1','0'),('155','Красный Лиман','KrasnyyLiman','1503017',NULL,'1','1','0'),('156','Димитровград','Dimitrovgrad','476',NULL,'1','1','0'),('157','Харьков','Harkov','280',NULL,'1','1','0'),('158','Балта','Balta','1508854',NULL,'1','1','0'),('159','Киев','Kiev','314',NULL,'1','1','0'),('160','Чехов','CHehov','872',NULL,'1','1','0'),('161','Полтава','Poltava','1581',NULL,'1','1','0'),('162','Хабаровск','Habarovsk','153',NULL,'1','1','0'),('163','Усть-Илимск','UstIlimsk','1036',NULL,'1','1','0'),('164','Днепропетровск','Dnepropetrovsk','650',NULL,'1','1','0'),('165','Лобня','Lobnia','8162',NULL,'1','1','0'),('166','Буздяк','Buzdiak','12690',NULL,'1','1','0'),('167','Федоровка','Fedorovka','16217',NULL,'1','1','0'),('168','Сумы','Sumy','528',NULL,'1','1','0'),('169','Тюльган','Tjulgan','18742',NULL,'1','1','0'),('170','New York City','NewYorkCity','378',NULL,'1','1','0'),('171','Городок','Gorodok','4652',NULL,'1','1','0'),('172','Ровно','Rovno','3170',NULL,'1','1','0'),('173','Йошкар-Ола','YoshkarOla','59',NULL,'1','1','0'),('174','Череповец','CHerepovets','8',NULL,'1','1','0'),('175','Львов','Lvov','1057',NULL,'1','1','0'),('176','Толбазы','Tolbazy','13201',NULL,'1','1','0'),('177','Калуш','Kalush','3740',NULL,'1','1','0'),('178','Белово','Belovo','994',NULL,'1','1','0'),('179','Хмельная','Hmelnaia','1514142',NULL,'1','1','0'),('180','Кролевец','Krolevets','6147',NULL,'1','1','0'),('181','Комсомольск','Komsomolsk','3004',NULL,'1','1','0'),('182','Гомель','Gomel','392',NULL,'1','1','0'),('183','Ухта','Uhta','152',NULL,'1','1','0'),('184','Евпатория','Evpatoriia','799',NULL,'1','1','0'),('185','Сольцы 2','Soltsy2','1063550',NULL,'1','1','0'),('186','Чишмы','CHishmy','2856',NULL,'1','1','0'),('187','Могилев-Подольский','MogilevPodolskiy','5177',NULL,'1','1','0'),('188','Большие Кибячи','BolshieKibiachi','1096927',NULL,'1','1','0'),('189','Кострома','Kostroma','71',NULL,'1','1','0'),('190','Херсон','Herson','427',NULL,'1','1','0'),('191','Феодосия','Feodosiia','1483',NULL,'1','1','0'),('192','Сортавала','Sortavala','891',NULL,'1','1','0'),('193','Минск','Minsk','282',NULL,'1','1','0'),('194','Victoria','Victoria','1973675',NULL,'1','1','0'),('195','Мелитополь','Melitopol','1315',NULL,'1','1','0'),('196','Магнитогорск','Magnitogorsk','82',NULL,'1','1','0'),('197','Усинск','Usinsk','21713',NULL,'1','1','0'),('198','Великие Луки','VelikieLuki','34',NULL,'1','1','0'),('199','Канск','Kansk','1930',NULL,'1','1','0'),('200','Лениногорск','Leninogorsk','1456',NULL,'1','1','0'),('201','Сланцы','Slantsy','525',NULL,'1','1','0'),('202','Первомайск','Pervomaysk','3827',NULL,'1','1','0'),('203','Черновцы','CHernovtsy','1568',NULL,'1','1','0'),('204','Луцк','Lutsk','5974',NULL,'1','1','0'),('205','Бирск','Birsk','29',NULL,'1','1','0'),('206','Барановичи','Baranovichi','538',NULL,'1','1','0'),('207','Химки','Himki','155',NULL,'1','1','0'),('208','Вуктыл','Vuktyl','2943',NULL,'1','1','0'),('209','Щекино','SHHekino','8171',NULL,'1','1','0'),('210','Иваново','Ivanovo','55',NULL,'1','1','0'),('211','Киров','Kirov','1919452',NULL,'1','1','0'),('212','Баймак','Baymak','5048',NULL,'1','1','0'),('213','Кош-Елга','KoshElga','1005639',NULL,'1','1','0'),('214','Новочебоксарск','Novocheboksarsk','2451',NULL,'1','1','0'),('215','Одинцово','Odintsovo','4890',NULL,'1','1','0'),('216','Кимры','Kimry','1769',NULL,'1','1','0'),('217','Cardiff','Cardiff','4872',NULL,'1','1','0'),('218','Запорожье','Zaporozhe','628',NULL,'1','1','0'),('219','Заинск','Zainsk','4242',NULL,'1','1','0'),('220','Севастополь','Sevastopol','185',NULL,'1','1','0'),('221','Лыткарино','Lytkarino','801',NULL,'1','1','0'),('222','Семеновка','Semenovka','1515420',NULL,'1','1','0'),('223','Луганск','Lugansk','552',NULL,'1','1','0'),('224','Шахты','SHahty','164',NULL,'1','1','0'),('225','Сокол','Sokol','12923',NULL,'1','1','0'),('226','Чайковский','CHaykovskiy','156',NULL,'1','1','0'),('227','Рязань','Riazan','122',NULL,'1','1','0'),('228','Ефремов','Efremov','1545',NULL,'1','1','0'),('229','R?ga','R?ga','1925340',NULL,'1','1','0'),('230','Благовещенск','Blagoveshhensk','30',NULL,'1','1','0'),('231','Бердянск','Berdiansk','4316',NULL,'1','1','0'),('232','','_1','16678',NULL,'1','1','0'),('233','Каменка','Kamenka','20962',NULL,'1','1','0'),('234','Зеленоград','Zelenograd','1463',NULL,'1','1','0'),('235','Дзержинск','Dzerzhinsk','621',NULL,'1','1','0'),('236','Шостка','SHostka','4494',NULL,'1','1','0'),('237','Мирный','Mirnyy','3621',NULL,'1','1','0'),('238','Идрица','Idritsa','5294',NULL,'1','1','0'),('239','Средняя Камышла','SredniaiaKamyshla','1096722',NULL,'1','1','0'),('240','Брест','Brest','281',NULL,'1','1','0'),('241','Когалым','Kogalym','246',NULL,'1','1','0'),('242','Донецк','Donetsk','223',NULL,'1','1','0'),('243','Семилетка','Semiletka','1006509',NULL,'1','1','0'),('244','Лиски','Liski','1024485',NULL,'1','1','0'),('245','Мукачево','Mukachevo','3031',NULL,'1','1','0'),('246','Долгопрудный','Dolgoprudnyy','857',NULL,'1','1','0'),('247','Ташкент','Tashkent','194',NULL,'1','1','0'),('248','Godmanchester','Godmanchester','4237599',NULL,'1','1','0'),('249','Панковка','Pankovka','1703',NULL,'1','1','0'),('250','Бровары','Brovary','1509',NULL,'1','1','0'),('251','Покровка','Pokrovka','1008377',NULL,'1','1','0'),('252','Ильичевск','Ilichevsk','3441',NULL,'1','1','0'),('253','Малая Вишера','MalaiaVishera','2465',NULL,'1','1','0'),('254','Киров','Kirov','1000215',NULL,'1','1','0'),('255','Норильск','Norilsk','102',NULL,'1','1','0'),('256','Владимир','Vladimir','39',NULL,'1','1','0'),('257','Братск','Bratsk','32',NULL,'1','1','0'),('258','Тверь','Tver','141',NULL,'1','1','0'),('259','Москва','Moskva','1074996',NULL,'1','1','0'),('260','Калуга','Kaluga','62',NULL,'1','1','0'),('261','Аша','Asha','5413',NULL,'1','1','0'),('262','Ноябрьск','Noiabrsk','403',NULL,'1','1','0'),('263','Африканда','Afrikanda','8282',NULL,'1','1','0'),('264','Канаш','Kanash','790',NULL,'1','1','0'),('265','Елец','Elets','51',NULL,'1','1','0'),('266','Кушнаренково','Kushnarenkovo','6470',NULL,'1','1','0'),('267','Лебедянь','Lebedian','760',NULL,'1','1','0'),('268','Богушевск','Bogushevsk','16702',NULL,'1','1','0'),('269','Черновское','CHernovskoe','6954',NULL,'1','1','0'),('270','Полярные Зори','PoliarnyeZori','376',NULL,'1','1','0'),('271','Красноусольский','Krasnousolskiy','15732',NULL,'1','1','0'),('272','Домодедово','Domodedovo','1054308',NULL,'1','1','0'),('273','Смела','Smela','4116',NULL,'1','1','0'),('274','Сибай','Sibay','1767',NULL,'1','1','0'),('275','Новомосковск','Novomoskovsk','964',NULL,'1','1','0'),('276','Чашники','CHashniki','2289',NULL,'1','1','0'),('277','Очеретиное','Ocheretinoe','10442',NULL,'1','1','0'),('278','Лебедин','Lebedin','6254',NULL,'1','1','0'),('279','Сызрань','Syzran','952',NULL,'1','1','0'),('280','Няндома','Niandoma','3644',NULL,'1','1','0'),('281','Староконстантинов','Starokonstantinov','4566',NULL,'1','1','0'),('282','Чекмагуш','CHekmagush','3044',NULL,'1','1','0'),('283','Таганрог','Taganrog','139',NULL,'1','1','0'),('284','Чернигов','CHernigov','444',NULL,'1','1','0'),('285','Воркута','Vorkuta','190',NULL,'1','1','0'),('286','Кушва','Kushva','1002',NULL,'1','1','0'),('287','Сердобск','Serdobsk','16746',NULL,'1','1','0'),('288','Шаргород','SHargorod','17320',NULL,'1','1','0'),('289','Кандры','Kandry','9107',NULL,'1','1','0'),('290','Черновцы','CHernovtsy','1501458',NULL,'1','1','0'),('291','Бавлы','Bavly','5887',NULL,'1','1','0'),('292','Чериков','CHerikov','17557',NULL,'1','1','0'),('293','Нижнекамск','Nizhnekamsk','94',NULL,'1','1','0'),('294','Кондопога','Kondopoga','1086',NULL,'1','1','0'),('295','Калуга','Kaluga','1066507',NULL,'1','1','0'),('296','Губкин','Gubkin','1009964',NULL,'1','1','0'),('297','Татышлы Верхние','TatyshlyVerhnie','1008499',NULL,'1','1','0'),('298','Териберка','Teriberka','1060386',NULL,'1','1','0'),('299','Jelgava','Jelgava','1801712',NULL,'1','1','0'),('300','Чурачики','CHurachiki','1115173',NULL,'1','1','0'),('301','Могилев','Mogilev','467',NULL,'1','1','0'),('302','Егорьевск','Egorevsk','910',NULL,'1','1','0'),('303','Октябрьский','Oktiabrskiy','1085444',NULL,'1','1','0'),('304','Миргород','Mirgorod','5379',NULL,'1','1','0'),('305','Емва','Emva','5828',NULL,'1','1','0'),('306','Кириши','Kirishi','1743',NULL,'1','1','0'),('307','Тула','Tula','146',NULL,'1','1','0'),('308','Ачинск','Achinsk','882',NULL,'1','1','0'),('309','Черкассы','CHerkassy','2642',NULL,'1','1','0'),('310','Первоуральск','Pervouralsk','2001',NULL,'1','1','0'),('311','Новый Бердяш','NovyyBerdiash','1007199',NULL,'1','1','0'),('312','Караидель','Karaidel','1007176',NULL,'1','1','0'),('313','Чернушка','CHernushka','6582',NULL,'1','1','0'),('314','Югорск','JUgorsk','2292',NULL,'1','1','0'),('315','Нефтеюганск','Neftejugansk','382',NULL,'1','1','0'),('316','Ермекеево','Ermekeevo','17647',NULL,'1','1','0'),('317','Краснокамск','Krasnokamsk','2117',NULL,'1','1','0'),('318','Учалы','Uchaly','11442',NULL,'1','1','0'),('319','Людиново','Ljudinovo','2417',NULL,'1','1','0'),('320','Сегежа','Segezha','1488',NULL,'1','1','0'),('321','Молодечно','Molodechno','2149',NULL,'1','1','0'),('322','Славутич','Slavutich','764',NULL,'1','1','0'),('323','Богуслав','Boguslav','4476',NULL,'1','1','0'),('324','Симферополь','Simferopol','627',NULL,'1','1','0'),('325','Старобельск','Starobelsk','13927',NULL,'1','1','0'),('326','Старая Русса','StaraiaRussa','640',NULL,'1','1','0'),('327','Павлодар','Pavlodar','536',NULL,'1','1','0'),('328','Amherst','Amherst','269',NULL,'1','1','0'),('329','Мироновский','Mironovskiy','4677',NULL,'1','1','0'),('330','Выльгорт','Vylgort','15174',NULL,'1','1','0'),('331','Ковель','Kovel','5891',NULL,'1','1','0'),('332','Кедровый','Kedrovyy','15415',NULL,'1','1','0'),('333','Вихоревка','Vihorevka','5141',NULL,'1','1','0'),('334','Сургут','Surgut','1083941',NULL,'1','1','0'),('335','Одесса','Odessa','292',NULL,'1','1','0'),('336','Житомир','ZHitomir','1158',NULL,'1','1','0'),('337','Дмитриевка','Dmitrievka','1008647',NULL,'1','1','0'),('338','Ярославль','IAroslavl','1133755',NULL,'1','1','0'),('339','Томск','Tomsk','144',NULL,'1','1','0'),('340','Белорецк','Beloretsk','524',NULL,'1','1','0'),('341','Балтаево','Baltaevo','1008527',NULL,'1','1','0'),('342','Краснослободск','Krasnoslobodsk','1052841',NULL,'1','1','0'),('343','Кызыл','Kyzyl','76',NULL,'1','1','0'),('344','Ишлеи','Ishlei','1115220',NULL,'1','1','0'),('345','Киреевск','Kireevsk','1156571',NULL,'1','1','0'),('346','Самара','Samara','1064994',NULL,'1','1','0'),('347','Лубны','Lubny','4917',NULL,'1','1','0'),('348','Несвиж','Nesvizh','13833',NULL,'1','1','0'),('349','Алкино-2','Alkino2','20469',NULL,'1','1','0'),('350','Бугульма','Bugulma','680',NULL,'1','1','0'),('351','Чусовой','CHusovoy','5459',NULL,'1','1','0'),('352','Киров','Kirov','66',NULL,'1','1','0'),('353','Шестеринцы','SHesterintsy','1514224',NULL,'1','1','0'),('354','Male','Male','1936136',NULL,'1','1','0'),('355','Аскино','Askino','1004863',NULL,'1','1','0'),('356','Красный Луч','KrasnyyLuch','3962',NULL,'1','1','0'),('357','Дмитров','Dmitrov','625',NULL,'1','1','0'),('358','Новоликеево','Novolikeevo','1141319',NULL,'1','1','0'),('359','Старосубхангулово','Starosubhangulovo','13799',NULL,'1','1','0'),('360','Африканда 2','Afrikanda2','1060431',NULL,'1','1','0'),('361','Харцызск','Hartsyzsk','5468',NULL,'1','1','0'),('362','Liberta','Liberta','4696330',NULL,'1','1','0'),('363','Прилуки','Priluki','1870',NULL,'1','1','0'),('364','Выборг','Vyborg','177',NULL,'1','1','0'),('365','Вельск','Velsk','12269',NULL,'1','1','0'),('366','Окуловка','Okulovka','1062785',NULL,'1','1','0'),('367','Санчурск','Sanchursk','1133409',NULL,'1','1','0'),('368','Синельниково','Sinelnikovo','21555',NULL,'1','1','0'),('369','Черкассы','CHerkassy','1507922',NULL,'1','1','0'),('370','Витебск','Vitebsk','244',NULL,'1','1','0'),('371','Тихвин','Tihvin','186',NULL,'1','1','0'),('372','Джалиль','Dzhalil','6385',NULL,'1','1','0'),('373','Катайск','Kataysk','4838',NULL,'1','1','0'),('374','Oulu','Oulu','10159',NULL,'1','1','0'),('375','Батырево','Batyrevo','1114106',NULL,'1','1','0'),('376','Лесосибирск','Lesosibirsk','3156',NULL,'1','1','0'),('377','Barcelona','Barcelona','1901501',NULL,'1','1','0'),('378','Кировоград','Kirovograd','2201',NULL,'1','1','0'),('379','Лангепас','Langepas','12014',NULL,'1','1','0'),('380','Брянск','Briansk','33',NULL,'1','1','0'),('381','Корюковка','Korjukovka','12366',NULL,'1','1','0'),('382','Дубна','Dubna','21611',NULL,'1','1','0'),('383','Тогучин','Toguchin','1144826',NULL,'1','1','0'),('384','Кременчуг','Kremenchug','276',NULL,'1','1','0'),('385','Оршанка','Orshanka','1051606',NULL,'1','1','0'),('386','Курган','Kurgan','74',NULL,'1','1','0'),('387','Першотравенск','Pershotravensk','2916',NULL,'1','1','0'),('388','Краматорск','Kramatorsk','22437',NULL,'1','1','0'),('389','Вязьма','Viazma','3048',NULL,'1','1','0'),('390','Нурлаты','Nurlaty','16634',NULL,'1','1','0'),('391','Славянка','Slavianka','1153241',NULL,'1','1','0'),('392','Троицк','Troitsk','21856',NULL,'1','1','0'),('393','Tokyo','Tokyo','1914764',NULL,'1','1','0'),('394','Энгельс','JEngels','1469',NULL,'1','1','0'),('395','Брянка','Brianka','12889',NULL,'1','1','0'),('396','Tel Aviv - Jaffa','TelAvivJaffa','3296',NULL,'1','1','0'),('397','Месягутово','Mesiagutovo','8888',NULL,'1','1','0'),('398','Прокопьевск','Prokopevsk','468',NULL,'1','1','0'),('399','Нежин','Nezhin','1515250',NULL,'1','1','0'),('400','Белая Церковь','BelaiaTSerkov','2334',NULL,'1','1','0'),('401','Дятьково','Diatkovo','9869',NULL,'1','1','0'),('402','Лиски','Liski','502',NULL,'1','1','0'),('403','Глухов','Gluhov','21403',NULL,'1','1','0'),('404','Льгов','Lgov','2448',NULL,'1','1','0'),('405','Красное Село','KrasnoeSelo','782',NULL,'1','1','0'),('406','Суджа','Sudzha','20671',NULL,'1','1','0'),('407','Ромны','Romny','13000',NULL,'1','1','0'),('408','Ругозеро','Rugozero','1036104',NULL,'1','1','0'),('409','Куровское','Kurovskoe','962',NULL,'1','1','0'),('410','Петрово','Petrovo','1500090',NULL,'1','1','0'),('411','Воткинск','Votkinsk','1100',NULL,'1','1','0'),('412','Нижнее Алькеево','NizhneeAlkeevo','1094617',NULL,'1','1','0'),('413','Алчевск','Alchevsk','5648',NULL,'1','1','0'),('414','Черемшан','CHeremshan','7090',NULL,'1','1','0'),('415','Фурманов','Furmanov','8521',NULL,'1','1','0'),('416','Стаханов','Stahanov','3369',NULL,'1','1','0'),('417','Медвежьегорск','Medvezhegorsk','2974',NULL,'1','1','0'),('418','Слободка','Slobodka','1509119',NULL,'1','1','0'),('419','Судак','Sudak','7188',NULL,'1','1','0'),('420','Северск','Seversk','126',NULL,'1','1','0'),('421','Коряжма','Koriazhma','210',NULL,'1','1','0'),('422','Знаменка','Znamenka','22031',NULL,'1','1','0'),('423','Васильевка','Vasilevka','12764',NULL,'1','1','0'),('424','Shenzhen','Shenzhen','3792251',NULL,'1','1','0'),('425','Семилуки','Semiluki','16503',NULL,'1','1','0'),('426','Искитим','Iskitim','14876',NULL,'1','1','0'),('427','Кричев','Krichev','8935',NULL,'1','1','0'),('428','Николаев','Nikolaev','377',NULL,'1','1','0'),('429','Красилов','Krasilov','22692',NULL,'1','1','0'),('430','Камешково','Kameshkovo','14067',NULL,'1','1','0'),('431','Инта','Inta','255',NULL,'1','1','0'),('432','Бугуруслан','Buguruslan','830',NULL,'1','1','0'),('433','Алексеевка','Alekseevka','1009407',NULL,'1','1','0'),('434','Орша','Orsha','1233',NULL,'1','1','0'),('435','Реутов','Reutov','459',NULL,'1','1','0'),('436','Клишковцы','Klishkovtsy','1514873',NULL,'1','1','0'),('437','Староарзаматово','Staroarzamatovo','1007967',NULL,'1','1','0'),('438','Комсомольск','Komsomolsk','1028500',NULL,'1','1','0'),('439','Керчь','Kerch','478',NULL,'1','1','0'),('440','Мариуполь','Mariupol','455',NULL,'1','1','0'),('441','Ереван','Erevan','762',NULL,'1','1','0'),('442','Коломна','Kolomna','69',NULL,'1','1','0'),('443','Никополь','Nikopol','4513',NULL,'1','1','0'),('444','Красноармейск','Krasnoarmeysk','8488',NULL,'1','1','0'),('445','Чувашский Брод','CHuvashskiyBrod','1094662',NULL,'1','1','0'),('446','Климовск','Klimovsk','709',NULL,'1','1','0'),('447','Мытищи','Mytishhi','312',NULL,'1','1','0'),('448','Кстово','Kstovo','548',NULL,'1','1','0'),('449','Редкино','Redkino','16578',NULL,'1','1','0'),('450','Агинское','Aginskoe','3155',NULL,'1','1','0'),('451','Асбест','Asbest','1135',NULL,'1','1','0'),('452','Винница','Vinnitsa','761',NULL,'1','1','0'),('453','Качканар','Kachkanar','604',NULL,'1','1','0'),('454','Бердичев','Berdichev','2738',NULL,'1','1','0'),('455','Чебаркуль','CHebarkul','1008210',NULL,'1','1','0'),('456','Лутугино','Lutugino','1507023',NULL,'1','1','0'),('457','Астрахань','Astrahan','23',NULL,'1','1','0'),('458','Зирган','Zirgan','16285',NULL,'1','1','0'),('459','Стебник','Stebnik','1507540',NULL,'1','1','0'),('460','Мочалейка','Mochaleyka','1067985',NULL,'1','1','0'),('461','Кузнецовск','Kuznetsovsk','537',NULL,'1','1','0'),('462','Улан-Удэ','UlanUdje','148',NULL,'1','1','0'),('463','Димитров','Dimitrov','8064',NULL,'1','1','0'),('464','Пушкин','Pushkin','12',NULL,'1','1','0'),('465','Десногорск','Desnogorsk','787',NULL,'1','1','0'),('466','Гвардейск','Gvardeysk','6379',NULL,'1','1','0'),('467','Коломыя','Kolomyia','2941',NULL,'1','1','0'),('468','Стрежевой','Strezhevoy','1046',NULL,'1','1','0'),('469','Дно','Dno','20317',NULL,'1','1','0'),('470','Можга','Mozhga','1966',NULL,'1','1','0'),('471','Павлово','Pavlovo','18160',NULL,'1','1','0'),('472','Донской','Donskoy','2539',NULL,'1','1','0'),('473','Никифарово','Nikifarovo','1004729',NULL,'1','1','0'),('474','Пено','Peno','19657',NULL,'1','1','0'),('475','Болхов','Bolhov','3941',NULL,'1','1','0'),('476','Алма-Ата','AlmaAta','183',NULL,'1','1','0'),('477','Бахмач','Bahmach','15401',NULL,'1','1','0'),('478','Абакан','Abakan','17',NULL,'1','1','0'),('479','Ольшанское','Olshanskoe','22701',NULL,'1','1','0'),('480','Видяево','Vidiaevo','637',NULL,'1','1','0'),('481','Курчатов','Kurchatov','290',NULL,'1','1','0'),('482','Заречный','Zarechnyy','586',NULL,'1','1','0'),('483','Черняховск','CHerniahovsk','12945',NULL,'1','1','0'),('484','Сергач','Sergach','11988',NULL,'1','1','0'),('485','Субханкулово','Subhankulovo','1008607',NULL,'1','1','0'),('486','Царичанка','TSarichanka','1502664',NULL,'1','1','0'),('487','Павлово','Pavlovo','1141697',NULL,'1','1','0'),('488','Котово','Kotovo','1014618',NULL,'1','1','0'),('489','Междуреченск','Mezhdurechensk','1129456',NULL,'1','1','0'),('490','Коростень','Korosten','9546',NULL,'1','1','0'),('491','Марфино','Marfino','1056533',NULL,'1','1','0'),('492','Тобольск','Tobolsk','182',NULL,'1','1','0'),('493','Радужный','Raduzhnyy','881',NULL,'1','1','0'),('494','Троицк','Troitsk','145',NULL,'1','1','0'),('495','Чулково','CHulkovo','1139666',NULL,'1','1','0'),('496','Ногинск-9','Noginsk9','1057000',NULL,'1','1','0'),('497','Городок','Gorodok','1156738',NULL,'1','1','0'),('498','Новокуйбышевск','Novokuybyshevsk','1489',NULL,'1','1','0'),('499','Schio','Schio','4539622',NULL,'1','1','0'),('500','Барнаул','Barnaul','1137256',NULL,'1','1','0'),('501','Канев','Kanev','5962',NULL,'1','1','0'),('502','Белозерск','Belozersk','2553',NULL,'1','1','0'),('503','Пологи','Pologi','12085',NULL,'1','1','0'),('504','Пугачево','Pugachevo','1110401',NULL,'1','1','0'),('505','Старый Оскол','StaryyOskol','274',NULL,'1','1','0'),('506','Великая Лепетиха','VelikaiaLepetiha','1512774',NULL,'1','1','0'),('507','Приютово','Prijutovo','2412',NULL,'1','1','0'),('508','Языково','IAzykovo','1005853',NULL,'1','1','0'),('509','Железнодорожный','ZHeleznodorozhnyy','21613',NULL,'1','1','0'),('510','Карловка','Karlovka','14595',NULL,'1','1','0'),('511','Коростышев','Korostyshev','21110',NULL,'1','1','0'),('512','Большие Нырты','BolshieNyrty','1096928',NULL,'1','1','0'),('513','Элиста','JElista','166',NULL,'1','1','0'),('514','Луга','Luga','79',NULL,'1','1','0'),('515','Бузулук','Buzuluk','2404',NULL,'1','1','0'),('516','Новополоцк','Novopolotsk','1299',NULL,'1','1','0'),('517','River Heights','RiverHeights','4974956',NULL,'1','1','0'),('518','Волоколамск','Volokolamsk','2079',NULL,'1','1','0'),('519','Советск','Sovetsk','5099',NULL,'1','1','0'),('520','Черногорск','CHernogorsk','1156',NULL,'1','1','0'),('521','Вад','Vad','1139424',NULL,'1','1','0'),('522','Владикавказ','Vladikavkaz','38',NULL,'1','1','0'),('523','Сосновец','Sosnovets','6312',NULL,'1','1','0'),('524','Монастырище','Monastyrishhe','1514269',NULL,'1','1','0'),('525','Карасук','Karasuk','4017',NULL,'1','1','0'),('526','Ступино','Stupino','1054402',NULL,'1','1','0'),('527','Волжск','Volzhsk','3249',NULL,'1','1','0'),('528','Днепродзержинск','Dneprodzerzhinsk','1064',NULL,'1','1','0'),('529','Первомайск','Pervomaysk','1008132',NULL,'1','1','0'),('530','Октябрьский','Oktiabrskiy','1085867',NULL,'1','1','0'),('531','Октябрьский','Oktiabrskiy','3556',NULL,'1','1','0'),('532','Романовка','Romanovka','20528',NULL,'1','1','0'),('533','Новая Каховка','NovaiaKahovka','9135',NULL,'1','1','0'),('534','Северодонецк','Severodonetsk','3454',NULL,'1','1','0'),('535','Новоград-Волынский','NovogradVolynskiy','5722',NULL,'1','1','0'),('536','Волгодонск','Volgodonsk','3700',NULL,'1','1','0'),('537','Новосемейкино','Novosemeykino','17732',NULL,'1','1','0'),('538','Шепетовка','SHepetovka','6383',NULL,'1','1','0'),('539','Валдай','Valday','527',NULL,'1','1','0'),('540','Шинеры','SHinery','1114251',NULL,'1','1','0'),('541','Урень','Uren','13519',NULL,'1','1','0'),('542','Ракитное','Rakitnoe','1010701',NULL,'1','1','0'),('543','Раздельная','Razdelnaia','6192',NULL,'1','1','0'),('544','Приморск','Primorsk','1585',NULL,'1','1','0'),('545','Глазов','Glazov','45',NULL,'1','1','0'),('546','Татарстан','Tatarstan','1094589',NULL,'1','1','0'),('547','Самбор','Sambor','12677',NULL,'1','1','0'),('548','Толочин','Tolochin','16706',NULL,'1','1','0'),('549','Кобрин','Kobrin','3063',NULL,'1','1','0'),('550','Горловка','Gorlovka','2256',NULL,'1','1','0'),('551','Васильков','Vasilkov','5571',NULL,'1','1','0'),('552','Выкса','Vyksa','6923',NULL,'1','1','0'),('553','Глубокое','Glubokoe','9328',NULL,'1','1','0'),('554','Ровеньки','Rovenki','2832',NULL,'1','1','0'),('555','Ахтырка','Ahtyrka','5799',NULL,'1','1','0'),('556','Печора','Pechora','589',NULL,'1','1','0'),('557','Гусятин','Gusiatin','1511473',NULL,'1','1','0'),('558','Дубно','Dubno','2789',NULL,'1','1','0'),('559','Ардатов','Ardatov','12415',NULL,'1','1','0'),('560','Пятигорск','Piatigorsk','116',NULL,'1','1','0'),('561','Алатырь','Alatyr','1113989',NULL,'1','1','0'),('562','Пыть-Ях','PytIAh','562',NULL,'1','1','0'),('563','Рожище','Rozhishhe','21381',NULL,'1','1','0'),('564','Северное','Severnoe','1148222',NULL,'1','1','0'),('565','Houston','Houston','8496',NULL,'1','1','0'),('566','Ханты-Мансийск','HantyMansiysk','154',NULL,'1','1','0'),('567','Berlin','Berlin','458',NULL,'1','1','0'),('568','Константиновка','Konstantinovka','6822',NULL,'1','1','0'),('569','Печоры','Pechory','2116',NULL,'1','1','0'),('570','Кисловодск','Kislovodsk','67',NULL,'1','1','0'),('571','Свободное','Svobodnoe','1502845',NULL,'1','1','0'),('572','Ялта','IAlta','818',NULL,'1','1','0'),('573','Умань','Uman','1711',NULL,'1','1','0'),('574','Озерск','Ozersk','941',NULL,'1','1','0'),('575','Los Angeles','LosAngeles','5331',NULL,'1','1','0'),('576','Смоленск','Smolensk','130',NULL,'1','1','0'),('577','Столбцы','Stolbtsy','20550',NULL,'1','1','0'),('578','Мезень','Mezen','6947',NULL,'1','1','0'),('579','Елмачи','Elmachi','1114345',NULL,'1','1','0'),('580','Жешарт','ZHeshart','6999',NULL,'1','1','0'),('581','Александрия','Aleksandriia','1707343',NULL,'1','1','0'),('582','Глобино','Globino','1509548',NULL,'1','1','0'),('583','Гродно','Grodno','649',NULL,'1','1','0'),('584','Хмельницкий','Hmelnitskiy','1507247',NULL,'1','1','0'),('585','Купянск','Kupiansk','5709',NULL,'1','1','0'),('586','Новороссийск','Novorossiysk','98',NULL,'1','1','0'),('587','Советский','Sovetskiy','1051967',NULL,'1','1','0'),('588','Балаклея','Balakleia','3124',NULL,'1','1','0'),('589','Алексеевское','Alekseevskoe','1004791',NULL,'1','1','0'),('590','Чемеровцы','CHemerovtsy','1513852',NULL,'1','1','0'),('591','Богородицк','Bogoroditsk','15216',NULL,'1','1','0'),('592','Чугуев','CHuguev','8142',NULL,'1','1','0'),('593','Борисоглебск','Borisoglebsk','505',NULL,'1','1','0'),('594','Туапсе','Tuapse','1151',NULL,'1','1','0'),('595','Медногорск','Mednogorsk','5539',NULL,'1','1','0'),('596','Лисичанск','Lisichansk','1217',NULL,'1','1','0'),('597','Воскресенск','Voskresensk','593',NULL,'1','1','0'),('598','Белицкое','Belitskoe','21561',NULL,'1','1','0'),('599','Пушкино','Pushkino','873',NULL,'1','1','0'),('600','Дебальцево','Debaltsevo','18026',NULL,'1','1','0'),('601','Балашов','Balashov','600',NULL,'1','1','0'),('602','Чебоксары','CHeboksary','1093182',NULL,'1','1','0'),('603','Кондрово','Kondrovo','1604',NULL,'1','1','0'),('604','Кировск','Kirovsk','4142',NULL,'1','1','0'),('605','Змиев','Zmiev','17692',NULL,'1','1','0'),('606','Переславль-Залесский','PereslavlZalesskiy','652',NULL,'1','1','0'),('607','Окуловка','Okulovka','1171',NULL,'1','1','0'),('608','Никольское','Nikolskoe','1942',NULL,'1','1','0'),('609','Бижбуляк','Bizhbuliak','15930',NULL,'1','1','0'),('610','Иршанск','Irshansk','1503438',NULL,'1','1','0'),('611','Андреаполь','Andreapol','1077',NULL,'1','1','0'),('612','Козловка','Kozlovka','16765',NULL,'1','1','0'),('613','Уральск','Uralsk','3069',NULL,'1','1','0'),('614','Черепаново','CHerepanovo','1344',NULL,'1','1','0'),('615','Широкое','SHirokoe','22045',NULL,'1','1','0'),('616','Изяслав','Iziaslav','9627',NULL,'1','1','0'),('617','Лодейное Поле','LodeynoePole','503',NULL,'1','1','0'),('618','San Jose','SanJose','5094472',NULL,'1','1','0'),('619','Старая Купавна','StaraiaKupavna','1300',NULL,'1','1','0'),('620','Нурлат','Nurlat','11320',NULL,'1','1','0'),('621','Анжеро-Судженск','AnzheroSudzhensk','9115',NULL,'1','1','0'),('622','Трускавец','Truskavets','20532',NULL,'1','1','0'),('623','Фряново','Frianovo','17052',NULL,'1','1','0'),('624','Amsterdam','Amsterdam','22168',NULL,'1','1','0'),('625','Мантурово','Manturovo','2859',NULL,'1','1','0'),('626','Усть-Кут','UstKut','2829',NULL,'1','1','0'),('627','Лянтор','Liantor','1389',NULL,'1','1','0'),('628','Ивано-Франковск','IvanoFrankovsk','2106',NULL,'1','1','0'),('629','Озерск','Ozersk','1031353',NULL,'1','1','0'),('630','Морки','Morki','8252',NULL,'1','1','0'),('631','Борисполь','Borispol','7623',NULL,'1','1','0'),('632','Центральный','TSentralnyy','1105736',NULL,'1','1','0'),('633','Медведево','Medvedevo','1051147',NULL,'1','1','0'),('634','Просяное','Prosianoe','1507052',NULL,'1','1','0'),('635','Энергодар','JEnergodar','2375',NULL,'1','1','0');

UNLOCK TABLES;

/*Table structure for table `club` */

DROP TABLE IF EXISTS `club`;

CREATE TABLE `club` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vkClubId` int(11) DEFAULT NULL,
  `entityStatus` int(3) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `club` */

LOCK TABLES `club` WRITE;

UNLOCK TABLES;

/*Table structure for table `coComment` */

DROP TABLE IF EXISTS `coComment`;

CREATE TABLE `coComment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `headId` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `sourceId` int(11) DEFAULT NULL,
  `rootId` int(11) DEFAULT NULL,
  `weight` bigint(19) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `toId` int(11) DEFAULT NULL,
  `userType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `toType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nickName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wasRead` int(1) DEFAULT NULL,
  `isPrivate` int(1) DEFAULT NULL,
  `isAnon` int(1) DEFAULT NULL,
  `dateCreate` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`),
  KEY `headId` (`headId`),
  KEY `dateCreate` (`dateCreate`),
  KEY `sourceId` (`sourceId`),
  KEY `rootId` (`rootId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `coComment` */

LOCK TABLES `coComment` WRITE;

UNLOCK TABLES;

/*Table structure for table `coNotification` */

DROP TABLE IF EXISTS `coNotification`;

CREATE TABLE `coNotification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `zakName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateUpdate` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `head_user_id` (`headId`,`userId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `coNotification` */

LOCK TABLES `coNotification` WRITE;

UNLOCK TABLES;

/*Table structure for table `content` */

DROP TABLE IF EXISTS `content`;

CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(50) DEFAULT NULL,
  `menu` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `pageTitle` text,
  `pageDesc` text,
  `pageKeys` text,
  `text` mediumtext,
  `ts` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`),
  KEY `alias_idx` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

/*Data for the table `content` */

LOCK TABLES `content` WRITE;

insert  into `content`(`id`,`alias`,`status`,`menu`,`title`,`pageTitle`,`pageDesc`,`pageKeys`,`text`,`ts`,`ownerSiteId`,`ownerOrgId`) values ('1','sovmestnyepokupki-otlichnyj-sposob-borby-s-krizisom','STATUS_ENABLED','MENU_BOTTOM','Совместные покупки - отличный способ борьбы с санкциями','Совместные покупки - отличный способ борьбы с санкциями','В период санкций в городах России появилось множество антикризисных социальных сетей.  Но и это ещё не предел выгоды. Появляются сайты, на которых выгода от совместных закупок возрасла для покупателей до 50% и больше','совместные покупки, совместные закупки','&lt;h1&gt;Совместные покупки - отличный способ борьбы с санкциями&lt;/h1&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;В период кризиса 2009 года в Москве, как и в других городах России появилось множество антикризисных социальных сетей. Если раньше московские менеджеры не вылезали с &amp;laquo;Одноклассников&amp;raquo;, то в кризис заставил обратить внимание на форумы и интернет-порталы, предлагающих сообща бороться с трудностями. Популярными стали сайты, на которых люди договариваются о совместных покупках.&lt;br /&gt;\r\n&lt;br /&gt;\r\n- Например, среднестатистический москвич ходит в супермаркет раз в неделю и оставляет там около 4 - 5 тысяч рублей (на 2009 год), - объясняет мне соседка Лена. - А если с тобой покупки сделают еще 10 человек, то сумма в чеке будет уже около 50 тысяч рублей. Магазин выписывает 15-процентную накопительную скидку. Я уже месяц делаю покупки с тремя семьями из соседнего дома.&lt;br /&gt;\r\n&lt;br /&gt;\r\nПоскольку потребности соседей не всегда могут совпадать с Вашими, лучше зарегистрироваться на каком-нибудь сайте и присоединится к группе &amp;laquo;закупщиков&amp;raquo;, где можно договариться о совместном походе в близлежащий гипермаркет вечером в воскресенье.&lt;br /&gt;\r\n&lt;br /&gt;\r\n- Это чтобы не стоять в очередях, - объясняет мой новый знакомый юрист Михаил. - В воскресенье в магазинах посвободнее.&lt;br /&gt;\r\n&lt;br /&gt;\r\nК гипермаркету мы сначала едем дружной толпой в полупустом метро, а затем на автобусе. Возле магазина нас уже поджидает бывший бизнес-консультант Юрий на своем авто. Все продумано: обратно до дома увешанный тяжеленными сумками на общественном транспорте не поедешь. Брать такси - дорого. Выход такой: Юра довезет пакеты всех &amp;laquo;сообщников&amp;raquo; до условного места недалеко от дома (все &amp;laquo;закупщики&amp;raquo; живут по соседству), остальные в это время спускаются в метро налегке. Денег водитель, разумеется, с нас не возьмет - ему все равно по пути.&lt;br /&gt;\r\n&lt;br /&gt;\r\nНо и это ещё не предел выгоды. Появляются сайты, на которых такие люди как Михаил только и занимаются что организацией закупок. Их так и называют &amp;quot;организатор&amp;quot;. Теперь уже не обязательно ехать на метро и встречаться&amp;nbsp; в гипермаркете, чтобы получить скидку. Достаточно передать организатору закупки деньги и заказ, а он, набрав минимальную сумму выкупа, сам отправится в гипермаркет. Поскольку живет он по соседству, забрать свой заказ можно вечером, зайдя к нему по пути с работы.&lt;br /&gt;\r\n&lt;br /&gt;\r\nОрганизаторы быстро смекнули, что ещё выгоднее закупаться прямо у оптовиков, а не в гипермаркете. И тут понеслось - выгода от совместных закупок возрасла для покупателей до 50% и больше, а ассортимент совместных покупок значительно расширился.&lt;/p&gt;\r\n&lt;script type=&quot;text/javascript&quot;&gt;(function(w,doc) {\r\nif (!w.__utlWdgt ) {\r\n    w.__utlWdgt = true;\r\n    var d = doc, s = d.createElement(\\\'script\\\'), g = \\\'getElementsByTagName\\\';\r\n    s.type = \\\'text/javascript\\\'; s.charset=\\\'UTF-8\\\'; s.async = true;\r\n    s.src = (\\\'https:\\\' == w.location.protocol ? \\\'https\\\' : \\\'http\\\')  + \\\'://w.uptolike.com/widgets/v1/uptolike.js\\\';\r\n    var h=d[g](\\\'body\\\')[0];\r\n    h.appendChild(s);\r\n}})(window,document);\r\n&lt;/script&gt;\r\n&lt;div data-orientation=&quot;horizontal&quot; data-share-size=&quot;LARGE&quot; data-following-enable=&quot;false&quot; data-like-text-enable=&quot;true&quot; data-sn-ids=&quot;fb.vk.ok.&quot; data-background-alpha=&quot;0.0&quot; data-pid=&quot;1360223&quot; data-selection-enable=&quot;false&quot; data-mode=&quot;like&quot; data-background-color=&quot;#ffffff&quot; data-exclude-show-more=&quot;false&quot; data-share-shape=&quot;SHARP&quot; data-icon-color=&quot;#ffffff&quot; data-counter-background-alpha=&quot;1.0&quot; data-text-color=&quot;#000000&quot; data-buttons-color=&quot;#FFFFFF&quot; data-counter-background-color=&quot;#ffffff&quot; class=&quot;uptolike-buttons&quot;&gt;&amp;nbsp;&lt;/div&gt;',NULL,'1','0'),('2','sovmestnye-pokupki-sekret-jekonomii','STATUS_ENABLED','MENU_BOTTOM','Совместные покупки - секрет экономии','Совместные покупки - секрет экономии','Для экономии следует воспользоваться совместными покупками. Такие как Вы, объединяются, выбирают у оптовика, что кому нравится, и закупаются оптом через &quot;организаторов закупок&quot;, которые занимаются сбором заказов, денег, а так же доставкой заказанного товара от оптовиков.','совместные покупки, совместные закупки, экономия','&lt;h1&gt;Совместные покупки - секрет экономии&lt;/h1&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;Возможно, вы уже слышали о &amp;laquo;совместных покупках&amp;raquo; (СП), а может и нет. Но тысячи людей уже не один год пользуются этим и реально экономят свои деньги. Как?&lt;br /&gt;\r\n&lt;br /&gt;\r\nДумаю, ни для кого не секрет, что любой магазин &amp;mdash; это посредник между производителем, оптовиком и конечным покупателем. У каждого товара есть две цены &amp;mdash; оптовая и розничная. Оптовая цена обычно отличается от розничной в 2, а иногда и более раз.&lt;br /&gt;\r\n&lt;br /&gt;\r\nПредставьте себе, что вы идете в магазин и покупаете платье за 3000 руб. А теперь представьте, что вы узнаете его оптовую цену, которая составляет всего 900 руб! А если бы у Вас была возможность купить то же платье по оптовой цене? Сэкономив около 2000 руб. Купить, минуя посредника - розничный магазин. А если покупать все вещи и бытовые предметы всегда только по оптовой цене? Не надо быть учителем в школе, чтобы подсчитать, насколько большая получится экономия.&lt;br /&gt;\r\n&lt;br /&gt;\r\nВозникает один ньюанс - чтобы купить по оптовой цене, нужно купить много, т.е. оптом. А Вам нужно всего одно платье...&lt;br /&gt;\r\n&lt;br /&gt;\r\nВот тут и следует воспользоваться совместными покупками. Такие как Вы, объединяются, выбирают у оптовика, что кому нравится, и закупаются оптом через &amp;quot;организаторов закупок&amp;quot;, которые занимаются сбором заказов, денег, а так же доставкой заказанного товара от оптовиков. За это организатор обычно берет небольшой процент (10-15% от оптовой суммы заказа). Транспортные расходы распределяются между всеми участниками закупки. &lt;br /&gt;\r\n&lt;br /&gt;\r\nСами понимаете, что 10-15% &amp;mdash; это не 100-300%, как в обычном розничном магазине.&lt;br /&gt;\r\n&lt;br /&gt;\r\nКонечно, совместные покупки совершенно не радуют продавцов магазинов. Их можно понять &amp;mdash; это лишает их покупателей. Они предпринимают различные меры, чтобы помешать проведению СП. Поэтому участники совместных покупок часто шифруются. Зато оптовикам это выгодно, и они уже идут на встречу организаторам СП, а значит направление будет развиваться и дальше.&lt;/p&gt;\r\n&lt;script type=&quot;text/javascript&quot;&gt;(function(w,doc) {\r\nif (!w.__utlWdgt ) {\r\n    w.__utlWdgt = true;\r\n    var d = doc, s = d.createElement(\\\'script\\\'), g = \\\'getElementsByTagName\\\';\r\n    s.type = \\\'text/javascript\\\'; s.charset=\\\'UTF-8\\\'; s.async = true;\r\n    s.src = (\\\'https:\\\' == w.location.protocol ? \\\'https\\\' : \\\'http\\\')  + \\\'://w.uptolike.com/widgets/v1/uptolike.js\\\';\r\n    var h=d[g](\\\'body\\\')[0];\r\n    h.appendChild(s);\r\n}})(window,document);\r\n&lt;/script&gt;\r\n&lt;div data-orientation=&quot;horizontal&quot; data-share-size=&quot;LARGE&quot; data-following-enable=&quot;false&quot; data-like-text-enable=&quot;true&quot; data-sn-ids=&quot;fb.vk.ok.&quot; data-background-alpha=&quot;0.0&quot; data-pid=&quot;1360223&quot; data-selection-enable=&quot;false&quot; data-mode=&quot;like&quot; data-background-color=&quot;#ffffff&quot; data-exclude-show-more=&quot;false&quot; data-share-shape=&quot;SHARP&quot; data-icon-color=&quot;#ffffff&quot; data-counter-background-alpha=&quot;1.0&quot; data-text-color=&quot;#000000&quot; data-buttons-color=&quot;#FFFFFF&quot; data-counter-background-color=&quot;#ffffff&quot; class=&quot;uptolike-buttons&quot; &gt;&lt;/div&gt;',NULL,'1','0'),('3','sovmestnye-pokupki-na-forumah','STATUS_ENABLED','MENU_BOTTOM','Совместные покупки на форумах','Совместные покупки на форумах','Совместная покупка – это покупка у оптовика частными лицами, объеденных с целью приобретения того или иного товара по цене значительно ниже розничной. Волна совместных покупок охватывает все больше и больше людей, в не которых городах где это явление более развито магазины розничной торговли начинают вставлять палки в колеса форумам совместных покупок','совместные покупки, совместные закупки','&lt;h1&gt;Совместные покупки на форумах&lt;/h1&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;На региональных родительских форумах все чаше стал появляться раздел &amp;ldquo;Совместные закупки&amp;rdquo;, часто он скрыт от не зарегистрированных пользователей. &lt;br /&gt;\r\n&lt;br /&gt;\r\nЧто такое &amp;ldquo;совместные покупки&amp;rdquo; и с чем их &amp;ldquo;едят&amp;rdquo;?&lt;br /&gt;\r\n&lt;br /&gt;\r\nНачалось все с того что сообщества мамочек, сидящих на форумах, решили сэкономить на покупке памперсов и прочих детских нужностях. И вот, сделав несколько пробных заказов через интернет, мамочки начинают понимать что так можно покупать напрямую у оптовиков и значительно экономить.&lt;br /&gt;\r\n&lt;br /&gt;\r\nИ тут началось - мамочки находят в сети оптовиков нужных товаров, узнают какая минимальная сумма закупки, получают прайс-лист с оптовыми ценами, условия доставки, кооперируются и делают заказ. Получается очень выгодно &amp;ndash; покупка идет напрямую от оптовика, а цены в магазинах на те же товары получаются на 100%-300% выше чем в &amp;ldquo;совместных покупках&amp;rdquo;.&lt;br /&gt;\r\n&lt;br /&gt;\r\nСовместная покупка &amp;ndash; это покупка у оптовика частными лицами, объеденных с целью приобретения того или иного товара по цене значительно ниже розничной.&lt;br /&gt;\r\n&lt;br /&gt;\r\nВолна совместных покупок охватывает все больше и больше людей, в не которых городах где это явление более развито магазины розничной торговли начинают вставлять палки в колеса форумам совместных покупок, еще бы ведь им это явление жутко не выгодно, они теряют на покупателей.&lt;br /&gt;\r\n&lt;br /&gt;\r\nНебольшой пример. В Иркутске, перед началом зимы 2009 года была проведена закупка детских валенок. Цена была такой привлекательной что в это закупке участвовало очень большее число пользователей. Итоговая сумма закупки ушла далеко за 100 тыс рублей. И вот в определенный час, в определенном месте началась раздача этих самых валенок. Подъехал грузовик доверху гружённый валенками и началась раздача. Проходящие мимо случайные прохожие, такой очереди за валенками наверное не видели со времен СССР, стали интересоваться ценой, но в ответ был такой &amp;ndash; это закрытая покупка и на весь товар уже есть покупатель. Это же ведь мечта любого коммерсанта &amp;ndash; продать грузовик товара в течении получаса, при этом не тратясь на аренду помещения.&lt;/p&gt;\r\n&lt;script type=&quot;text/javascript&quot;&gt;(function(w,doc) {\r\nif (!w.__utlWdgt ) {\r\n    w.__utlWdgt = true;\r\n    var d = doc, s = d.createElement(\\\'script\\\'), g = \\\'getElementsByTagName\\\';\r\n    s.type = \\\'text/javascript\\\'; s.charset=\\\'UTF-8\\\'; s.async = true;\r\n    s.src = (\\\'https:\\\' == w.location.protocol ? \\\'https\\\' : \\\'http\\\')  + \\\'://w.uptolike.com/widgets/v1/uptolike.js\\\';\r\n    var h=d[g](\\\'body\\\')[0];\r\n    h.appendChild(s);\r\n}})(window,document);\r\n&lt;/script&gt;\r\n&lt;div data-orientation=&quot;horizontal&quot; data-share-size=&quot;LARGE&quot; data-following-enable=&quot;false&quot; data-like-text-enable=&quot;true&quot; data-sn-ids=&quot;fb.vk.ok.&quot; data-background-alpha=&quot;0.0&quot; data-pid=&quot;1360223&quot; data-selection-enable=&quot;false&quot; data-mode=&quot;like&quot; data-background-color=&quot;#ffffff&quot; data-exclude-show-more=&quot;false&quot; data-share-shape=&quot;SHARP&quot; data-icon-color=&quot;#ffffff&quot; data-counter-background-alpha=&quot;1.0&quot; data-text-color=&quot;#000000&quot; data-buttons-color=&quot;#FFFFFF&quot; data-counter-background-color=&quot;#ffffff&quot; class=&quot;uptolike-buttons&quot; &gt;&lt;/div&gt;',NULL,'1','0'),('4','sovmestnaja-pokupka-odezhdy-v-internet-magazine','STATUS_ENABLED','MENU_BOTTOM','Совместная покупка одежды в интернет магазине','Совместная покупка одежды в интернет магазине','К вопросу доверия. Стоит появится в сети хотя бы одному слуху о некачественной работе или обмане на сайте, как тут же слух будет растиражирован и, естественно, продажи интернет магазина сильно упадут. Поэтому сайты дорожат своей репутацией гораздо больше, чем обычные магазины.','совместные покупки, совместные закупки','&lt;h1&gt;Совместная покупка одежды в интернет магазине&lt;/h1&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;Энтузиазм покупателей, внезапно узнавших, что в розничном магазине они платят лишних 200% от первоначальной цены, сильно снижается. Поскольку эта информация уже не секрет, популярность набирают совместные покупоки. Статистика свидетельствует, что одежда покупается в интернете примерно полумиллионом россиян. Ежегодный прирост оборота интернет магазинов держится на уровне 30-35 % свидетельствует о стремительном распространении услуги. А сайты совместных покупок - это просто бомба! Ведь собрав заказы на большую сумму можно закупить товары напрямую у оптовика!&lt;br /&gt;\r\n&lt;br /&gt;\r\nКак можно быть уверенным, что одежда подойдет по размеру? У всех производителей свои взгляды на стандарты размеров, однако, они не держат их в тайне. На всех интернет магазинах имеются таблицы соответствия всех мыслимых мировых систем размеров. Во многих есть функция расчета. Все, что Вам нужно знать &amp;ndash; это собственные данные. Можно даже попросить сделать замеры в нужных местах обышным &amp;quot;метром&amp;quot;.&lt;br /&gt;\r\n&lt;br /&gt;\r\nСдать одежду назад при совместной покупке очень тяжело, но реально. А на некоторых ресурсах есть, так называемый, &amp;quot;пристрой&amp;quot;, где можно выставить на продажу вещи, которые не подошли по размеру.&lt;br /&gt;\r\n&lt;br /&gt;\r\nДля уверенности в качестве, приобретайте одежду известных Вам марок. Это заблуждение, что в совместных покупках продают вещи с рынка. На самом деле всё как раз наоборот - совместные покупки делаются у оптовиком, которые торгуют бреэндами, а вещи на рынках привозятся от неизвестных производителей, причем часто производителя невозможно проверить.&lt;br /&gt;\r\n&lt;br /&gt;\r\nК вопросу доверия. Стоит появится в сети хотя бы одному слуху о некачественной работе или обмане на сайте, как тут же слух будет растиражирован и, естественно, продажи интернет магазина сильно упадут. Поэтому сайты дорожат своей репутацией гораздо больше, чем обычные магазины. &lt;br /&gt;\r\n&lt;br /&gt;\r\nСовет напоследок: сделайте первую совместную покупку какой-нибудь не очень дорогой, например футболки, чтобы прочувствовать механизм, войти во вкус. Уверен &amp;ndash; Вам понравится!&lt;/p&gt;\r\n&lt;script type=&quot;text/javascript&quot;&gt;(function(w,doc) {\r\nif (!w.__utlWdgt ) {\r\n    w.__utlWdgt = true;\r\n    var d = doc, s = d.createElement(\\\'script\\\'), g = \\\'getElementsByTagName\\\';\r\n    s.type = \\\'text/javascript\\\'; s.charset=\\\'UTF-8\\\'; s.async = true;\r\n    s.src = (\\\'https:\\\' == w.location.protocol ? \\\'https\\\' : \\\'http\\\')  + \\\'://w.uptolike.com/widgets/v1/uptolike.js\\\';\r\n    var h=d[g](\\\'body\\\')[0];\r\n    h.appendChild(s);\r\n}})(window,document);\r\n&lt;/script&gt;\r\n&lt;div data-orientation=&quot;horizontal&quot; data-share-size=&quot;LARGE&quot; data-following-enable=&quot;false&quot; data-like-text-enable=&quot;true&quot; data-sn-ids=&quot;fb.vk.ok.&quot; data-background-alpha=&quot;0.0&quot; data-pid=&quot;1360223&quot; data-selection-enable=&quot;false&quot; data-mode=&quot;like&quot; data-background-color=&quot;#ffffff&quot; data-exclude-show-more=&quot;false&quot; data-share-shape=&quot;SHARP&quot; data-icon-color=&quot;#ffffff&quot; data-counter-background-alpha=&quot;1.0&quot; data-text-color=&quot;#000000&quot; data-buttons-color=&quot;#FFFFFF&quot; data-counter-background-color=&quot;#ffffff&quot; class=&quot;uptolike-buttons&quot; &gt;&lt;/div&gt;',NULL,'1','0'),('5','sovmestnye-pokupki-rukovodstvo-dlja-nachinajuwih','STATUS_ENABLED','MENU_BOTTOM','Совместные покупки. Руководство для начинающих','Совместные покупки. Руководство для начинающих','Приобретение товаров по оптовым ценам у отечественных компаний, в зарубежных интернет-магазинах. Совместное приобретение товаров по ОПТОВЫМ ЦЕНАМ','совместные покупки, совместные закупки','&lt;h1&gt;Совместные покупки. Руководство для начинающих&lt;/h1&gt;\r\n&lt;p&gt;Совместные покупки &amp;ndash; это совместное приобретение каких-то товаров по ОПТОВЫМ ЦЕНАМ.&lt;/p&gt;\r\n&lt;h3&gt;Как это происходит&lt;/h3&gt;\r\n&lt;ul&gt;\r\n    &lt;li&gt;Человек (его еще называют &amp;quot;Организатором Закупки&amp;quot;) изучает рынок и ищет оптовиков , торгующих известными брендами.&lt;/li&gt;\r\n    &lt;li&gt;Организатор предлагает посетителям сайта учавствовать в совместной закупке. Он выкладывает прайс или дает ссылку на каталог товара на сайте оптовика.&lt;/li&gt;\r\n    &lt;li&gt;Заинтересованные в данном товаре люди делают заказы и &amp;laquo;набирают&amp;raquo; определенный объем - сумму, необходимую для оптовой закупки.&lt;/li&gt;\r\n    &lt;li&gt;Организатор собирает деньги (когда нужна предоплата, а она нужна в большинстве случаев) и делает заказ.&lt;/li&gt;\r\n    &lt;li&gt;Товар доставляется организатору, после чего он раздает его участникам совместной закупки.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;h3&gt;&lt;br /&gt;\r\nПравила совместных закупок&lt;/h3&gt;\r\n&lt;ul&gt;\r\n    &lt;li&gt;Принято, что организатор закупок берет небольшой процент за свои труды. Ведь он ведет переговоры с компанией, формирует заказ, принимает посылку, организует встречи, распределяет товар. Стандартный процент организатора закупки &amp;ndash; 10-15% от стоимости товара.&lt;/li&gt;\r\n    &lt;li&gt;Поскольку товар приобретается оптом, могут быть разные нюансы. Например, при приобретении детской обуви нужно выкупать целый размерный ряд, только тогда покупка определенной модели становится возможной. То есть если Вы захотите приобрести синенькие кеды 26 размера, нужно будет, чтобы еще нашлись покупатели на 22, 23, 24, 25 и 27 размер.&lt;/li&gt;\r\n    &lt;li&gt;После того как заказ набран и объявлен &amp;quot;стоп&amp;quot; уже нельзя отредактировать свой заказ.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;&lt;br /&gt;\r\nОсобый вид СП &amp;ndash; приобретение товаров в зарубежных интернет-магазинах&lt;br /&gt;\r\n&lt;br /&gt;\r\nКроме совместного приобретения товаров по оптовым ценам у отечественных компаний, многие заинтересованы в приобретении вещей в зарубежных интернет-магазинах. Конечно, скажете Вы, можно и самостоятельно попытаться заказать понравившуюся Вам вещицу. НО&amp;hellip; тут можно столкнуться с разными трудностями. Например, многие иностранные магазины просто не доставляют свои товары в страны СНГ. Кроме того, коммерческая посылка стоит достаточно дорого и люди нашли способ сэкономить и на доставке. Одним словом, нет ничего невозможного.&lt;/p&gt;\r\n&lt;h3&gt;&lt;br /&gt;\r\nЧаще всего, такая закупка осуществляется по следующей схеме&lt;/h3&gt;\r\n&lt;ul&gt;\r\n    &lt;li&gt;Разыскивается человек ТАМ. Это может быть Ваша подруга, друг или даже родственник. Если таких ТАМ нет &amp;ndash; не страшно, уже существует много фирм-посредников, готовых взять на себя эту ношу (небескорыстно, конечно же).&lt;/li&gt;\r\n    &lt;li&gt;Вы заказываете товары в иностранном интернет-магазине на адрес ТОГО человека (или компании).&lt;/li&gt;\r\n    &lt;li&gt;Он переправляет ее Вам. В зависимости от договоренности, он может проверить целостность товара, его качество. Насколько я поняла, пересылка посылки частными лицами в частном порядке обходится гораздо дешевле, чем коммерческая посылка из магазина в другую страну.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;Конечно, зарубежному помощнику тоже отчисляется какой-то процент, как и тому, кто организует эту закупку отсюда. Но цена все равно получается очень выгодная.&lt;/p&gt;\r\n&lt;h3&gt;Выгоды совместных закупок&lt;/h3&gt;\r\n&lt;ul&gt;\r\n    &lt;li&gt;Выгода первая &amp;ndash; это, конечно же, приобретение желанной вещи по оптовой цене. Иногда экономия получается не просто ощутимой, а ОЧЕНЬ ОЩУТИМОЙ.&lt;/li&gt;\r\n    &lt;li&gt;Второй плюс &amp;ndash; это возможность подзаработать, став Организатором. Конечно, чтобы стать им нужны определенные условия, но они вполне выполнимы. Я уже познакомилась с несколькими мамочками из других городов, которые имеют неплохой заработок от организации и проведения СП в своем городе.&lt;/li&gt;\r\n    &lt;li&gt;Третий большой плюс &amp;ndash; это возможность приобретать вещи из зарубежных магазинов. Тут может привлекать и качество (вещь делается не на экспорт, а для своих), доступная цена (такие же вещи в русских магазинах будут стоить в разы дороже). А иногда желаемой вещи просто не найти в нашей стране, и покупка в зарубежном магазине может стать единственным вариантом.&lt;br /&gt;\r\n    &amp;nbsp;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;script type=&quot;text/javascript&quot;&gt;(function(w,doc) {\r\nif (!w.__utlWdgt ) {\r\n    w.__utlWdgt = true;\r\n    var d = doc, s = d.createElement(\\\'script\\\'), g = \\\'getElementsByTagName\\\';\r\n    s.type = \\\'text/javascript\\\'; s.charset=\\\'UTF-8\\\'; s.async = true;\r\n    s.src = (\\\'https:\\\' == w.location.protocol ? \\\'https\\\' : \\\'http\\\')  + \\\'://w.uptolike.com/widgets/v1/uptolike.js\\\';\r\n    var h=d[g](\\\'body\\\')[0];\r\n    h.appendChild(s);\r\n}})(window,document);\r\n&lt;/script&gt;\r\n&lt;div data-orientation=&quot;horizontal&quot; data-share-size=&quot;LARGE&quot; data-following-enable=&quot;false&quot; data-like-text-enable=&quot;true&quot; data-sn-ids=&quot;fb.vk.ok.&quot; data-background-alpha=&quot;0.0&quot; data-pid=&quot;1360223&quot; data-selection-enable=&quot;false&quot; data-mode=&quot;like&quot; data-background-color=&quot;#ffffff&quot; data-exclude-show-more=&quot;false&quot; data-share-shape=&quot;SHARP&quot; data-icon-color=&quot;#ffffff&quot; data-counter-background-alpha=&quot;1.0&quot; data-text-color=&quot;#000000&quot; data-buttons-color=&quot;#FFFFFF&quot; data-counter-background-color=&quot;#ffffff&quot; class=&quot;uptolike-buttons&quot; &gt;&lt;/div&gt;',NULL,'1','0'),('7','chto-dumajut-ljudi-o-sovmestnyh-pokupkah','STATUS_ENABLED','MENU_BOTTOM','Что думают люди о совместных покупках','Что думают люди о совместных покупках','Приличная одежда для малыша в магазине стоит не малых денег, и если с двумя тысячами российских рубликов в магазинах делать нечего, то в СП - на эту сумму можно приобрести ворох вещей отличного качества','совместные покупки, совместные закупки','&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;strong&gt;Наталья&lt;/strong&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\nчто касается одежды, особенно детской, да, экономия реальная. Вещи с рынка &amp;quot;маде ин китай&amp;quot; мне на своего ребенка одевать не хочется, и не только по эстетическим соображения но и в целях безопасности. А приличная одежда для малыша в магазине стоит не малых денег, и если с двумя тысячами российских рубликов в магазинах делать нечего, то в СП - на эту сумму можно приобрести ворох вещей отличного качества (я про летнюю одежду). Для сравнения сарафан купленный дочке в СП обошелся мне в 230 руб, такой-же в соседнем от дома магазине за 950 висит. Чувствуете разницу. Мы молодая семья, и работает у нас на данный момент только муж, и &amp;quot;кормить&amp;quot; владельце магазинов делающих 100-200% накрутку я не собираюсь. И ребенка своего в, простите &amp;quot;дерьмо&amp;quot; одевать тоже не хочется...&lt;br /&gt;\r\n&lt;br /&gt;\r\n&lt;br /&gt;\r\n&lt;strong&gt;Юля&lt;/strong&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\nя вот теперь почти всё покупаю только так (кроме продуктов - это действительно бред). Вы же покупаете одежду, например? Так вот отповику продают в 2-3, а то и больше раз дешевле. Маме на это лето так купила льняной костюм за 350! рублей, абсолютно такой, какие продают в магазе за 1300рэ... А еще так можно купить и детские вещи, коляски и прочие необходимые вещи. В магазин хожу теперь только померить, выбрать размер, чтобы наверняка подошло, а заказываю в сп.&lt;/p&gt;\r\n&lt;p&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\n&lt;strong&gt;Николай&lt;/strong&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\nСовместная покупка это fun! Одни встречи в Москве чего стоят&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n&lt;script type=&quot;text/javascript&quot;&gt;(function(w,doc) {\r\nif (!w.__utlWdgt ) {\r\n    w.__utlWdgt = true;\r\n    var d = doc, s = d.createElement(\\\'script\\\'), g = \\\'getElementsByTagName\\\';\r\n    s.type = \\\'text/javascript\\\'; s.charset=\\\'UTF-8\\\'; s.async = true;\r\n    s.src = (\\\'https:\\\' == w.location.protocol ? \\\'https\\\' : \\\'http\\\')  + \\\'://w.uptolike.com/widgets/v1/uptolike.js\\\';\r\n    var h=d[g](\\\'body\\\')[0];\r\n    h.appendChild(s);\r\n}})(window,document);\r\n&lt;/script&gt;\r\n&lt;div data-orientation=&quot;horizontal&quot; data-share-size=&quot;LARGE&quot; data-following-enable=&quot;false&quot; data-like-text-enable=&quot;true&quot; data-sn-ids=&quot;fb.vk.ok.&quot; data-background-alpha=&quot;0.0&quot; data-pid=&quot;1360223&quot; data-selection-enable=&quot;false&quot; data-mode=&quot;like&quot; data-background-color=&quot;#ffffff&quot; data-exclude-show-more=&quot;false&quot; data-share-shape=&quot;SHARP&quot; data-icon-color=&quot;#ffffff&quot; data-counter-background-alpha=&quot;1.0&quot; data-text-color=&quot;#000000&quot; data-buttons-color=&quot;#FFFFFF&quot; data-counter-background-color=&quot;#ffffff&quot; class=&quot;uptolike-buttons&quot; &gt;&lt;/div&gt;',NULL,'1','0'),('8','o-sisteme-sovmestnyh-pokupok','STATUS_ENABLED','MENU_BOTTOM','О системе совместных покупок','О системе совместных покупок','Как это работает? Как купить качественную вещь по низкой цене, да еще не выезжая за пределы своего города? Тысячи россиян уже убедились в том, что это удобный и выгодный способ покупать по-настоящему качественные вещи.','совместные покупки, совместные закупки','&lt;h1&gt;О системе совместных покупок&lt;/h1&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;Как купить качественную вещь по низкой цене, да еще не выезжая за пределы своего города? Об этом мечтают все &amp;ndash; девушки, желающие стильно одеваться, молодые родители, которые ищут хорошие вещи для малыша, мужчины, имеющие авто и желающие купить для него стильные аксессуары&amp;hellip; С некоторых пор их мечта стала реальностью &amp;ndash; ведь появился сервис &lt;strong&gt;&amp;laquo;совместная покупка&amp;raquo;&lt;/strong&gt;!&lt;br /&gt;\r\n&lt;br /&gt;\r\nВы удивитесь, но это явление в нашей стране стало популярным еще в голодные 90-е годы: бабушки штурмовали оптовые базы продуктов, чтобы вскладчину купить провизию по более низким ценам. Оказалось, принцип работает не только с продуктами: &lt;strong&gt;совместная покупка одежды&lt;/strong&gt;, обуви, бытовой техники, детских вещей &amp;ndash; это очень выгодно и удобно! А теперь это вообще можно делать через Интернет &amp;ndash; не выходя из дома найти себе компанию, чтобы покупать совместно детские игрушки и любые другие товары.&lt;br /&gt;\r\n&lt;br /&gt;\r\nКак это работает? Сначала нужно отыскать подходящий &lt;strong&gt;сайт совместных покупок&lt;/strong&gt;. Их много, и все они разные: где-то покупают элитные сорта кофе, где-то &amp;ndash; мобильные телефоны&amp;hellip; Если хорошо поискать, то можно найти желающих купить любые, даже самые необычные товары. Итак, вы регистрируетесь там, и сообщаете, что очень хотите купить такую-то вещь по такой-то цене. После этого нужно ждать, когда наберется количество желающих &amp;ndash; так, чтобы &lt;strong&gt;совместная покупка&lt;/strong&gt; вписывалась в сумму минимального заказа. Тогда организатор отправляет заявку, и через некоторое время получает (обычно по почте) заказ. После этого происходит раздача заказов.&lt;br /&gt;\r\n&lt;br /&gt;\r\nЛюбой &lt;strong&gt;форум совместных покупок&lt;/strong&gt; не может существовать без толкового организатора &amp;ndash; он собирает заявки, находит поставщиков (&lt;strong&gt;совместная покупка одежды&lt;/strong&gt; выгодна только в том случае, если товар качественный, а цена низкая &amp;ndash; такое предложение нужно еще поискать). Организатор собирает деньги и получает заказ &amp;ndash; все это требует времени и сил, поэтому справедливо, когда организатор берет себе процент за работу. Кроме этого, нужно оплачивать доставку общего заказа и пересылку участникам &lt;strong&gt;совместной покупки&lt;/strong&gt; &amp;ndash; если они живут в разных городах.&lt;br /&gt;\r\n&lt;br /&gt;\r\nСервис &lt;strong&gt;&amp;laquo;&lt;/strong&gt;&lt;strong&gt;совместная покупка&lt;/strong&gt;&lt;strong&gt;&amp;raquo;&lt;/strong&gt; появился в нашей стране относительно недавно, и многие пока относятся к нему с недоверием. Но тысячи россиян уже убедились в том, что это удобный и выгодный способ покупать по-настоящему качественные вещи.&lt;/p&gt;\r\n&lt;script type=&quot;text/javascript&quot;&gt;(function(w,doc) {\r\nif (!w.__utlWdgt ) {\r\n    w.__utlWdgt = true;\r\n    var d = doc, s = d.createElement(\\\'script\\\'), g = \\\'getElementsByTagName\\\';\r\n    s.type = \\\'text/javascript\\\'; s.charset=\\\'UTF-8\\\'; s.async = true;\r\n    s.src = (\\\'https:\\\' == w.location.protocol ? \\\'https\\\' : \\\'http\\\')  + \\\'://w.uptolike.com/widgets/v1/uptolike.js\\\';\r\n    var h=d[g](\\\'body\\\')[0];\r\n    h.appendChild(s);\r\n}})(window,document);\r\n&lt;/script&gt;\r\n&lt;div data-orientation=&quot;horizontal&quot; data-share-size=&quot;LARGE&quot; data-following-enable=&quot;false&quot; data-like-text-enable=&quot;true&quot; data-sn-ids=&quot;fb.vk.ok.&quot; data-background-alpha=&quot;0.0&quot; data-pid=&quot;1360223&quot; data-selection-enable=&quot;false&quot; data-mode=&quot;like&quot; data-background-color=&quot;#ffffff&quot; data-exclude-show-more=&quot;false&quot; data-share-shape=&quot;SHARP&quot; data-icon-color=&quot;#ffffff&quot; data-counter-background-alpha=&quot;1.0&quot; data-text-color=&quot;#000000&quot; data-buttons-color=&quot;#FFFFFF&quot; data-counter-background-color=&quot;#ffffff&quot; class=&quot;uptolike-buttons&quot; &gt;&lt;/div&gt;',NULL,'1','0'),('9','sovmestnie-pokupki-nachalo','STATUS_ENABLED','MENU_BOTTOM','Совместные покупки - начало','Совместные покупки - начало','Как найти единомышленников? Для этого нужно зайти на сайт совместных покупок. Раньше участники собирались на форумах, где объявляли о своем желании приобрести что-либо (например, совместная покупка одежды)','совместные покупки, совместные закупки','&lt;h1&gt;Совместные покупки - начало&lt;/h1&gt;\r\n&lt;p&gt;Все еще думаете, что дешево купить брендовую одежду, качественную косметику и хорошие детские игрушки предлагают только мошенники? Тогда вы еще не знаете, что такое совместные покупки! Это надежный способ не только сэкономить, но и приобрести качественные вещи, которых нет в магазинах.&lt;/p&gt;\r\n&lt;p&gt;Как выглядит сервис под названием &amp;laquo;совместная покупка&amp;raquo;? Сначала собирается группа единомышленников, которые хотят купить что-либо &amp;ndash; обувь, кухонную утварь, товары для новорожденных &amp;ndash; список не ограничен. Потом находится оптовый поставщик, который готов продать партию интересующего их товара. Тот факт, что покупателей на самом деле много, оптовиков обычно не интересует &amp;ndash; главное, чтобы они вместе могли приобрести минимальную партию товара. Товар оплачивается, доставляется, участники разбирают свои заказы.&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;Как найти единомышленников? Для этого нужно зайти на сайт совместных покупок. Раньше участники собирались на форумах, где объявляли о своем желании приобрести что-либо (например, совместная покупка одежды), и ждали, пока к ним кто-нибудь присоединится. Но теперь удобная автоматическая система совместных покупок &amp;ndash; с ее помощью можно найти участников в считанные минуты! Все просто &amp;ndash; выбираете свой город, и смотрите, есть ли в нем участники совместных покупок (сп). Возможно, как раз сейчас там проходят интересующие вас совместные детские покупки &amp;ndash; присоединяйтесь! &lt;br /&gt;\r\n&lt;br /&gt;\r\nНа этом сайте вы сможете найти людей, без которых совместная покупка невозможна &amp;ndash; организаторов. Они помогут найти участников, договорятся с поставщиками товаров и организуют доставку заказов покупателям. &lt;br /&gt;\r\n&lt;br /&gt;\r\nЭта система помогает организаторам и участникам находить друг-друга, но никто не будет контролировать ваши финансовые вопросы &amp;ndash; все расчеты производятся пользователями самостоятельно. &lt;br /&gt;\r\n&lt;br /&gt;\r\nНе нашли свой город в списке? Для того, чтобы он там появился, достаточно просто написать разработчикам сайта. Есть и другой способ: зайдите в систему через социальную сеть &amp;laquo;ВКонтакте&amp;raquo; - и ваш родной город будет добавлен в список.&lt;/p&gt;\r\n&lt;script type=&quot;text/javascript&quot;&gt;(function(w,doc) {\r\nif (!w.__utlWdgt ) {\r\n    w.__utlWdgt = true;\r\n    var d = doc, s = d.createElement(\\\'script\\\'), g = \\\'getElementsByTagName\\\';\r\n    s.type = \\\'text/javascript\\\'; s.charset=\\\'UTF-8\\\'; s.async = true;\r\n    s.src = (\\\'https:\\\' == w.location.protocol ? \\\'https\\\' : \\\'http\\\')  + \\\'://w.uptolike.com/widgets/v1/uptolike.js\\\';\r\n    var h=d[g](\\\'body\\\')[0];\r\n    h.appendChild(s);\r\n}})(window,document);\r\n&lt;/script&gt;\r\n&lt;div data-orientation=&quot;horizontal&quot; data-share-size=&quot;LARGE&quot; data-following-enable=&quot;false&quot; data-like-text-enable=&quot;true&quot; data-sn-ids=&quot;fb.vk.ok.&quot; data-background-alpha=&quot;0.0&quot; data-pid=&quot;1360223&quot; data-selection-enable=&quot;false&quot; data-mode=&quot;like&quot; data-background-color=&quot;#ffffff&quot; data-exclude-show-more=&quot;false&quot; data-share-shape=&quot;SHARP&quot; data-icon-color=&quot;#ffffff&quot; data-counter-background-alpha=&quot;1.0&quot; data-text-color=&quot;#000000&quot; data-buttons-color=&quot;#FFFFFF&quot; data-counter-background-color=&quot;#ffffff&quot; class=&quot;uptolike-buttons&quot; &gt;&lt;/div&gt;',NULL,'1','0'),('48','admin','STATUS_ENABLED','MENU_TOP','Что такое совместные покупки (СП)?','Это организованная покупка каких-либо вещей у фирмы-поставщика по оптовым ценам','Это организованная покупка каких-либо вещей у фирмы-поставщика по оптовым ценам','Это организованная покупка каких-либо вещей у фирмы-поставщика по оптовым ценам','&lt;p&gt;&lt;span style=&quot;font-family: verdana; line-height: 18px;&quot;&gt;Это организованная покупка каких-либо вещей у фирмы-поставщика по оптовым ценам. Для кого-то это способ сэкономить, для кого-то наоборот, возможность приобрести дорогие, эксклюзивные вещи, не представленные в магазинах города. Для того чтобы решить, подходит ли Вам такой способ приобретения товара, рассмотрите ниже все плюсы и минусы совместных покупок.&lt;/span&gt;&lt;strong&gt;&lt;br /&gt;\r\n&lt;/strong&gt;&lt;/p&gt;\r\n&lt;div class=&quot;b-form&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; font-family: verdana; line-height: 18px; background-image: initial; background-attachment: initial; background-size: initial; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: initial;&quot;&gt;&lt;br /&gt;\r\n&lt;h3 style=&quot;margin: 0px; padding: 12px 0px 10px; border: 0px; outline: 0px; font-size: 18px; vertical-align: baseline; color: rgb(65, 65, 65); font-weight: normal; font-family: arial, sans-serif; line-height: 26px; background: transparent;&quot;&gt;&lt;u&gt;&lt;strong&gt;Преимущества СП&lt;/strong&gt;&lt;/u&gt;&lt;/h3&gt;\r\n&lt;div class=&quot;info2&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;&lt;br /&gt;\r\n&lt;p class=&quot;a-arrow2&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;&lt;strong&gt;Широкий ассортимент товара.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;br /&gt;\r\n&lt;p class=&quot;a-text&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;Покупка осуществляется непосредственно со склада поставщика, обычно там представлен более широкий ассортимент, в сравнении с магазином, в котором обычно &amp;laquo;к сожалению, вашего размера нет&amp;raquo;, &amp;laquo;это последний экземпляр&amp;raquo;, &amp;laquo;только такой цвет&amp;raquo;. Вы можете подобрать нужный размер, цвет, материал, сравнить несколько моделей и спокойно подумав, сделать выбор. При совместной покупке есть отличная возможность заказать товар на будущий сезон. Вы можете ознакомиться с новыми коллекциями гораздо раньше, чем они появятся в магазинах и сделать заказ. К моменту, когда новая коллекция только появится в магазинах, заветная вещица уже будет висеть у вас в шкафу.&lt;/p&gt;\r\n&lt;br /&gt;\r\n&lt;p class=&quot;a-arrow2&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;Возможность купить товар, не представленный в магазинах города.&lt;/p&gt;\r\n&lt;br /&gt;\r\n&lt;p class=&quot;a-text&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;Интернет дает возможность купить практически любой товар по всему миру, а участие в совместной покупке позволяет снизить стоимость товара и его доставки. На СП можно заказать эксклюзивные товары, которые не продаются в нашем городе.&lt;/p&gt;\r\n&lt;/div&gt;\r\n&lt;div class=&quot;info2&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;&lt;br /&gt;\r\n&lt;p class=&quot;a-arrow2&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;&lt;strong&gt;Значительно более низкая цена товара, чем в магазинах.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;br /&gt;\r\n&lt;p class=&quot;a-text&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;Цена товара на Сайте Покупок может отличаться от розничной в разы за счет приобретения на оптовом складе. Ваша выгода может составить до 90% от розничной стоимости аналогичных товаров в магазине. Организация совместной покупки практически не требуют затрат, это покупка товара непосредственно у поставщика по оптовой цене. В то время как реально существующим магазинам необходимо оплачивать производственные затраты (аренда, оплата складских помещений, зарплата продавцам). Соответственно все эти издержки магазина включены в стоимость товара и оплачиваются покупателями.&lt;/p&gt;\r\n&lt;br /&gt;\r\n&lt;p class=&quot;a-arrow2&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;&lt;strong&gt;Шоппинг без ограничений.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;/div&gt;\r\n&lt;/div&gt;\r\n&lt;p&gt;&lt;span style=&quot;font-family: verdana; line-height: 18px; background-color: transparent;&quot;&gt;Поход по магазинам и излишне навязчивый сервис или наоборот, мило беседующие между собой продавцы, не обращающие внимания на покупателей, могут раздражать и утомлять покупателей. Благодаря СП Вам не нужно часами бродить по магазинам в поисках нужной вещи. Вы можете посетить сайт в любой момент и выбрать товар, не выходя их дома.&lt;/span&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;div class=&quot;b-form&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; font-family: verdana; line-height: 18px; background-image: initial; background-attachment: initial; background-size: initial; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: initial;&quot;&gt;\r\n&lt;h3 style=&quot;margin: 0px; padding: 12px 0px 10px; border: 0px; outline: 0px; font-size: 18px; vertical-align: baseline; color: rgb(65, 65, 65); font-weight: normal; font-family: arial, sans-serif; line-height: 26px; background: transparent;&quot;&gt;&lt;u&gt;&lt;strong&gt;Недостатки СП&lt;/strong&gt;&lt;/u&gt;&lt;/h3&gt;\r\n&lt;div class=&quot;info2&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;&lt;br /&gt;\r\n&lt;p class=&quot;a-arrow3&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;Срок выкупа заказа всегда дольше, чем просто покупка в магазине.&lt;/p&gt;\r\n&lt;br /&gt;\r\n&lt;p class=&quot;a-text&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;Срок выкупа заказа зависит от наличия товара на складе, от сбора минимальной партии и от наличия необходимой суммы на выкуп. Обычно время доставки товара составляет около 2-х недель.&lt;/p&gt;\r\n&lt;/div&gt;\r\n&lt;div class=&quot;info2&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;&lt;br /&gt;\r\n&lt;p class=&quot;a-arrow3&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;Иногда возможно несовпадение цветов и некоторых других характеристик товара.&lt;/p&gt;\r\n&lt;br /&gt;\r\n&lt;p class=&quot;a-text&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;СП &amp;ndash; не магазин, то есть не место, где в стоимость товара включен &amp;quot;любой каприз&amp;quot;. Совпадение цветов и некоторых других характеристик товара в некоторых закупках не гарантируется. Организатор не может проконтролировать поставщика и проследить, чтобы на складе отгрузили товар нужного цвета.&lt;/p&gt;\r\n&lt;br /&gt;\r\n&lt;p class=&quot;a-arrow3&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;Невозможность вернуть деньги только потому, что товар не подошел по размеру или внешнему виду.&lt;/p&gt;\r\n&lt;br /&gt;\r\n&lt;p class=&quot;a-text&quot; style=&quot;margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background: transparent;&quot;&gt;Претензии можно предъявлять только организатору, и только если обнаружен брак.&lt;/p&gt;\r\n&lt;/div&gt;\r\n&lt;/div&gt;\r\n&lt;p&gt;&lt;span style=&quot;font-family: verdana; line-height: 18px;&quot;&gt;Помните, что с того момента, как вы согласились участвовать в СП, вы взяли на себя обязательства соблюдать&lt;/span&gt;&lt;/p&gt;',NULL,'2','0'),('49','predator','STATUS_ENABLED','MENU_NONE','Движок для автоматизации ведения совместных покупок','Движок для автоматизации ведения совместных покупок','Возможность для организаторов совместных покупок автоматизировать процесс сбора заказов, приема оплаты, раздачи, в том числе в офисах раздачь - готовый движок совместных покупок ПРОДАЕТСЯ!','движок,сп,совместные покупки,автоматизация,CMS','&lt;p&gt;&lt;b&gt;Всего за 9990 рублей!&lt;/b&gt; Для организаторов совместных покупок, а так же для веб мастеров, предоставляющих сервис совместных покупок - возможность стабильного заработка за счет использования полностью автоматизированного движка совместных покупок.&lt;/p&gt;\r\n&lt;p&gt;Функционал (если кратко) включает в себя следующее:&lt;/p&gt;\r\n&lt;p&gt;&lt;b&gt;Для покупателей:&lt;/b&gt;&lt;br /&gt;\r\n1) Возможность видеть все сделанные заказы в одном месте (в корзине),&lt;br /&gt;\r\n2) Отслеживание статусов закупки, в том числе за счёт уведомлений, которые отправляет сайт покупателям, а так же визуально (в корзине),&lt;br /&gt;\r\n3) Возможность оплачивать заказы организаторам в корзине на карту СБ или через Яндекс Кассу (если орг - юридическое лицо),&lt;br /&gt;\r\n4) Оплата доставки закупок. Все расчёты по закупке в одном месте - в корзине. Сделан контроль оплаты с подтверждением получения, есть возможность проводить возвраты.&lt;br /&gt;\r\n5) Есть возможность подписаться на регулярно проводимые закупки (выкупы) с получением уведомления при открытии новой закупки по данному выкупу.&lt;br /&gt;\r\n6) Есть встроенная система личных сообщений с контролем прочтения сообщений.&lt;br /&gt;\r\n7) В закупках есть комментарии, которыми покупатель может задать вопрос организатору и получить ответ.&lt;br /&gt;\r\n8) С заданной периодичностью покупатели получают дайджест от сайта, в котором сгруппировано количество закупок, открытых со времени его предыдущего посещения, кол-во непрочитанных личных сообщений, список ссылок на закупки, в которых организатор ответил на комментарий покупателя (чтобы было удобно отслеживать где покупатель получил ответ на его вопрос).&lt;br /&gt;\r\n9) Есть информационный раздел, в котором имеется &amp;quot;Пристрой&amp;quot;, &amp;quot;Хвастики&amp;quot;, &amp;quot;Барахолка&amp;quot;, &amp;quot;Услуги&amp;quot;, &amp;quot;Куплю&amp;quot;  и т.д.&lt;br /&gt;\r\n10) Ну и есть интегрированный с сайтом форум (единая регистрация через сайт и авторизация).&lt;/p&gt;\r\n&lt;p&gt;&lt;b&gt;Для организаторов:&lt;/b&gt;&lt;br /&gt;\r\n1) Закупки можно наполнять, используя парсер sliza.ru, либо через csv файл, либо вручную,&lt;br /&gt;\r\n2) По каждой закупке организатор может сформировать заявку поставщику,&lt;br /&gt;\r\n3) По закупке организатор может распределить стоимость доставки, как автоматически (пропорционально), так и вручную,&lt;br /&gt;\r\n4) Организатор может отмечать наличие заказанных товаров в своей закупке, при этом новая информация отображается в корзинах покупателей,&lt;br /&gt;\r\n5) Организатор контролирует взаиморасчеты с покупателями прямо в закупке, в зависимости от оплаты и текущей ситуации может поставить &amp;quot;нет в наличии&amp;quot;,&lt;br /&gt;\r\n6) Может делать рассылки пользователям как по закупке, так и всем (модерируется админом),&lt;br /&gt;\r\n7) Есть закрепление поставщиков. Это чтобы не получилось что несколько организаторов открывают одно и то же,&lt;br /&gt;\r\n8) Перед открытием, за закупки можно голосовать, изучая потребности покупателей и открывать только заведомо интересные закупки,&lt;br /&gt;\r\n9) Есть отдельный функционал - запись на раздачу. И собственно раздачи есть через сайт с отметкой по заказу &amp;quot;Выдано&amp;quot;,&lt;br /&gt;\r\n10) Есть механизм заказа в офисы раздач. Передача в офис может быть платной. Администратор может назначить офис-менеджера, который через систему сможет выдавать пользователям заказы.&lt;/p&gt;\r\n&lt;p&gt;&lt;b&gt;Для администратора:&lt;/b&gt;&lt;br /&gt;\r\n1) Контроль за пользователями, организаторами, закупками, комментариями,&lt;br /&gt;\r\n2) Информация по всем закупкам,&lt;br /&gt;\r\n3) Возможность зарабатывать % с оборота организаторов с контролем взаиморасчётов, начисления сумм, оплаты процента.&lt;br /&gt;\r\n4) Возможность заработать с сайта &amp;quot;сателита&amp;quot; (открытого на базе движка) за каждый заказ (к примеру рубль или типа того - настраивается).&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gt;&lt;i&gt;Для более детальной информации, скачайте:&lt;/i&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\n&lt;a target=&quot;_blank&quot; href=&quot;http://massapokupok.ru/storage/docs/manual_organizator.pdf&quot;&gt;Руководство организатора&lt;/a&gt;,&lt;br /&gt;\r\n&lt;a target=&quot;_blank&quot; href=&quot;http://massapokupok.ru/storage/docs/manual_user.pdf&quot;&gt;Руководство участника закупки&lt;/a&gt;,&lt;br /&gt;\r\n&lt;a target=&quot;_blank&quot; href=&quot;#&quot;&gt;Руководство администратора&lt;/a&gt;.&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gt;&lt;i&gt;Для покупки движка используйте форму:&lt;/i&gt;&lt;/p&gt;\r\n&lt;p&gt;&lt;iframe height=&quot;390&quot; width=&quot;275&quot; style=&quot;border: 1px solid #e8e8e8;&quot; src=&quot;https://money.yandex.ru/fastpay/form/2d22da609a1643a1a7690dd5d7d6b8b9&quot; class=&quot;iframe&quot;&gt;&lt;/iframe&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;',NULL,'1','0');

UNLOCK TABLES;

/*Table structure for table `country` */

DROP TABLE IF EXISTS `country`;

CREATE TABLE `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vkCountryId` int(11) DEFAULT NULL,
  `entityStatus` int(3) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `country` */

LOCK TABLES `country` WRITE;

insert  into `country`(`id`,`name`,`vkCountryId`,`entityStatus`,`ownerSiteId`,`ownerOrgId`) values ('1','Россия','1','1','1','0'),('2','Украина','2','1','1','0'),('15','Япония','229','1','1','0'),('16','Израиль','8','1','1','0'),('17','Китай','97','1','1','0'),('18','Армения','6','1','1','0'),('19','Италия','88','1','1','0'),('20','Германия','65','1','1','0'),('21','Нидерланды','139','1','1','0');

UNLOCK TABLES;

/*Table structure for table `csvfile` */

DROP TABLE IF EXISTS `csvfile`;

CREATE TABLE `csvfile` (
  `sessHash` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `userId` int(11) NOT NULL,
  `headId` int(11) DEFAULT NULL,
  `tsCreated` int(11) DEFAULT NULL,
  `field1` text COLLATE utf8_unicode_ci,
  `field2` text COLLATE utf8_unicode_ci,
  `field3` text COLLATE utf8_unicode_ci,
  `field4` text COLLATE utf8_unicode_ci,
  `field5` text COLLATE utf8_unicode_ci,
  `field6` text COLLATE utf8_unicode_ci,
  `field7` text COLLATE utf8_unicode_ci,
  `field8` text COLLATE utf8_unicode_ci,
  `field9` text COLLATE utf8_unicode_ci,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  KEY `sessHash` (`sessHash`),
  KEY `user_idx` (`userId`),
  KEY `head_idx` (`headId`),
  KEY `site_idx` (`ownerSiteId`),
  KEY `ownerorg_idx` (`ownerOrgId`),
  KEY `tsCreated` (`tsCreated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `csvfile` */

LOCK TABLES `csvfile` WRITE;

UNLOCK TABLES;

/*Table structure for table `hint` */

DROP TABLE IF EXISTS `hint`;

CREATE TABLE `hint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hint` longtext COLLATE utf8_unicode_ci,
  `contAlias` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `hint` */

LOCK TABLES `hint` WRITE;

insert  into `hint`(`id`,`alias`,`title`,`hint`,`contAlias`,`ownerSiteId`,`ownerOrgId`) values ('6','addzhnoact','Если не добавляет','&lt;p&gt;Если Вы нажали кнопку и ничего не произошло, пролистайте вврех страницу и проверьте все ли поля Вы ввели и правильно ли.&lt;/p&gt;','addzhnoact','1','0'),('9','coladdtovname','Впишите размер и цвет','&lt;p&gt;Если для выбранного Вами товара важен размер, цвет, то укажите их в поле с наименованием.&lt;/p&gt;',NULL,'1','0');

UNLOCK TABLES;

/*Table structure for table `mainCommision` */

DROP TABLE IF EXISTS `mainCommision`;

CREATE TABLE `mainCommision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `baseAmount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `needAmount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `payAmount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `dateCreate` int(11) DEFAULT NULL,
  `dateConfirm` int(11) DEFAULT NULL,
  `userInfo` text COLLATE utf8_unicode_ci,
  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `way` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `mainCommision` */

LOCK TABLES `mainCommision` WRITE;

UNLOCK TABLES;

/*Table structure for table `meeting` */

DROP TABLE IF EXISTS `meeting`;

CREATE TABLE `meeting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8_unicode_ci,
  `headId` int(11) DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `userCount` int(11) NOT NULL DEFAULT '0',
  `userLimit` int(11) NOT NULL DEFAULT '0',
  `startTs` int(11) DEFAULT NULL,
  `finishTs` int(11) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`),
  KEY `headId` (`headId`),
  KEY `orgId` (`orgId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `meeting` */

LOCK TABLES `meeting` WRITE;

UNLOCK TABLES;

/*Table structure for table `messageDialogue` */

DROP TABLE IF EXISTS `messageDialogue`;

CREATE TABLE `messageDialogue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user1` int(11) DEFAULT NULL COMMENT 'Р’СЃРµРіРґР° СЃРѕСЂС‚РёСЂРѕРІР°С‚СЊ id СЃРѕР±РµСЃРµРґРЅРёРєРѕРІ РѕС‚ РјРµРЅСЊС€РµРіРѕ Рє Р±РѕР»СЊС€РµРјСѓ!',
  `userNick1` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user2` int(11) DEFAULT NULL COMMENT 'Р’СЃРµРіРґР° СЃРѕСЂС‚РёСЂРѕРІР°С‚СЊ id СЃРѕР±РµСЃРµРґРЅРёРєРѕРІ РѕС‚ РјРµРЅСЊС€РµРіРѕ Рє Р±РѕР»СЊС€РµРјСѓ!',
  `userNick2` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastReadId12` int(11) NOT NULL DEFAULT '0',
  `lastReadId21` int(11) NOT NULL DEFAULT '0',
  `hasNew12` int(1) NOT NULL DEFAULT '0',
  `hasNew21` int(1) NOT NULL DEFAULT '0',
  `dateUpdate` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usr12_idx` (`user1`,`user2`),
  KEY `user1_idx` (`user1`),
  KEY `user2_idx` (`user2`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `messageDialogue` */

LOCK TABLES `messageDialogue` WRITE;

UNLOCK TABLES;

/*Table structure for table `messageFrom` */

DROP TABLE IF EXISTS `messageFrom`;

CREATE TABLE `messageFrom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `userFromId` int(11) DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `dateCreate` int(11) DEFAULT NULL,
  `dateUpdate` int(11) DEFAULT NULL,
  `isDeleteReceiver` int(1) NOT NULL DEFAULT '0',
  `isDeleteSender` int(1) NOT NULL DEFAULT '0',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cre_idx` (`userId`,`userFromId`,`dateCreate`),
  KEY `user_idx` (`userId`),
  KEY `userfrom_idx` (`userFromId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `messageFrom` */

LOCK TABLES `messageFrom` WRITE;

UNLOCK TABLES;

/*Table structure for table `messageTo` */

DROP TABLE IF EXISTS `messageTo`;

CREATE TABLE `messageTo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `userToId` int(11) DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `dateCreate` int(11) DEFAULT NULL,
  `dateUpdate` int(11) DEFAULT NULL,
  `isDeleteSender` int(1) NOT NULL DEFAULT '0',
  `isDeleteReceiver` int(1) NOT NULL DEFAULT '0',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_idx` (`userId`),
  KEY `userto_idx` (`userToId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `messageTo` */

LOCK TABLES `messageTo` WRITE;

UNLOCK TABLES;

/*Table structure for table `news` */

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) DEFAULT NULL,
  `body` text,
  `showDate` int(11) DEFAULT NULL,
  `creationDate` int(11) DEFAULT NULL,
  `type` int(3) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `news` */

LOCK TABLES `news` WRITE;

UNLOCK TABLES;

/*Table structure for table `office` */

DROP TABLE IF EXISTS `office`;

CREATE TABLE `office` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `schedule` text COLLATE utf8_unicode_ci,
  `status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'STATUS_ENABLED',
  `price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `office` */

LOCK TABLES `office` WRITE;

UNLOCK TABLES;

/*Table structure for table `officeOrder` */

DROP TABLE IF EXISTS `officeOrder`;

CREATE TABLE `officeOrder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `officeId` int(11) DEFAULT NULL,
  `orderId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `headId` int(11) DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rejectReason` text COLLATE utf8_unicode_ci,
  `price` decimal(5,2) NOT NULL DEFAULT '0.00',
  `payHold` decimal(9,2) NOT NULL DEFAULT '0.00',
  `payAmount` decimal(9,2) NOT NULL DEFAULT '0.00',
  `payBackAmount` decimal(9,2) NOT NULL DEFAULT '0.00',
  `payStatus` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tsOrder` int(11) NOT NULL DEFAULT '0',
  `tsOrg` int(11) NOT NULL DEFAULT '0',
  `tsOffice` int(11) NOT NULL DEFAULT '0',
  `tsUser` int(11) NOT NULL DEFAULT '0',
  `officeUserId` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`headId`,`orgId`,`status`),
  KEY `orderId` (`orderId`),
  KEY `officeId` (`officeId`),
  KEY `site_idx` (`ownerSiteId`),
  KEY `org_idx` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `officeOrder` */

LOCK TABLES `officeOrder` WRITE;

UNLOCK TABLES;

/*Table structure for table `olapOwnerOrg` */

DROP TABLE IF EXISTS `olapOwnerOrg`;

CREATE TABLE `olapOwnerOrg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataDate` date NOT NULL,
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  `countUsers` int(11) NOT NULL DEFAULT '0',
  `countActiveUsers` int(11) NOT NULL DEFAULT '0',
  `countZak` int(11) NOT NULL DEFAULT '0',
  `countZakOpen` int(11) NOT NULL DEFAULT '0',
  `countZakFinished` int(11) NOT NULL DEFAULT '0',
  `amountZak` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountOrgCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountOrgCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountSiteCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountSiteCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountMainCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountMainCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `dataDate` (`dataDate`,`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `olapOwnerOrg` */

LOCK TABLES `olapOwnerOrg` WRITE;

UNLOCK TABLES;

/*Table structure for table `olapOwnerSite` */

DROP TABLE IF EXISTS `olapOwnerSite`;

CREATE TABLE `olapOwnerSite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataDate` date NOT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `countUsers` int(11) NOT NULL DEFAULT '0',
  `countActiveUsers` int(11) NOT NULL DEFAULT '0',
  `countZak` int(11) NOT NULL DEFAULT '0',
  `countZakOpen` int(11) NOT NULL DEFAULT '0',
  `countZakFinished` int(11) NOT NULL DEFAULT '0',
  `amountZak` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountOrgCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountOrgCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountSiteCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountSiteCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountMainCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountMainCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `dataDate` (`dataDate`,`ownerSiteId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `olapOwnerSite` */

LOCK TABLES `olapOwnerSite` WRITE;

UNLOCK TABLES;

/*Table structure for table `operLog` */

DROP TABLE IF EXISTS `operLog`;

CREATE TABLE `operLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operId` int(11) DEFAULT NULL,
  `actionName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entityName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entityId` int(11) DEFAULT NULL,
  `actionDateTime` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='лог действий операторов';

/*Data for the table `operLog` */

LOCK TABLES `operLog` WRITE;

UNLOCK TABLES;

/*Table structure for table `operator` */

DROP TABLE IF EXISTS `operator`;

CREATE TABLE `operator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `roleId` int(11) DEFAULT NULL,
  `dateCreate` int(11) DEFAULT NULL,
  `dateLastVisit` int(11) DEFAULT NULL,
  `entityStatus` int(3) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='операторы - те, кто работает с сайтом, банит и т.д.';

/*Data for the table `operator` */

LOCK TABLES `operator` WRITE;

insert  into `operator`(`id`,`login`,`password`,`name`,`phone1`,`roleId`,`dateCreate`,`dateLastVisit`,`entityStatus`,`status`,`ownerSiteId`,`ownerOrgId`) values ('1','admin','04dab57569b1c759602054e83150b820','admin',NULL,'1',NULL,NULL,'1',NULL,'1','0');

UNLOCK TABLES;

/*Table structure for table `optovik` */

DROP TABLE IF EXISTS `optovik`;

CREATE TABLE `optovik` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `canParse` tinyint(1) NOT NULL DEFAULT '0',
  `parseRequest` tinyint(1) NOT NULL DEFAULT '0',
  `dateCreate` int(11) DEFAULT NULL,
  `dateUpdate` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `optovik` */

LOCK TABLES `optovik` WRITE;

UNLOCK TABLES;

/*Table structure for table `orderHead` */

DROP TABLE IF EXISTS `orderHead`;

CREATE TABLE `orderHead` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `headId` int(11) DEFAULT NULL,
  `code` int(5) DEFAULT NULL,
  `orgRate` int(3) NOT NULL DEFAULT '0',
  `optAmount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `payHold` decimal(11,2) NOT NULL DEFAULT '0.00',
  `payAmount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `payBackAmount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `opttoorgDlvrAmount` decimal(9,5) NOT NULL DEFAULT '0.00000',
  `dateCreate` int(11) DEFAULT NULL,
  `dateUpdate` int(11) DEFAULT NULL,
  `datePaymentConfirm` int(11) DEFAULT NULL,
  `allProdCount` int(11) NOT NULL DEFAULT '0',
  `confirmedProdCount` int(11) NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateUser` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usr_hd_idx` (`userId`,`headId`),
  KEY `user_idx` (`userId`),
  KEY `head_idx` (`headId`),
  KEY `code_idx` (`code`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `orderHead` */

LOCK TABLES `orderHead` WRITE;

UNLOCK TABLES;

/*Table structure for table `orderList` */

DROP TABLE IF EXISTS `orderList`;

CREATE TABLE `orderList` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `headId` int(11) DEFAULT NULL,
  `zlId` int(11) DEFAULT NULL,
  `rp` int(5) DEFAULT NULL,
  `num` int(5) NOT NULL DEFAULT '1',
  `stopDel` int(1) NOT NULL DEFAULT '0',
  `isFull` int(1) NOT NULL DEFAULT '0',
  `prodId` int(11) DEFAULT NULL,
  `prodName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prodArt` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optPrice` decimal(11,2) NOT NULL DEFAULT '0.00',
  `opttoorgDlvrAmount` decimal(9,5) NOT NULL DEFAULT '0.00000',
  `count` int(5) NOT NULL DEFAULT '0',
  `size` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateCreate` int(11) DEFAULT NULL,
  `dateUpdate` int(11) DEFAULT NULL,
  `dateConfirm` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order_idx` (`orderId`),
  KEY `user_idx` (`userId`),
  KEY `org_idx` (`orgId`),
  KEY `head_idx` (`headId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `orderList` */

LOCK TABLES `orderList` WRITE;

UNLOCK TABLES;

/*Table structure for table `orderListChangeLog` */

DROP TABLE IF EXISTS `orderListChangeLog`;

CREATE TABLE `orderListChangeLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `headId` int(11) DEFAULT NULL,
  `zlId` int(11) DEFAULT NULL,
  `prodNameOld` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prodArtOld` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optPriceOld` decimal(11,2) NOT NULL DEFAULT '0.00',
  `countOld` int(5) NOT NULL DEFAULT '0',
  `sizeOld` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `colorOld` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commentOld` text COLLATE utf8_unicode_ci,
  `prodNameNew` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prodArtNew` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `optPriceNew` decimal(11,2) NOT NULL DEFAULT '0.00',
  `countNew` int(5) NOT NULL DEFAULT '0',
  `sizeNew` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `colorNew` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commentNew` text COLLATE utf8_unicode_ci,
  `dateCreate` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order_idx` (`orderId`),
  KEY `user_idx` (`userId`),
  KEY `org_idx` (`orgId`),
  KEY `head_idx` (`headId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `orderListChangeLog` */

LOCK TABLES `orderListChangeLog` WRITE;

UNLOCK TABLES;

/*Table structure for table `ownerOrg` */

DROP TABLE IF EXISTS `ownerOrg`;

CREATE TABLE `ownerOrg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownerSiteId` int(11) DEFAULT NULL,
  `alias` varchar(20) DEFAULT NULL,
  `tplFolder` varchar(20) DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `dateCreate` int(11) DEFAULT NULL,
  `countUsers` int(11) NOT NULL DEFAULT '0',
  `countActiveUsers` int(11) NOT NULL DEFAULT '0',
  `countZak` int(11) NOT NULL DEFAULT '0',
  `countZakOpen` int(11) NOT NULL DEFAULT '0',
  `countZakFinished` int(11) NOT NULL DEFAULT '0',
  `amountZak` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountOrgCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountOrgCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountSiteCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountSiteCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountMainCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountMainCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`,`alias`,`orgId`),
  KEY `tplFolder` (`tplFolder`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `ownerOrg` */

LOCK TABLES `ownerOrg` WRITE;

UNLOCK TABLES;

/*Table structure for table `ownerSite` */

DROP TABLE IF EXISTS `ownerSite`;

CREATE TABLE `ownerSite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hostName` varchar(100) DEFAULT NULL,
  `tplFolder` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `dateCreate` int(11) DEFAULT NULL,
  `countUsers` int(11) NOT NULL DEFAULT '0',
  `countActiveUsers` int(11) NOT NULL DEFAULT '0',
  `countZak` int(11) NOT NULL DEFAULT '0',
  `countZakOpen` int(11) NOT NULL DEFAULT '0',
  `countZakFinished` int(11) NOT NULL DEFAULT '0',
  `amountZak` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountOrgCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountOrgCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountSiteCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountSiteCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountMainCommisionAdded` decimal(11,2) NOT NULL DEFAULT '0.00',
  `amountMainCommisionPayed` decimal(11,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `hostName` (`hostName`),
  KEY `tplFolder` (`tplFolder`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `ownerSite` */

LOCK TABLES `ownerSite` WRITE;

insert  into `ownerSite`(`id`,`hostName`,`tplFolder`,`status`,`dateCreate`,`countUsers`,`countActiveUsers`,`countZak`,`countZakOpen`,`countZakFinished`,`amountZak`,`amountOrgCommisionAdded`,`amountOrgCommisionPayed`,`amountSiteCommisionAdded`,`amountSiteCommisionPayed`,`amountMainCommisionAdded`,`amountMainCommisionPayed`) values ('1','43pokupki.ru','massapokupok','STATUS_ENABLED',NULL,'0','0','0','0','0','0.00','0.00','0.00','0.00','0.00','0.00','0.00');

UNLOCK TABLES;

/*Table structure for table `pay` */

DROP TABLE IF EXISTS `pay`;

CREATE TABLE `pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `headId` int(11) DEFAULT NULL,
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `dateCreate` int(11) DEFAULT NULL,
  `dateConfirm` int(11) DEFAULT NULL,
  `userInfo` text COLLATE utf8_unicode_ci,
  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `way` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_idx` (`userId`),
  KEY `org_idx` (`orgId`),
  KEY `head_idx` (`headId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `pay` */

LOCK TABLES `pay` WRITE;

UNLOCK TABLES;

/*Table structure for table `pro` */

DROP TABLE IF EXISTS `pro`;

CREATE TABLE `pro` (
  `id` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `validTo` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pro` */

LOCK TABLES `pro` WRITE;

UNLOCK TABLES;

/*Table structure for table `product` */

DROP TABLE IF EXISTS `product`;

CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orgId` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `artNumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prodLink` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entityStatus` int(3) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rankValue` int(11) DEFAULT NULL,
  `saleValue` int(11) DEFAULT NULL,
  `dateCreate` int(11) DEFAULT NULL,
  `dateUpdate` int(11) DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `picFile1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picFile2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picFile3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picSrv1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picSrv2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picSrv3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picVer1` int(3) NOT NULL DEFAULT '1',
  `picVer2` int(3) NOT NULL DEFAULT '1',
  `picVer3` int(3) NOT NULL DEFAULT '1',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `art_idx` (`artNumber`),
  KEY `org_idx` (`orgId`),
  KEY `lnk_idx` (`prodLink`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='основная сущность системы, то что организаторы выставляют дл';

/*Data for the table `product` */

LOCK TABLES `product` WRITE;

UNLOCK TABLES;

/*Table structure for table `publicEvent` */

DROP TABLE IF EXISTS `publicEvent`;

CREATE TABLE `publicEvent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fromUserId` int(11) DEFAULT NULL,
  `fromNickName` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `toUserId` int(11) DEFAULT NULL,
  `headId` int(11) DEFAULT NULL,
  `headName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meetId` int(11) DEFAULT NULL,
  `dateMeetAccept` int(11) DEFAULT NULL,
  `resellId` int(11) DEFAULT NULL,
  `dateResellUpdate` int(11) DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `dateCreate` int(11) DEFAULT NULL,
  `dateRead` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `touser_idx` (`toUserId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`),
  KEY `headId` (`headId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `publicEvent` */

LOCK TABLES `publicEvent` WRITE;

UNLOCK TABLES;

/*Table structure for table `purse` */

DROP TABLE IF EXISTS `purse`;

CREATE TABLE `purse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `merchantId` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `skey` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(3) NOT NULL DEFAULT '1',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchantId` (`merchantId`),
  KEY `userId` (`userId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `purse` */

LOCK TABLES `purse` WRITE;

UNLOCK TABLES;

/*Table structure for table `queueMysql` */

DROP TABLE IF EXISTS `queueMysql`;

CREATE TABLE `queueMysql` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taskName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `fromUserId` int(11) NOT NULL DEFAULT '0',
  `fromNickName` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `headId` int(11) DEFAULT '0',
  `headName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meetId` int(11) NOT NULL DEFAULT '0',
  `otherData` text COLLATE utf8_unicode_ci,
  `dateCreate` int(11) NOT NULL,
  `dateStart` int(11) DEFAULT NULL,
  `dateFinish` int(11) DEFAULT NULL,
  `isFinish` int(1) NOT NULL DEFAULT '0',
  `isError` tinyint(1) NOT NULL DEFAULT '0',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`dateCreate`),
  UNIQUE KEY `task_idx` (`taskName`,`fromUserId`,`headId`,`meetId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `queueMysql` */

LOCK TABLES `queueMysql` WRITE;

UNLOCK TABLES;

/*Table structure for table `role` */

DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permissions` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='роли операторов';

/*Data for the table `role` */

LOCK TABLES `role` WRITE;

insert  into `role`(`id`,`name`,`permissions`,`ownerSiteId`,`ownerOrgId`) values ('1','Admin','*','1','0');

UNLOCK TABLES;

/*Table structure for table `securityLog` */

DROP TABLE IF EXISTS `securityLog`;

CREATE TABLE `securityLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(20) NOT NULL DEFAULT '' COMMENT 'Раздел, подвергшийся атаке',
  `userId` int(11) DEFAULT NULL COMMENT 'Id юзера, выполнявшего запрещенные действия',
  `orgId` int(11) DEFAULT NULL,
  `operId` int(11) DEFAULT NULL COMMENT 'Id оператора, выполнявшего запрещенное действие',
  `actionType` varchar(255) DEFAULT NULL,
  `request` text COMMENT 'Текст HTTP запроса',
  `info` text COMMENT 'Дополнительная информация',
  `ip` varchar(20) NOT NULL DEFAULT '' COMMENT 'Ip адрес, выполнявшего опасный запрос',
  `longIp` int(11) DEFAULT NULL COMMENT 'Ip адрес, выполнявшего опасный запрос',
  `dateCreate` int(11) DEFAULT NULL COMMENT 'метка времени',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `section_idx` (`section`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='журнал потенциальных опасностей';

/*Data for the table `securityLog` */

LOCK TABLES `securityLog` WRITE;

UNLOCK TABLES;

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `type` varchar(20) NOT NULL DEFAULT 'TYPE_TEXT',
  `value` text,
  `info` text,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`),
  KEY `unique_idx` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8;

/*Data for the table `settings` */

LOCK TABLES `settings` WRITE;

insert  into `settings`(`id`,`name`,`description`,`type`,`value`,`info`,`ownerSiteId`,`ownerOrgId`) values ('1','master','Пароль, позволяющий войти под любым пользователем','TYPE_TEXT','60d19d94ce3e0638c7c34cf03e7c967e',NULL,'1','0'),('2','city','Название города размещения сайта или городов размещения.','TYPE_TEXT','- готовый движок для ведения совместных покупок',NULL,'1','0'),('3','vkgroupid','ID группы вконтакте для отображения виджета','TYPE_TEXT','',NULL,'1','0'),('4','mail_from','E-Mail исходящего письма ','TYPE_TEXT','no-reply@massapokupok.ru',NULL,'1','0'),('5','mail_fromName','Автор исходящих сообщений','TYPE_TEXT','MassaPokupok.ru',NULL,'1','0'),('6','mail_sign','Подпись в исходящих письмах','TYPE_TEXT','С уважением MassaPokupok.ru',NULL,'1','0'),('25','zakseeonlymembers','Закупки могут видель только зарегистрированные пользователи','TYPE_CHECKBOX','',NULL,'1','0'),('28','shownabraniyezakupki','Показывает набранные закупки непользователям','TYPE_CHECKBOX','on',NULL,'1','0'),('31','needphone','При регистрации требуется номер телефона','TYPE_CHECKBOX','',NULL,'1','0'),('43','useoffice','Участник может выбрать офис для получения заказов закупки','TYPE_CHECKBOX','on',NULL,'1','0'),('47','okgroupid','ID группы в Одноклассниках для отображения виджета','TYPE_TEXT','',NULL,'1','0'),('51','showusers','Показывать кол-во пользователей и тех кто online','TYPE_CHECKBOX','on',NULL,'1','0'),('101','orgcommision','% комиссии с организаторов сайту','TYPE_TEXT','1.5',NULL,'1','0');

UNLOCK TABLES;

/*Table structure for table `siteCommision` */

DROP TABLE IF EXISTS `siteCommision`;

CREATE TABLE `siteCommision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orgId` int(11) DEFAULT NULL,
  `headId` int(11) DEFAULT NULL,
  `orgPersent` decimal(5,4) NOT NULL DEFAULT '0.0000',
  `baseAmount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `needAmount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `payAmount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `dateCreate` int(11) DEFAULT NULL,
  `dateConfirm` int(11) DEFAULT NULL,
  `userInfo` text COLLATE utf8_unicode_ci,
  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `way` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`),
  KEY `orgId` (`orgId`),
  KEY `headId` (`headId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `siteCommision` */

LOCK TABLES `siteCommision` WRITE;

UNLOCK TABLES;

/*Table structure for table `social` */

DROP TABLE IF EXISTS `social`;

CREATE TABLE `social` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `network` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `identity` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `social` */

LOCK TABLES `social` WRITE;

UNLOCK TABLES;

/*Table structure for table `tocken` */

DROP TABLE IF EXISTS `tocken`;

CREATE TABLE `tocken` (
  `token` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sessHash` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `tsCreated` int(11) NOT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`token`),
  KEY `sessHash` (`sessHash`,`action`,`userId`,`tsCreated`,`ownerSiteId`,`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `tocken` */

LOCK TABLES `tocken` WRITE;

UNLOCK TABLES;

/*Table structure for table `urlList` */

DROP TABLE IF EXISTS `urlList`;

CREATE TABLE `urlList` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `optId` int(11) DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `main` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `canParse` tinyint(1) NOT NULL DEFAULT '0',
  `control` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parseRequest` tinyint(1) NOT NULL DEFAULT '0',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `opt_idx` (`optId`),
  KEY `url_idx` (`url`),
  KEY `main` (`main`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `urlList` */

LOCK TABLES `urlList` WRITE;

UNLOCK TABLES;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dateCreate` int(11) DEFAULT NULL,
  `dateLastVisit` int(11) NOT NULL DEFAULT '0',
  `login` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `confirmCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nickName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `secondName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone1` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone2` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `urlId` int(11) DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isOrg` int(3) DEFAULT NULL,
  `orgPersent` decimal(5,4) NOT NULL DEFAULT '0.0000',
  `isOpt` int(3) DEFAULT NULL,
  `isBot` int(3) DEFAULT NULL,
  `requestOrg` int(3) DEFAULT NULL,
  `requestOpt` int(3) DEFAULT NULL,
  `entityStatus` int(3) DEFAULT NULL,
  `status` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rating` int(3) DEFAULT NULL,
  `pozitiveFeeds` int(11) DEFAULT NULL,
  `negativeFeeds` int(11) DEFAULT NULL,
  `isAproved` int(1) NOT NULL DEFAULT '0',
  `digestFrequency` int(5) NOT NULL DEFAULT '7',
  `dateDigest` int(11) NOT NULL DEFAULT '0',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `login_idx` (`login`),
  KEY `code_idx` (`confirmCode`),
  KEY `nick_idx` (`nickName`),
  KEY `viz_idx` (`dateLastVisit`),
  KEY `dig_idx` (`dateDigest`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=2359 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='пользователь - конечный покупатель товара';

/*Data for the table `user` */

LOCK TABLES `user` WRITE;

insert  into `user`(`id`,`dateCreate`,`dateLastVisit`,`login`,`password`,`confirmCode`,`nickName`,`firstName`,`lastName`,`secondName`,`phone1`,`phone2`,`urlId`,`name`,`orgId`,`avatar`,`isOrg`,`orgPersent`,`isOpt`,`isBot`,`requestOrg`,`requestOpt`,`entityStatus`,`status`,`rating`,`pozitiveFeeds`,`negativeFeeds`,`isAproved`,`digestFrequency`,`dateDigest`,`ownerSiteId`,`ownerOrgId`) values ('1','1412783434','1480746339','info@massapokupok.ru','2e33442790151d51e06c7e0a0beeb600','d62010ecb3470509fcf8d42d8f08928e','admin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1.0000',NULL,NULL,'0',NULL,'1',NULL,NULL,NULL,NULL,'1','7','1480586401','1','0');

UNLOCK TABLES;

/*Table structure for table `userPermissions` */

DROP TABLE IF EXISTS `userPermissions`;

CREATE TABLE `userPermissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `additionalData` text COLLATE utf8_unicode_ci NOT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `site_idx` (`ownerSiteId`),
  KEY `org_idx` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `userPermissions` */

LOCK TABLES `userPermissions` WRITE;

UNLOCK TABLES;

/*Table structure for table `viewArea` */

DROP TABLE IF EXISTS `viewArea`;

CREATE TABLE `viewArea` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vkClubId` int(11) DEFAULT NULL,
  `vkCityId` int(11) DEFAULT NULL,
  `vkCountryId` int(11) DEFAULT NULL,
  `zakCount` int(11) DEFAULT NULL,
  `siteId` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `viewArea` */

LOCK TABLES `viewArea` WRITE;

UNLOCK TABLES;

/*Table structure for table `vikupSubscribers` */

DROP TABLE IF EXISTS `vikupSubscribers`;

CREATE TABLE `vikupSubscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `vikupId` int(11) DEFAULT NULL,
  `dateUpdate` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`,`vikupId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `vikupSubscribers` */

LOCK TABLES `vikupSubscribers` WRITE;

UNLOCK TABLES;

/*Table structure for table `yakassabutton` */

DROP TABLE IF EXISTS `yakassabutton`;

CREATE TABLE `yakassabutton` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `buttonCode` text COLLATE utf8_unicode_ci,
  `status` int(3) NOT NULL DEFAULT '1',
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `yakassabutton` */

LOCK TABLES `yakassabutton` WRITE;

UNLOCK TABLES;

/*Table structure for table `zParseWork` */

DROP TABLE IF EXISTS `zParseWork`;

CREATE TABLE `zParseWork` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `control` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mode` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iteration` int(11) DEFAULT NULL,
  `prodLink` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `catLink` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `params` mediumtext COLLATE utf8_unicode_ci,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sess` (`control`),
  KEY `cntl_idx` (`control`),
  KEY `prodlnk_idx` (`prodLink`),
  KEY `catLink` (`catLink`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `zParseWork` */

LOCK TABLES `zParseWork` WRITE;

UNLOCK TABLES;

/*Table structure for table `zakupkaHeader` */

DROP TABLE IF EXISTS `zakupkaHeader`;

CREATE TABLE `zakupkaHeader` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vikupId` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `categoryId1` int(11) DEFAULT NULL,
  `categoryId2` int(11) DEFAULT NULL,
  `categoryId3` int(11) DEFAULT NULL,
  `entityStatus` int(3) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateCreate` int(11) DEFAULT NULL,
  `dateUpdate` int(11) DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `optId` int(11) DEFAULT NULL,
  `orgRate` int(3) DEFAULT NULL,
  `minAmount` int(11) DEFAULT NULL,
  `minValue` int(11) DEFAULT NULL,
  `curAmount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `opttoorgDlvrAmount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `curValue` int(11) NOT NULL DEFAULT '0',
  `dateAmount` int(11) DEFAULT NULL,
  `dateValue` int(11) DEFAULT NULL,
  `narate` decimal(11,2) DEFAULT NULL COMMENT 'РЎСѓРјРјР° РЅР°Р±РѕСЂР° (Р°РіСЂРµРіРіРёСЂРѕРІР°РЅРЅС‹Рµ РґР°РЅРЅС‹Рµ)',
  `voteCount` int(11) DEFAULT NULL,
  `orderCount` int(11) NOT NULL DEFAULT '0',
  `startDate` int(11) DEFAULT NULL,
  `validDate` int(11) DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `specialNotes` longtext COLLATE utf8_unicode_ci,
  `useForm` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usePay` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pageUrl` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picFile1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picSrv1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picVer1` int(3) NOT NULL DEFAULT '1',
  `docFile1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `docFile2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `docFile3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `docSrv1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `docSrv2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `docSrv3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `categoryId` (`categoryId1`),
  KEY `orgId` (`orgId`),
  KEY `vikupId` (`vikupId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='голова закупки (особенно когда в ней нескольдко товаров)';

/*Data for the table `zakupkaHeader` */

LOCK TABLES `zakupkaHeader` WRITE;

UNLOCK TABLES;

/*Table structure for table `zakupkaLine` */

DROP TABLE IF EXISTS `zakupkaLine`;

CREATE TABLE `zakupkaLine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headId` int(11) DEFAULT NULL,
  `productId` int(11) DEFAULT NULL,
  `prodLink` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `wholePrice` decimal(11,2) DEFAULT NULL,
  `oldWholePrice` decimal(11,2) NOT NULL DEFAULT '0.00',
  `finalPrice` decimal(11,2) DEFAULT NULL,
  `dateCreate` int(11) DEFAULT NULL,
  `dateUpdate` int(11) DEFAULT NULL,
  `minValue` int(11) DEFAULT NULL,
  `isGrow` int(1) NOT NULL DEFAULT '1',
  `shouldClose` int(1) NOT NULL DEFAULT '0',
  `minName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rowNumbers` int(11) NOT NULL DEFAULT '1',
  `orderedValue` int(11) DEFAULT NULL,
  `sizes` text COLLATE utf8_unicode_ci,
  `sizesChoosen` text COLLATE utf8_unicode_ci,
  `sizesComplete` int(1) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `orgId` (`orgId`),
  KEY `head_idx` (`headId`),
  KEY `lnk_idx` (`prodLink`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='строка закупки (!)';

/*Data for the table `zakupkaLine` */

LOCK TABLES `zakupkaLine` WRITE;

UNLOCK TABLES;

/*Table structure for table `zakupkaStatusLog` */

DROP TABLE IF EXISTS `zakupkaStatusLog`;

CREATE TABLE `zakupkaStatusLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headId` int(11) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateCreate` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `head_idx` (`headId`),
  KEY `dt_idx` (`dateCreate`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `zakupkaStatusLog` */

LOCK TABLES `zakupkaStatusLog` WRITE;

UNLOCK TABLES;

/*Table structure for table `zakupkaVikup` */

DROP TABLE IF EXISTS `zakupkaVikup`;

CREATE TABLE `zakupkaVikup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `countZheads` int(11) NOT NULL DEFAULT '1',
  `dateCreate` int(11) DEFAULT NULL,
  `dateSentremind` int(11) NOT NULL DEFAULT '0',
  `calendarData` mediumtext COLLATE utf8_unicode_ci,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `orgId` (`orgId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `zakupkaVikup` */

LOCK TABLES `zakupkaVikup` WRITE;

UNLOCK TABLES;

/*Table structure for table `zakupkaVote` */

DROP TABLE IF EXISTS `zakupkaVote`;

CREATE TABLE `zakupkaVote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headId` int(11) DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) DEFAULT NULL,
  `ownerOrgId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`),
  KEY `headId` (`headId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `zakupkaVote` */

LOCK TABLES `zakupkaVote` WRITE;

UNLOCK TABLES;

/*Table structure for table `zhComment` */

DROP TABLE IF EXISTS `zhComment`;

CREATE TABLE `zhComment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headId` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `sourceId` int(11) DEFAULT NULL,
  `rootId` int(11) DEFAULT NULL,
  `weight` bigint(19) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `toId` int(11) DEFAULT NULL,
  `userType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `toType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nickName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wasRead` int(1) DEFAULT NULL,
  `isPrivate` int(1) DEFAULT NULL,
  `isAnon` int(1) DEFAULT NULL,
  `dateCreate` int(11) DEFAULT NULL,
  `ownerSiteId` int(11) NOT NULL DEFAULT '0',
  `ownerOrgId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ownerSiteId` (`ownerSiteId`),
  KEY `ownerOrgId` (`ownerOrgId`),
  KEY `headId` (`headId`),
  KEY `sourceId` (`sourceId`),
  KEY `rootId` (`rootId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `zhComment` */

LOCK TABLES `zhComment` WRITE;

UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
