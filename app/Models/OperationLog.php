<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

// 操作日志
class OperationLog extends BaseModel
{
    // 软删除
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    // 获取管理员信息(操作人)
    public function admin()
    {
        return $this->belongsTo('App\Models\Admin');
    }

    // 列表
    public function search($params)
    {
        $query = new OperationLog();
        $query = $query->with(['admin'=>function ($query) {
            $query->withTrashed();
        }]);
        if (isset($params['title']) && $params['title'] !== '') {
            $query = $query->where('title', 'like', '%' . $params['title'] . '%');
        }
        if (isset($params['method']) && $params['method'] !== '') {
            $query = $query->where('method', 'like', '%' . $params['method'] . '%');
        }
        if (isset($params['path']) && $params['path'] !== '') {
            $query = $query->where('path', 'like', '%' . $params['path'] . '%');
        }
        if (isset($params['username']) && $params['username'] !== '') {
            $adminId = Admin::where('username', 'like', '%' . $params['username'] . '%')
                ->orWhere('name', 'like', '%' . $params['username'] . '%')
                ->pluck('id')->toArray();
            $query = $query->whereIn('admin_id', $adminId);
        }
        $query = $query->paginate();
        return $query;
    }
}
