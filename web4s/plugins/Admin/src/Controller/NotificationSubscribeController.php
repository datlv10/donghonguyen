<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;


class NotificationSubscribeController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->js_page = [
            '/assets/js/pages/list_notification_subscribe.js',
        ];

        $this->set('path_menu', 'notification_subscribe');
        $this->set('title_for_layout', __d('admin', 'danh_sach_thiet_bi_nhan_thong_bao'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('NotificationsSubscribe');
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

        $params[FILTER][LANG] = !empty($params[FILTER][LANG]) ? $params[FILTER][LANG] : TableRegistry::get('Languages')->getDefaultLanguage();

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        
        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $notifications = $this->paginate($table->queryListNotificationsSubscribe($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $notifications = $this->paginate($table->queryListNotificationsSubscribe($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        // parse data before output
        $result = [];

        $pagination_info = !empty($this->request->getAttribute('paging')['NotificationsSubscribe']) ? $this->request->getAttribute('paging')['NotificationsSubscribe'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $notifications, 
            META => $meta_info
        ]);
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
        $table = TableRegistry::get('NotificationsSubscribe');

        $tokens = [];
        foreach($ids as $id){
            $subscribe = $table->find()->where(['id' => $id])->select(['token'])->first();
            if(empty($subscribe['token'])) continue;
            $tokens[] = $subscribe['token'];
        }


        // unscribe token from topic firebase
        if(!empty($tokens)){
            $notification_component = $this->loadComponent('Admin.Notification');
            $messaging = $notification_component->initFirebaseMessaging();
            if(empty($messaging)){
                $this->responseJson([MESSAGE => __d('admin', 'khoi_tao_{0}_khong_thanh_cong', ['Firebase Messaging'])]);
            }

            $topic_domain = $notification_component->topicOfWebsite();
            $result = $messaging->unsubscribeFromTopics([$topic_domain], $tokens);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $subscribe = $table->find()->where(['id' => $id])->first();
                if (empty($subscribe)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_ban_ghi'));
                }

                $delete = $table->delete($subscribe);
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