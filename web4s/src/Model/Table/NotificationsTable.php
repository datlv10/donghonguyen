<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Utility\Hash;
use Cake\Cache\Cache;
use Cake\I18n\FrozenTime;

class NotificationsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('notifications');
        $this->setPrimaryKey('id');

        $this->belongsTo('User', [
            'className' => 'Users',
            'foreignKey' => 'created_by',
            'propertyName' => 'User'
        ]);

        $this->hasMany('NotificationsSent', [
            'className' => 'NotificationsSent',
            'foreignKey' => 'notification_id',
            'joinType' => 'LEFT',
            'propertyName' => 'NotificationsSent'
        ]);

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);
    }

    public function queryListNotifications($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_user = !empty($params['get_user']) ? $params['get_user'] : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $type = !empty($filter['type']) ? trim($filter['type']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['Notifications.id', 'Notifications.title'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['Notifications.id', 'Notifications.type', 'Notifications.title', 'Notifications.body', 'Notifications.link', 'Notifications.image', 'Notifications.icon', 'Notifications.mobile_action', 'Notifications.created', 'Notifications.created_by', 'Notifications.sent', 'Notifications.status'];
            break;
        }

        $sort_string = 'Notifications.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Notifications.id '. $sort_type;
                break;  
            }
        }
        $contain = [];

        // filter by conditions
        $where = [];

        if(!empty($keyword)){
            $where['Notifications.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['Notifications.status'] = $status;
        }

        if(!empty($type)){
            switch($type){                
                case WEBSITE:
                case MOBILE_APP:
                    $where['Notifications.type IN'] = [ALL, $type];
                break;

                case ALL:                    
                default:
                    $where['Notifications.type'] = ALL;
                break;
            }
        }
        

        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }

        return $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }

    public function checkExistNotification($type = null)
    {
        if(empty($type) || !in_array($type, Configure::read('LIST_TYPE_NOTIFICATION'))) return 0;

        $cache_key = NOTIFICATION . '_' . $type . '_exist';
        $result = Cache::read($cache_key);
        if(is_null($result)){
            $where = ['Notifications.status' => 1];

            switch($type){
                case ALL:
                    $where['Notifications.type'] = ALL;
                break;

                case WEBSITE:
                case MOBILE_APP:
                    $where['Notifications.type IN'] = [ALL, $type];
                break;
            }
            $notification = $this->find()->where($where)->select(['Notifications.id'])->first();

            $result = !empty($notification) ? 1 : 0;
            Cache::write($cache_key, $result);
        }

        return $result;
    }

    public function getLastTimeNotification($type = null)
    {
        if(empty($type) || !in_array($type, Configure::read('LIST_TYPE_NOTIFICATION'))) return 0;

        $cache_key = NOTIFICATION . '_' . $type . '_last_time';
        $result = Cache::read($cache_key);
        if(is_null($result)){
            $where = ['Notifications.status' => 1];

            switch($type){
                case ALL:
                    $where['Notifications.type'] = ALL;
                break;

                case WEBSITE:
                case MOBILE_APP:
                    $where['Notifications.type IN'] = [ALL, $type];
                break;
            }
            $notification = $this->find()->where($where)->select(['Notifications.created'])->order('Notifications.id DESC')->first();
            $result = !empty($notification['created']) ? intval($notification['created']) : null;
            Cache::write($cache_key, $result);
        }

        return $result;
    }

    public function parseTimeComment($time = null)
    {
        $result = [
            'time' => '',
            'full_time' => ''
        ];

        if(empty($time)){
            return $result;
        }

        $time = date('Y-m-d H:i:s', $time);
        $time_input = new FrozenTime($time);
        $now = new FrozenTime();


        $interval = $now->diff($time_input);
        if (!empty($interval->format('%i'))) {
            $result['time'] = $interval->format('%i') . ' ' . __d('template', 'phut_truoc');
        }

        if (!empty($interval->format('%h'))) {
            $result['time'] = $interval->format('%h') . ' ' . __d('template', 'gio_truoc');
        }        

        if (!empty($interval->format('%d'))) {
            $result['time'] = $interval->format('%d') . ' ' . __d('template', 'ngay_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }

        if (!empty($interval->format('%m'))) {
            $result['time'] = $interval->format('%m') . ' ' . __d('template', 'thang_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }


        if (!empty($interval->format('%y'))) {
            $result['time'] = $interval->format('%y') . ' ' . __d('template', 'nam_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }

        if (empty($result['time'])) {
            $result['time'] = __d('template', 'vua_xong');
        }

        $result['full_time'] = str_replace('MONTH', __d('template', 'thang'), trim($result['full_time']));
        $result['full_time'] = str_replace('AT', __d('template', 'luc'), trim($result['full_time']));

        return $result;
    }

}





