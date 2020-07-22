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