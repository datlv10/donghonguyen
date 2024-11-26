<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class PaymentAdminHelper extends Helper
{   
    public function getListStatus()
    {
        $result = [
            0 => __d('admin', 'da_huy'),
            1 => __d('admin', 'thanh_cong'),
            2 => __d('admin', 'cho_xet_duyet')
        ];

        return $result;
    }

    public function getListObjectType()
    {
        $result = [
            CUSTOMER => __d('admin', 'khach_hang'),
            SUPPLIER => __d('admin', 'nha_cung_cap'),
            EMPLOYEE => __d('admin', 'nhan_vien'),
            SHIPPER => __d('admin', 'doi_tac_van_chuyen'),
            OTHER => __d('admin', 'doi_tuong_khac'),
        ];

        return $result;
    }
    
    public function getListPaymentsForDropdown($params = [])
    {
        $list_payment = [
            CASH => __d('admin', 'tien_mat'),
            BANK => __d('admin', 'chuyen_khoan'),
            CREDIT => __d('admin', 'quet_the'),
            COD => __d('admin', 'cod'),
        ];

        return $list_payment;
    }


    public function getListGateWay($lang = null)
    {
        $contain = [
            'PaymentsGatewayContent' => function ($q) use ($lang) {
                return $q->where([
                    'PaymentsGatewayContent.lang' => $lang
                ]);
            }
        ];
        
        $gateway = TableRegistry::get('PaymentsGateway')->find()->contain($contain)->select([
            'PaymentsGateway.code', 
            'PaymentsGatewayContent.name'
        ])->toArray();

        $result = Hash::combine($gateway, '{n}.code', '{n}.PaymentsGatewayContent.name');

        return $result;
    }
}
