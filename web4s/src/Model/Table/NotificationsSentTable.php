<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;

class NotificationsSentTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('notifications_sent');
        $this->setPrimaryKey('id');

        $this->belongsTo('User', [
            'className' => 'Users',
            'foreignKey' => 'created_by',
            'propertyName' => 'User'
        ]);

        $this->hasOne('Notifications', [
            'className' => 'Notifications',
            'foreignKey' => 'id',
            'bindingKey' => 'notification_id',
            'joinType' => 'INNER',
            'propertyName' => 'Notifications'
        ]);

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);
    }

    public function queryListNotificationsSent($params = []) 
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

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['NotificationsSent.id', 'Notifications.title'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['NotificationsSent.id', 'NotificationsSent.notification_id', 'NotificationsSent.platform', 'NotificationsSent.token', 'NotificationsSent.created', 'NotificationsSent.created', 'NotificationsSent.created_by', 'Notifications.title'];
            break;
        }

        $sort_string = 'NotificationsSent.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'NotificationsSent.id '. $sort_type;
                break;  
            }
        }

        $contain = ['Notifications'];

        // filter by conditions
        $where = [];

        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }

        return $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }


}