<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class OrderHelper extends Helper
{
    /** Danh sách nhóm trạng thái của đơn hàng
     * 
     * {assign var = data value = $this->Order->getListStatusGroupOrder()}
    */
    public function getListStatusGroupOrder()
    {
        $result = [
            WAIT_PAYMENT => __d('template', 'cho_thanh_toan'),
            PROCESSING => __d('template', 'dang_xu_ly'),
            TRANSPORT => __d('template', 'van_chuyen'),
            CANCEL => __d('template', 'da_huy')
        ];

        return $result;
    }

    /** Danh sách trạng thái của đơn hàng template
     * 
     * {assign var = data value = $this->Order->getListStatusOrderTemplate()}
    */
    public function getListStatusOrderTemplate()
    {
        $result = [
            DRAFT => [
                'code' => DRAFT,
                'class' => 'font-weight-normal badge badge-primary',
                'title' => __d('template', 'cho_thanh_toan')
            ],
            NEW_ORDER => [
                'code' => NEW_ORDER,
                'class' => 'font-weight-normal badge badge-primary',
                'title' => __d('template', 'don_moi')
            ],
            CONFIRM => [
                'code' => CONFIRM,
                'class' => 'font-weight-normal badge badge-primary',
                'title' => __d('template', 'da_xac_nhan')
            ],
            PACKAGE => [
                'code' => PACKAGE,
                'class' => 'font-weight-normal badge badge-warning',
                'title' => __d('template', 'cho_van_chuyen')
            ],
            EXPORT => [
                'code' => EXPORT,
                'class' => 'font-weight-normal badge badge-warning',
                'title' => __d('template', 'dang_chuyen')
            ],
            DONE => [
                'code' => DONE,
                'class' => 'font-weight-normal badge badge-success',
                'title' => __d('template', 'thanh_cong')
            ],
            CANCEL => [
                'code' => CANCEL,
                'class' => 'font-weight-normal badge badge-dark',
                'title' => __d('template', 'da_huy')
            ]
        ];

        return $result;
    }

    /** Danh sách trạng thái của đơn hàng định dạng code => title
     * 
     * {assign var = data value = $this->Order->getListStatusOrder()}
    */
    public function getListStatusOrder()
    {   
        $result = Hash::combine($this->getListStatusOrderTemplate(), '{*}.code', '{*}.title');
        return $result;    
    }

    /** Lấy thông tin đơn hàng
     * 
     * $id*: ID của đơn hàng
     * $params[{LANG}]: mã ngôn ngữ ví dụ 'en' | 'vi'
     * 
     * {assign var = data value = $this->Order->getInfoOrder($id, [
     *      {LANG} => LANGUAGE
     * ])}
    */
    public function getInfoOrder($id = null, $params = [])
    {
        if(empty($id)) return [];

        $table = TableRegistry::get('Orders');

        $lang = !empty($params[LANG]) ? $params[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $order_info = $table->getDetailOrder($id, [
            'get_items' => true,
            'get_contact' => true,
            'get_payment' => true,
            'get_shipping' => true,
            'get_user' => true,
            'get_staff' => true
        ]);

        return $table->formatDataOrderDetail($order_info, $lang);
    }
}
