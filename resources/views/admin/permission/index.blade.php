@extends('admin.layouts.index')

@section('title', '权限管理')

@section('header')
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
                        <div class="box-header with-border">
                            <div class="pull-left">
                                @if(config('admin.develop') && getAdminAuth()->id() == 1)
                                <a href="javascript:add();" class="btn btn-sm btn-success" title="添加"><i class="fa fa-plus"></i><span class="hidden-xs"> 添加</span></a>
                                @endif
                                @if(config('admin.develop') && getAdminAuth()->id() == 1)
                                <a href="javascript:check();" class="btn btn-sm btn-warning" title="检测"><i class="fa fa-check"></i><span class="hidden-xs"> 检测</span></a>
                                @endif
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <form action="" method="post" onsubmit="return sort()">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>#</th>
                                    <th>排序</th>
                                    <th>标题</th>
                                    <th>标识</th>
                                    <th>图标</th>
                                    <th>菜单</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                                @if($result->count())
                                    @foreach($result as $key=>$value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="text" name="sort[{{ $value['id'] }}]" value="{{ $value['sort'] }}" style="width: 70px;border: 1px solid #ccc;">
                                            </td>
                                            <td>@if($value['parent_id'] == 0) ｜ @endif {{ str_repeat('－', $value['level'] * 4) }} {{ $value['title'] }}</td>
                                            <td>{{ str_limit($value['slug']) }}</td>
                                            <td>{{ $value['icon'] }}</td>
                                            <td>{{ $value['is_menu_text'] }}</td>
                                            <td>{{ $value['status_text'] }}</td>
                                            <td>
                                                @if(checkPermission('admin.permission.show'))
                                                <a href="javascript:show({{ $value['id'] }});" class="btn btn-xs btn-info" title="查看"><i class="fa fa-eye"></i></a>
                                                @endif
                                                @if(checkPermission('admin.permission.update'))
                                                <a href="javascript:edit({{ $value['id'] }});" class="btn btn-xs btn-success" title="修改"><i class="fa fa-pencil"></i></a>
                                                @endif
                                                @if(config('admin.develop') && getAdminAuth()->id() == 1)
                                                <a href="javascript:del({{ $value['id'] }});" class="btn btn-xs btn-danger" title="删除"><i class="fa fa-trash"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td colspan="7">
                                            <button type="submit" class="btn btn-sm btn-info">排序</button>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center">暂无数据</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        </form>
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
                        content: "{{ route('admin.permission.create') }}"
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
                        content: "{{ route('admin.permission.show') }}?id="+id
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
                        content: "{{ route('admin.permission.update') }}?id="+id
                    });
                });
            });
        }
        function del(id) {
            layui.use('layer', function () {
                var layer = layui.layer;
                layer.ready(function () {
                    layer.confirm('将会把子权限一同删除，确定要删除吗？', function (index) {
                        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
                        $.ajax({
                            url: "{{ route('admin.permission.delete') }}",
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
        function sort() {
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            $.ajax({
                url: "{{ route('admin.permission') }}",
                type: "PUT",
                data: $('form').serialize(),
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
            return false;
        }
        function check() {
            layui.use('layer', function(){
                var layer = layui.layer;
                layer.ready(function(){
                    layer.confirm("未入库的路由将会入库，确定要操作吗？", function(index){
                        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
                        $.ajax({
                            url: "{{ route('admin.permission.check') }}",
                            type: 'POST',
                            data: {},
                            dataType: "json",
                            success: function(res){
                                if (res.code == 200) {
                                    layui.use('layer', function () {
                                        var layer = layui.layer;
                                        layer.ready(function () {
                                            layer.alert("共新增"+res.data.addRouteName.length+"条路由，"+res.data.failRouteName.length+"条操作失败。\n", {}, function (index) {
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
                            error: function(){
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
