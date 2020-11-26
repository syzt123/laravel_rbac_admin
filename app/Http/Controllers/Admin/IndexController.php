<?php

namespace App\Http\Controllers\Admin;

use App\Libraries\Cache;
use App\Models\Admin;
use App\Validate\AdminValidate;
use Illuminate\Http\Request;

// 基本操作
class IndexController extends BaseController
{
    // 后台首页
    public function index(Request $request)
    {
        return view('admin.index');
    }

    // 个人信息
    public function profile(Request $request)
    {
        $params = $request->all();
        $admin = getAdminAuth()->user();
        if ($request->isMethod('put')) {
            // 数据过滤
            $validate = AdminValidate::profile($params);
            if ($validate['code'] != 200) {
                return response()->json($validate);
            }
            // 密码处理
            if ($request->filled('password')) {
                $params['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
            } else {
                $params['password'] = $admin->password;
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
                $params['avatar'] = getAdminAuth()->user()->avatar;
            }
            // 数据操作
            $adminUser = (new Admin())->profile($params);
            return response()->json($adminUser);
        }
        return view('admin.profile', compact('admin'));
    }

    // 后台登录
    public function login(Request $request)
    {
        $params = $request->all();
        if ($request->isMethod('post')) {
            // 数据过滤
            $validate = AdminValidate::login($params);
            if ($validate['code'] != 200) {
                return response()->json($validate);
            }
            // 核对数据
            $adminUser = (new Admin())->login($params);
            if ($adminUser['code'] != 200) {
                return response()->json($adminUser);
            }
            // 登录处理
            $remember = $request->has('remember');
            getAdminAuth()->login($adminUser['data'], $remember);
            return response()->json(['code'=>200, 'data'=>[], 'msg'=>'登录成功']);
        }
        return view('admin.login');
    }

    // 退出登录
    public function logout(Request $request)
    {
        getAdminAuth()->logout();
        return redirect()->route('admin.login');
    }

    // 清空缓存
    public function clearCache(Request $request)
    {
        Cache::getInstance()->clear();
        return response()->json(['code'=>200, 'data'=>[], 'msg'=>'清理完成']);
    }
}
