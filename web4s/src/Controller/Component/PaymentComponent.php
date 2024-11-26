<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;

class PaymentComponent extends Component
{
	public $controller = null;

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function listGateway($data = [], $options = [])
    {
        $payment_gateway = TableRegistry::get('PaymentsGateway')->getList(LANGUAGE);        

        // thêm danh sách cổng azpay
        if(!empty($payment_gateway[AZPAY])){
            // one pay
            $payment_gateway[AZPAY . '_' . 1] = [
                'code' => AZPAY . '_' . 1,
                'is_installment' => false,
                'name' => __d('template', 'ten_cong_azpay_01'),
                'name' => __d('template', 'thong_tin_cong_azpay_01'),
            ];

            // azpay - momo
            $payment_gateway[AZPAY . '_' . 4] = [
                'code' => AZPAY . '_' . 4,
                'is_installment' => false,
                'name' => __d('template', 'ten_cong_azpay_04'),
                'name' => __d('template', 'thong_tin_cong_azpay_04'),
            ];

            unset($payment_gateway[AZPAY]);
        }

        // xóa cổng bank và code
        unset($payment_gateway[BANK]);
        unset($payment_gateway[COD]);

        return $payment_gateway;
    }
}
