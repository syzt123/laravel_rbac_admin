@extends('admin.layouts.index')

@section('title', '系统设置')

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
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <form action="" method="post" onsubmit="return setting()">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 100px;">#</th>
                                    <th>标题</th>
                                    <th>配置值</th>
                                </tr>
                                @if(count($result))
                                    @foreach($result as $key=>$value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->title }}</td>
                                            <td>
                                                @if ($value->type == \App\Models\Config::TYPE_INPUT)
                                                    {{-- 单行文本框 --}}
                                                    <input type="text" name="value[{{ $value->id }}]" value="{{ $value->value }}" class="form-control" style="width: 400px;">
                                                @elseif ($value->type == \App\Models\Config::TYPE_TEXT_AREA)
                                                    {{-- 多行文本框 --}}
                                                    <textarea name="value[{{ $value->id }}]" rows="3" class="form-control" style="width: 400px;">{{ $value->value }}</textarea>
                                                @elseif ($value->type == \App\Models\Config::TYPE_RADIO)
                                                    {{-- 单选按钮 --}}
                                                    @if (strlen($value->item))
                                                        @foreach(($items = explode(',', $value->item)) as $val)
                                                            <label class="radio-inline"><input type="radio" name="value[{{ $value->id }}]" value="{{ $val }}" @if ($val == $value->value) checked @endif> {{ $val }}</label>
                                                        @endforeach
                                                    @endif
                                                @elseif ($value->type == \App\Models\Config::TYPE_CHECKBOX)
                                                    {{-- 复选框 --}}
                                                    @if (strlen($value->item))
                                                        @php
                                                            if (strlen($value->value)) {
                                                                $values = explode(',', $value->value);
                                                            } else {
                                                                $values = [];
                                                            }
                                                        @endphp
                                                        @foreach(($items = explode(',', $value->item)) as $val)
                                                            <label class="checkbox-inline"><input type="checkbox" name="value[{{ $value->id }}][]" value="{{ $val }}" @if (in_array($val, $values)) checked @endif> {{ $val }}</label>
                                                        @endforeach
                                                    @endif
                                                @elseif ($value->type == \App\Models\Config::TYPE_SELECT)
                                                    {{-- 下拉框 --}}
                                                    @if (strlen($value->item))
                                                        <select name="value[{{ $value->id }}]" class="form-control" style="width: 120px;">
                                                            @foreach(($items = explode(',', $value->item)) as $val)
                                                                <option value="{{ $val }}" @if ($val == $value->value) selected @endif>{{ $val }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                @else
                                                    &nbsp;
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td colspan="2">
                                            <button type="reset" class="btn btn-sm btn-warning">重置</button>
                                            <button type="submit" class="btn btn-sm btn-info">提交</button>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">暂无数据</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        </form>
                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
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
        function setting() {
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            $.ajax({
                url: "{{ route('admin.setting') }}",
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
