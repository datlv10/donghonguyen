<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class OtpController extends AppController { 

    public function initialize(): void
    {
        parent::initialize();
    }

    public function otpNumberPhone()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Otp')->otpNumberPhone($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function verifyNumberPhone()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Otp')->verifyNumberPhone($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }
}