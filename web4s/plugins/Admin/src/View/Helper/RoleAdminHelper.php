<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;

class RoleAdminHelper extends Helper
{   
    public function getList()
    {
        $roles = TableRegistry::get('Roles')->find()->where(['deleted' => 0])->select(['id', 'name'])->toArray();
        if(empty($roles)) return [];
        $list_role = Hash::combine($roles, '{n}.id', '{n}.name');
        return $list_role;
    }

    public function getPermissionAllRouter()
    {
        $all_routes = Router::routes();
        if(empty($all_routes)) return [];

        // lấy danh sách router admin        
        $routes = [];
        foreach($all_routes as $route){
            $url = !empty($route->template) ? $route->template : null;
            $action = !empty($route->defaults) ? $route->defaults : [];

            if(empty($url) || strpos($url, ADMIN_PATH) === false) continue;
            $routes[$url] = $action;
        }
        
        if(empty($routes)) return [];

        // kiểm tra quyền cho tất cả các router theo tài khoản hiện tại
        $result = [];
        $table = TableRegistry::get('Roles');
        $auth = $this->getView()->get('auth_user');    
        $role_id = !empty($auth['role_id']) ? intval($auth['role_id']) : null;
        foreach($routes as $url => $item){
            $controller = !empty($item['controller']) ? $item['controller'] : null;
            $action = !empty($item['action']) ? $item['action'] : null;
            $result[$url] = $table->checkPermissionRequest($role_id, $controller, $action);
        }
        
        return $result;
    }
}