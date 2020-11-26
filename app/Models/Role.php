<?php

namespace App\Models;

use App\Libraries\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

// 角色
class Role extends BaseModel
{
    // 软删除
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    // 追加字段
    protected $appends = ['status_text'];

    // 状态
    const STATUS_NORMAL = 1;
    const STATUS_INVALID = 2;
    public $statusLabel = [self::STATUS_NORMAL=>'正常', self::STATUS_INVALID=>'禁用'];
    public function getStatusTextAttribute()
    {
        return $this->statusLabel[$this->status] ?? $this->status;
    }

    // 获取权限
    public function permission()
    {
        return $this->belongsToMany('App\Models\Permission', 'role_permissions');
    }

    // 列表
    public function search($params)
    {
        $query = new Role();
        if (isset($params['name']) && $params['name'] !== '') {
            $query = $query->where('name', 'like', '%' . $params['name'] . '%');
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query = $query->where('status', '=', $params['status']);
        }
        $query = $query->paginate();
        return $query;
    }

    // 添加
    public function add($params)
    {
        try {
            DB::beginTransaction();
            // 角色入库
            $model = new Role();
            $model->name = $params['name'];
            $model->status = $params['status'];
            $model->remark = $params['remark'];
            if (!$model->save()) {
                throw new \Exception('添加失败');
            }
            // 权限入库
            if (isset($params['permission_id']) && is_array($params['permission_id'])) {
                $params['permission_id'] = Permission::whereIn('id', $params['permission_id'])->pluck('id')->toArray();
                foreach ($params['permission_id'] as $value) {
                    $rolePermission = new RolePermission();
                    $rolePermission->role_id = $model->id;
                    $rolePermission->permission_id = $value;
                    if (!$rolePermission->save()) {
                        throw new \Exception('添加失败');
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code'=>400, 'data'=>[], 'msg'=>'添加失败'];
        }
        // 更新Redis
        Cache::getInstance()->updateAllRole();
        return ['code'=>200, 'data'=>[], 'msg'=>'添加成功'];
    }

    // 修改
    public function edit($params)
    {
        try {
            DB::beginTransaction();
            // 角色入库
            $model = Role::find($params['id']);
            $model->name = $params['name'];
            $model->status = $params['status'];
            $model->remark = $params['remark'];
            if (!$model->save()) {
                throw new \Exception('修改失败');
            }
            // 权限入库
            RolePermission::where('role_id', $params['id'])->delete();
            if (isset($params['permission_id']) && is_array($params['permission_id'])) {
                $params['permission_id'] = Permission::whereIn('id', $params['permission_id'])->pluck('id')->toArray();
                foreach ($params['permission_id'] as $value) {
                    $rolePermission = new RolePermission();
                    $rolePermission->role_id = $model->id;
                    $rolePermission->permission_id = $value;
                    if (!$rolePermission->save()) {
                        throw new \Exception('修改失败');
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code'=>400, 'data'=>[], 'msg'=>'修改失败'];
        }
        // 更新Redis
        Cache::getInstance()->clearRole($params['id']);
        return ['code'=>200, 'data'=>[], 'msg'=>'修改成功'];
    }

    // 删除
    public function del($id)
    {
        Role::destroy($id);
        RoleAdmin::where('role_id', $id)->delete();
        RolePermission::where('role_id', $id)->delete();
        // 更新Redis
        Cache::getInstance()->clearRole($id);
        return ['code'=>200, 'data'=>[], 'msg'=>'删除成功'];
    }
}
