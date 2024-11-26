<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\WebPushConfig;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Kreait\Firebase\Exception\Messaging\NotFound;

class NotificationController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function send()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Notifications');

        $notification_id = !empty($data['notification_id']) ? intval($data['notification_id']) : null;
        $platform = !empty($data['platform']) ? $data['platform'] : null;
        $token = !empty($data['token']) ? trim($data['token']) : null;

        if(empty($notification_id)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $notification_info = $table->find()->where(['id' => $notification_id])->first();
        if(empty($notification_info)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);   
        }

        if($platform == 'token'){
            if(empty($token)){
                $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);    
            }

            $subscribe_info = TableRegistry::get('NotificationsSubscribe')->getInfoByToken($token);
            if(empty($subscribe_info)){
                $this->responseJson([MESSAGE => __d('admin', 'token_khong_ton_tai_tren_he_thong')]);   
            }            
        }else{
            $token = null;
        }

        $notification_component = $this->loadComponent('Admin.Notification');

        $messaging = $notification_component->initFirebaseMessaging();
        if(empty($messaging)){
            $this->responseJson([MESSAGE => __d('admin', 'khoi_tao_{0}_khong_thanh_cong', ['Firebase Messaging'])]);
        }

        $title = !empty($notification_info['title']) ? $notification_info['title'] : null;
        $body = !empty($notification_info['body']) ? $notification_info['body'] : null;
        $link = !empty($notification_info['link']) ? $notification_info['link'] : '';
        $mobile_action = !empty($notification_info['mobile_action']) ? $notification_info['mobile_action'] : null;
        $image = !empty($notification_info['image']) ? CDN_URL . $notification_info['image'] : null;
        $icon = !empty($notification_info['icon']) ? CDN_URL . $notification_info['icon'] : null;

        $content = [
            'title' => $title,
            'body' => $body,
            'image' => $image,
            'icon' => $icon
        ];

        // format data
        $data_push = [];
        if($platform == 'token'){
            $data_push['token'] = $token;            
        }else{
            $topic_domain = $notification_component->topicOfWebsite();
            $data_push['topic'] = $topic_domain;
        }

        $data_push['notification'] = $content;
        $data_push['data'] = [
            'link' => $link,
            'mobile_action' => $mobile_action,
            'data_mobile_app' => json_encode([
                'link' => $link,
                'mobile_action' => $mobile_action
            ])
        ];

        // config for webpush
        if($platform == 'all' || $platform == 'web'){
            $data_push['webpush'] = [
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => $icon
                ],
                'fcm_options' => [
                    'link' => $link
                ]
            ];
        }        

        // config for ios
        if($platform == 'all' || $platform == 'ios'){
            $data_push['apns'] = [
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => $title,
                            'body' => $body
                        ],
                        'badge' => 9,
                        'sound' => 'default',
                    ]
                ],
            ];
        }

        // config for android
        if($platform == 'all' || $platform == 'android'){
            $data_push['android'] = [
                'notification' => $content
            ];
        }

        $entity = $table->patchEntity($notification_info, [
            'sent' => 1
        ]);

        // gửi thông báo
        try{
            $message = CloudMessage::fromArray($data_push);
            $send = $messaging->send($message);
        }catch(InvalidMessage $ex){
            $this->responseJson([
                CODE => SUCCESS, 
                MESSAGE => $ex->getMessage()
            ]);
        }

        $entity_sent = TableRegistry::get('NotificationsSent')->newEntity([
            'notification_id' => $notification_id,
            'platform' => $platform,
            'token' => $token,
            'created_by' =>  $this->Auth->user('id')
        ]);

        // cập nhật trạng thái đã gửi
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $sent_notification = TableRegistry::get('NotificationsSent')->save($entity_sent);
            if (empty($sent_notification->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([
                CODE => SUCCESS, 
                MESSAGE => __d('admin', 'gui_thong_bao_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function list()
    {
        $this->js_page = [
            '/assets/js/pages/list_notification.js',
        ];

        $this->set('path_menu', 'notification');
        $this->set('title_for_layout', __d('admin', 'danh_sach_thong_bao'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Notifications');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $notifications = [];

        $limit = PAGINATION_LIMIT_ADMIN;
        $page = 1;
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $params['get_user'] = true;
        
        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $notifications = $this->paginate($table->queryListNotifications($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $notifications = $this->paginate($table->queryListNotifications($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        // parse data before output
        $pagination_info = !empty($this->request->getAttribute('paging')['Notifications']) ? $this->request->getAttribute('paging')['Notifications'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        if (!empty($notifications)){
            $sent_table = TableRegistry::get('NotificationsSent');
            foreach($notifications as $k => $item){
                $count_sent = $sent_table->find()->where(['notification_id' => intval($item['id'])])->count();

                $notifications[$k]['count_sent'] = $count_sent;
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $notifications, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $notification_setting = !empty($settings['notification']) ? $settings['notification'] : [];
        $icon_default = !empty($notification_setting['icon']) ? $notification_setting['icon'] : null;
        $this->js_page = [
            '/assets/js/pages/notification.js'
        ];

        $this->set('icon_default',  $icon_default);
        $this->set('path_menu', 'notification_add');
        $this->set('title_for_layout', __d('admin', 'tao_thong_bao_moi'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $notification = !empty($id) ? TableRegistry::get('Notifications')->find()->where(['id' => $id])->first() : [];
        if(empty($notification)){
            $this->showErrorPage();
        }
        
        $this->set('id', $id);
        $this->set('notification', $notification);
        $this->set('icon_default', null);

        $this->js_page = [
            '/assets/js/pages/notification.js'
        ];

        $this->set('path_menu', 'notification');
        $this->set('title_for_layout', __d('admin', 'cap_nhat_thong_bao'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $utilities = $this->loadComponent('Utilities');

        $table = TableRegistry::get('Notifications');
        $title = !empty($data['title']) ? trim($data['title']) : null;

        // validate data
        if(empty($title)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(!empty($id)){
            $notification = TableRegistry::get('Notifications')->find()->where(['id' => $id])->first();
            if(empty($notification)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        $data_save = [
            'type' => !empty($data['type']) ? $data['type'] : null,
            'title' => $title,
            'body' => !empty($data['body']) ? $data['body'] : null,
            'image' => !empty($data['image']) ? $data['image'] : null,
            'icon' => !empty($data['icon']) ? $data['icon'] : null,
            'link' => !empty($data['link']) ? $data['link'] : null,
            'mobile_action' => !empty($data['mobile_action']) ? $data['mobile_action'] : null,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$title]))
        ];

        // merge data with entity 
        if(empty($notification)){
            $data_save['created_by'] = $this->Auth->user('id');
            $entity = $table->newEntity($data_save);
        }else{            
            $entity = $table->patchEntity($notification, $data_save);
        }

        // show error validation in model
        if($entity->hasErrors()){
            $list_errors = $utilities->errorModel($entity->getErrors());            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Notifications');

        $notifications = $table->find()->where([
            'Notifications.id IN' => $ids,
        ])->select(['Notifications.id', 'Notifications.status'])->toArray();
        
        if(empty($notifications)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_ban_ghi')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $notification_id) {
            $patch_data[] = [
                'id' => $notification_id,
                'status' => $status
            ];
        }

        $entities = $table->patchEntities($notifications, $patch_data, ['validate' => false]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($entities);            
            if (empty($change_status)){
                throw new Exception();
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Notifications');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){

                $notification = $table->find()->where(['id' => $id])->first();
                if (empty($notification)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_ban_ghi'));
                }

                $delete = $table->delete($notification);
                if (empty($delete)){
                    throw new Exception();
                }
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

}