@extends('admin.layouts.index')

@section('title', '日志列表')

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
                                        <span class="input-group-addon">标题</span>
                                        <input type="text" name="title" class="form-control" placeholder="标题" value="{{ @$_GET['title'] }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-xs-12 clearfix m5">
                                    <div class="input-group">
                                        <span class="input-group-addon">类型</span>
                                        <input type="text" name="method" class="form-control" placeholder="类型" value="{{ @$_GET['method'] }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-xs-12 clearfix m5">
                                    <div class="input-group">
                                        <span class="input-group-addon">路径</span>
                                        <input type="text" name="path" class="form-control" placeholder="路径" value="{{ @$_GET['path'] }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-xs-12 clearfix m5">
                                    <div class="input-group">
                                        <span class="input-group-addon">操作用户</span>
                                        <input type="text" name="username" class="form-control" placeholder="操作用户" value="{{ @$_GET['username'] }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-xs-12 clearfix m5">
                                    <div class="input-group">
                                        <a href="{{ route('admin.operation_log') }}" class="btn btn-sm btn-default"><i class="fa fa-undo"></i> 重置</a>&nbsp;
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
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>#</th>
                                    <th>标题</th>
                                    <th>类型</th>
                                    <th>路径</th>
                                    <th>操作用户</th>
                                    <th>操作</th>
                                </tr>
                                @if($result->count())
                                    @foreach($result as $key=>$value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->title }}</td>
                                            <td>{{ $value->method }}</td>
                                            <td>{{ $value->path }}</td>
                                            <td>{{ $value->admin->username }}</td>
                                            <td>
                                                @if(checkPermission('admin.operation_log.show'))
                                                <a href="javascript:show({{ $value->id }});" class="btn btn-xs btn-info" title="查看"><i class="fa fa-eye"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">暂无数据</td>
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
                        content: "{{ route('admin.operation_log.show') }}?id="+id
                    });
                });
            });
        }
    </script>
@endsection
