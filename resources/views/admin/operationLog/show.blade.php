@extends('admin.layouts.details')

@section('header')
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" action="" method="post" enctype="multipart/form-data" onsubmit="">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="title" class="col-sm-2 control-label">标题</label>
                            <div class="col-sm-9">
                                <input type="text" name="title" value="{{ $info->title  }}" class="form-control" id="title" placeholder="标题" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="method" class="col-sm-2 control-label">类型</label>
                            <div class="col-sm-9">
                                <input type="text" name="method" value="{{ $info->method  }}" class="form-control" id="method" placeholder="类型" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="path" class="col-sm-2 control-label">路径</label>
                            <div class="col-sm-9">
                                <input type="text" name="path" value="{{ $info->path  }}" class="form-control" id="path" placeholder="路径" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input" class="col-sm-2 control-label">数据</label>
                            <div class="col-sm-9">
                                <textarea name="input" rows="3" class="form-control" placeholder="数据">{{ $info->input }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="admin_user_id" class="col-sm-2 control-label">操作用户</label>
                            <div class="col-sm-9">
                                <input type="text" name="admin_user_id" value="{{ $info->admin->username }}" class="form-control" placeholder="操作用户" />
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
        $('input,textarea').attr('readonly', true);
    </script>
@endsection
