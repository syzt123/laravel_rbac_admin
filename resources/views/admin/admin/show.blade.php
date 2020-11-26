@extends('admin.layouts.details')

@section('header')
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" action="" method="post" enctype="multipart/form-data" onsubmit="return edit()">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $info->id }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="username" class="col-sm-2 control-label"><i class="text-red">*</i> 用户名</label>
                            <div class="col-sm-9">
                                <input type="text" name="username" value="{{ $info->username }}" class="form-control" id="username" placeholder="用户名" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label"><i class="text-red">*</i> 姓名</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" value="{{ $info->name }}" class="form-control" id="name" placeholder="姓名" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><i class="text-red">*</i> 性别</label>
                            <div class="col-sm-9">
                                @foreach((new \App\Models\Admin())->sexLabel as $key=>$val)
                                    <label class="radio-inline">
                                        <input type="radio" name="sex" value="{{ $key }}" @if($info->sex == $key) checked @endif > {{ $val }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="avatar" class="col-sm-2 control-label">头像</label>
                            <div class="col-sm-9">
                                <input type="file" class="avatar" name="avatar" data-initial-preview="{{ $info->avatar ? asset($info->avatar) : config('admin.avatar') }}"
                                       data-initial-caption="{{ $info->avatar ? asset($info->avatar) : config('admin.avatar') }}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">电子邮箱</label>
                            <div class="col-sm-9">
                                <input type="email" name="email" value="{{ $info->email }}" class="form-control" id="email" placeholder="电子邮箱" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><i class="text-red">*</i> 状态</label>
                            <div class="col-sm-9">
                                @foreach((new \App\Models\Admin())->statusLabel as $key=>$val)
                                    <label class="radio-inline">
                                        <input type="radio" name="status" value="{{ $key }}" @if($info->status == $key) checked @endif > {{ $val }}
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
                                            <option value="{{ $value['id'] }}" @if(in_array($value['id'], $roleId)) selected @endif >{{ $value['name'] }}</option>
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
                                            <option value="{{ $value['id'] }}" @if(in_array($value['id'], $permissionId)) selected @endif >@if($value['parent_id'] === 0) ｜ @endif {{ str_repeat('－', $value['level'] * 4) }} {{ $value['title'] }}</option>
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
        $('input,textarea').attr('readonly', true);
        $('select').attr('disabled', true);
        $("input[type='checkbox']").attr('disabled', true);
        $("input[type='radio']").attr('disabled', true);
    </script>
@endsection
