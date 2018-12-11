# think-admin
# ENV
- php >= 7.1.3
- mysql >= 5.5

# install
- composer config -g repo.packagist composer https://packagist.laravel-china.org
- composer update
- 配置 config/database.php 数据库配置
- php think migrate:run
- php think seed:run

# Use
- 配置虚拟域名 OR 在根目录下执行 php think run
- yourUrl/login
- 默认用户名 admin 密码 admin
# Problem
> SQLSTATE[42000]: Syntax error or access violation: 1067 Invalid default value for 'updated_at'

设置 sql_mode;
```
show variables like 'sql_mode' ; 
```
> remove 'NO_ZERO_IN_DATE,NO_ZERO_DATE'
```
SET GLOBAL sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'
```
# Talking
欢迎进入 Q 群 302266230 讨论，可以及时反馈一些问题。
