<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class NotificationController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

	public function subscribe() 
	{
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $browser_info = !empty($data['browser_info']) ? $data['browser_info'] : [];
        $data['platform'] = 'web';
        $data['browser'] = !empty($browser_info['browser']) ? $browser_info['browser'] : null;
        $data['user_agent'] = !empty($browser_info['userAgent']) ? $browser_info['userAgent'] : null;

        $result = $this->loadComponent('Admin.Notification')->subscribe($data);

        $this->responseJson($result);
    }

    public function unsubscribe()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('Admin.Notification')->unsubscribe($data);

        $this->responseJson($result);
    }

    public function listNotification()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $data['type'] = WEBSITE;
        $result = $this->loadComponent('NotificationFrontend')->listNotifications($data);

        $all_notifications = [];
        if(!empty($result[CODE]) && $result[CODE] == SUCCESS && !empty($result[DATA])){
            $all_notifications = !empty($result[DATA]) ? $result[DATA] : [];
        }

        $this->set('all_notifications', $all_notifications);

        $this->render('notifications');
    }


}









































