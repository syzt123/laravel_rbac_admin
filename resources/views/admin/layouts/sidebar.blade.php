    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{ ($avatar = getAdminAuth()->user()->avatar) ? asset($avatar) : config('admin.avatar') }}" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>{{ getAdminAuth()->user()->name }}</p>
                    <a href="javascript:;"><i class="fa fa-circle text-success"></i> 在线</a>
                </div>
            </div>
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">

                <li class="header">菜单</li>

                {{-- 每个人都应该有权限查看，所以直接写在这里 --}}
                <li @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin') class="active" @endif >
                    <a href="{{ route('admin') }}">
                        <i class="fa fa-home"></i> <span>首页</span>
                    </a>
                </li>

                {!! \App\Libraries\Cache::getInstance()->getAdminPermissionMenu(getAdminAuth()->id()) !!}

                {{-- 开发阶段的辅助功能，所以直接写在这里 --}}
                @if(config('admin.develop') && getAdminAuth()->id() == 1)
                    <li @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.config') class="active" @endif >
                        <a href="{{ route('admin.config') }}">
                            <i class="fa fa-circle-o"></i> <span>配置管理</span>
                        </a>
                    </li>
                @endif

            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>
