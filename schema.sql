create table receive_record
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`date_receive` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`customer` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`email` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`description` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`quantity` int(11) DEFAULT 0,
	`supplier` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`picname` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`kilo` float DEFAULT 0,
	`cuft` float DEFAULT 0,
	`taiwan_pay` int(11) DEFAULT 0,
	`courier_pay` int(11) DEFAULT 0,
	`courier_money` int(11) DEFAULT 0,
	`remark` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`batch_num` int(11) DEFAULT 0,
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE receive_record
ADD COLUMN `mdf_time` timestamp NULL AFTER crt_user;

ALTER TABLE receive_record
ADD COLUMN `mdf_user` varchar(128) DEFAULT '' AFTER mdf_time;

ALTER TABLE receive_record
ADD COLUMN `del_time` timestamp NULL AFTER mdf_user;

ALTER TABLE receive_record
ADD COLUMN `del_user` varchar(128) DEFAULT '' AFTER del_time;

alter table receive_record change quantity quantity varchar(128);

create table receive_picture
(
	`record_id` bigint(20) UNSIGNED NOT NULL,
	`serial` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`filename` varchar(256)  DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`serial`),
	INDEX `receive_picture_record_id` (`record_id`)
);

create table contactor
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`shipping_mark` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`customer` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`c_phone` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`c_fax` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`c_email` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`supplier` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`s_phone` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`s_fax` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`s_email` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



ALTER TABLE contactor
ADD COLUMN `status` varchar(2) DEFAULT '' AFTER s_email;

ALTER TABLE contactor
ADD COLUMN `mdf_time` timestamp NULL AFTER crt_user;

ALTER TABLE contactor
ADD COLUMN `mdf_user` varchar(128) DEFAULT '' AFTER mdf_time;

ALTER TABLE contactor
ADD COLUMN `del_time` timestamp NULL AFTER mdf_user;

ALTER TABLE contactor
ADD COLUMN `del_user` varchar(128) DEFAULT '' AFTER del_time;


ALTER TABLE contactor
ADD COLUMN `company_title` varchar(128) DEFAULT '' AFTER status;

ALTER TABLE contactor
ADD COLUMN `vat_number` varchar(40) DEFAULT '' AFTER company_title;

ALTER TABLE contactor
ADD COLUMN `address` varchar(256) DEFAULT '' AFTER vat_number;

insert into contactor(shipping_mark, customer, c_phone, c_fax, c_email, supplier, s_phone, s_fax, s_email) values('86551342', '中連貨運1', '04-763221', '', 'shippingchunlain＠gmail.com', '世星1', '04-25237596', '', 'shinshin＠gmail.com');
insert into contactor(shipping_mark, customer, c_phone, c_fax, c_email, supplier, s_phone, s_fax, s_email) values('86551343', '中連貨運2', '04-763221', '', 'shippingchunlain＠gmail.com', '世星2', '04-25237596', '', 'shinshin＠gmail.com');
insert into contactor(shipping_mark, customer, c_phone, c_fax, c_email, supplier, s_phone, s_fax, s_email) values('86551344', '中連貨運3', '04-763221', '', 'shippingchunlain＠gmail.com', '世星3', '04-25237596', '', 'shinshin＠gmail.com');
insert into contactor(shipping_mark, customer, c_phone, c_fax, c_email, supplier, s_phone, s_fax, s_email) values('86551345', '中連貨運4', '04-763221', '', 'shippingchunlain＠gmail.com', '世星4', '04-25237596', '', 'shinshin＠gmail.com');
insert into contactor(shipping_mark, customer, c_phone, c_fax, c_email, supplier, s_phone, s_fax, s_email) values('86551346', '中連貨運5', '04-763221', '', 'shippingchunlain＠gmail.com', '世星5', '04-25237596', '', 'shinshin＠gmail.com');

create table loading
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`shipping_mark` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`estimate_weight` float DEFAULT 0,
	`actual_weight` float DEFAULT 0,
	`container_number` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`seal` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`so` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`ship_company` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`ship_boat` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`neck_cabinet` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`date_sent` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`etd_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`ob_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`eta_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`broker` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`remark` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



create table loading_date_history
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`loading_id` bigint(20) unsigned NOT NULL,
	`date_sent` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`etd_date` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`ob_date` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`eta_date` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 傾印  表格 ludb.user 結構
CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE user ADD COLUMN status INT DEFAULT 0;
-- 取消選取資料匯出。

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

ALTER TABLE user
ADD COLUMN `is_admin` varchar(1) DEFAULT '' AFTER status;


-- user 登入歷史
CREATE TABLE IF NOT EXISTS `login_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';



ALTER TABLE loading
ADD COLUMN `measure_num` int(11) DEFAULT 0 AFTER status;


create table measure
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`date_encode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`date_arrive` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`currency_rate` float DEFAULT 0,
	`remark` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


create table measure_history
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`measure_id` bigint(20) unsigned NOT NULL,
	`customer` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`kilo` float DEFAULT 0,
	`cuft` float DEFAULT 0,
	`price_kilo` float DEFAULT 0,
	`price_cuft` float DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


alter table contactor change c_phone c_phone varchar(82);
alter table contactor change c_fax c_fax varchar(82);
alter table contactor change s_phone s_phone varchar(82);
alter table contactor change s_fax s_fax varchar(82);


create table contact_us
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`gender` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`customer` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`emailinfo` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`telinfo` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE contact_us
ADD COLUMN `crt_time` timestamp NULL AFTER telinfo;

ALTER TABLE contact_us
ADD COLUMN `status` varchar(2) DEFAULT '' AFTER telinfo;

ALTER TABLE loading
ADD COLUMN `date_arrive` varchar(10) DEFAULT '' AFTER eta_date;


alter table receive_record change customer customer varchar(256);

alter table receive_record change supplier supplier varchar(256);

-- 修改欄位 2021/6/21
ALTER TABLE loading
ADD COLUMN `shipper` int(11) DEFAULT 0 AFTER neck_cabinet;

-- expense record 06/30
CREATE TABLE IF NOT EXISTS `price_record` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account` int default 0,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `sub_category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `project_name` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `related_account` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `details` varchar(4096) COLLATE utf8mb4_unicode_ci  default '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `gcp_url` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `payee` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `paid_date` Date NULL DEFAULT NULL,
  `cash_in` decimal(10, 2) default 0.0,
  `cash_out` decimal(10, 2) default 0.0,
  `remarks` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `is_locked` bool default false,
  `is_enabled` bool default false,
  `is_marked` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- 20210712
ALTER TABLE user ADD COLUMN status_1 INT DEFAULT 0;

-- expense record for sea 07/30
CREATE TABLE IF NOT EXISTS `price_record_sea` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account` int default 0,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `sub_category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `project_name` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `related_account` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `details` varchar(4096) COLLATE utf8mb4_unicode_ci  default '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `gcp_url` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `payee` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `paid_date` Date NULL DEFAULT NULL,
  `cash_in` decimal(10, 2) default 0.0,
  `cash_out` decimal(10, 2) default 0.0,
  `staff_name` varchar(256) COLLATE utf8mb4_unicode_ci  default '',
  `company_name` varchar(256) COLLATE utf8mb4_unicode_ci  default '',
  `remarks` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `is_locked` bool default false,
  `is_enabled` bool default false,
  `is_marked` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- 20210806
ALTER TABLE user ADD COLUMN sea_expense INT DEFAULT 0;


-- expense record for sea v2 07/30
CREATE TABLE IF NOT EXISTS `price_record_sea_v2` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account` int default 0,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `sub_category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `project_name` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `related_account` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `details` varchar(4096) COLLATE utf8mb4_unicode_ci  default '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `gcp_url` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `payee` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `paid_date` Date NULL DEFAULT NULL,
  `cash_in` decimal(10, 2) default 0.0,
  `cash_out` decimal(10, 2) default 0.0,
  `staff_name` varchar(256) COLLATE utf8mb4_unicode_ci  default '',
  `company_name` varchar(256) COLLATE utf8mb4_unicode_ci  default '',
  `remarks` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `is_locked` bool default false,
  `is_enabled` bool default false,
  `is_marked` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 20210806
ALTER TABLE user ADD COLUMN sea_expense_v2 INT DEFAULT 0;

-- expense record v2 08/25
CREATE TABLE IF NOT EXISTS `price_record_v2` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account` int default 0,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `sub_category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `project_name` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `related_account` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `details` varchar(4096) COLLATE utf8mb4_unicode_ci  default '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `gcp_url` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `payee` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `paid_date` Date NULL DEFAULT NULL,
  `cash_in` decimal(10, 2) default 0.0,
  `cash_out` decimal(10, 2) default 0.0,
  `remarks` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `is_locked` bool default false,
  `is_enabled` bool default false,
  `is_marked` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE user ADD COLUMN status_2 INT DEFAULT 0;



-- 20210901 for sea take photo
CREATE TABLE IF NOT EXISTS `gcp_storage_file` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `batch_id` bigint(20)  DEFAULT 0 NOT NULL,
  `batch_type` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `bucketname` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT 'feliiximg',
  `filename` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `gcp_name` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `gcp_msg` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT 0,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


create table receive_library
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`date_receive` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`customer` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`email` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`description` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`quantity` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`supplier` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`picname` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`kilo` float DEFAULT 0,
	`cuft` float DEFAULT 0,
	`taiwan_pay` int(11) DEFAULT 0,
	`courier_pay` int(11) DEFAULT 0,
	`courier_money` int(11) DEFAULT 0,
	`remark` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`batch_num` int(11) DEFAULT 0,
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE receive_record
ADD COLUMN `photo` varchar(12) DEFAULT '' AFTER picname;

ALTER TABLE receive_library
ADD COLUMN `photo` varchar(12) DEFAULT '' AFTER picname;

ALTER TABLE gcp_storage_file
ADD COLUMN `batch_id_org` bigint(20)  DEFAULT 0  AFTER gcp_msg;


-- 2021/09/07 staff list
create table staff_list
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`staff` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`phone` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`email` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`address` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
  `mdf_time` timestamp NULL,
	`mdf_user` varchar(128) DEFAULT '',
  `del_time` timestamp NULL,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

create table staff_list_sea
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`staff` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`phone` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`email` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`address` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
  `mdf_time` timestamp NULL,
	`mdf_user` varchar(128) DEFAULT '',
  `del_time` timestamp NULL,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- salary record  09/07 
CREATE TABLE IF NOT EXISTS `price_record_salary` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account` int default 0,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `sub_category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `project_name` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `related_account` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `details` varchar(4096) COLLATE utf8mb4_unicode_ci  default '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `gcp_url` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `payee` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `staff_name` varchar(256) COLLATE utf8mb4_unicode_ci  default '',
  `paid_date` Date NULL DEFAULT NULL,
  `cash_in` decimal(10, 2) default 0.0,
  `cash_out` decimal(10, 2) default 0.0,
  `remarks` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `company_name` varchar(256) COLLATE utf8mb4_unicode_ci  default '',
  `is_locked` bool default false,
  `is_enabled` bool default false,
  `is_marked` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `price_record_salary_sea` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account` int default 0,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `sub_category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `project_name` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `related_account` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `details` varchar(4096) COLLATE utf8mb4_unicode_ci  default '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `gcp_url` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `payee` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `staff_name` varchar(256) COLLATE utf8mb4_unicode_ci  default '',
  `paid_date` Date NULL DEFAULT NULL,
  `cash_in` decimal(10, 2) default 0.0,
  `cash_out` decimal(10, 2) default 0.0,
  `remarks` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `company_name` varchar(256) COLLATE utf8mb4_unicode_ci  default '',
  `is_locked` bool default false,
  `is_enabled` bool default false,
  `is_marked` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 2021/09/17
ALTER TABLE receive_record
ADD COLUMN `email_customer` varchar(256) DEFAULT '' AFTER customer;

-- 2021/10/04
ALTER TABLE receive_record
ADD COLUMN `mail_cnt` int default 0 AFTER batch_num;

ALTER TABLE receive_record
ADD COLUMN `mail_note` varchar(512) DEFAULT '' AFTER mail_cnt;

-- 2021/10/28 - create measurement
create table measure_ph
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`date_encode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`date_arrive` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`currency_rate` float DEFAULT 0,
	`remark` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


create table measure_detail
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `measure_id` bigint(20) unsigned NOT NULL,
  `customer` varchar(128)  DEFAULT '',
	`kilo` float,
	`cuft` float,
	`kilo_price` float,
	`cuft_price` float,
  `charge` float,
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


create table measure_record_detail
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `detail_id` bigint(20) unsigned NOT NULL,
	`record_id` bigint(20) unsigned NOT NULL,
  `cust` bigint(20)  DEFAULT 0,
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `contactor_ph` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `customer` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `address` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phone` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `remark` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  
	`status` varchar(2) DEFAULT '',
 
  `crt_time` timestamp NULL DEFAULT current_timestamp(),
  `crt_user` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `mdf_time` timestamp NULL DEFAULT NULL,
  `mdf_user` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `del_time` timestamp NULL DEFAULT NULL,
  `del_user` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2021/11/08 pickup
ALTER TABLE measure_detail
ADD COLUMN `encode` varchar(128) DEFAULT '' AFTER charge;

ALTER TABLE measure_detail
ADD COLUMN `encode_status` varchar(1) DEFAULT '' AFTER charge;

ALTER TABLE measure_detail
ADD COLUMN `pickup_status` varchar(1) DEFAULT '' AFTER charge;

ALTER TABLE measure_detail
ADD COLUMN `payment_status` varchar(1) DEFAULT '' AFTER charge;

ALTER TABLE measure_ph
ADD COLUMN `pick_id` bigint(20) DEFAULT 0 AFTER remark;


create table pick
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`remark` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


create table pick_group
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`group_id` bigint(20) unsigned NOT NULL,
  `measure_id` bigint(20) unsigned NOT NULL,
  `measure_detail_id` bigint(20) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE receive_record
ADD COLUMN `pick_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE receive_record
ADD COLUMN `pick_person` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE receive_record
ADD COLUMN `pick_note` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE receive_record
ADD COLUMN `pick_time` timestamp NULL;

ALTER TABLE receive_record
ADD COLUMN `pick_user` varchar(128) DEFAULT '';

create table payment
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `detail_id` bigint(20) unsigned NOT NULL,
  `type` int(11) DEFAULT 0,
  `issue_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `payment_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `person` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `amount` decimal(10, 2) default 0.0,
	`remark` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20211112
ALTER TABLE user ADD COLUMN phili INT DEFAULT 0;

-- expense record
ALTER TABLE contactor_ph
ADD COLUMN `color` varchar(12) DEFAULT '' AFTER remark;



CREATE TABLE IF NOT EXISTS `contactor_ph_po` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `customer` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `address` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phone` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `remark` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `color` varchar(12) DEFAULT '',
  `acquisition`  varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `acquisition_by` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `date_to_call` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
 
  `crt_time` timestamp NULL DEFAULT current_timestamp(),
  `crt_user` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `mdf_time` timestamp NULL DEFAULT NULL,
  `mdf_user` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `del_time` timestamp NULL DEFAULT NULL,
  `del_user` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20211119
ALTER TABLE contactor_ph
ADD COLUMN `tags` varchar(256) DEFAULT 'Main' AFTER remark;

-- 20211229
ALTER TABLE user ADD COLUMN phili_read INT DEFAULT 0;

ALTER TABLE user ADD COLUMN taiwan_read INT DEFAULT 0;

-- 20220113 add date arrived history
ALTER TABLE loading_date_history ADD COLUMN `date_arrive` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 20220118 add pick date & payment time
ALTER TABLE receive_record
ADD COLUMN `real_pick_time` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE receive_record
ADD COLUMN `real_payment_time` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 20220119 charge column typo
alter table measure_detail change charge charge DECIMAL(10, 2);

-- 20220121 pickup payment change and courier
ALTER TABLE payment
ADD COLUMN `change` decimal(10, 2) default 0.0 AFTER amount;

ALTER TABLE payment
ADD COLUMN `courier` decimal(10, 2) default 0.0 AFTER `change`;


-- 20220217 add pick group creator and date change
ALTER TABLE pick_group
ADD COLUMN `crt_user` varchar(128) DEFAULT '' AFTER measure_detail_id;

ALTER TABLE pick_group
ADD COLUMN `crt_time` timestamp NULL AFTER crt_user;

ALTER TABLE pick_group
ADD COLUMN `mdf_user` varchar(128) DEFAULT '' AFTER crt_time;

ALTER TABLE pick_group
ADD COLUMN `mdf_time` timestamp NULL AFTER mdf_user;

ALTER TABLE pick_group
ADD COLUMN `del_user` varchar(128) DEFAULT '' AFTER mdf_time;

ALTER TABLE pick_group
ADD COLUMN `del_time` timestamp NULL AFTER del_user;

-- 20211229
ALTER TABLE pick_group ADD COLUMN `status` INT DEFAULT 0 AFTER measure_detail_id;

create table taiwan_pay_record
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `record_id` bigint(20) UNSIGNED NOT NULL,
	`ar_php` DECIMAL(10, 2) NULL,
  `ar` DECIMAL(10, 2) NULL,
  `amount` DECIMAL(10, 2) NULL,
  `payment_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `note` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
  `mdf_time` timestamp NULL,
	`mdf_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 20220307 details_ntd_php
CREATE TABLE details_ntd_php (
  `id` INT NOT NULL AUTO_INCREMENT, 
  `client_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `payee_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `amount` decimal(10,2) null,
  `amount_php` decimal(10,2) null,
  `rate` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `rate_yahoo` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `total_receive` decimal(10,2) null,
  `overpayment` decimal(10,2) null,
  `pay_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `payee` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `remark` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
  `mdf_time` timestamp NULL,
	`mdf_user` varchar(128) DEFAULT '',
  `del_time` timestamp NULL DEFAULT NULL,
  `del_user` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY(id)
);

create table details_ntd_php_record
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sales_id` bigint(20) unsigned NOT NULL,
  `receive_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `payment_method` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `account_number` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `check_details` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`receive_amount` decimal(10,2) DEFAULT 0.0,
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE user ADD COLUMN report1 INT DEFAULT 0;

-- 20220317
ALTER TABLE taiwan_pay_record ADD COLUMN `rate` varchar(128) DEFAULT '' AFTER ar;
-- 20220325
ALTER TABLE user ADD COLUMN report2 INT DEFAULT 0;

-- 20220328
ALTER TABLE contact_us
ADD COLUMN `source` varchar(2) DEFAULT '' AFTER telinfo;

-- 20220426
ALTER TABLE loading
ADD COLUMN `picname` varchar(512) DEFAULT '' AFTER remark;
ALTER TABLE loading
ADD COLUMN `photo` varchar(12) DEFAULT '' AFTER remark;

create table pickup_payment_export
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `measure_detail_id` bigint(20) unsigned NOT NULL,
  `exp_dr` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `exp_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `exp_sold_to` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `exp_quantity` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `exp_unit` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `exp_discription` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `exp_amount` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`payment` JSON,
	`record` JSON,
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20220620 airship_records
CREATE TABLE airship_records (
  `id` INT NOT NULL AUTO_INCREMENT, 
  `date_receive` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `customer` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `address` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `description` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `quantity` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `kilo` decimal(10,2) null,
  `supplier` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `flight` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `flight_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `total` decimal(10,2) null,
  `total_php` decimal(10,2) null,
  `pay_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pay_status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `payee` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `date_arrive` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `receiver` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `remark` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `amount` decimal(10,2) null,
  `amount_php` decimal(10,2) null,
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
  `mdf_time` timestamp NULL,
	`mdf_user` varchar(128) DEFAULT '',
  `del_time` timestamp NULL DEFAULT NULL,
  `del_user` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY(id)
);

create table airship_records_detail
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `airship_id` bigint(20) unsigned NOT NULL,
  `title` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `qty` decimal(10,2) null,
  `price` decimal(10,2) null,
  `type` varchar(128) DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20220628 dr too short
alter table pickup_payment_export change exp_dr exp_dr varchar(15);

-- 20220628 sign data pick
ALTER TABLE pickup_payment_export
ADD COLUMN `assist_by` varchar(64) DEFAULT '' AFTER measure_detail_id;
ALTER TABLE pickup_payment_export
ADD COLUMN `file_export` varchar(512) DEFAULT '' AFTER measure_detail_id;
ALTER TABLE pickup_payment_export
ADD COLUMN `upd_user` varchar(128) DEFAULT '' AFTER del_user;
ALTER TABLE pickup_payment_export
ADD COLUMN `upd_time` timestamp NULL AFTER del_user;

-- 20220629 airship privileage
ALTER TABLE user
ADD COLUMN `airship` INT DEFAULT 0;
ALTER TABLE user
ADD COLUMN `airship_read` INT DEFAULT 0;

-- 20220704 jyf on_duty
CREATE TABLE `on_duty` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `duty_date` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duty_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `remark` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `duty_time` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `explain` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pos_lat` decimal(24,12) DEFAULT 0.000000000000,
  `pos_lng` decimal(24,12) DEFAULT 0.000000000000,
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pic_time` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pic_lat` decimal(24,12) DEFAULT 0.000000000000,
  `pic_lng` decimal(24,12) DEFAULT 0.000000000000,
  `pic_server_time` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pic_server_lat` decimal(24,12) DEFAULT 0.000000000000,
  `pic_server_lng` decimal(24,12) DEFAULT 0.000000000000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
