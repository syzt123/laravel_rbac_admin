## 功能

- 完成了RBAC权限系统功能
- 完成了配置项功能
- 日志功能待完善(只需要在中间件中拿到数据入库就行了)

为了方便以后搭项目，直接pull下来就可以用了。写过好几次后台基本功能的架子，有Laravel，ThinkPHP的，都不是很称心，每一次都会有所改进和收获。

## 环境

- PHP >= 7.0.0
- MySQL >= 5.7
- Laravel >= 5.5.0
- Redis
- Node.js

Git、Composer就没列出来了，心里清楚就行。Redis用在RBAC权限这块，Node.js用于下载安装静态资源包。

## 安装

1.把项目下载或克隆到本地，进入项目根目录
```
git clone https://github.com/strval/laravel-admin.git laravel
cd laravel
```
2.Composer安装
```
composer install
```
3.安装静态资源包。使用Node.js的npm命令全局安装bower
```
npm install bower -g
#执行下载命令，下载过程中可能会失败，若失败多试几次
bower install
```
4.导入laravel_admin.sql数据库文件，修改.env配置信息就OK了
```
# 功能不是很多，后面有时间再慢慢加吧。如消息，日志，用户，导入导出等功能。
后台地址:/admin 账号:admin 密码:123456
```
