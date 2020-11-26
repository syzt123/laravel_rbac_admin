<?php

namespace App\Models;

use App\Libraries\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

// 配置
class Config extends BaseModel
{
    // 软删除
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    // 追加字段
    protected $appends = ['type_text'];

    // 类型
    const TYPE_INPUT = 1;
    const TYPE_TEXT_AREA = 2;
    const TYPE_RADIO = 3;
    const TYPE_CHECKBOX = 4;
    const TYPE_SELECT = 5;
    public $typeLabel = [self::TYPE_INPUT=>'单行文本', self::TYPE_TEXT_AREA=>'多行文本', self::TYPE_RADIO=>'单选按钮', self::TYPE_CHECKBOX=>'复选框', self::TYPE_SELECT=>'下拉框'];
    public function getTypeTextAttribute()
    {
        return $this->typeLabel[$this->type] ?? $this->type;
    }

    // 设置
    public function setting($params)
    {
        // 只能数据表来循环，因为checkbox框可能一个都没选(也就是什么都没传过来)
        $result = Config::all();
        foreach ($result as $value) {
            // 判断是否传值，否则就清空配置值
            if (isset($params[$value->id])) {
                // 如果传过来的是数组,就通过,符号分隔
                if (is_array($params[$value->id])) {
                    Config::where('id', $value->id)->update(['value'=>implode(',', $params[$value->id])]);
                } else {
                    Config::where('id', $value->id)->update(['value'=>$params[$value->id]]);
                }
            } else {
                // 没传值则清空配置值
                Config::where('id', $value->id)->update(['value'=>null]);
            }
        }
        // 更新Redis
        Cache::getInstance()->updateConfig();
    }

    // 添加
    public function add($params)
    {
        $model = new Config();
        $model->title = $params['title'];
        $model->variable = $params['variable'];
        $model->type = $params['type'];
        $model->item = $params['item'];
        $model->value = $params['value'];
        $model->sort = strlen($params['sort']) ? $params['sort'] : $this->max('sort') + 1;
        if ($model->save()) {
            // 更新Redis
            Cache::getInstance()->updateConfig();
            return ['code'=>200, 'data'=>[], 'msg'=>'添加成功'];
        } else {
            return ['code'=>400, 'data'=>[], 'msg'=>'添加失败'];
        }
    }

    // 修改
    public function edit($params)
    {
        $data = array_only($params, ['title', 'variable', 'type', 'item', 'value', 'sort']);
        $model = Config::where('id', $params['id'])->update($data);
        if ($model) {
            // 更新Redis
            Cache::getInstance()->updateConfig();
            return ['code'=>200, 'data'=>[], 'msg'=>'修改成功'];
        } else {
            return ['code'=>400, 'data'=>[], 'msg'=>'修改失败'];
        }
    }

    // 排序
    public static function sort($params)
    {
        foreach ($params as $key=>$value) {
            Config::where('id', (int)$key)->update(['sort'=>(int)$value]);
        }
    }
}
