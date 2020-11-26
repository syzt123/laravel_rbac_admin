<?php

namespace App\Models;

use App\Libraries\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

// 权限
class Permission extends BaseModel
{
    // 软删除
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    // 追加字段
    protected $appends = ['is_menu_text', 'status_text'];

    // 菜单
    const IS_MENU_ON = 1;
    const IS_MENU_OFF = 2;
    public $is_menuLabel = [self::IS_MENU_OFF=>'否', self::IS_MENU_ON=>'是'];
    public function getIsMenuTextAttribute()
    {
        return $this->is_menuLabel[$this->is_menu] ?? $this->is_menu;
    }

    // 状态
    const STATUS_NORMAL = 1;
    const STATUS_INVALID = 2;
    public $statusLabel = [self::STATUS_NORMAL=>'正常', self::STATUS_INVALID=>'禁用'];
    public function getStatusTextAttribute()
    {
        return $this->statusLabel[$this->status] ?? $this->status;
    }

    // 获取父级权限信息
    public function permission()
    {
        return $this->belongsTo('App\Models\Permission', 'parent_id', 'id');
    }

    // 添加
    public function add($params)
    {
        $model = new Permission();
        $model->parent_id = $params['parent_id'];
        $model->title = $params['title'];
        $model->slug = $params['slug'];
        $model->icon = $params['icon'];
        $model->is_menu = $params['is_menu'];
        $model->status = $params['status'];
        $model->remark = $params['remark'];
        $model->sort = strlen($params['sort']) ? $params['sort'] : $this->max('sort') + 1;
        if ($model->save()) {
            // 更新Redis
            Cache::getInstance()->clearPermission();
            return ['code'=>200, 'data'=>[], 'msg'=>'添加成功'];
        } else {
            return ['code'=>400, 'data'=>[], 'msg'=>'添加失败'];
        }
    }

    // 修改
    public function edit($params)
    {
        $data = array_only($params, ['parent_id', 'title', 'slug', 'icon', 'is_menu', 'status', 'remark', 'sort']);
        $model = Permission::where('id', $params['id'])->update($data);
        if ($model) {
            // 更新Redis
            Cache::getInstance()->clearPermission();
            return ['code'=>200, 'data'=>[], 'msg'=>'修改成功'];
        } else {
            return ['code'=>400, 'data'=>[], 'msg'=>'修改失败'];
        }
    }

    // 删除
    public function del($id)
    {
        $info = Permission::find($id);
        if (!$info) {
            return ['code'=>422, 'data'=>[], 'msg'=>'该信息未找到，建议刷新页面后重试！'];
        }
        // 拿到所有子权限ID
        $allChildPermissionId = getChildPermissionId($id);
        array_push($allChildPermissionId, $id);

        // 删除
        AdminPermission::whereIn('permission_id', $allChildPermissionId)->delete();
        RolePermission::whereIn('permission_id', $allChildPermissionId)->delete();
        Permission::whereIn('id', $allChildPermissionId)->delete();

        // 更新Redis
        Cache::getInstance()->clearPermission();

        return ['code'=>200, 'data'=>[], 'msg'=>'删除成功'];
    }

    // 排序
    public static function sort($params)
    {
        foreach ($params as $key=>$value) {
            Permission::where('id', (int)$key)->update(['sort'=>(int)$value]);
        }
        // 更新Redis
        Cache::getInstance()->clearPermission();
    }
}
