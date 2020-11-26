<?php

namespace App\Http\Controllers\Admin;

use App\Libraries\Cache;
use App\Models\Role;
use App\Models\RoleAdmin;
use App\Models\Admin;
use App\Models\AdminPermission;
use App\Validate\AdminValidate;
use Illuminate\Http\Request;

// 管理员
class AdminController extends BaseController
{
    // 列表
    public function index(Request $request)
    {
        $params = $request->all();
        $result = (new Admin())->search($params);
        $breadcrumb = getBreadcrumb();
        return view('admin.admin.index', compact('result', 'breadcrumb'));
    }

    // 添加
    public function create(Request $request)
    {
        $params = $request->all();
        if ($request->isMethod('post')) {
            // 数据过滤
            $validate = AdminValidate::add($params);
            if ($validate['code'] != 200) {
                return response()->json($validate);
            }
            // 密码处理
            if ($request->filled('password')) {
                $params['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
            } else {
                $params['password'] = password_hash('123456', PASSWORD_DEFAULT);
            }
            // 头像处理
            if ($avatar = $request->file('avatar')) {
                $avatarResult = fileUpload($avatar);
                if ($avatarResult['code'] == 200) {
                    $params['avatar'] = $avatarResult['data'];
                } else {
                    return response()->json($avatarResult);
                }
            } else {
                $params['avatar'] = null;
            }
            // 数据操作
            $model = (new Admin())->add($params);
            return response()->json($model);
        }
        $allRole = Cache::getInstance()->getAllRole();
        $allPermission = Cache::getInstance()->getAllPermission();
        return view('admin.admin.create', compact('allRole', 'allPermission'));
    }

    // 查看
    public function show(Request $request)
    {
        $info = Admin::find($request->input('id'));
        if (!$info) {
            abort(422, '该信息未找到，建议刷新页面后重试！');
        }
        $roleId = Cache::getInstance()->getAdminRoleId($info->id);
        $permissionId = Cache::getInstance()->getAdminPermissionId($info->id);
        $allRole = Cache::getInstance()->getAllRole();
        $allPermission = Cache::getInstance()->getAllPermission();
        return view('admin.admin.show', compact('info', 'roleId', 'permissionId', 'allRole', 'allPermission'));
    }

    // 修改
    public function update(Request $request)
    {
        $params = $request->all();
        if ($request->isMethod('put')) {
            // 数据过滤
            $validate = AdminValidate::edit($params);
            if ($validate['code'] != 200) {
                return response()->json($validate);
            }
            // 密码处理
            if ($request->filled('password')) {
                $params['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
            } else {
                // 不修改密码
                unset($params['password']);
            }
            // 头像处理
            if ($avatar = $request->file('avatar')) {
                $avatarResult = fileUpload($avatar);
                if ($avatarResult['code'] == 200) {
                    $params['avatar'] = $avatarResult['data'];
                } else {
                    return response()->json($avatarResult);
                }
            } else {
                // 不修改头像
                unset($params['avatar']);
            }
            // 默认管理员不受状态限制
            if ($params['id'] == 1) {
                $params['status'] = Admin::STATUS_NORMAL;
            }
            // 数据操作
            $model = (new Admin())->edit($params);
            return response()->json($model);
        }
        $info = Admin::find($request->input('id'));
        if (!$info) {
            abort(422, '该信息未找到，建议刷新页面后重试！');
        }
        $roleId = Cache::getInstance()->getAdminRoleId($info->id);
        $permissionId = Cache::getInstance()->getAdminPermissionId($info->id);
        $allRole = Cache::getInstance()->getAllRole();
        $allPermission = Cache::getInstance()->getAllPermission();
        return view('admin.admin.update', compact('info', 'roleId', 'permissionId', 'allRole', 'allPermission'));
    }

    // 删除
    public function delete(Request $request)
    {
        $params = $request->all();
        // 数据过滤
        $validate = AdminValidate::del($params);
        if ($validate['code'] != 200) {
            return response()->json($validate);
        }
        // 数据操作
        $model = (new Admin())->del($params['id']);
        return response()->json($model);
    }
}
