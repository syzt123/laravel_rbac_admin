<?php

// 文件上传
function fileUpload(&$file, $exceptSuffix = ['php'])
{
    // 目录处理
    $relativePath = 'uploads/' . date('Y/m/d');
    $uploadPath = public_path($relativePath);
    if (!is_dir($uploadPath)) {
        @mkdir($uploadPath, 0777, true);
    }
    if (!is_writeable($uploadPath)) {
        return ['code'=>422, 'data'=>[], 'msg'=>'文件上传目录不可写'];
    }
    // 文件处理
    $fileSuffix = $file->getClientOriginalExtension();
    if (in_array($fileSuffix, $exceptSuffix)) {
        return ['code'=>422, 'data'=>[], 'msg'=>'该文件后缀禁止上传'];
    }
    $fileName = sha1(uniqid(null, true)) . '.' . $fileSuffix;
    $filePath = $relativePath . '/' . $fileName;
    return $file->move($uploadPath, $fileName) ? ['code'=>200, 'data'=>$filePath, 'msg'=>'文件上传成功'] : ['code'=>422, 'data'=>[], 'msg'=>'文件上传失败'];
}

// curl函数
function curlPost($url, $data = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if ($data) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// 后台权限信息
function getAdminAuth()
{
    return \Illuminate\Support\Facades\Auth::guard('admin');
}

// 检测权限(用于页面上按钮之类的)
function checkPermission($slug)
{
    if (getAdminAuth()->check()) {
        $isDefaultAdmin = getAdminAuth()->id() == 1 ? true : false;
        if (!$isDefaultAdmin && getAdminAuth()->user()->status == \App\Models\Admin::STATUS_INVALID) {
            return false;
        }
        if ($isDefaultAdmin && !config('admin.develop') && in_array($slug, config('admin.noNeedDevelop'))) {
            return false;
        }
        if (!$isDefaultAdmin && !in_array($slug, config('admin.noNeedLogin')) && !in_array($slug, config('admin.noNeedRight'))) {
            // 获取当前管理员的所有权限
            $adminPermissionSlug = \App\Libraries\Cache::getInstance()->getAdminPermissionSlug(getAdminAuth()->id());
            if (!in_array($slug, $adminPermissionSlug)) {
                return false;
            }
        }
    } else {
        if (!in_array($slug, config('admin.noNeedLogin'))) {
            return false;
        }
    }
    return true;
}

// 无限级数组排序
function arraySort($data = [], $parent_id = 0, $level = 0, $clear = false)
{
    static $result = [];
    if ($clear) {
        $result = [];
        return $result;
    }
    foreach ($data as $key=>$value) {
        if ($value['parent_id'] == $parent_id) {
            $value['level'] = $level;
            $result[] = $value;
            arraySort($data, $value['id'], $level + 1);
        }
    }
    return $result;
}

// 无限级数组转换成多维数组
function toMultiArray($data = [], $parent_id = 0)
{
    $result = [];
    foreach ($data as $key=>$value) {
        if ($value['parent_id'] == $parent_id) {
            $value['child'] = toMultiArray($data, $value['id']);
            $result[] = $value;
        }
    }
    return $result;
}

// 无限级多维数组转换成菜单字符串
function toMenuHtml($data)
{
    $result = '';
    foreach ($data as $key=>$value) {
        // 这个路由名称不存在会抛异常,所以用try捕获
        try {
            $routeUrl = strlen($value['slug']) ? route($value['slug']) : 'javascript:;';
        } catch (\Exception $e) {
            $routeUrl = 'javascript:;';
        }
        if (empty($value['child'])) {
            $className = '';
            $aTag  = '<a href="'. $routeUrl .'"><i class="fa '. $value['icon'] .'"></i> <span>'. $value['title'] .'</span></a>';
        } else {
            $className = 'treeview';
            $aTag  = '<a href="'. $routeUrl .'"><i class="fa '. $value['icon'] .'"></i> <span>'. $value['title'] .'</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>';
        }
        $result .= '<li class="'. $className .'">'.$aTag;
        if (!empty($value['child'])) {
            $result .= '<ul class="treeview-menu" style="display: none;">'. toMenuHtml($value['child']) .'</ul>';
        }
        $result .= '</li>';
    }
    return $result;
}

// 无限级获取上级
function getParents($data = [], $id)
{
    $result = [];
    foreach ($data as $val) {
        if ($val['id'] == $id) {
            $result[] = $val;
            $result = array_merge(getParents($data, $val['parent_id']), $result);
        }
    }
    return $result;
}

// 获取子权限ID
function getChildPermissionId($id)
{
    $allPermission = \App\Libraries\Cache::getInstance()->getAllPermission();
    $allChildPermission = arraySort($allPermission, $id);
    arraySort([], 0, 0, true);
    $allChildPermissionId = array_column($allChildPermission, 'id');
    return $allChildPermissionId;
}

// 通过当前路由生成面包屑
function getBreadcrumb()
{
    // 当前路由名称
    $slug = \Illuminate\Support\Facades\Route::currentRouteName();
    if (!$slug) {
        return [];
    }
    // 所有的权限ID=》标识，方便查找到相应的ID
    $allPermission = \App\Libraries\Cache::getInstance()->getAllPermission();
    $allSlug = array_column($allPermission, 'slug', 'id');
    $id = array_search($slug, $allSlug);
    if ($id === false) {
        return [];
    }
    // 递归查找上级
    $breadcrumb = getParents($allPermission, $id);
    return $breadcrumb;
}
