# Paradox

*Paradox(悖论)—— 一条看似自洽、人人都默认成立的业务规则，往往在某个被忽略的边界上自我矛盾。逻辑漏洞的本质正是如此：代码逻辑上"说得通"，却在攻击者按非预期顺序、非预期取值走一遍时露出缺口。本靶场以"找悖论"为核心训练目标。*

---

## 一、靶场简介

Paradox 是一个面向 **Web 渗透与代码审计** 的训练靶场，模拟一套企业管理系统（用户/员工/客户/公告/文件/消息等业务模块），并在其中**有意植入多类常见安全缺陷**。

与多数偏重"注入类"漏洞的靶场不同，**Paradox 的核心重点是逻辑漏洞**：这类问题不依赖特殊字符或畸形负载，而是利用业务规则、状态流转、信任边界上的设计缺陷，往往是真实 SRC / 攻防中"高危且难以自动化扫描"的一类。其余注入类、XSS、文件上传题目作为基础面与组合利用的补充。

> 推荐练习方式：先黑盒摸清业务流程与角色边界，再结合 `Code/` 下源码做白盒验证，最后在 `Write_Up/` 记录每关的思路与修复建议。

---

## 二、漏洞分类（按重点排序）

### 1. 逻辑漏洞（★ 核心，占比最高）

逻辑漏洞是 Paradox 的主线，覆盖以下子类：

- **越权访问（水平 / 垂直）**
  - 水平越权：通过篡改 `id` / `to` 等资源标识，读取或修改他人数据（IDOR）。
  - 垂直越权：低权限角色访问仅管理员可见的功能（强制浏览隐藏入口、后端缺失角色校验）。
- **支付与订单金额篡改**
  - 关键金额 / 余额 / 成交额等字段由客户端提交且服务端无校验，可任意改写。
- **验证码 / Token 绕过与重放**
  - 验证结果由前端响应决定（改返回包绕过）；令牌无签名、可伪造、可重放。
- **密码重置流程缺陷**
  - 找回密码各步骤之间信任前端状态，跳过验证步骤即可重置任意账号。
- **竞态条件（Race Condition）**
  - 并发请求下的"先校验后使用"窗口，导致额度 / 次数 / 状态被重复消费。
- **业务流程跳步与状态机绕过**
  - 多步流程（注册 → 审核、下单 → 支付 → 发货等）中直接请求后置步骤，跳过前置约束。

> 逻辑漏洞往往需要**组合利用**：例如"伪造 Token（绕过）→ 越权调用管理接口（垂直越权）→ 篡改余额（金额篡改）"形成完整攻击链。

### 2. SQL 注入

- 联合查询注入（UNION-based）
- 报错注入（error-based，利用报错回显带出数据）
- 布尔 / 时间盲注（boolean / time-based blind）
- 二次注入（数据先入库、再在另一处被拼接执行）
- 过滤绕过（关键字、空格、引号、编码等绕过技巧）

### 3. XSS（跨站脚本）

- 反射型 / 存储型 / DOM 型
- 富文本与标签过滤绕过
- CSP 场景下的利用与绕过思路

### 4. 文件上传

- 前端校验绕过（仅前端限制类型 / 后缀）
- 服务端类型 / 后缀 / 内容检测绕过（MIME、魔术字节、双扩展名等）
- 解析漏洞与条件竞争上传（中间件解析特性、TOCTOU 竞态写入）

---

## 三、目录结构

```
Paradox/
├── README.md            本文档（靶场介绍 + 规划）
├── Code/                靶场源码（前端 + 后端）
│   ├── index.html       前端单页应用（fetch 调用 api/*.php）
│   └── api/             后端接口（PHP）
│       ├── *.php        各业务模块接口
│       ├── install.sql  建表 + 种子数据
│       └── uploads/     上传文件落盘目录
└── Write_Up/            各关卡解题复盘（按漏洞 / 关卡分文件存放）
```

> `Write_Up/` 建议每关一个文件，记录：漏洞定位、利用步骤、关键请求包、修复建议。

---

## 四、环境搭建与启动

### 运行环境

- PHP 7.x / 8.x（需开启 `mysqli` 扩展）
- MySQL 5.7+ / 8.x
- 推荐 phpStudy（Windows）一键集成环境

### 基本信息

| 项 | 默认值 |
|----|--------|
| 数据库名 | **Paradox**（需手动新建） |
| 数据库主机 | `127.0.0.1` |
| 数据库端口 | `3306` |
| 数据库用户 | `root` |
| 数据库密码 | `root` |
| 字符集 | `utf8mb4` |
| 站点根目录 | `Paradox/Code/` |
| 访问入口 | `index.html` |

> 以上连接参数在 `Code/api/config.php` 中配置，也可用环境变量 `EMS_DB_HOST` / `EMS_DB_PORT` / `EMS_DB_NAME` / `EMS_DB_USER` / `EMS_DB_PASS` 覆盖。

### 1. 新建数据库

数据库名必须为 **`Paradox`**：

```sql
CREATE DATABASE Paradox DEFAULT CHARSET utf8mb4;
```

命令行方式：

```bash
mysql -uroot -p -e "CREATE DATABASE Paradox DEFAULT CHARSET utf8mb4;"
```

### 2. 导入表结构与种子数据

```bash
mysql -uroot -p Paradox < Code/api/install.sql
```

> phpStudy 用户也可在 phpMyAdmin 中选中 `Paradox` 库后导入 `Code/api/install.sql`。

### 3. 配置数据库连接（如与默认不同）

编辑 `Code/api/config.php`，确认 / 修改：

```php
$DB_NAME = getenv('EMS_DB_NAME') ?: 'Paradox';
$DB_USER = getenv('EMS_DB_USER') ?: 'root';
$DB_PASS = getenv('EMS_DB_PASS') ?: 'root';
```

### 4. 启动服务

**方式 A — phpStudy（推荐）**
项目已置于 `D:\Programing\phpstudy_pro\WWW\Paradox`。在 phpStudy 启动 Apache/Nginx + MySQL 后，浏览器访问：

```
http://localhost/Paradox/Code/index.html
```

> 若希望直接用 `http://localhost/` 访问，可将站点根目录指向 `Paradox/Code/`。

**方式 B — PHP 内置服务器**
在 `Code/` 目录下执行（前后端同源，免跨域配置）：

```bash
cd Code
php -S 0.0.0.0:8080
```

浏览器访问： http://localhost:8080/index.html

### 5. 默认账号

| 用户名 | 密码 | 角色 |
|--------|------|------|
| admin | admin123 | admin |
| zhangwei | 123456 | user |
| liwu | liwu@2000 | user |
| manager | Manager#88 | manager |

> 关卡清单与逐题解题思路见 `Write_Up/`。

---

## 五、免责声明

本项目**仅用于本地学习、安全研究与已授权的渗透测试演练**。其中的漏洞均为教学目的有意保留，请勿将相关代码模式用于生产环境，**严禁利用本项目所学攻击任何未取得授权的系统**。由此产生的一切后果由使用者自行承担。
