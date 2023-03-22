ALTER TABLE employee ADD COLUMN gender char(1) NOT NULL DEFAULT '' AFTER dob;

//change facilities table => facility_id (char) => (int)

//ALTER TABLE facilities ALTER COLUMN facility_id type int using facility_id::int



ALTER TABLE attach_files ADD COLUMN original_name char(100) NOT NULL DEFAULT '';

CREATE TABLE `facility_options` (
  `facility_id` int(11) NOT NULL AUTO_INCREMENT,
  `facility` char(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`facility_id`)
)

CREATE TABLE `leave_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_id` int(11) DEFAULT NULL,
  `file_name` char(20) DEFAULT NULL,
  `original_file_name` char(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `roster_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_code` char(2) NOT NULL,
  `slot_no` char(1) NOT NULL,
  `from` time NOT NULL,
  `to` time NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `roster_slot` (`id`, `dept_code`, `slot_no`, `from`, `to`) VALUES
(1, 'SY', '1', '08:00:00', '17:00:00'),
(2, 'SY', '2', '09:00:00', '18:00:00'),
(3, 'SY', '3', '13:15:00', '22:15:00'),
(4, 'SY', '4', '22:00:00', '08:15:00'),
(5, 'CA', '1', '07:00:00', '15:00:00'),
(6, 'CA', '2', '15:00:00', '23:00:00'),
(7, 'CA', '3', '23:00:00', '07:00:00');



CREATE TABLE `rostering_tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `stime` datetime DEFAULT NULL,
  `etime` datetime DEFAULT NULL,
  `is_incharge` char(1) DEFAULT 'N',
  `tstamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  PRIMARY KEY (`id`)
);

CREATE TABLE `weekend_tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `tstamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  PRIMARY KEY (`id`)
);



/* */
CREATE TABLE `rostering_control` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `tstamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 `dept_code` varchar(10) NOT NULL,
 `emp_ids` varchar(100) NOT NULL,
 `sdate` date NOT NULL DEFAULT '0000-00-00',
 `edate` date NOT NULL DEFAULT '0000-00-00',
 `reason` varchar(150) NOT NULL DEFAULT '',
 `sender_id` varchar(10) NOT NULL DEFAULT '',
 `admin_id` varchar(10) NOT NULL DEFAULT '',
 PRIMARY KEY (`id`)
);

CREATE TABLE `store_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` char(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`category_id`)
);


CREATE TABLE `store_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item` char(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `description` text,
  PRIMARY KEY (`item_id`)
);

CREATE TABLE `store_ledger` (
  `ledger_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `quantity` int(5) NOT NULL,
  `unit_price` float(13,2) NOT NULL,
  `total_price` float(13,2) NOT NULL,
  `voucher_id` int(11) NOT NULL,

  PRIMARY KEY (`ledger_id`)
);

CREATE TABLE `store_voucher` (
  `voucher_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `requested_by` varchar(10) NOT NULL DEFAULT '',
 `approved_by` varchar(10) NOT NULL DEFAULT '',
 `verified_by` varchar(10) NOT NULL DEFAULT '',

  PRIMARY KEY (`voucher_id`)
);

CREATE TABLE `store_req_ledger` (
  `ledger_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `quantity` int(5) NOT NULL,
  `remark` text,
  `voucher_id` int(11) NOT NULL,

  PRIMARY KEY (`ledger_id`)
);

CREATE TABLE `store_req_voucher` (
  `voucher_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `requested_by` varchar(10) NOT NULL DEFAULT '',
  `approved_by` varchar(10) NOT NULL DEFAULT '',
  `verified_by` varchar(10) NOT NULL DEFAULT '',

  PRIMARY KEY (`voucher_id`)
);

ALTER TABLE `employee` ADD `key` VARCHAR(32) NOT NULL ;
ALTER TABLE `employee` ADD `key_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';





/*  Alter after date: 29-11-15  */

ALTER TABLE `attach_msg` ADD `custom_recipient` text DEFAULT NULL;


CREATE TABLE `missing_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `in` time DEFAULT NULL,
  `out` time DEFAULT NULL,
  `status` char(1) DEFAULT NULL COMMENT 'A=waitin:m.apprv, B=waitin:a.apprv, C=a.approved, D=a.refused, E=m.refused',
  `m_approve_date` date DEFAULT NULL,
  `a_approve_date` date DEFAULT NULL,
  `manager_id` varchar(10) DEFAULT NULL,
  `admin_id` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `missing_attendance` ADD `reason` text NOT NULL DEFAULT '' AFTER `out`;

/* new one */
CREATE TABLE `missing_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) NOT NULL,
  `date` date DEFAULT NULL,
  `in` time DEFAULT NULL,
  `out` time DEFAULT NULL,
  `reason` text NOT NULL,
  `status` char(1) NOT NULL COMMENT 'A=waitin:m.apprv, B=waitin:a.apprv, C=a.approved, D=a.refused, E=m.refused',
  `m_approved_date` date DEFAULT NULL,
  `a_verified_date` date DEFAULT NULL,
  `manager_id` varchar(10) DEFAULT NULL,
  `admin_id` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
);


CREATE TABLE `missing_attendance_mail_sent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) NOT NULL,
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
);


ALTER TABLE `leaves` ADD `manager_remark` TINYTEXT DEFAULT '' AFTER `m_approved_date`;
ALTER TABLE `leaves` ADD `admin_remark` TINYTEXT DEFAULT '' AFTER `admin_approve_date`;


/* 28-12-15 */

CREATE TABLE `activity_priv` (
	`activity_id` int NOT NULL AUTO_INCREMENT,
	`activity_code` CHAR(100) NOT NULL,
	`activity_name` CHAR(100) NOT NULL,
	`group_id` int NOT NULL,	
	PRIMARY KEY(`activity_id`)
);

CREATE TABLE `activity_stack` (
	`stack_id` int NOT NULL AUTO_INCREMENT,
	`activity_id` int NOT NULL,
	`emp_id` CHAR(10) NOT NULL,
	PRIMARY KEY(`stack_id`)
);


CREATE TABLE `activity_group` (
	`group_id` int NOT NULL AUTO_INCREMENT,
	`group_name` CHAR(100) NOT NULL,
	PRIMARY KEY(`group_id`)
);
/* 28-12-15 */
