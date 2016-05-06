
create database if not exists mmfq_admin_yimei DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

use mmfq_admin_yimei;

create table if not exists `users` (
  `id` int primary key auto_increment,
  `user_name` varchar(30) unique not null,
  `password` varchar(32) not null,
  `real_name` varchar(30) not null,
  `role` enum('admin','employee')
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `customers`(
  `id` int primary key auto_increment,
  `user_id` int not null,
  `user_real_name` varchar(30) not null,
  `name` varchar(30) not null,
  `sign_date` timestamp not null,
  `telephone` varchar(20) not null,
  `school` varchar(255) not null,
  `age` int not null,
  `star` tinyint(1) not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `projects`(
  `id` int primary key auto_increment,
  `customer_id` int not null,
  `description` varchar(255),
  `detail` text,
  `advance_payment` float,
  `by_stages` int,
  `repayment_date` timestamp,
  `per_payment` float,
  `hospital_location` varchar(30),
  `hospital_name` varchar(255),
  `counselor` varchar(30),
  `project_kind` varchar(255) not null,
  `stat` enum('complete', 'cancel', 'want') not null,
  `url` varchar(255),
  `create_date` timestamp,
  `complete_date` timestamp
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `return_visit_records`(
  `id` int primary key auto_increment,
  `customer_id` int not null,
  `create_date` timestamp not null,
  `return_date` timestamp not null,
  `detail` text,
  `is_return` tinyint(1) not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `project_kinds`(
  `id` int primary key auto_increment,
  `label` varchar(255) not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
