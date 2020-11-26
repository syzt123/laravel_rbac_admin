@extends('admin.layouts.details')

@section('header')
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" action="" method="post" enctype="multipart/form-data" onsubmit="return add()">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="username" class="col-sm-2 control-label"><i class="text-red">*</i> 用户名</label>
                            <div class="col-sm-9">
                                <input type="text" name="username" class="form-control" id="username" placeholder="用户名" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-sm-2 control-label"> 密码</label>
                            <div class="col-sm-9">
                                <input type="password" name="password" class="form-control" id="password" placeholder="不设置默认123456" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation" class="col-sm-2 control-label"> 确认密码</label>
                            <div class="col-sm-9">
                                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="确认密码必须与密码一致" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label"><i class="text-red">*</i> 姓名</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" class="form-control" id="name" placeholder="姓名" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><i class="text-red">*</i> 性别</label>
                            <div class="col-sm-9">
                                @foreach((new \App\Models\Admin())->sexLabel as $key=>$val)
                                    <label class="radio-inline">
                                        <input type="radio" name="sex" value="{{ $key }}" @if($loop->iteration == 1) checked @endif > {{ $val }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="avatar" class="col-sm-2 control-label">头像</label>
                            <div class="col-sm-9">
                                <input type="file" class="avatar" name="avatar" data-initial-preview="{{ config('admin.avatar') }}"
                                       data-initial-caption="{{ config('admin.avatar') }}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">电子邮箱</label>
                            <div class="col-sm-9">
                                <input type="email" name="email" class="form-control" id="email" placeholder="电子邮箱" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><i class="text-red">*</i> 状态</label>
                            <div class="col-sm-9">
                                @foreach((new \App\Models\Admin())->statusLabel as $key=>$val)
                                    <label class="radio-inline">
                                        <input type="radio" name="status" value="{{ $key }}" @if($loop->iteration == 1) checked @endif > {{ $val }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">角色</label>
                            <div class="col-sm-9">
                                <select name="role_id[]" id="role_id" class="form-control select2" multiple>
                                    @if(count($allRole))
                                        @foreach ($allRole as $key=>$value)
                                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>暂无数据</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">权限</label>
                            <div class="col-sm-9">
                                <select name="permission_id[]" id="permission_id" class="form-control select2" multiple>
                                    @if(count($allPermission))
                                        @foreach ($allPermission as $key=>$value)
                                            <option value="{{ $value['id'] }}">@if($value['parent_id'] === 0) ｜ @endif {{ str_repeat('－', $value['level'] * 4) }} {{ $value['title'] }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>暂无数据</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-9">
                                <button type="reset" class="btn btn-warning">重置</button>
                                <button type="submit" class="btn btn-info">提交</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-footer -->
                </form>
            </div>
        </div>
    </section>
    <!-- /.content -->
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
        function add() {
            var formData = new FormData($('form')[0]);
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            $.ajax({
                url: "{{ route('admin.admin.create') }}",
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
                                    parent.location.href = "{{ route('admin.admin') }}";
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
