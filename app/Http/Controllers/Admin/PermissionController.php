<?php

namespace App\Http\Controllers\Admin;

use App\Libraries\Cache;
use App\Models\Permission;
use App\Validate\PermissionValidate;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;

// 权限
class PermissionController extends BaseController
{
    // 列表
    public function index(Request $request)
    {
        // 排序
        if ($request->isMethod('put')) {
            $sort = $request->input('sort');
            (new Permission)->sort($sort);
            return ['code'=>200, 'data'=>[], 'msg'=>'排序成功'];
        }

        // 分页参数
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('pageSize', 15);
        $offset = ($page - 1) * $pageSize;

        // 查找所有数据排序后再分页显示
        $result = Cache::getInstance()->getAllPermission();
        $result = new LengthAwarePaginator(array_slice($result, $offset, $pageSize), count($result), $pageSize, $page, ['path'=>$request->url(), 'query'=>$request->query()]);
        $breadcrumb = getBreadcrumb();

        return view('admin.permission.index', compact('result', 'breadcrumb'));
    }

    // 添加
    public function create(Request $request)
    {
        $params = $request->all();
        if ($request->isMethod('post')) {
            // 数据过滤
            $validate = PermissionValidate::add($params);
            if ($validate['code'] != 200) {
                return response()->json($validate);
            }
            // 数据操作
            $model = (new Permission())->add($params);
            return response()->json($model);
        }
        $allPermission = Cache::getInstance()->getAllPermission();
        return view('admin.permission.create', compact('allPermission'));
    }

    // 查看
    public function show(Request $request)
    {
        $info = Permission::find($request->input('id'));
        if (!$info) {
            abort(422, '该信息未找到，建议刷新页面后重试！');
        }
        $allPermission = Cache::getInstance()->getAllPermission();
        return view('admin.permission.show', compact('info', 'allPermission'));
    }

    // 修改
    public function update(Request $request)
    {
        $params = $request->all();
        if ($request->isMethod('put')) {
            // 数据过滤
            $validate = PermissionValidate::edit($params);
            if ($validate['code'] != 200) {
                return response()->json($validate);
            }
            // 如果 非开发模式并且非默认管理员 是不能修改上级权限,权限标识的(防止普通用户乱操作,导致权限错乱),是否菜单
            if (!(config('admin.develop') && getAdminAuth()->id() == 1)) {
                unset($params['parent_id'], $params['slug'], $params['is_menu']);
            }
            // 数据操作
            $model = (new Permission())->edit($params);
            return response()->json($model);
        }
        $info = Permission::find($request->input('id'));
        if (!$info) {
            abort(422, '该信息未找到，建议刷新页面后重试！');
        }
        $allPermission = Cache::getInstance()->getAllPermission();
        return view('admin.permission.update', compact('info', 'allPermission'));
    }

    // 删除
    public function delete(Request $request)
    {
        $result = (new Permission())->del($request->input('id'));
        return response()->json($result);
    }

    // 检测(如果表中不存在,则放入表中)
    public function check(Request $request)
    {
        $addRouteName = [];             //新增路由
        $failRouteName = [];            //操作失败路由
        $routes = Route::getRoutes();   //所有路由
        foreach ($routes as $route) {
            // 仅对admin中间件中的路由操作
            if(in_array('admin', $route->action['middleware'])) {
                $currentRouteName = $route->getName();
                // 排除不入库路由
                if (in_array($currentRouteName, config('admin.noNeedLogin')) || in_array($currentRouteName, config('admin.noNeedRight')) || in_array($currentRouteName, config('admin.noNeedDevelop'))) {
                    continue;
                }
                // 判断是否入库,如果未入库则入库
                $hasRouteName = Permission::where('slug', $currentRouteName)->value('id');
                if (!$hasRouteName) {
                    $permission = new Permission();
                    $permission->title = $currentRouteName;
                    $permission->slug = $currentRouteName;
                    $permission->icon = 'fa-circle-o';
                    $permission->sort = 100;
                    if ($permission->save()) {
                        $addRouteName[] = $currentRouteName;
                    } else {
                        $failRouteName[] = $currentRouteName;
                    }
                }
            }

        }
        if ($addRouteName) {
            // 更新Redis
            Cache::getInstance()->clearPermission();
        }
        return response()->json(['code'=>200, 'data'=>compact('addRouteName', 'failRouteName'), 'msg'=>'请求成功']);
    }
}
