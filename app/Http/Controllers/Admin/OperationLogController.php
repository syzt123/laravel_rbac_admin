<?php

namespace App\Http\Controllers\Admin;

use App\Models\OperationLog;
use Illuminate\Http\Request;

// 操作日志
class OperationLogController extends BaseController
{
    // 列表
    public function index(Request $request)
    {
        $params = $request->all();
        $result = (new OperationLog())->search($params);
        $breadcrumb = getBreadcrumb();
        return view('admin.operationLog.index', compact('result', 'breadcrumb'));
    }

    // 查看
    public function show(Request $request)
    {
        $info = OperationLog::with(['admin'=>function($query){
            $query->withTrashed();
        }])->find($request->input('id'));
        if (!$info) {
            abort(422, '该信息未找到，建议刷新页面后重试！');
        }
        return view('admin.operationLog.show', compact('info'));
    }
}
