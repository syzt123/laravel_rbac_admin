<?php

namespace App\Validate;

use App\Models\Permission;
use Illuminate\Support\Facades\Validator;

// 权限
class PermissionValidate
{
    // 添加
    public static function add($params)
    {
        $model = (new Permission());
        $rules = [
            'title'=>'required|string|max:30',
            'slug'=>'present|nullable|string|max:200',
            'icon'=>'present|nullable|string|max:200',
            'is_menu'=>'required|in:' . implode(',', array_keys($model->is_menuLabel)),
            'status'=>'required|in:' . implode(',', array_keys($model->statusLabel)),
            'remark'=>'present|nullable|string|max:200',
            'sort'=>'present|nullable|integer',
            'parent_id'=>[
                'required',
                function($attribute,$value,$fail) {
                    if ($value != 0) {
                        $permission = Permission::where('id', $value)->value('id');
                        if (!$permission) {
                            return $fail('上级权限不存在，请刷新页面后重试！');
                        }
                    }
                },
            ],
        ];
        $messages = [];
        $customAttributes = [
            'title'=>'标题',
            'slug'=>'标识',
            'icon'=>'图标',
            'is_menu'=>'菜单',
            'status'=>'状态',
            'remark'=>'备注',
            'sort'=>'排序',
            'parent_id'=>'上级权限',
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
        // 基本信息
        $permission = Permission::find(@$params['id']);
        if (!$permission) {
            return ['code'=>422, 'data'=>[], 'msg'=>'该信息未找到，建议刷新页面后重试！'];
        }
        // 获取所有权限 及 获取上级权限不能是子级权限的数组
        $notInIds = getChildPermissionId($params['id']); //获取当前权限下的所有子权限(上级权限不能是子权限)
        array_push($notInIds, $params['id']); // 把自己也加入数组中(上级权限不能是自己)
        if (in_array(@$params['parent_id'], $notInIds)) {
            return ['code'=>422, 'data'=>[], 'msg'=>'上级权限，不能是自己或子权限！'];
        }

        $model = (new Permission());
        $rules = [
            'id'=>'required',
            'title'=>'required|string|max:30',
            'slug'=>'present|nullable|string|max:200',
            'icon'=>'present|nullable|string|max:200',
            'is_menu'=>'required|in:' . implode(',', array_keys($model->is_menuLabel)),
            'status'=>'required|in:' . implode(',', array_keys($model->statusLabel)),
            'remark'=>'present|nullable|string|max:200',
            'sort'=>'required|integer',
            'parent_id'=>[
                'required',
                function($attribute,$value,$fail) {
                    if ($value != 0) {
                        $permission = Permission::where('id', $value)->value('id');
                        if (!$permission) {
                            return $fail('上级权限不存在，请刷新页面后重试！');
                        }
                    }
                },
            ],
        ];
        $messages = [
            'id.exists'=>'该信息未找到，建议刷新页面后重试！',
        ];
        $customAttributes = [
            'id'=>'ID',
            'title'=>'标题',
            'slug'=>'标识',
            'icon'=>'图标',
            'is_menu'=>'菜单',
            'status'=>'状态',
            'remark'=>'备注',
            'sort'=>'排序',
            'parent_id'=>'上级权限',
        ];
        $validator = Validator::make($params, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            return ['code'=>422, 'data'=>[], 'msg'=>$validator->errors()->first()];
        }
        return ['code'=>200, 'data'=>[], 'msg'=>'验证成功'];
    }
}
