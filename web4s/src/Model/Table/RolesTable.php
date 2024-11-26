<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Cache\Cache;

class RolesTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('roles');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name')
            ->notEmptyString('name');

        return $validator;
    }

    public function queryListRoles($params = []) 
    {

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['Roles.id', 'Roles.name', 'Roles.short_description', 'Roles.permission', 'Roles.created', 'Roles.updated', 'Roles.created_by', 'Roles.order'];
            break;

            case LIST_INFO:
                $fields = ['Roles.id', 'Roles.name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Roles.id', 'Roles.name', 'Roles.short_description', 'Roles.updated', 'Roles.created_by', 'Roles.order'];
            break;
        }

        // filter by conditions
        $where = ['Roles.deleted' => 0];        
        $filter = !empty($params['filter']) ? $params['filter'] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;

        if(!empty($keyword)){
            $where['Roles.name LIKE'] = '%' . $keyword . '%';
        }

        return TableRegistry::get('Roles')->find()->where($where)->select($fields);
    }

    public function checkExistName($name = null, $id = null) 
    {
        if(empty($name)) return false;

        $where = [
            'deleted' => 0,
            'name' => $name
        ];

        if(!empty($id)){
            $where['id !='] = $id;
        }

        $role = TableRegistry::get('Roles')->find()->where($where)->first();

        return !empty($role->id) ? true :false;
    }

    private function _getAllPermission()
    {
        $result = [];
        $roles = TableRegistry::get('Roles')->find()->where([
            'Roles.deleted' => 0
        ])->select([
            'Roles.id', 'Roles.permission'
        ])->toArray();

        
        if(!empty($roles)){
            foreach ($roles as $key => $role) {
                $role_id = !empty($role['id']) ? intval($role['id']) : null;
                if(empty($role_id)) continue;
                $result[$role_id] = !empty($role['permission']) ? json_decode($role['permission'], true) : [];
            }
        }

        return $result;
    }

    public function getAllPermission($read_cache = true)
    {
        // option read_cache dùng trong trường hợp không cần lấy dữ liệu từ cache file
        if(!$read_cache){
            return $this->_getAllPermission();
        }

        $cache_key = ROLE . '_permission';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $result = $this->_getAllPermission();
            Cache::write($cache_key, $result);
        }

        return $result;
    }

    public function getPermissionDefault()
    {
        $plugins = TableRegistry::get('Plugins')->getList();
        $result = Configure::read('ARTICLE_TYPE_CONTROLLER');
        if(!empty($plugins[PRODUCT])){
            $result = array_merge($result, Configure::read('PRODUCT_TYPE_CONTROLLER'));
        }

        if(!empty($plugins[MOBILE_APP])){
            $result = array_merge($result, Configure::read('MOBILE_APP_TYPE_CONTROLLER'));
        }

        if(!empty($plugins[PROMOTION])){
            $result = array_merge($result, Configure::read('PROMOTION_TYPE_CONTROLLER'));
        }

        if(!empty($plugins[POINT])){
            $result = array_merge($result, Configure::read('POINT_TYPE_CONTROLLER'));
        }

        if(!empty($plugins[AFFILIATE])){
            $result = array_merge($result, Configure::read('AFFILIATE_TYPE_CONTROLLER'));
        }

        $result = array_merge($result, Configure::read('BASE_CONTROLLER'));

        return $result;
    }

    public function checkPermissionRequest($role_id = null, $controller = null, $action = null)
    {
        // tài khoản supper admin thì có tất cả quyền
        if(defined('SUPPER_ADMIN') && SUPPER_ADMIN) return true;

        if(empty($role_id)) return false;
        if(empty($controller) || empty($action)) return true;

        // trong trường hợp khi ta lưu cấu hình phân quyền thì sẽ không đọc thông tin từ file cache nữa
        // vì nếu đọc thì file cache role_permission sẽ không thể xóa
        $read_cache = true;
        if($controller == 'Role' && $action == 'permissionSave'){
            $read_cache = false;
        }

        $default = $this->getPermissionDefault();
        $configed = $this->getAllPermission($read_cache);

        // nếu cấu hình mặc định không có controller thì trả về true
        if(!isset($default[$controller])) return true;

        // Kiểm tra action của request có nằm trong cấu hình mặc định hay không    
        $permission = null;
        if(!empty($default[$controller])){
            $check_action = false;
            foreach($default[$controller] as $type => $list_action){
                if(in_array($action, $list_action)){
                    $check_action = true;
                    $permission = $type;
                    break;
                }
            }

            // nếu action của request không nằm trong cấu hình mặc định thì trả về true
            if(!$check_action) return true;
        }



        // nếu không có cấu hình quyền cho role_id thì trả về false
        if(empty($configed[$role_id])) return false;


        // nếu cấu hình mặc định của controller bằng [] -> thì chỉ cho phép cấu hình quyền 'all' mới trả về true
        if(empty($default[$controller]) && empty($configed[$role_id][$controller]['all'])) return false;
        if(empty($default[$controller]) && !empty($configed[$role_id][$controller]['all'])) return true;



        // nếu cấu hình quyền không có controller thì trả về false
        if(empty($configed[$role_id][$controller])) return false;

        // Kiểm tra quyền hiện tại có nằm trong cấu hình
        if(empty($configed[$role_id][$controller][$permission])) return false;

        return true;
    }
}