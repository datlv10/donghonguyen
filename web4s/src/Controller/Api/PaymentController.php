<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;
use App\Lib\Payment\NhPayment;


class PaymentController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $data = $this->data_bearer;

        $result = TableRegistry::get('PaymentsGateway')->getList(LANGUAGE);

        $payments = [];
        if(!empty($result)) {
            foreach ($result as $payment) {
                $payment['image'] = [
                    'src' => '/templates/mobile_'. CODE_MOBILE_TEMPLATE .'/assets/img/payment/' . $payment['code'] . '.png',
                    'source' => 'template'
                ];
                unset($payment['config']);
                $payments[] = $payment;
            }
        }

        $this->responseApi([DATA => $payments]);
    }

    public function returnPayment($gateway_code = null)
    {
        $nh_payment = new NhPayment($gateway_code);
        if ($this->request->is('post')) {
            $params = $this->request->getData();
        } else {
            $params = $this->request->getQueryParams();
        }

        $payment_gateway = TableRegistry::get('PaymentsGateway')->getList(LANGUAGE);
        if(empty($payment_gateway[$gateway_code])){
            $this->responseErrorApi([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_cong_thanh_toan')]);
        }

        $result = $nh_payment->returnResult($params);
        $payment_code = !empty($result[DATA]['code']) ? $result[DATA]['code'] : null;
        if(empty($payment_code)){
            $this->responseErrorApi([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        if(!empty($result[CODE]) && $result[CODE] == ERROR) {
            $message = !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('template', 'thanh_toan_khong_thanh_cong');
            $this->responseErrorApi([MESSAGE => $message]);
        }

        $this->responseApi([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'thanh_toan_thanh_cong')
        ]);
    }

    public function listGateway()
    {
        $data = $this->data_bearer;

        $result = $this->loadComponent('Payment')->listGateway();

        $payments = [];
        if(!empty($result)) {
            foreach ($result as $payment) {
                $payment['image'] = [
                    'src' => '/templates/mobile_'. CODE_MOBILE_TEMPLATE .'/assets/img/payment/' . $payment['code'] . '.png',
                    'source' => 'template'
                ];
                unset($payment['config']);
                $payments[] = $payment;
            }
        }

        $this->responseApi([DATA => $payments]);
    }

}