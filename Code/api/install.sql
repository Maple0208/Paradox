-- MaAp1e&靶场 数据库结构
-- CREATE DATABASE Paradox DEFAULT CHARSET utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(64) NOT NULL,
  password VARCHAR(128) NOT NULL,
  role VARCHAR(20) DEFAULT 'user',
  name VARCHAR(64),
  email VARCHAR(128),
  phone VARCHAR(32),
  dept VARCHAR(64),
  status VARCHAR(20) DEFAULT 'active',
  secret VARCHAR(128) DEFAULT '',
  balance INT DEFAULT 0,
  reset_token VARCHAR(128) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS employees (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(64),
  dept VARCHAR(64),
  position VARCHAR(64),
  salary INT,
  idcard VARCHAR(32),
  entry VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS customers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(128),
  contact VARCHAR(64),
  phone VARCHAR(32),
  level VARCHAR(20),
  amount BIGINT,
  owner VARCHAR(64)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS comments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user VARCHAR(64),
  content TEXT,
  time VARCHAR(32)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS notices (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  content TEXT,
  author VARCHAR(64),
  time VARCHAR(32),
  top TINYINT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS messages (
  id INT PRIMARY KEY AUTO_INCREMENT,
  `to` VARCHAR(64),
  `from` VARCHAR(64),
  title VARCHAR(255),
  content TEXT,
  `read` TINYINT DEFAULT 0,
  time VARCHAR(32)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS files (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  size VARCHAR(32),
  owner VARCHAR(64),
  path VARCHAR(512),
  time VARCHAR(32),
  content LONGTEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS logs (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user VARCHAR(64),
  action VARCHAR(255),
  ip VARCHAR(64),
  time VARCHAR(32)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
  k VARCHAR(64) PRIMARY KEY,
  v TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS chat (
  id INT PRIMARY KEY AUTO_INCREMENT,
  who VARCHAR(64),
  uid INT,
  text TEXT,
  time VARCHAR(32)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (id,username,password,role,name,email,phone,dept,status,secret,balance) VALUES
(1,'admin','admin123','admin','系统管理员','admin@corp.com','13800000001','信息中心','active','sk_live_8f23ad91c0',99999),
(2,'zhangwei','123456','user','张伟','zhangwei@corp.com','13800000002','销售部','active','',5000),
(3,'liwu','liwu@2000','user','李武','liwu@corp.com','13800000003','财务部','active','',8000),
(4,'manager','Manager#88','manager','王经理','manager@corp.com','13800000004','运营部','active','',20000);

INSERT INTO employees (id,name,dept,position,salary,idcard,entry) VALUES
(1001,'张伟','销售部','销售主管',18000,'310101199001011234','2020-03-01'),
(1002,'李武','财务部','会计',15000,'310101199203054321','2021-06-15'),
(1003,'刘洋','技术部','工程师',22000,'310101198807129876','2019-09-10');

INSERT INTO customers (id,name,contact,phone,level,amount,owner) VALUES
(2001,'恒大集团','陈总','13911112222','VIP',1200000,'zhangwei'),
(2002,'万达广场','赵经理','13933334444','普通',450000,'liwu'),
(2003,'腾讯科技','孙主管','13955556666','VIP',3000000,'manager');

INSERT INTO comments (user,content,time) VALUES
('张伟','这个平台用起来很方便','2026-05-01 10:23'),
('李武','希望增加导出功能','2026-05-02 14:11');

INSERT INTO notices (title,content,author,time,top) VALUES
('关于系统升级的通知','平台将于本周六凌晨2点进行升级维护，预计2小时。','admin','2026-05-10 09:00',1),
('端午节放假安排','6月19日至21日放假，共三天。','admin','2026-05-12 11:30',0);

INSERT INTO messages (`to`,`from`,title,content,`read`,time) VALUES
('zhangwei','admin','欢迎使用 MaAp1e&靶场','您的账号已开通，请尽快修改初始密码。',0,'2026-05-01 08:00'),
('zhangwei','manager','本月销售目标','请关注本月销售指标完成情况。',0,'2026-05-08 16:20');

INSERT INTO files (name,size,owner,path,time,content) VALUES
('2026年度预算报告.xlsx','245KB','liwu','/uploads/finance/budget_2026.xlsx','2026-04-20 10:00','机密：2026年度预算总额 1.2亿元'),
('员工通讯录.csv','18KB','admin','/uploads/hr/contacts.csv','2026-04-22 15:30','姓名,电话,身份证\n张伟,13800000002,310101199001011234');

INSERT INTO logs (user,action,ip,time) VALUES
('admin','登录系统','192.168.1.10','2026-05-12 08:30'),
('zhangwei','查看客户列表','192.168.1.22','2026-05-12 09:15');

INSERT INTO settings (k,v) VALUES
('siteName','MaAp1e&靶场'),
('registerOpen','1'),
('debug','0'),
('apiKey','AKID-PROD-3f9a2b7c8e'),
('smtp','smtp.corp.com'),
('maintenance','0'),
('version','v2.3.1');
