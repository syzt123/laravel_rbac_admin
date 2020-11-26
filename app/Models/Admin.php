<?php

namespace App\Models;

use App\Libraries\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

// 管理员
class Admin extends Authenticatable
{
    use Notifiable;

    // 软删除
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    // 追加字段
    protected $appends = ['sex_text', 'status_text'];

    // 性别
    const SEX_UNKNOWN = 0;
    const SEX_MAN = 1;
    const SEX_WOMAN = 2;
    public $sexLabel = [self::SEX_UNKNOWN=>'保密', self::SEX_MAN=>'男', self::SEX_WOMAN=>'女'];
    public function getSexTextAttribute()
    {
        return $this->sexLabel[$this->sex] ?? $this->sex;
    }

    // 状态
    const STATUS_NORMAL = 1;
    const STATUS_INVALID = 2;
    public $statusLabel = [self::STATUS_NORMAL=>'正常', self::STATUS_INVALID=>'禁用'];
    public function getStatusTextAttribute()
    {
        return $this->statusLabel[$this->status] ?? $this->status;
    }

    // 获取关联的角色
    public function role()
    {
        return $this->belongsToMany('App\Models\Role', 'role_admins');
    }

    // 获取管理员直接拥有的权限
    public function permission()
    {
        return $this->belongsToMany('App\Models\Permission', 'admin_permissions');
    }

    // 个人信息
    public function profile($params)
    {
        $data = array_only($params, ['username', 'password', 'name', 'sex', 'avatar', 'email']);
        $model = Admin::where('id', getAdminAuth()->id())->update($data);
        return $model ? ['code'=>200, 'data'=>[], 'msg'=>'修改成功'] : ['code'=>400, 'data'=>[], 'msg'=>'修改失败'];
    }

    // 后台登录
    public function login($params)
    {
        // 查找管理员
        $model = Admin::where('username', $params['username'])->first();
        if (!$model) {
            return ['code'=>422, 'data'=>[], 'msg'=>'用户名或密码错误'];
        }
        // 验证密码
        if (!password_verify($params['password'], $model->password)) {
            return ['code'=>422, 'data'=>[], 'msg'=>'用户名或密码错误'];
        }
        // 验证状态(默认管理员不受限制)
        if ($model->id != 1 && $model->status == Admin::STATUS_INVALID) {
            return ['code'=>422, 'data'=>[], 'msg'=>'您的账号已被禁用'];
        }
        return ['code'=>200, 'data'=>$model, 'msg'=>'验证成功'];
    }

    // 列表
    public function search($params)
    {
        $query = new Admin();
        if (isset($params['username']) && $params['username'] !== '') {
            $query = $query->where('username', 'like', '%' . $params['username'] . '%');
        }
        if (isset($params['name']) && $params['name'] !== '') {
            $query = $query->where('name', 'like', '%' . $params['name'] . '%');
        }
        if (isset($params['sex']) && $params['sex'] !== '') {
            $query = $query->where('sex', '=', $params['sex']);
        }
        if (isset($params['email']) && $params['email'] !== '') {
            $query = $query->where('email', 'like', '%' . $params['email'] . '%');
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
            // 管理员入库
            $model = new Admin();
            $model->username = $params['username'];
            $model->password = $params['password'];
            $model->name = $params['name'];
            $model->sex = $params['sex'];
            $model->avatar = $params['avatar'];
            $model->email = $params['email'];
            $model->status = $params['status'];
            if (!$model->save()) {
                throw new \Exception('添加失败');
            }
            // 关联角色入库
            if (isset($params['role_id']) && is_array($params['role_id'])) {
                $params['role_id'] = Role::whereIn('id', $params['role_id'])->pluck('id')->toArray();
                foreach ($params['role_id'] as $value) {
                    $roleAdmin = new RoleAdmin();
                    $roleAdmin->role_id = $value;
                    $roleAdmin->admin_id = $model->id;
                    if (!$roleAdmin->save()) {
                        throw new \Exception('添加失败');
                    }
                }
            }
            // 关联权限入库
            if (isset($params['permission_id']) && is_array($params['permission_id'])) {
                $params['permission_id'] = Permission::whereIn('id', $params['permission_id'])->pluck('id')->toArray();
                foreach ($params['permission_id'] as $value) {
                    $adminPermission = new AdminPermission();
                    $adminPermission->admin_id = $model->id;
                    $adminPermission->permission_id = $value;
                    if (!$adminPermission->save()) {
                        throw new \Exception('添加失败');
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code'=>400, 'data'=>[], 'msg'=>'添加失败'];
        }
        return ['code'=>200, 'data'=>[], 'msg'=>'添加成功'];
    }

    // 修改
    public function edit($params)
    {
        try {
            DB::beginTransaction();
            // 管理员入库
            $data = array_only($params, ['username', 'password', 'name', 'sex', 'avatar', 'email', 'status']);
            $model = Admin::where('id', $params['id'])->update($data);
            if (!$model) {
                throw new \Exception('修改失败');
            }
            // 删除拥有过的角色，权限
            RoleAdmin::where('admin_id', $params['id'])->delete();
            AdminPermission::where('admin_id', $params['id'])->delete();
            // 关联角色入库
            if (isset($params['role_id']) && is_array($params['role_id'])) {
                $params['role_id'] = Role::whereIn('id', $params['role_id'])->pluck('id')->toArray();
                foreach ($params['role_id'] as $value) {
                    $roleAdmin = new RoleAdmin();
                    $roleAdmin->role_id = $value;
                    $roleAdmin->admin_id = $params['id'];
                    if (!$roleAdmin->save()) {
                        throw new \Exception('修改失败');
                    }
                }
            }
            // 关联权限入库
            if (isset($params['permission_id']) && is_array($params['permission_id'])) {
                $params['permission_id'] = Permission::whereIn('id', $params['permission_id'])->pluck('id')->toArray();
                foreach ($params['permission_id'] as $value) {
                    $adminPermission = new AdminPermission();
                    $adminPermission->admin_id = $params['id'];
                    $adminPermission->permission_id = $value;
                    if (!$adminPermission->save()) {
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
        Cache::getInstance()->clearAdmin($params['id']);
        return ['code'=>200, 'data'=>[], 'msg'=>'修改成功'];
    }

    // 删除
    public function del($id)
    {
        try {
            DB::beginTransaction();
            // 管理员删除
            $model = Admin::destroy($id);
            if (!$model) {
                throw new \Exception('删除失败');
            }
            // 删除拥有的角色，权限
            RoleAdmin::where('admin_id', $id)->delete();
            AdminPermission::where('admin_id', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code'=>400, 'data'=>[], 'msg'=>'删除失败'];
        }
        // 更新Redis
        Cache::getInstance()->clearAdmin($id);
        return ['code'=>200, 'data'=>[], 'msg'=>'删除成功'];
    }
}
