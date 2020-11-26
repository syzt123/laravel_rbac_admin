<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 后台开发模式
    |--------------------------------------------------------------------------
    |
    | 处于开发阶段的时候方便后台设置定义权限、配置项等，开发完成请关闭这个选项
    |
    */

    'develop' => env('ADMIN_DEVELOP', false),

    /*
    |--------------------------------------------------------------------------
    | 后台路由前缀
    |--------------------------------------------------------------------------
    |
    | 方便以后修改后台地址，在使用url时应该使用route通过路由名称生成
    |
    */

    'routePrefix' => env('ADMIN_ROUTE_PREFIX', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | 无需登录，无需鉴权 的路由
    |--------------------------------------------------------------------------
    |
    | 将会在中间件中过滤。如登陆，退出登录等路由
    |
    */

    'noNeedLogin' => ['admin.login', 'admin.logout'],

    /*
    |--------------------------------------------------------------------------
    | 需要登录，无需鉴权 的路由
    |--------------------------------------------------------------------------
    |
    | 用于存放基础权限路由，如后台首页，个人信息等
    |
    */

    'noNeedRight' => ['admin', 'admin.profile'],

    /*
    |--------------------------------------------------------------------------
    | 开发辅助路由
    |--------------------------------------------------------------------------
    |
    | 处于开发阶段时用的辅助路由(仅默认管理员能用)，开发模式关闭后就不能用了
    |
    */

    'noNeedDevelop' => [
        'admin.config',
        'admin.config.create',
        'admin.config.update',
        'admin.config.delete',
        'admin.permission.create',
        'admin.permission.delete',
        'admin.permission.check',
    ],

    /*
    |--------------------------------------------------------------------------
    | 默认头像地址
    |--------------------------------------------------------------------------
    |
    | 有多个地方用到，所有写在这里，方便后面修改。
    | 这里不能使用asset函数，否则在执行composer install时会报错。
    |
    */

    'avatar' => '/dist/img/user2-160x160.jpg',

];
