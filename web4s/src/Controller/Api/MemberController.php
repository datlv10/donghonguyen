<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class MemberController extends AppController {

    private $action_session = [
        'updateProfile', 
        'listAddress',
        'saveAddress',
        'setDefaultAddress', 
        'deleteAddress', 
        'infomation', 
        'changePassword',
        'updateAvatar',
        'listOrders',
        'orderInfomation',
        'cancelOrder',
        'changeImportantInfo',
        'deleteAccount'
    ]; 

    public function initialize(): void
    {
        parent::initialize();

        $session = $this->request->getSession();  
        $member = $session->read(MEMBER);
        
        if(in_array($this->request->getParam('action'), $this->action_session) && !empty($member['customer_id'])) {
            if($this->loadComponent('Member')->memberDoesntExistLogout($member['customer_id'])){
                $this->responseErrorApi([
                    STATUS => 403,
                    MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')
                ]);
            }
        }

        if (in_array($this->request->getParam('action'), $this->action_session) && empty($member['customer_id'])){
            $this->responseErrorApi([
                STATUS => 403,
                MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')
            ]);
        }
    }

    public function login()
    {
        $data = $this->data_bearer;

        $result = $this->loadComponent('Member')->login($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function socialLogin()
    {
        $data = $this->data_bearer;

        $result = $this->loadComponent('Member')->socialLogin($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function logout()
    {
        $data = $this->data_bearer;

        $result = $this->loadComponent('Member')->logout();

        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function register()
    {
        $data = $this->data_bearer;
        $data['from_website_template'] = true;
        $result = $this->loadComponent('Member')->register($data, ['api' => true]);

        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function updateProfile()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->updateProfile($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function listAddress()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->listAddress($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function saveAddress()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->saveAddress($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function setDefaultAddress()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->setDefaultAddress($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function deleteAddress()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->deleteAddress($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function infomation()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->infomation($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function changePassword()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->changePassword($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function forgotPassword()
    {
        $data = $this->data_bearer;
        $data['from_website_template'] = true;
        $result = $this->loadComponent('Member')->forgotPassword($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function verifyForgotPassword()
    {
        $data = $this->data_bearer;
        $data['from_website_template'] = true;
        $result = $this->loadComponent('Member')->verifyForgotPassword($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function resendVerifyCode()
    {
        $data = $this->data_bearer;
        $data['from_website_template'] = true;
        $result = $this->loadComponent('Member')->resendVerifyCode($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function verifyAccount()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->verifyAccount($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function updateAvatar()
    {
        $file = !empty($_FILES['file']) ? $_FILES['file'] : [];
        $data_upload = [
            'path' => CUSTOMER,
            'file' => $file
        ];
        
        $result = $this->loadComponent('Member')->updateAvatar($data_upload, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function listOrders()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->listOrders($data, [
            'api' => true,
            'get_items' => true
        ]);        

        if($result[CODE] == SUCCESS){
            $data_result = !empty($result[DATA]) ? $result[DATA] : [];

            $orders = !empty($data_result['orders']) ? $data_result['orders'] : [];
            $pagination = !empty($data_result[PAGINATION]) ? $data_result[PAGINATION] : [];
            $this->responseApi([
                DATA => $orders,
                EXTEND => !empty($orders) && !empty($pagination) ? [PAGINATION => $pagination] : []
            ]);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function orderInfomation()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->orderInfomation($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function cancelOrder()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->cancelOrder($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function registerByNumberPhone()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->registerByNumberPhone($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function getVerifyCode()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->getVerifyCode($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    } 

    public function changeImportantInfo()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->changeImportantInfo($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    } 
    
    public function customerLogin()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->customerLogin($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    } 

    public function deleteAccount()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->deleteAccount($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    } 
}