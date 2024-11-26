<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Utility\Hash;


class MemberAffiliateController extends AppController 
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $action_check = [
            'affiliateDashboard',
            'affiliatePolicy',
            'affiliateActive',
            'processAffiliateActive',
            'loadStatisticMonth',
            'loadChartProfit',
            'affiliateOrder',
            'listPointToMoney',
            'createRequestPointToMoney',
            'affiliateOrderInfomation'
        ];

        $session = $this->request->getSession();  
        $member = $session->read(MEMBER);

        if(in_array($this->request->getParam('action'), $action_check) && !empty($member['customer_id'])) {
            if($this->loadComponent('Member')->memberDoesntExistLogout($member['customer_id'])){
                if($this->request->is('ajax')){
                    $this->responseJson([
                        STATUS => 403,
                        MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')
                    ]);
                }else{
                    return $this->redirect('/member/login?redirect=' . urlencode($this->request->getPath()), 303);
                }
            }
        }

        if (in_array($this->request->getParam('action'), $action_check) && empty($member['customer_id'])){
            if($this->request->is('ajax')){
                $this->responseJson([
                    STATUS => 403,
                    MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')
                ]);
            }else{
                return $this->redirect('/member/login?redirect=' . urlencode($this->request->getPath()), 303);
            }
        }
    }

    public function affiliateDashboard()
    {
        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        if(empty($customer_id)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $customers_table = TableRegistry::get('Customers');
        
        $member_info = $customers_table->getDetailCustomer($customer_id);
        $member_info = $customers_table->formatDataCustomerDetail($member_info);

        $is_partner_affiliate = !empty($member_info['is_partner_affiliate']) && $member_info['is_partner_affiliate'] == 1 ? true : false;
        if(!$is_partner_affiliate){
            return $this->redirect('/member/affiliate/policy');
        }

        $statistical_result = $this->loadComponent('AffiliateFrontend')->allStatistical();
        $statistical = !empty($statistical_result[DATA]) ? $statistical_result[DATA] : [];

        $chart_result = $this->loadComponent('AffiliateFrontend')->chartProfit();
        $chart_data = !empty($chart_result[DATA]) ? $chart_result[DATA] : [];
        
        $list_month = [
            '01' => __d('template', 'thang_1'),
            '02' => __d('template', 'thang_2'),
            '03' => __d('template', 'thang_3'),
            '04' => __d('template', 'thang_4'),
            '05' => __d('template', 'thang_5'),
            '06' => __d('template', 'thang_6'),
            '07' => __d('template', 'thang_7'),
            '08' => __d('template', 'thang_8'),
            '09' => __d('template', 'thang_9'),
            '10' => __d('template', 'thang_10'),
            '11' => __d('template', 'thang_11'),
            '12' => __d('template', 'thang_12')
        ];

        $this->set('statistical', $statistical);
        $this->set('chart_data', $chart_data);
        $this->set('member', $member_info);
        $this->set('list_month', $list_month);
        $this->set('title_for_layout', __d('template', 'tong_quan'));
    }

    public function affiliatePolicy()
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $affiliate_setting = !empty($settings['affiliate']) ? $settings['affiliate'] : [];

        $commissions = !empty($affiliate_setting['commissions']) ? json_decode($affiliate_setting['commissions'], true) : [];
        $affiliate_ranks = Hash::combine($commissions, '{n}.key', '{n}');

        $this->set('affiliate_ranks', $affiliate_ranks);
        $this->set('title_for_layout', __d('template', 'kich_hoat_tai_khoan_doi_tac'));
    }

    public function affiliateActive() 
    {
        $member = $this->request->getSession()->read(MEMBER);

        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $customers_table = TableRegistry::get('Customers');

        $member_info = $customers_table->getDetailCustomer($customer_id, [
            'get_default_address' => true
        ]);
        $member_info = $customers_table->formatDataCustomerDetail($member_info);

        $this->set('member', $member_info);
        $this->set('title_for_layout', __d('template', 'kich_hoat_tai_khoan_doi_tac'));
    }

    public function processAffiliateActive()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $result = $this->loadComponent('AffiliateFrontend')->registerAffiliate($data);
        $this->responseJson($result);
    }

    public function loadStatisticMonth()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $month = !empty($data['month']) ? $data['month'] : null;

        $statistical_result = $this->loadComponent('AffiliateFrontend')->monthStatistical($data);
        $statistical = !empty($statistical_result[DATA]) ? $statistical_result[DATA] : [];

        $this->set('statistical', $statistical);
        $this->render('load_statistic_month');
    }

    public function loadChartProfit()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        if (!$this->getRequest()->is('post')) die;        

        $data = $this->getRequest()->getData();

        $chart_result = $this->loadComponent('AffiliateFrontend')->chartProfit($data);
        $chart_data = !empty($chart_result[DATA]) ? $chart_result[DATA] : [];

        $this->set('chart_data', $chart_data);
        $this->render('chart_profit');
    }

    public function affiliateOrder() 
    {
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $member = $this->request->getSession()->read(MEMBER);

        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $customers_table = TableRegistry::get('Customers');

        $member_info = $customers_table->getDetailCustomer($customer_id);
        $member_info = $customers_table->formatDataCustomerDetail($member_info);
        $is_partner_affiliate = !empty($member_info['is_partner_affiliate']) && $member_info['is_partner_affiliate'] == 1 ? true : false;
        if(!$is_partner_affiliate){
            if(!$this->request->is('ajax')){
                return $this->redirect('/member/affiliate/policy');
            }else{
                die();
            }
        }

        $result = $this->loadComponent('AffiliateFrontend')->affiliateOrder($data);

        $data_result = !empty($result[DATA]) ? $result[DATA] : [];

        $affiliate_order = !empty($data_result['affiliate']) ? $data_result['affiliate'] : [];
        $pagination = !empty($data_result[PAGINATION]) ? $data_result[PAGINATION] : [];

        $this->set('member', $member_info);
        $this->set('affiliate_order', $affiliate_order);
        $this->set('pagination', $pagination);
        $this->set('title_for_layout', __d('template', 'don_gioi_thieu'));

        if($this->request->is('ajax')){
            $this->viewBuilder()->enableAutoLayout(false);
            $this->render('list_affiliate_order_element');
        }else{
            $this->render('affiliate_order');
        }
    }

    public function listPointToMoney() 
    {
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $member_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id, [
            'get_point' => true
        ]);
        $member_info = TableRegistry::get('Customers')->formatDataCustomerDetail($member_info);
        $is_partner_affiliate = !empty($member_info['is_partner_affiliate']) && $member_info['is_partner_affiliate'] == 1 ? true : false;
        if(!$is_partner_affiliate){
            if(!$this->request->is('ajax')){
                return $this->redirect('/member/affiliate/policy');
            }else{
                die();
            }
        }

        $result = $this->loadComponent('AffiliateFrontend')->listPointToMoney($data);

        $data_result = !empty($result[DATA]) ? $result[DATA] : [];

        $point_tomoney = !empty($data_result['point_tomoney']) ? $data_result['point_tomoney'] : [];
        $pagination = !empty($data_result[PAGINATION]) ? $data_result[PAGINATION] : [];

        $this->set('point_tomoney', $point_tomoney);
        $this->set('pagination', $pagination);
        $this->set('member', $member_info);
        $this->set('title_for_layout', __d('template', 'lich_su_rut_tien'));

        if($this->request->is('ajax')){
            $this->viewBuilder()->enableAutoLayout(false);
            $this->render('list_point_to_money_element');
        }else{
            $this->render('list_point_to_money');
        }
    }

    public function createRequestPointToMoney()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $result = $this->loadComponent('AffiliateFrontend')->createRequestPointToMoney($data);
        $this->responseJson($result);
    }

    public function affiliateOrderInfomation($code = null)
    {
        $member = $this->request->getSession()->read(MEMBER);

        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $customers_table = TableRegistry::get('Customers');

        $member_info = $customers_table->getDetailCustomer($customer_id);
        $member_info = $customers_table->formatDataCustomerDetail($member_info);

        $data['code'] = !empty($code) ? $code : null;
        $order = $this->loadComponent('AffiliateFrontend')->affiliateOrderInfomation($data);
        $order = !empty($order[DATA]) ? $order[DATA] : [];

        $this->set('member', $member_info);
        $this->set('order', $order);
        $this->set('title_for_layout', __d('template', 'chi_tiet_don_gioi_thieu'));
    }

}