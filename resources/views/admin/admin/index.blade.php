@extends('admin.layouts.index')

@section('title', '用户管理')

@section('header')
    <style>
        .m5 {margin: 5px 0;}
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @if($breadcrumb)
                    {{ end($breadcrumb)['title'] }}
                @endif
                <small></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin') }}"><i class="fa fa-home"></i> 首页</a></li>
                @if($breadcrumb)
                    @foreach($breadcrumb as $value)
                        @php
                            try {
                                $route = route($value['slug']);
                            } catch (\Exception $e) {
                                $route = 'javascript:;';
                            }
                        @endphp
                        <li><a href="{{ $route }}">{{ $value['title'] }}</a></li>
                    @endforeach
                @endif
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="row">
                                <form action="" method="get">
                                    <div class="col-lg-3 col-sm-6 col-xs-12 clearfix m5">
                                        <div class="input-group">
                                            <span class="input-group-addon">用户名</span>
                                            <input type="text" name="username" class="form-control" placeholder="用户名" value="{{ @$_GET['username'] }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-xs-12 clearfix m5">
                                        <div class="input-group">
                                            <span class="input-group-addon">姓名</span>
                                            <input type="text" name="name" class="form-control" placeholder="姓名" value="{{ @$_GET['name'] }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-xs-12 clearfix m5">
                                        <div class="input-group">
                                            <span class="input-group-addon">电子邮箱</span>
                                            <input type="text" name="email" class="form-control" placeholder="电子邮箱" value="{{ @$_GET['email'] }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-xs-12 clearfix m5">
                                        <div class="input-group">
                                            <span class="input-group-addon">性别</span>
                                            <select name="sex" id="sex" class="form-control">
                                                <option value="">全部</option>
                                                @foreach((new \App\Models\Admin())->sexLabel as $key=>$value)
                                                    <option value="{{ $key }}" @if(isset($_GET['sex']) && $_GET['sex'] !== '' && $_GET['sex'] == $key) selected @endif >{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-xs-12 clearfix m5">
                                        <div class="input-group">
                                            <span class="input-group-addon">状态</span>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">全部</option>
                                                @foreach((new \App\Models\Admin())->statusLabel as $key=>$value)
                                                    <option value="{{ $key }}" @if(@$_GET['status'] == $key) selected @endif >{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-xs-12 clearfix m5">
                                        <div class="input-group">
                                            <a href="{{ route('admin.admin') }}" class="btn btn-sm btn-default"><i class="fa fa-undo"></i> 重置</a>&nbsp;
                                            <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-search"></i> 提交</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <div class="pull-left">
                                @if(checkPermission('admin.admin.create'))
                                <a href="javascript:add();" class="btn btn-sm btn-success" title="添加"><i class="fa fa-plus"></i><span class="hidden-xs"> 添加</span></a>
                                @endif
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>#</th>
                                    <th>用户名</th>
                                    <th>姓名</th>
                                    <th>性别</th>
                                    <th>电子邮箱</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                                @if($result->count())
                                    @foreach($result as $key=>$value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ str_limit($value->username) }}</td>
                                            <td>{{ str_limit($value->name) }}</td>
                                            <td>{{ $value->sex_text }}</td>
                                            <td>{{ str_limit($value->email) }}</td>
                                            <td>{{ $value->status_text }}</td>
                                            <td>
                                                @if(checkPermission('admin.admin.show'))
                                                <a href="javascript:show({{ $value->id }});" class="btn btn-xs btn-info" title="查看"><i class="fa fa-eye"></i></a>
                                                @endif
                                                @if(checkPermission('admin.admin.update'))
                                                <a href="javascript:edit({{ $value->id }});" class="btn btn-xs btn-success" title="修改"><i class="fa fa-pencil"></i></a>
                                                @endif
                                                @if(checkPermission('admin.admin.delete'))
                                                    @if($value->id == 1)
                                                        <a href="javascript:;" class="btn btn-xs btn-danger" title="默认管理员不能删除" disabled><i class="fa fa-trash"></i></a>
                                                    @else
                                                        <a href="javascript:del({{ $value->id }});" class="btn btn-xs btn-danger" title="删除"><i class="fa fa-trash"></i></a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">暂无数据</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
                            @if($result->count())
                                <div class="pull-left" style="padding-top: 8px;white-space: nowrap;">
                                    显示第 {{ $result->firstItem() }} 到第 {{ $result->lastItem() }} 条记录，总共{{ $result->total() }}条记录
                                </div>
                            @endif
                            {{ $result->links('vendor.pagination.admin') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('footer')
    <script>
        function add() {
            layui.use('layer', function () {
                if (window.innerWidth >= 800) {
                    var _w = '800px';
                    var _h = '600px';
                } else {
                    var _w = '100%';
                    var _h = '100%';
                }
                var layer = layui.layer;
                layer.ready(function () {
                    layer.open({
                        type: 2,
                        title: '添加',
                        area: [_w, _h],
                        content: "{{ route('admin.admin.create') }}"
                    });
                });
            });
        }
        function show(id) {
            layui.use('layer', function () {
                if (window.innerWidth >= 800) {
                    var _w = '800px';
                    var _h = '600px';
                } else {
                    var _w = '100%';
                    var _h = '100%';
                }
                var layer = layui.layer;
                layer.ready(function () {
                    layer.open({
                        type: 2,
                        title: '查看',
                        area: [_w, _h],
                        content: "{{ route('admin.admin.show') }}?id="+id
                    });
                });
            });
        }
        function edit(id) {
            layui.use('layer', function () {
                if (window.innerWidth >= 800) {
                    var _w = '800px';
                    var _h = '600px';
                } else {
                    var _w = '100%';
                    var _h = '100%';
                }
                var layer = layui.layer;
                layer.ready(function () {
                    layer.open({
                        type: 2,
                        title: '修改',
                        area: [_w, _h],
                        content: "{{ route('admin.admin.update') }}?id="+id
                    });
                });
            });
        }
        function del(id) {
            layui.use('layer', function () {
                var layer = layui.layer;
                layer.ready(function () {
                    layer.confirm('确定要删除吗？', function (index) {
                        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
                        $.ajax({
                            url: "{{ route('admin.admin.delete') }}",
                            type: "DELETE",
                            data: {id: id},
                            dataType: "json",
                            success: function (res) {
                                if (res.code == 200) {
                                    layui.use('layer', function () {
                                        var layer = layui.layer;
                                        layer.ready(function () {
                                            layer.msg(res.msg, {}, function () {
                                                location.reload();
                                            });
                                        });
                                    });
                                } else {
                                    layui.use('layer', function () {
                                        var layer = layui.layer;
                                        layer.ready(function () {
                                            layer.msg(res.msg);
                                        });
                                    });
                                }
                            },
                            error: function () {
                                layui.use('layer', function () {
                                    var layer = layui.layer;
                                    layer.ready(function () {
                                        layer.msg('网络错误');
                                    });
                                });
                            }
                        });
                    });
                });
            });
        }
    </script>
@endsection
