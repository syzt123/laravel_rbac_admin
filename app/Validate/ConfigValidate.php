<?php

namespace App\Validate;

use App\Models\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

// 配置
class ConfigValidate
{
    // 添加
    public static function add($params)
    {
        $rules = [
            'title'=>'required|string|max:30',
            'variable'=>['required', 'alpha', 'max:20', Rule::unique('configs')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            'type'=>'required|in:' . implode(',', array_keys((new Config())->typeLabel)),
            'item'=>'present',
            'value'=>'present',
            'sort'=>'present|nullable|integer',
        ];
        $messages = [];
        $customAttributes = [
            'title'=>'标题',
            'variable'=>'变量名',
            'type'=>'类型',
            'item'=>'可选项',
            'value'=>'配置值',
            'sort'=>'排序',
        ];
        $validator = Validator::make($params, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            return ['code'=>422, 'data'=>[], 'msg'=>$validator->errors()->first()];
        }
        return ['code'=>200, 'data'=>[], 'msg'=>'验证成功'];
    }

    // 添加
    public static function edit($params)
    {
        $rules = [
            'id'=>['required', Rule::exists('configs')->where(function ($query) {
                $query->whereNull('deleted_at');
            })],
            'title'=>'required|string|max:30',
            'variable'=>['required', 'alpha', 'max:20', Rule::unique('configs')->where(function ($query) use ($params) {
                $query->whereNull('deleted_at')->where('id', '!=', @$params['id']);
            }),
            ],
            'type'=>'required|in:' . implode(',', array_keys((new Config())->typeLabel)),
            'item'=>'present',
            'value'=>'present',
            'sort'=>'required|integer',
        ];
        $messages = [
            'id.exists'=>'该信息未找到，建议刷新页面后重试！',
        ];
        $customAttributes = [
            'id'=>'ID',
            'title'=>'标题',
            'variable'=>'变量名',
            'type'=>'类型',
            'item'=>'可选项',
            'value'=>'配置值',
            'sort'=>'排序',
        ];
        $validator = Validator::make($params, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            return ['code'=>422, 'data'=>[], 'msg'=>$validator->errors()->first()];
        }
        return ['code'=>200, 'data'=>[], 'msg'=>'验证成功'];
    }
}
