<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Core\Configure;
use Cake\Utility\Text;

class NotificationsSubscribeTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('notifications_subscribe');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->hasOne('Customers', [
            'className' => 'Customers',
            'foreignKey' => 'id',
            'bindingKey' => 'customer_id',
            'propertyName' => 'Customers'
        ]);

        $this->hasOne('Users', [
            'className' => 'Users',
            'foreignKey' => 'id',
            'bindingKey' => 'user_admin_id',
            'propertyName' => 'Users'
        ]);

    }

    public function queryListNotificationsSubscribe($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_customer = !empty($params['get_customer']) ? $params['get_customer'] : false;
        $get_user_admin = !empty($params['get_user_admin']) ? $params['get_user_admin'] : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $user_admin_id = !empty($filter['user_admin_id']) ? intval($filter['user_admin_id']) : null;
        $customer_id = !empty($filter['customer_id']) ? intval($filter['customer_id']) : null;

        $platform = !empty($filter['platform']) ? $filter['platform'] : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['NotificationsSubscribe.id', 'NotificationsSubscribe.token'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['NotificationsSubscribe.id', 'NotificationsSubscribe.token', 'NotificationsSubscribe.platform', 'NotificationsSubscribe.browser', 'NotificationsSubscribe.user_agent', 'NotificationsSubscribe.customer_id', 'NotificationsSubscribe.user_admin_id', 'NotificationsSubscribe.created', 'NotificationsSubscribe.updated'];
            break;
        }

        $sort_string = 'NotificationsSubscribe.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'NotificationsSubscribe.id '. $sort_type;
                break;     
            }
        }

        // filter by conditions
        $where = $contain = [];    

        if (!empty($customer_id)) {
            $where['NotificationsSubscribe.customer_id'] = $customer_id;
        }

        if (!empty($user_admin_id)) {
            $where['NotificationsSubscribe.user_admin_id'] = $user_admin_id;
        }

        if (!empty($platform)) {
            $where['NotificationsSubscribe.platform'] = $platform;
        }

        if($get_customer){
            $contain[] = 'Customers';
            $where['Customers.deleted'] = 0;
            
            $fields[] = 'Customers.full_name';
        }

        if($get_user_admin){
            $fields[] = 'Users.full_name';
            $contain[] = 'Users';
        }
        return $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }

    public function getInfoByToken($token = null)
    {
        if(empty($token)) return [];
        return $this->find()->where(['token' => $token])->first();
    }

}