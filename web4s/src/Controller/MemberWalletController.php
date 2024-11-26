<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;


class MemberWalletController extends AppController 
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $action_check = [            
            'wallet',
            'ajaxHistoryPoint',
            'givePoint',
            'ajaxGivePoint',
            'buyPoint',
            'ajaxBuyPoint',
            'buyPointSuccess'
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
	
    public function wallet() 
    {
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $histories_using = $this->loadComponent('CustomersPointFrontend')->historyUsingPoint($data);
        $histories_using = !empty($histories_using[DATA]) ? $histories_using[DATA] : [];

        $history = !empty($histories_using['history_point']) ? $histories_using['history_point'] : [];
        $pagination = !empty($histories_using[PAGINATION]) ? $histories_using[PAGINATION] : [];

        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        $point_info = TableRegistry::get('CustomersPoint')->getInfoCustomerPoint($customer_id);  
        $member_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id, [
            'get_default_address' => true
        ]);
        $member_info = TableRegistry::get('Customers')->formatDataCustomerDetail($member_info);     

        $this->set('member', $member_info);
        $this->set('point_info', $point_info);

        $this->set('history', $history);
        $this->set('pagination', $pagination);
        $this->set('title_for_layout', __d('template', 'vi_cua_ban'));
    }

    public function ajaxHistoryPoint()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        if(isset($data['action']) && $data['action'] == '') unset($data['action']);
        $histories_using = $this->loadComponent('CustomersPointFrontend')->historyUsingPoint($data);
        $histories_using = !empty($histories_using[DATA]) ? $histories_using[DATA] : [];

        $history = !empty($histories_using['history_point']) ? $histories_using['history_point'] : [];
        $pagination = !empty($histories_using[PAGINATION]) ? $histories_using[PAGINATION] : [];

        $this->set('history', $history);
        $this->set('pagination', $pagination);
        $this->render('element_wallet');
    }


    public function givePoint()
    {
        $member = $this->request->getSession()->read(MEMBER);
        $member_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $point_info = TableRegistry::get('CustomersPoint')->getInfoCustomerPoint($member_id);

        $this->set('point_info', $point_info);
        $this->set('member', $member);
        $this->set('title_for_layout', __d('template', 'tang_diem'));
    }

    public function ajaxGivePoint()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $result = $this->loadComponent('MemberWallet')->givePoint($data);
        $this->responseJson($result);
    }

    public function buyPoint()
    {
        $member = $this->request->getSession()->read(MEMBER);
        $member_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $point_info = TableRegistry::get('CustomersPoint')->getInfoCustomerPoint($member_id);

        $payment_gateway = $this->loadComponent('Payment')->listGateway();

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $config_point = !empty($settings['point']) ? $settings['point'] : [];

        $currencies = TableRegistry::get('Currencies')->getDefaultCurrency();
        $currency_default = !empty($currencies['code']) ? $currencies['code'] : null;

        $this->set('config_point', $config_point);
        $this->set('currency_default', $currency_default);
        $this->set('payment_gateway', $payment_gateway);
        $this->set('point_info', $point_info);
        $this->set('member', $member);
        $this->set('title_for_layout', __d('template', 'nap_diem'));
    }

    public function ajaxBuyPoint()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $result = $this->loadComponent('MemberWallet')->buyPoint($data);
        $this->responseJson($result);
    }

    public function buyPointSuccess()
    {
        $params = $this->request->getQueryParams();
        $code = !empty($params['code']) ? $params['code'] : null;

        $point_history = TableRegistry::get('CustomersPointHistory')->getInfoCustomerPointHistory(['code' => $code]);
        $point_history_id = !empty($point_history['id']) ? $point_history['id'] : null;

        $info_payment = [];
        if(!empty($point_history_id)){
            $info_payment = TableRegistry::get('Payments')->find()->where([
                'Payments.foreign_id' => $point_history_id,
                'Payments.foreign_type' => POINT
            ])->first();
        }        

        $member = $this->request->getSession()->read(MEMBER);
        $member_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        $this->set('member', $member);
        $this->set('point_history', $point_history);
        $this->set('info_payment', $info_payment);
        $this->set('title_for_layout', __d('template', 'nap_diem_thanh_cong'));
    }


}