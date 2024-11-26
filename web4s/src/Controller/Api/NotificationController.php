<?php

namespace App\Controller\Api;

use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class NotificationController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

	public function subscribe() 
	{
        $data = $this->data_bearer;
        $result = $this->loadComponent('Admin.Notification')->subscribe($data, ['api' => true]);

        if(!empty($result[CODE]) && $result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function unsubscribe()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Admin.Notification')->unsubscribe($data, ['api' => true]);

        if(!empty($result[CODE]) && $result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function listNotification()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('NotificationFrontend')->listNotifications($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $data_result = !empty($result[DATA]) ? $result[DATA] : [];
            $this->responseApi([
                DATA => $data_result,
                EXTEND => []
            ]);
        }else{
            $this->responseErrorApi($result);
        }
    }


}









































