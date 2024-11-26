<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Cache\Cache;
use Cake\I18n\FrozenTime;

class NhNotificationsTable extends AppTable
{
    private $limit = 7;
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('nh_notifications');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);
    }

    public function queryListNhNotifications($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $type = !empty($filter['type']) ? trim($filter['type']) : null;
        $group_notification = !empty($filter['group_notification']) ? trim($filter['group_notification']) : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['NhNotifications.id', 'NhNotifications.title'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['NhNotifications.id', 'NhNotifications.type', 'NhNotifications.title', 'NhNotifications.link', 'NhNotifications.created'];
            break;
        }

        $sort_string = 'NhNotifications.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'NhNotifications.id '. $sort_type;
                break;  
            }
        }

        // filter by conditions
        $where = [];

        if(!empty($type)){
            $where['NhNotifications.type'] = $type;
        }

        if(!empty($group_notification)){
            $where['NhNotifications.group_notification'] = $group_notification;
        }

        return $this->find()->where($where)->select($fields)->order($sort_string);
    }

    public function getLastTimeNotification()
    {
        $cache_key = NH_NOTIFICATION .'_last_time';
        $result = Cache::read($cache_key);
        if(is_null($result)){
            $notification = $this->find()->select(['NhNotifications.created'])->order('NhNotifications.id DESC')->first();
            $result = !empty($notification['created']) ? intval($notification['created']) : null;
            Cache::write($cache_key, $result);
        }

        return $result;
    }

    public function parseTimeNotification($time = null)
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
            $result['time'] = $interval->format('%i') . ' ' . __d('admin', 'phut_truoc');
        }

        if (!empty($interval->format('%h'))) {
            $result['time'] = $interval->format('%h') . ' ' . __d('admin', 'gio_truoc');
        }        

        if (!empty($interval->format('%d'))) {
            $result['time'] = $interval->format('%d') . ' ' . __d('admin', 'ngay_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }

        if (!empty($interval->format('%m'))) {
            $result['time'] = $interval->format('%m') . ' ' . __d('admin', 'thang_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }


        if (!empty($interval->format('%y'))) {
            $result['time'] = $interval->format('%y') . ' ' . __d('admin', 'nam_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }

        if (empty($result['time'])) {
            $result['time'] = __d('admin', 'vua_xong');
        }

        $result['full_time'] = str_replace('MONTH', __d('admin', 'thang'), trim($result['full_time']));
        $result['full_time'] = str_replace('AT', __d('admin', 'luc'), trim($result['full_time']));

        return $result;
    }

    public function getFirstPageNotifcation($group = null)
    {
        if(!in_array($group, ['my_notification', 'general'])) return [];

        $cache_key = NH_NOTIFICATION . '_first_page_' . $group ;
        $result = Cache::read($cache_key);
        if(is_null($result)){
            if($group == 'my_notification'){
                $params = [FILTER => ['group_notification' => 'my_notification']];
            }

            if($group == 'general'){
                $params = [FILTER => ['group_notification' => 'general']];
            }

            $result = $this->queryListNhNotifications($params)->limit($this->limit)->toArray();
            Cache::write($cache_key, !empty($result) ? $result : []);
        }

        return $result;
    }

    public function existmorePageNotifcation($group = null)
    {
        if(!in_array($group, ['my_notification', 'general'])) return false;

        $cache_key = NH_NOTIFICATION . '_exist_more_page_' . $group ;
        $result = Cache::read($cache_key);
        if(is_null($result)){
            if($group == 'my_notification'){
                $params = [FILTER => ['group_notification' => 'my_notification']];
            }

            if($group == 'general'){
                $params = [FILTER => ['group_notification' => 'general']];
            }

            $count = $this->queryListNhNotifications($params)->count();            
            Cache::write($cache_key, $count > $this->limit ? true : false);
        }

        return $result;
    }

    public function addMyNotificationCallback($id = null, $type = null)
    {
        if(empty($id) || empty($type) || !in_array($type, ['order', 'contact'])) return false;

        $title = $link = null;

        // thêm đơn mới
        if($type == 'order'){
            $order_info = TableRegistry::get('Orders')->find()->where(['id' => $id])->select(['id', 'code'])->first();
            if(empty($order_info)) return false;

            $code = !empty($order_info['code']) ? $order_info['code'] : 'ORD';

            $title = "Đơn hàng mới $code";
            $link = ADMIN_PATH . "/order/detail/$id";
        }

        // thêm liên hệ 
        if($type == 'contact'){
            $contact_info = TableRegistry::get('Contacts')->find()->where(['id' => $id])->select(['id', 'form_id'])->first();
            $form_id = !empty($contact_info['form_id']) ? $contact_info['form_id'] : null;
            if(empty($contact_info) || empty($form_id)) return false;

            $form_info = TableRegistry::get('ContactsForm')->find()->where(['id' => $form_id])->select(['id', 'send_email', 'name'])->first();
            $name = !empty($form_info['name']) ? $form_info['name'] : 'khách hàng';
            if(empty($form_info['send_email'])) return false;

            $title = "Liên hệ mới từ $name";
            $link = ADMIN_PATH . "/contact/detail/$id";
        }


        if(empty($title) || empty($link)) return false;


        $entity = $this->newEntity([
            'type' => $type,
            'group_notification' => 'my_notification',
            'title' => $title,
            'link' => $link
        ]);

        try{
            $this->save($entity);
            return true;

        }catch (Exception $e) {
            debug($e->getMessage());
            return false;
        }
    }









}