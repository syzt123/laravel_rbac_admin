@extends('admin.layouts.index')

@section('title', '配置管理')

@section('header')
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                配置管理
                <small></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin') }}"><i class="fa fa-home"></i> 首页</a></li>
                <li class="active">配置管理</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <div class="pull-left">
                                <a href="javascript:add();" class="btn btn-sm btn-success" title="添加"><i class="fa fa-plus"></i><span class="hidden-xs"> 添加</span></a>
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
                                    <th>变量名</th>
                                    <th>类型</th>
                                    <th>配置值</th>
                                    <th>操作</th>
                                </tr>
                                @if($result->count())
                                    @foreach($result as $key=>$value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="text" name="sort[{{ $value->id }}]" value="{{ $value->sort }}" style="width: 70px;border: 1px solid #ccc;">
                                            </td>
                                            <td>{{ str_limit($value->title) }}</td>
                                            <td>{{ str_limit($value->variable) }}</td>
                                            <td>{{ $value->type_text }}</td>
                                            <td>{{ str_limit($value->value) }}</td>
                                            <td>
                                                <a href="javascript:edit({{ $value->id }});" class="btn btn-xs btn-success" title="修改"><i class="fa fa-pencil"></i></a>
                                                <a href="javascript:del({{ $value->id }});" class="btn btn-xs btn-danger" title="删除"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td colspan="6">
                                            <button type="submit" class="btn btn-sm btn-info">排序</button>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">暂无数据</td>
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
                        content: "{{ route('admin.config.create') }}"
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
                        content: "{{ route('admin.config.update') }}?id="+id
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
                            url: "{{ route('admin.config.delete') }}",
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
                url: "{{ route('admin.config') }}",
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
    </script>
@endsection
