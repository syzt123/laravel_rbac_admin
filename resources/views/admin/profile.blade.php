@extends('admin.layouts.index')

@section('title', '个人信息')

@section('header')
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                个人信息
                <small></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin') }}"><i class="fa fa-home"></i> 首页</a></li>
                <li class="active">个人信息</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">修改</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="form-horizontal" action="" method="post" enctype="multipart/form-data" onsubmit="return profile()">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="username" class="col-sm-2 control-label"><i class="text-red">*</i> 用户名</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="username" value="{{ $admin->username }}" class="form-control" id="username" placeholder="用户名" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="col-sm-2 control-label">新密码</label>
                                    <div class="col-sm-8">
                                        <input type="password" name="password" class="form-control" id="password" placeholder="不设置则不修改密码">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation" class="col-sm-2 control-label">确认新密码</label>
                                    <div class="col-sm-8">
                                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="确认新密码必须与新密码一致">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-sm-2 control-label"><i class="text-red">*</i> 姓名</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="name" value="{{ $admin->name }}" class="form-control" id="name" placeholder="姓名" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><i class="text-red">*</i> 性别</label>
                                    <div class="col-sm-8">
                                        @foreach($admin->sexLabel as $key=>$val)
                                            <label class="radio-inline">
                                                <input type="radio" name="sex" value="{{ $key }}" @if($admin->sex == $key) checked @endif > {{ $val }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="avatar" class="col-sm-2 control-label">头像</label>
                                    <div class="col-sm-8">
                                        <input type="file" class="avatar" name="avatar" data-initial-preview="{{ $admin->avatar ? asset($admin->avatar) : config('admin.avatar') }}"
                                               data-initial-caption="{{ $admin->avatar ? asset($admin->avatar) : config('admin.avatar') }}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-sm-2 control-label">电子邮箱</label>
                                    <div class="col-sm-8">
                                        <input type="email" name="email" value="{{ $admin->email }}" class="form-control" id="email" placeholder="电子邮箱" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-8">
                                        <button type="reset" class="btn btn-warning">重置</button>
                                        <button type="submit" class="btn btn-info">提交</button>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-footer -->
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('footer')
    <script>
        $(function () {
            $("input.avatar").fileinput({
                "overwriteInitial":true,
                "initialPreviewAsData":true,
                "browseLabel":"\u6d4f\u89c8",
                "cancelLabel":"\u53d6\u6d88",
                "showRemove":false,
                "showUpload":false,
                "showCancel":false,
                "dropZoneEnabled":false,
                "msgPlaceholder":"\u9009\u62e9\u56fe\u7247",
                "allowedFileTypes":["image"],
                "language":"zh",
                "maxFileSize":2048
            });
        });
        function profile() {
            var formData = new FormData($('form')[0]);
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            $.ajax({
                url: "{{ route('admin.profile') }}",
                type: "POST",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.code == 200) {
                        layui.use('layer', function () {
                            var layer = layui.layer;
                            layer.ready(function () {
                                layer.msg(res.msg, {}, function () {
                                    location.href = "{{ route('admin') }}";
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
