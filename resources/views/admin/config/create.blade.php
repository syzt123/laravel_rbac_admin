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
                            <label for="title" class="col-sm-2 control-label"><i class="text-red">*</i> 标题</label>
                            <div class="col-sm-9">
                                <input type="text" name="title" class="form-control" id="title" placeholder="标题" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="variable" class="col-sm-2 control-label"><i class="text-red">*</i> 变量名</label>
                            <div class="col-sm-9">
                                <input type="text" name="variable" class="form-control" id="variable" placeholder="变量名" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><i class="text-red">*</i> 类型</label>
                            <div class="col-sm-9">
                                <select name="type" class="form-control">
                                    @foreach((new \App\Models\Config())->typeLabel as $key=>$value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="item" class="col-sm-2 control-label"> 可选项</label>
                            <div class="col-sm-9">
                                <textarea name="item" rows="3" class="form-control" placeholder="可选项"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="value" class="col-sm-2 control-label"> 配置值</label>
                            <div class="col-sm-9">
                                <textarea name="value" rows="3" class="form-control" placeholder="配置值"></textarea>
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
                url: "{{ route('admin.config.create') }}",
                type: "POST",
                data: $('form').serialize(),
                dataType: "json",
                success: function (res) {
                    if (res.code == 200) {
                        layui.use('layer', function () {
                            var layer = layui.layer;
                            layer.ready(function () {
                                layer.msg(res.msg, {}, function () {
                                    parent.location.href = "{{ route('admin.config') }}";
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
