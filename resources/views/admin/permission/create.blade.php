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
                            <label for="parent_id" class="col-sm-2 control-label"><i class="text-red">*</i> 上级权限</label>
                            <div class="col-sm-9">
                                <select name="parent_id" id="parent_id" class="form-control select2">
                                    <option value="0">顶级权限</option>
                                    @foreach($allPermission as $value)
                                        <option value="{{ $value['id'] }}">@if($value['parent_id'] == 0) ｜ @endif {{ str_repeat('－', $value['level'] * 4) }} {{ $value['title'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="title" class="col-sm-2 control-label"><i class="text-red">*</i> 标题</label>
                            <div class="col-sm-9">
                                <input type="text" name="title" class="form-control" id="title" placeholder="标题" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="slug" class="col-sm-2 control-label">标识</label>
                            <div class="col-sm-9">
                                <input type="text" name="slug" class="form-control" id="slug" placeholder="标识" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="icon" class="col-sm-2 control-label">图标</label>
                            <div class="col-sm-9">
                                <input type="text" name="icon" value="fa-circle-o" class="form-control" id="name" placeholder="图标" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><i class="text-red">*</i> 菜单</label>
                            <div class="col-sm-9">
                                <select name="is_menu" class="form-control">
                                    @foreach((new \App\Models\Permission())->is_menuLabel as $key=>$value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><i class="text-red">*</i> 状态</label>
                            <div class="col-sm-9">
                                <select name="status" class="form-control">
                                    @foreach((new \App\Models\Permission())->statusLabel as $key=>$value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="remark" class="col-sm-2 control-label">备注</label>
                            <div class="col-sm-9">
                                <textarea name="remark" rows="3" class="form-control" placeholder="备注"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sort" class="col-sm-2 control-label"> 排序</label>
                            <div class="col-sm-9">
                                <input type="number" name="sort" class="form-control" placeholder="排序" />
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
        function add() {
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            $.ajax({
                url: "{{ route('admin.permission.create') }}",
                type: "POST",
                data: $('form').serialize(),
                dataType: "json",
                success: function (res) {
                    if (res.code == 200) {
                        layui.use('layer', function () {
                            var layer = layui.layer;
                            layer.ready(function () {
                                layer.msg(res.msg, {}, function () {
                                    parent.location.href = "{{ route('admin.permission') }}";
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
