<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationComponent extends AppComponent
{
    public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function topicOfWebsite()
    {
        return str_replace('.', '_', $this->controller->getRequest()->host());
    }

    public function initFirebaseMessaging()
    {
        try{
            $service_account_file = SOURCE_DOMAIN . DS . 'google-service-account.json';

            $factory = new Factory();
            $factory = $factory->withServiceAccount($service_account_file);
            $messaging = $factory->createMessaging();
        }catch(Exception $ex){
            return null;
        }

        return $messaging;
    }

    public function subscribe($data = [])
    {
        $api = !empty($options['api']) ? true : false;

        $token = !empty($data['token']) ? $data['token'] : null;
        $platform = !empty($data['platform']) ? $data['platform'] : null;
        $browser = !empty($data['browser']) ? $data['browser'] : null;
        $user_agent = !empty($data['user_agent']) ? $data['user_agent'] : null;

        $session = $this->controller->getRequest()->getSession();

        $member = $session->read(MEMBER);
        $admin_user = $session->read('Auth.Admin');

        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $user_admin_id = !empty($admin_user['id']) ? intval($admin_user['']) : null;

        if (empty($token) || empty($platform) || !in_array($platform, Configure::read('LIST_FLATFORM_NOTIFICATION'))) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('NotificationsSubscribe');

        $data_save = [
            'token' => $token,
            'platform' => $platform,
            'browser' => $browser,
            'user_agent' => $user_agent,
            'customer_id' => $customer_id,
            'user_admin_id' => $user_admin_id
        ];

        // kiểm tra token đã tồn tại trên hệ thống chưa
        $token_info = $table->getInfoByToken($token);
        if(!empty($token_info)){
            $entity = $table->patchEntity($token_info, $data_save);
        }else{
            $entity = $table->newEntity($data_save);
        }

        // show error validation in model
        if($entity->hasErrors()){
            $list_errors = $this->Utilities->errorModel($entity->getErrors());
            
            return $this->System->getResponse([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            // subscribe token to topic website
            $messaging = $this->initFirebaseMessaging();
            if(empty($messaging)){
                throw new Exception();
            }

            $subscribe_to_topic = $messaging->subscribeToTopic($this->topicOfWebsite(), $token);
            $conn->commit();

            return $this->System->getResponse([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function unsubscribe($data = [])
    {
        if(!$this->controller->getRequest()->is('post') || empty($data)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $token = !empty($data['token']) ? $data['token'] : null;
        if(empty($token)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('NotificationsSubscribe');

        $token_info = $table->getInfoByToken($token);
        if(empty($token_info)) {
            return $this->System->getResponse([
                CODE => SUCCESS
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $delete = $table->delete($token_info);
            if (empty($delete)){
                throw new Exception();
            }

            // unsubscribe token to topic website
            $messaging = $this->initFirebaseMessaging();
            if(empty($messaging)){
                throw new Exception();
            }

            $unsubscribe_from_topic = $messaging->unsubscribeFromTopic($this->topicOfWebsite(), $token);
            
            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }
}
