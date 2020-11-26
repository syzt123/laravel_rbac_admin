<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'welcome');

// 后台路由
Route::group(['prefix'=>config('admin.routePrefix'), 'namespace'=>'Admin', 'middleware'=>['web', 'admin']], function () {
    Route::any('/', 'IndexController@index')->name('admin');                            //后台首页
    Route::any('profile', 'IndexController@profile')->name('admin.profile');            //个人信息
    Route::any('login', 'IndexController@login')->name('admin.login');                  //后台登录
    Route::any('logout', 'IndexController@logout')->name('admin.logout');               //退出登录
    Route::any('clear_cache', 'IndexController@clearCache')->name('admin.clear_cache'); //清空缓存

    Route::any('admin', 'AdminController@index')->name('admin.admin');                  //用户管理
    Route::any('admin/create', 'AdminController@create')->name('admin.admin.create');   //用户添加
    Route::any('admin/show', 'AdminController@show')->name('admin.admin.show');         //用户查看
    Route::any('admin/update', 'AdminController@update')->name('admin.admin.update');   //用户修改
    Route::any('admin/delete', 'AdminController@delete')->name('admin.admin.delete');   //用户删除

    Route::any('role', 'RoleController@index')->name('admin.role');                 //角色管理
    Route::any('role/create', 'RoleController@create')->name('admin.role.create');  //角色添加
    Route::any('role/show', 'RoleController@show')->name('admin.role.show');        //角色查看
    Route::any('role/update', 'RoleController@update')->name('admin.role.update');  //角色修改
    Route::any('role/delete', 'RoleController@delete')->name('admin.role.delete');  //角色删除

    Route::any('permission', 'PermissionController@index')->name('admin.permission');                   //权限管理
    Route::any('permission/create', 'PermissionController@create')->name('admin.permission.create');    //权限添加
    Route::any('permission/show', 'PermissionController@show')->name('admin.permission.show');          //权限查看
    Route::any('permission/update', 'PermissionController@update')->name('admin.permission.update');    //权限修改
    Route::any('permission/delete', 'PermissionController@delete')->name('admin.permission.delete');    //权限删除
    Route::any('permission/check', 'PermissionController@check')->name('admin.permission.check');       //权限检测

    Route::any('setting', 'ConfigController@setting')->name('admin.setting');   //系统设置

    Route::any('operation_log', 'OperationLogController@index')->name('admin.operation_log');           //日志列表
    Route::any('operation_log/show', 'OperationLogController@show')->name('admin.operation_log.show');  //日志查看

    Route::any('config', 'ConfigController@index')->name('admin.config');                   //配置管理
    Route::any('config/create', 'ConfigController@create')->name('admin.config.create');    //配置添加
    Route::any('config/update', 'ConfigController@update')->name('admin.config.update');    //配置修改
    Route::any('config/delete', 'ConfigController@delete')->name('admin.config.delete');    //配置删除
});
