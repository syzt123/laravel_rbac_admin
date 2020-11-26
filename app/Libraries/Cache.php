<?php

namespace App\Libraries;

use App\Models\AdminPermission;
use App\Models\Config;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleAdmin;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Redis;

class Cache
{
    // 所有权限二维数组(不管状态是正常还是禁用都在里面),用于角色勾选权限时用等
    const ALL_PERMISSION = 'laravel_all_permission';
    // 所有菜单二维数组(不管状态是正常还是禁用都在里面),用于默认管理员构建菜单时用等
    const ALL_PERMISSION_MENU = 'laravel_all_permission_menu';

    // 所有角色，用于管理员的添加，修改。
    const ALL_ROLE = 'laravel_all_role';
    // 角色所拥有的权限ID数组，由于多个角色，所以使用哈希。用于角色在编辑时的已拥有权限ID(不管是正常还是禁用都在里面)
    const ROLE_PERMISSION_ID = 'laravel_role_permission_id';
    // 角色所拥有的权限标识数组，由于多个角色，所以使用哈希。用于鉴权功能(所以只能拿状态是正常的数据)
    const ROLE_PERMISSION_SLUG = 'laravel_role_permission_slug';
    // 角色所拥有的权限菜单，由于多个角色，所以使用哈希。用于鉴权功能(所以只能拿状态是正常的数据)
    const ROLE_PERMISSION_MENU = 'laravel_role_permission_menu';

    // 管理员所拥有的角色ID，由于多个管理员，所以使用哈希。(不管状态是正常还是禁用都在里面),用于管理员编辑
    const ADMIN_ROLE_ID = 'laravel_admin_role_id';
    // 管理员所拥有的权限ID，由于多个管理员，所以使用哈希。(不管状态是正常还是禁用都在里面),用于管理员编辑
    const ADMIN_PERMISSION_ID = 'laravel_admin_permission_id';
    // 管理员所拥有的权限标识，由于多个管理员，所以使用哈希。用于鉴权功能(所以只能拿状态正常的数据)
    const ADMIN_PERMISSION_SLUG = 'laravel_admin_permission_slug';
    // 管理员所拥有的权限菜单，由于多个管理员，所以使用哈希。用于菜单构建功能，这里会直接转字符串
    const ADMIN_PERMISSION_MENU = 'laravel_admin_permission_menu';

    // 配置项,键值对数组格式存储
    const CONFIG = 'laravel_config';

    // 单例模式实例
    private static $instance = null;

    // 获取单例模式
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Cache();
        }
        return self::$instance;
    }

    // 清空所有缓存
    public function clear()
    {
        Redis::del(self::ALL_PERMISSION);
        Redis::del(self::ALL_PERMISSION_MENU);
        Redis::del(self::ALL_ROLE);
        Redis::del(self::ROLE_PERMISSION_ID);
        Redis::del(self::ROLE_PERMISSION_SLUG);
        Redis::del(self::ROLE_PERMISSION_MENU);
        Redis::del(self::ADMIN_ROLE_ID);
        Redis::del(self::ADMIN_PERMISSION_ID);
        Redis::del(self::ADMIN_PERMISSION_SLUG);
        Redis::del(self::ADMIN_PERMISSION_MENU);
        Redis::del(self::CONFIG);
    }

    // 清空管理员缓存,用于管理员的修改和删除
    public function clearAdmin($adminId = 0)
    {
        $this->updateAdminRoleId($adminId);
        $this->updateAdminPermissionId($adminId);
        $this->updateAdminPermissionSlug($adminId);
        $this->updateAdminPermissionMenu($adminId);
    }

    // 清空角色的缓存，用于角色的修改和删除
    public function clearRole($roleId = 0)
    {
        $this->updateAllRole();
        $this->updateRolePermissionId($roleId);
        $this->updateRolePermissionSlug($roleId);
        $this->updateRolePermissionMenu($roleId);
        // 更新所有管理员的缓存
        $this->clearAdmin();
    }

    // 清空权限缓存，用于权限的添加，修改，删除
    public function clearPermission()
    {
        $this->updateAllPermission();
        $this->updateAllPermissionMenu();
        // 更新角色，角色又会去更新管理员的
        $this->clearRole();
    }

    // 更新所有角色
    public function updateAllRole()
    {
        $allRole = Role::get()->toArray();
        $allRole = serialize($allRole);
        Redis::set(self::ALL_ROLE, $allRole);
    }

    // 获取所有角色
    public function getAllRole()
    {
        static $allRole = null;
        if ($allRole === null) {
            if (!Redis::exists(self::ALL_ROLE)) {
                $this->updateAllRole();
            }
            $allRole = Redis::get(self::ALL_ROLE);
            $allRole = unserialize($allRole);
        }
        return $allRole;
    }

    // 更新所有权限
    public function updateAllPermission()
    {
        $allPermission = Permission::orderBy('sort', 'ASC')->get()->toArray();
        $allPermission = arraySort($allPermission);
        arraySort([], 0, 0, true);
        $allPermission = serialize($allPermission);
        Redis::set(self::ALL_PERMISSION, $allPermission);
    }

    // 获取所有权限
    public function getAllPermission()
    {
        static $allPermission = null;
        if ($allPermission === null) {
            if (!Redis::exists(self::ALL_PERMISSION)) {
                $this->updateAllPermission();
            }
            $allPermission = Redis::get(self::ALL_PERMISSION);
            $allPermission = unserialize($allPermission);
        }
        return $allPermission;
    }

    // 更新所有菜单
    public function updateAllPermissionMenu()
    {
        $allPermissionMenu = Permission::where('is_menu', Permission::IS_MENU_ON)->orderBy('sort', 'ASC')->get()->toArray();
        $allPermissionMenu = arraySort($allPermissionMenu);
        arraySort([], 0, 0, true);
        $allPermissionMenu = serialize($allPermissionMenu);
        Redis::set(self::ALL_PERMISSION_MENU, $allPermissionMenu);
    }

    // 获取所有菜单
    public function getAllPermissionMenu()
    {
        static $allPermissionMenu = null;
        if ($allPermissionMenu === null) {
            if (!Redis::exists(self::ALL_PERMISSION_MENU)) {
                $this->updateAllPermissionMenu();
            }
            $allPermissionMenu = Redis::get(self::ALL_PERMISSION_MENU);
            $allPermissionMenu = unserialize($allPermissionMenu);
        }
        return $allPermissionMenu;
    }

    // 更新角色拥有的权限ID
    public function updateRolePermissionId($roleId = 0)
    {
        // roleId为0的话表示更新所有角色拥有的权限ID，一次更新所有角色拥有的权限ID代价可能有点大，
        // 这里就直接删除掉这个键，等获取时，获取不到指定角色时再获取那个指定角色的权限ID
        if ($roleId == 0) {
            Redis::del(self::ROLE_PERMISSION_ID);
            return;
        }
        $permissionId = RolePermission::where('role_id', $roleId)->pluck('permission_id')->toArray();
        $permissionId = serialize($permissionId);
        Redis::hSet(self::ROLE_PERMISSION_ID, $roleId, $permissionId);
    }

    // 获取角色拥有的权限ID
    public function getRolePermissionId($roleId = 0)
    {
        static $rolePermissionId = [];
        if (!isset($rolePermissionId[$roleId])) {
            if (!Redis::hExists(self::ROLE_PERMISSION_ID, $roleId)) {
                $this->updateRolePermissionId($roleId);
            }
            $rolePermissionId[$roleId] = Redis::hGet(self::ROLE_PERMISSION_ID, $roleId);
            $rolePermissionId[$roleId] = unserialize($rolePermissionId[$roleId]);
        }
        return $rolePermissionId[$roleId];
    }

    // 更新角色拥有的权限标识
    public function updateRolePermissionSlug($roleId = 0)
    {
        // roleId为0的话表示更新所有角色拥有的权限标识，一次更新所有角色拥有的权限标识代价可能有点大，
        // 这里就直接删除掉这个键，等获取时，获取不到指定角色时再获取那个指定角色的权限标识
        if ($roleId == 0) {
            Redis::del(self::ROLE_PERMISSION_SLUG);
            return;
        }
        // 获取正常的权限标识，角色必须也得正常
        $role = Role::with(['permission'=>function($query){
            $query->where('status', Permission::STATUS_NORMAL);
        }])->where('id', $roleId)->where('status', Role::STATUS_NORMAL)->first();
        // 如果这个角色不正常也就是说这个角色没有任何权限
        if (!$role) {
            Redis::hSet(self::ROLE_PERMISSION_SLUG, $roleId, serialize([]));
            return;
        }
        // 拿到权限标识
        $slug = [];
        foreach ($role->permission as $value) {
            $slug[] = $value->slug;
        }
        // 由于权限标识可能为空字符串，为了节省内存和冗余数据就过滤一下
        $slug = array_filter($slug);
        $slug = serialize($slug);
        Redis::hSet(self::ROLE_PERMISSION_SLUG, $roleId, $slug);
    }

    // 获取角色拥有的权限标识
    public function getRolePermissionSlug($roleId = 0)
    {
        static $rolePermissionSlug = [];
        if (!isset($rolePermissionSlug[$roleId])) {
            if (!Redis::hExists(self::ROLE_PERMISSION_SLUG, $roleId)) {
                $this->updateRolePermissionSlug($roleId);
            }
            $rolePermissionSlug[$roleId] = Redis::hGet(self::ROLE_PERMISSION_SLUG, $roleId);
            $rolePermissionSlug[$roleId] = unserialize($rolePermissionSlug[$roleId]);
        }
        return $rolePermissionSlug[$roleId];
    }

    // 更新角色拥有的权限菜单
    public function updateRolePermissionMenu($roleId = 0)
    {
        // roleId为0的话表示更新所有角色拥有的权限菜单，一次更新所有角色拥有的权限菜单代价可能有点大，
        // 这里就直接删除掉这个键，等获取时，获取不到指定角色时再获取那个指定角色的权限菜单
        if ($roleId == 0) {
            Redis::del(self::ROLE_PERMISSION_MENU);
            return;
        }
        // 获取正常的权限菜单，角色必须也得正常
        $role = Role::where('id', $roleId)->where('status', Role::STATUS_NORMAL)->first();
        // 如果这个角色不正常也就是说这个角色没有任何菜单
        if (!$role) {
            Redis::hSet(self::ROLE_PERMISSION_MENU, $roleId, serialize([]));
            return;
        }
        // 获取这个角色所有权限ID
        $permissionId = RolePermission::where('role_id', $roleId)->pluck('permission_id')->toArray();
        $permissionId = Permission::whereIn('id', $permissionId)->where('status', Permission::STATUS_NORMAL)->where('is_menu', Permission::IS_MENU_ON)->get()->toArray();
        $permissionId = serialize($permissionId);
        Redis::hSet(self::ROLE_PERMISSION_MENU, $roleId, $permissionId);
    }

    // 获取角色拥有的权限菜单
    public function getRolePermissionMenu($roleId = 0)
    {
        static $rolePermissionMenu = [];
        if (!isset($rolePermissionMenu[$roleId])) {
            if (!Redis::hExists(self::ROLE_PERMISSION_MENU, $roleId)) {
                $this->updateRolePermissionMenu($roleId);
            }
            $rolePermissionMenu[$roleId] = Redis::hGet(self::ROLE_PERMISSION_MENU, $roleId);
            $rolePermissionMenu[$roleId] = unserialize($rolePermissionMenu[$roleId]);
        }
        return $rolePermissionMenu[$roleId];
    }

    // 更新管理员拥有的角色ID
    public function updateAdminRoleId($adminId = 0)
    {
        // adminId为0的话表示更新所有管理员拥有的角色ID，一次更新所有管理员拥有的角色ID代价可能有点大，
        // 这里就直接删除掉这个键，等获取时，获取不到指定管理员时再获取那个指定管理员的角色ID
        if ($adminId == 0) {
            Redis::del(self::ADMIN_ROLE_ID);
            return;
        }
        // 拿到角色ID
        $roleId = RoleAdmin::where('admin_id', $adminId)->pluck('role_id')->toArray();
        $roleId = serialize($roleId);
        Redis::hSet(self::ADMIN_ROLE_ID, $adminId, $roleId);
    }

    // 获取管理员拥有的角色ID
    public function getAdminRoleId($adminId = 0)
    {
        static $adminRoleId = [];
        if (!isset($adminRoleId[$adminId])) {
            if (!Redis::hExists(self::ADMIN_ROLE_ID, $adminId)) {
                $this->updateAdminRoleId($adminId);
            }
            $adminRoleId[$adminId] = Redis::hGet(self::ADMIN_ROLE_ID, $adminId);
            $adminRoleId[$adminId] = unserialize($adminRoleId[$adminId]);
        }
        return $adminRoleId[$adminId];
    }

    // 更新管理员拥有的权限ID
    public function updateAdminPermissionId($adminId = 0)
    {
        // adminId为0的话表示更新所有管理员拥有的角色ID，一次更新所有管理员拥有的角色ID代价可能有点大，
        // 这里就直接删除掉这个键，等获取时，获取不到指定管理员时再获取那个指定管理员的角色ID
        if ($adminId == 0) {
            Redis::del(self::ADMIN_PERMISSION_ID);
            return;
        }
        // 拿到权限ID
        $permissionId = AdminPermission::where('admin_id', $adminId)->pluck('permission_id')->toArray();
        $permissionId = serialize($permissionId);
        Redis::hSet(self::ADMIN_PERMISSION_ID, $adminId, $permissionId);
    }

    // 获取管理员拥有的权限ID
    public function getAdminPermissionId($adminId = 0)
    {
        static $adminPermissionId = [];
        if (!isset($adminPermissionId[$adminId])) {
            if (!Redis::hExists(self::ADMIN_PERMISSION_ID, $adminId)) {
                $this->updateAdminPermissionId($adminId);
            }
            $adminPermissionId[$adminId] = Redis::hGet(self::ADMIN_PERMISSION_ID, $adminId);
            $adminPermissionId[$adminId] = unserialize($adminPermissionId[$adminId]);
        }
        return $adminPermissionId[$adminId];
    }

    // 更新管理员拥有的权限标识
    public function updateAdminPermissionSlug($adminId = 0)
    {
        // adminId为0的话表示更新所有管理员拥有的权限标识，一次更新所有管理员拥有的权限标识代价可能有点大，
        // 这里就直接删除掉这个键，等获取时，获取不到指定管理员时再获取那个指定管理员的权限标识
        if ($adminId == 0) {
            Redis::del(self::ADMIN_PERMISSION_SLUG);
            return;
        }
        // 默认管理员不受权限控制，拥有所有权限标识。
        if ($adminId == 1) {
            $slug = Permission::pluck('slug')->toArray();
            $slug = array_filter($slug);
            $slug = serialize($slug);
            Redis::hSet(self::ADMIN_PERMISSION_SLUG, $adminId, $slug);
            return;
        }
        // 拥有的所有权限标识
        $slug = [];
        // 获取这个管理员的所有角色ID
        $role = $this->getAdminRoleId($adminId);
        foreach ($role as $val) {
            // 获取每个管理员的正常权限标识
            $_slug = $this->getRolePermissionSlug($val);
            $slug = array_merge($slug, $_slug);
        }
        // 清除多余变量，和去重权限标识
        unset($role, $_slug);
        $slug = array_unique($slug);
        $slug = array_filter($slug);

        // 再来拿直接拥有的权限标识(里面包含了禁用了的权限ID所以得过滤一下)
        $permissionId = $this->getAdminPermissionId($adminId);
        $permissionId = Permission::whereIn('id', $permissionId)->where('status', Permission::STATUS_NORMAL)->pluck('slug')->toArray();
        // 合并角色拥有的，和直接拥有的标识
        $permissionId = array_filter($permissionId);
        $slug = array_merge($slug, $permissionId);
        $slug = array_unique($slug);
        $slug = serialize($slug);
        Redis::hSet(self::ADMIN_PERMISSION_SLUG, $adminId, $slug);
    }

    // 获取管理员拥有的权限标识
    public function getAdminPermissionSlug($adminId = 0)
    {
        static $adminPermissionSlug = [];
        if (!isset($adminPermissionSlug[$adminId])) {
            if (!Redis::hExists(self::ADMIN_PERMISSION_SLUG, $adminId)) {
                $this->updateAdminPermissionSlug($adminId);
            }
            $adminPermissionSlug[$adminId] = Redis::hGet(self::ADMIN_PERMISSION_SLUG, $adminId);
            $adminPermissionSlug[$adminId] = unserialize($adminPermissionSlug[$adminId]);
        }
        return $adminPermissionSlug[$adminId];
    }

    // 更新管理员拥有的权限菜单
    public function updateAdminPermissionMenu($adminId = 0)
    {
        // adminId为0的话表示更新所有管理员拥有的权限菜单，一次更新所有管理员拥有的权限菜单代价可能有点大，
        // 这里就直接删除掉这个键，等获取时，获取不到指定管理员时再获取那个指定管理员的权限菜单
        if ($adminId == 0) {
            Redis::del(self::ADMIN_PERMISSION_MENU);
            return;
        }
        // 默认管理员不受权限控制，拥有所有权限标识。
        if ($adminId == 1) {
            $permissionMenu = $this->getAllPermissionMenu();
            $permissionMenu = toMultiArray($permissionMenu);
            $permissionMenu = toMenuHtml($permissionMenu);
            $permissionMenu = serialize($permissionMenu);
            Redis::hSet(self::ADMIN_PERMISSION_MENU, $adminId, $permissionMenu);
            return;
        }
        // 拥有的所有权限菜单
        $permissionMenu = [];
        // 获取这个管理员的所有角色ID
        $role = $this->getAdminRoleId($adminId);
        foreach ($role as $val) {
            // 获取每个管理员的正常权限菜单
            $_permissionMenu = $this->getRolePermissionMenu($val);
            $permissionMenu = array_merge($permissionMenu, $_permissionMenu);
        }
        // 清除多余变量，和去重权限标识
        unset($role, $_permissionMenu);
        // 这里需要注意，某些php版本中,如果数组中的值是整型则会报Array to string conversion，所以加上SORT_REGULAR
        $permissionMenu = array_unique($permissionMenu, SORT_REGULAR);

        // 再来拿直接拥有的权限权限(里面包含了禁用了的权限ID所以得过滤一下)
        $permissionId = $this->getAdminPermissionId($adminId);
        $permission = Permission::whereIn('id', $permissionId)->where('status', Permission::STATUS_NORMAL)->where('is_menu', Permission::IS_MENU_ON)->get()->toArray();
        // 合并角色拥有的，和直接拥有的标识
        $permission = array_filter($permission);
        $permissionMenu = array_merge($permissionMenu, $permission);
        // 这里需要注意，某些php版本中,如果数组中的值是整型则会报Array to string conversion，所以加上SORT_REGULAR
        $permissionMenu = array_unique($permissionMenu, SORT_REGULAR);
        // 转换字符串菜单
        $permissionMenu = toMultiArray($permissionMenu);
        $permissionMenu = toMenuHtml($permissionMenu);
        $permissionMenu = serialize($permissionMenu);
        Redis::hSet(self::ADMIN_PERMISSION_MENU, $adminId, $permissionMenu);
    }

    // 获取管理员拥有的权限菜单
    public function getAdminPermissionMenu($adminId = 0)
    {
        static $adminPermissionMenu = [];
        if (!isset($adminPermissionMenu[$adminId])) {
            if (!Redis::hExists(self::ADMIN_PERMISSION_MENU, $adminId)) {
                $this->updateAdminPermissionMenu($adminId);
            }
            $adminPermissionMenu[$adminId] = Redis::hGet(self::ADMIN_PERMISSION_MENU, $adminId);
            $adminPermissionMenu[$adminId] = unserialize($adminPermissionMenu[$adminId]);
        }
        return $adminPermissionMenu[$adminId];
    }

    // 更新配置项
    public function updateConfig()
    {
        $config = Config::pluck('value', 'variable')->toArray();
        $config = serialize($config);
        Redis::set(self::CONFIG, $config);
    }

    // 获取配置项
    public function getConfig($key = null, $default = null)
    {
        static $config = null;
        if ($config === null) {
            if (!Redis::exists(self::CONFIG)) {
                $this->updateConfig();
            }
            $config = Redis::get(self::CONFIG);
            $config = unserialize($config);
        }
        if ($key === null) {
            return $config;
        } else {
            return $config[$key] ?? $default;
        }
    }
}
