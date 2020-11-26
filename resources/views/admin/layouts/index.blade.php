<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    @include('admin.layouts.header')
    @yield('header')
</head>
<body class="hold-transition skin-blue fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="{{ route('admin') }}" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>{{ config('app.name') }}</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>{{ config('app.name') }}</b>后台管理</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- refresh -->
                    <li><a href="" title="刷新页面"><i class="fa fa-refresh"></i></a></li>
                    @if(checkPermission('admin.clear_cache'))
                    <!-- trash -->
                    <li><a href="javascript:clearCache();" title="清空缓存"><i class="fa fa-trash"></i></a></li>
                    @endif
                    <!-- home -->
                    <li><a href="{{ url('/') }}" target="_blank" title="网站首页"><i class="fa fa-home"></i></a></li>
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ ($avatar = getAdminAuth()->user()->avatar) ? asset($avatar) : config('admin.avatar') }}" class="user-image" alt="User Image">
                            <span class="hidden-xs">{{ getAdminAuth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="{{ ($avatar = getAdminAuth()->user()->avatar) ? asset($avatar) : config('admin.avatar') }}" class="img-circle" alt="User Image">

                                <p>
                                    {{ getAdminAuth()->user()->username }}
                                    <small>{{ getAdminAuth()->user()->created_at }}</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{ route('admin.profile') }}" class="btn btn-default btn-flat">个人信息</a>
                                </div>
                                <div class="pull-right">
                                    <a href="{{ route('admin.logout') }}" class="btn btn-default btn-flat">退出登录</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    @include('admin.layouts.sidebar')

    @yield('content')

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.4.18
        </div>
        <strong>Copyright &copy; 2014-2019 <a href="{{ url('/') }}">{{ config('app.name') }}</a>.</strong> All rights
        reserved.
    </footer>
</div>
<!-- ./wrapper -->

    @include('admin.layouts.footer')
    @yield('footer')
    <script>
        $(document).ready(function () {
            $('.sidebar-menu').tree()
        })
    </script>
</body>
</html>
