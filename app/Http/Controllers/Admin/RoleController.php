<?php

namespace App\Http\Controllers\Admin;

use App\Libraries\Cache;
use App\Models\Role;
use App\Models\RolePermission;
use App\Validate\RoleValidate;
use Illuminate\Http\Request;

// 角色
class RoleController extends BaseController
{
    // 列表
    public function index(Request $request)
    {
        $params = $request->all();
        $result = (new Role())->search($params);
        $breadcrumb = getBreadcrumb();
        return view('admin.role.index', compact('result', 'breadcrumb'));
    }

    // 添加
    public function create(Request $request)
    {
        $params = $request->all();
        if ($request->isMethod('post')) {
            // 数据过滤
            $validate = RoleValidate::add($params);
            if ($validate['code'] != 200) {
                return response()->json($validate);
            }
            // 数据操作
            $model = (new Role())->add($params);
            return response()->json($model);
        }
        $allPermission = Cache::getInstance()->getAllPermission();
        return view('admin.role.create', compact('allPermission'));
    }

    // 查看
    public function show(Request $request)
    {
        $info = Role::find($request->input('id'));
        if (!$info) {
            abort(422, '该信息未找到，建议刷新页面后重试！');
        }
        $permissionId = Cache::getInstance()->getRolePermissionId($info->id);
        $allPermission = Cache::getInstance()->getAllPermission();
        return view('admin.role.show', compact('info', 'allPermission', 'permissionId'));
    }

    // 修改
    public function update(Request $request)
    {
        $params = $request->all();
        if ($request->isMethod('put')) {
            // 数据过滤
            $validate = RoleValidate::edit($params);
            if ($validate['code'] != 200) {
                return response()->json($validate);
            }
            // 数据操作
            $model = (new Role())->edit($params);
            return response()->json($model);
        }
        $info = Role::find($request->input('id'));
        if (!$info) {
            abort(422, '该信息未找到，建议刷新页面后重试！');
        }
        $permissionId = Cache::getInstance()->getRolePermissionId($info->id);
        $allPermission = Cache::getInstance()->getAllPermission();
        return view('admin.role.update', compact('info', 'allPermission', 'permissionId'));
    }

    // 删除
    public function delete(Request $request)
    {
        $params = $request->all();
        // 数据过滤
        $validate = RoleValidate::del($params);
        if ($validate['code'] != 200) {
            return response()->json($validate);
        }
        // 数据操作
        $model = (new Role())->del($params['id']);
        return response()->json($model);
    }
}
