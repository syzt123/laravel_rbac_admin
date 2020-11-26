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
                            <label for="name" class="col-sm-2 control-label"><i class="text-red">*</i> 名称</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" value="{{ $info->name }}" class="form-control" id="name" placeholder="名称" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><i class="text-red">*</i> 状态</label>
                            <div class="col-sm-9">
                                <select name="status" class="form-control">
                                    @foreach((new \App\Models\Role())->statusLabel as $key=>$value)
                                        <option value="{{ $key }}" @if($info->status == $key) selected @endif >{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="remark" class="col-sm-2 control-label">备注</label>
                            <div class="col-sm-9">
                                <textarea name="remark" rows="3" class="form-control" placeholder="备注">{{ $info->remark }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">权限</label>
                            <div class="col-sm-9">
                                @if (count($allPermission))
                                    @foreach ($allPermission as $key=>$value)
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="permission_id[]" value="{{ $value['id'] }}" data-id="{{ $value['id'] }}" data-parentid="{{ $value['parent_id'] }}" @if(in_array($value['id'], $permissionId)) checked @endif >@if($value['parent_id'] === 0) ｜ @endif {{ str_repeat('－', $value['level'] * 4) }} {{ $value['title'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="checkbox"><label><input type="checkbox" disabled>暂无权限</label></div>
                                @endif
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
        $(function(){
            // 点击权限父级及子级也跟着选中
            $('input[type="checkbox"]').click(function(){
                if($(this).prop('checked')) {
                    $('input[data-parentid="' + $(this).attr('data-id') + '"]').prop('checked', true);
                    $('input[data-id="' + $(this).attr('data-parentid') + '"]').prop('checked', true);
                }
            });
        });
        function edit() {
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            $.ajax({
                url: "{{ route('admin.role.update') }}",
                type: "PUT",
                data: $('form').serialize(),
                dataType: "json",
                success: function (res) {
                    if (res.code == 200) {
                        layui.use('layer', function () {
                            var layer = layui.layer;
                            layer.ready(function () {
                                layer.msg(res.msg, {}, function () {
                                    parent.location.reload();
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
