<?php

namespace App\Validate;

use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

// 角色
class RoleValidate
{
    // 添加
    public static function add($params)
    {
        $model = (new Role());
        $rules = [
            'name'=>'required|string|max:30',
            'status'=>'required|in:' . implode(',', array_keys($model->statusLabel)),
            'remark'=>'present|nullable|string|max:200',
            'permission_id'=>'nullable|array',
        ];
        $messages = [];
        $customAttributes = [
            'name'=>'名称',
            'status'=>'状态',
            'remark'=>'备注',
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
        $model = (new Role());
        $rules = [
            'id'=>['required', Rule::exists('roles')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'name'=>'required|string|max:30',
            'status'=>'required|in:' . implode(',', array_keys($model->statusLabel)),
            'remark'=>'present|nullable|string|max:200',
            'permission_id'=>'nullable|array',
        ];
        $messages = [];
        $customAttributes = [
            'id'=>'角色ID',
            'name'=>'名称',
            'status'=>'状态',
            'remark'=>'备注',
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
            'id'=>['required', Rule::exists('roles')->where(function($query){
                $query->whereNull('deleted_at');
            })],
        ];
        $messages = [];
        $customAttributes = [
            'id'=>'角色ID',
        ];
        $validator = Validator::make($params, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            return ['code'=>422, 'data'=>[], 'msg'=>$validator->errors()->first()];
        }
        return ['code'=>200, 'data'=>[], 'msg'=>'验证成功'];
    }
}
