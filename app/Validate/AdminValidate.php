<?php

namespace App\Validate;

use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

// 管理员
class AdminValidate
{
    // 后台登录
    public static function login($params)
    {
        $rules = [
            'username'=>'required|alpha_num|between:3,20',
            'password'=>'required|alpha_dash|between:6,20',
        ];
        $messages = [];
        $customAttributes = [
            'username'=>'用户名',
            'password'=>'密码',
        ];
        $validator = Validator::make($params, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            return ['code'=>422, 'data'=>[], 'msg'=>$validator->errors()->first()];
        }
        return ['code'=>200, 'data'=>[], 'msg'=>'验证成功'];
    }

    // 个人信息
    public static function profile($params)
    {
        $admin = getAdminAuth()->user();
        $rules = [
            'username'=>['required', 'alpha_num', 'between:3,20', Rule::unique('admins')->where(function ($query) use ($admin) {
                $query->whereNull('deleted_at')->where('id', '!=', $admin->id);
            })],
            'password'=>'present|nullable|alpha_dash|between:6,20|confirmed',
            'name'=>'required|string|between:3,20',
            'sex'=>'required|in:' . implode(',', array_keys($admin->sexLabel)),
            'avatar'=>'image',
            'email'=>'present|nullable|email|max:200',
        ];
        $messages = [];
        $customAttributes = [
            'username'=>'用户名',
            'password'=>'新密码',
            'name'=>'姓名',
            'sex'=>'性别',
            'avatar'=>'头像',
            'email'=>'电子邮箱',
        ];
        $validator = Validator::make($params, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            return ['code'=>422, 'data'=>[], 'msg'=>$validator->errors()->first()];
        }
        return ['code'=>200, 'data'=>[], 'msg'=>'验证成功'];
    }

    // 添加
    public static function add($params)
    {
        $model = (new Admin());
        $rules = [
            'username'=>['required', 'alpha_num', 'between:3,20', Rule::unique('admins')->where(function ($query) {
                $query->whereNull('deleted_at');
            })],
            'password'=>'present|nullable|alpha_dash|between:6,20|confirmed',
            'name'=>'required|string|between:3,20',
            'sex'=>'required|in:' . implode(',', array_keys($model->sexLabel)),
            'avatar'=>'image',
            'email'=>'present|nullable|email|max:200',
            'status'=>'required|in:' . implode(',', array_keys($model->statusLabel)),
            'role_id'=>'nullable|array',
            'permission_id'=>'nullable|array',
        ];
        $messages = [];
        $customAttributes = [
            'username'=>'用户名',
            'password'=>'密码',
            'name'=>'姓名',
            'sex'=>'性别',
            'avatar'=>'头像',
            'email'=>'电子邮箱',
            'status'=>'状态',
            'role_id'=>'角色ID',
            'permission_id'=>'权限ID',
        ];
        $validator = Validator::make($params, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            return ['code'=>422, 'data'=>[], 'msg'=>$validator->errors()->first()];
        }
        return ['code'=>200, 'data'=>[], 'msg'=>'验证成功'];
    }

    // 修改
    public static function edit($params)
    {
        $model = (new Admin());
        $rules = [
            'id'=>['required', Rule::exists('admins')->where(function ($query) {
                $query->whereNull('deleted_at');
            })],
            'username'=>['required', 'alpha_num', 'between:3,20', Rule::unique('admins')->where(function ($query) use ($params) {
                $query->whereNull('deleted_at')->where('id', '!=', @$params['id']);
            })],
            'password'=>'present|nullable|alpha_dash|between:6,20|confirmed',
            'name'=>'required|string|between:3,20',
            'sex'=>'required|in:' . implode(',', array_keys($model->sexLabel)),
            'avatar'=>'image',
            'email'=>'present|nullable|email|max:200',
            'status'=>'required|in:' . implode(',', array_keys($model->statusLabel)),
            'role_id'=>'nullable|array',
            'permission_id'=>'nullable|array',
        ];
        $messages = [];
        $customAttributes = [
            'id'=>'管理员ID',
            'username'=>'用户名',
            'password'=>'新密码',
            'name'=>'姓名',
            'sex'=>'性别',
            'avatar'=>'头像',
            'email'=>'电子邮箱',
            'status'=>'状态',
            'role_id'=>'角色ID',
            'permission_id'=>'权限ID',
        ];
        $validator = Validator::make($params, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            return ['code'=>422, 'data'=>[], 'msg'=>$validator->errors()->first()];
        }
        return ['code'=>200, 'data'=>[], 'msg'=>'验证成功'];
    }

    // 删除
    public static function del($params)
    {
        $rules = [
            'id'=>['required', Rule::exists('admins')->where(function ($query) {
                $query->whereNull('deleted_at');
            }), function($attribute,$value,$fail){
                if ($value == '1') {
                    return $fail('默认管理员不能删除');
                }
            }],
        ];
        $messages = [];
        $customAttributes = [
            'id'=>'管理员ID',
        ];
        $validator = Validator::make($params, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            return ['code'=>422, 'data'=>[], 'msg'=>$validator->errors()->first()];
        }
        return ['code'=>200, 'data'=>[], 'msg'=>'验证成功'];
    }
}
