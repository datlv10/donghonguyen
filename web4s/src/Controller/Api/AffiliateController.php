<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;

class AffiliateController extends AppController {

    private $action_session = [
        'applyAffiliate',
        'deleteAffiliate',
        'allStatistical',
        'monthStatistical',
        'chartProfit',
        'affiliateOrder',
        'affiliateOrderInfomation',
        'listPointToMoney',
        'createRequestPointToMoney',
        'listBankOfPartner',
        'saveBank',
        'deleteBank',
        'listBank',
        'listSurvey',
        'listRank'
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

    public function applyAffiliate()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('AffiliateFrontend')->apply($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function deleteAffiliate()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post')){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
    
        $this->getRequest()->getSession()->write(AFFILIATE, null);
        $this->responseApi([
            MESSAGE => __d('template', 'cap_nhat_thanh_cong')
        ]);
    }

    public function registerAffiliate()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('AffiliateFrontend')->registerAffiliate($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function allStatistical()
    {
        $data = $this->data_bearer;
        $statistical_result = $this->loadComponent('AffiliateFrontend')->allStatistical();
        if(!empty($statistical_result) && $statistical_result[CODE] != SUCCESS) {
            $this->responseErrorApi($statistical_result);
        }

        $result = [
            CODE => SUCCESS,
            DATA => !empty($statistical_result[DATA]) ? $statistical_result[DATA] : []
        ];  

        $this->responseApi($result);
    }

    public function monthStatistical()
    {
        $data = $this->data_bearer;
        $statistical_result = $this->loadComponent('AffiliateFrontend')->monthStatistical($data);
        if(!empty($statistical_result) && $statistical_result[CODE] != SUCCESS) {
            $this->responseErrorApi($statistical_result);
        }

        $result = [
            CODE => SUCCESS,
            DATA => !empty($statistical_result[DATA]) ? $statistical_result[DATA] : []
        ];  

        $this->responseApi($result);
    }

    public function chartProfit()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('AffiliateFrontend')->chartProfit($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function affiliateOrder()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('AffiliateFrontend')->affiliateOrder($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function affiliateOrderInfomation()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('AffiliateFrontend')->affiliateOrderInfomation($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function listPointToMoney()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('AffiliateFrontend')->listPointToMoney($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function createRequestPointToMoney()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('AffiliateFrontend')->createRequestPointToMoney($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function listBankOfPartner()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->listBankOfPartner($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function saveBank()
    {
        $data = $this->data_bearer;

        $result = $this->loadComponent('Member')->saveBank($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function deleteBank()
    {
        $data = $this->data_bearer;
        $result = $this->loadComponent('Member')->deleteBank($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function listBank()
    {
        $list_bank = Configure::read('LIST_BANK');
        $result[DATA] = $list_bank;
        $this->responseApi($result);
    }

    public function listSurvey()
    {
        $list_survey = [
            'Giới thiệu bạn bè',
            'Review sản phẩm',
            'Quảng cáo qua website cá nhân',
            'Quảng cáo facebook',
            'Email marketing',
            'Website mã giảm giá'
        ];
        
        $result[DATA] = $list_survey;
        $this->responseApi($result);
    }

    public function listRank()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post')){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $affiliate_setting = !empty($settings['affiliate']) ? $settings['affiliate'] : [];

        $commissions = !empty($affiliate_setting['commissions']) ? json_decode($affiliate_setting['commissions'], true) : [];
        $commissions = Hash::combine($commissions, '{n}.key', '{n}');

        $result[DATA] = $commissions;
        $this->responseApi($result);
    }
}