<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class MemberHelper extends Helper
{
    public $helpers = ['Utilities'];

    /** Lấy thông tin tài khoản khách hàng khi đã đăng nhập
     * 
     * 
     * {assign var = data value = $this->Member->getMemberInfo()}
     * 
    */
    public function getMemberInfo()
    {
        $session = $this->getView()->getRequest()->getSession();
        return $session->read(MEMBER);     
    }

    /** Lấy thông tin khách hàng qua ID khách hàng
     * 
     * $id (*): ID khách hàng(int)
     * $params['get_user']: lấy thông tin của nhân viên - ví dụ: 'true'| 'false'
     * $params['get_account']: lấy thông tin của tài khoản - ví dụ: 'true'| 'false'
     * $params['get_default_address']: lấy thông tin địa chỉ mặc định - ví dụ: 'true'| 'false'
     * $params['get_list_address']: lấy danh sách địa chỉ - ví dụ: 'true'| 'false'
     * $params['get_point']: lấy thông tin điểm - ví dụ: 'true'| 'false'
     * $params['get_bank']: lấy thông tin tài khoản ngân hàng - ví dụ: 'true'| 'false'
     * 
     * {assign var = data value = $this->Member->getDetailCustomer($id, [
     *      'get_default_address' => true
     * ])}
     * 
    */
    public function getDetailCustomer($customer_id = null, $param = [])
    {
        if(empty($customer_id)) return [];

        $table = TableRegistry::get('Customers');

        $customer = $table->getDetailCustomer($customer_id, $param);
        
        return $table->formatDataCustomerDetail($customer);
    }

    /** Lấy danh sách ngân hàng
     * 
     * 
     * {assign var = data value = $this->Member->getListBank()}
     * 
    */
    public function getListBank()
    {
        return Configure::read('LIST_BANK');
    }

    /** Lấy danh sách khảo sát
     * 
     * 
     * {assign var = data value = $this->Member->getListSurvey()}
     * 
    */
    public function getListSurvey()
    {
        return Configure::read('LIST_SURVEY');
    }

    /** Lấy danh sách ngân hàng kiểu dropdown
     * 
     * $customer_id*: ID của khách hàng(int)
     * 
     * {assign var = data value = $this->Member->getListBankDropdown($customer_id)}
     * 
    */
    public function getListBankDropdown($customer_id = null, $params = [])
    {
        $result = [];
        if(empty($customer_id)) return $result;

        $params[FILTER]['customer_id'] = $customer_id;
        $params[FIELD] = LIST_INFO;

        $banks = TableRegistry::get('CustomersBank')->queryListCustomersBank($params)->toArray();

        foreach ($banks as $k => $bank) {
            $bank_name = !empty($bank['bank_name']) ? $bank['bank_name'] : null;
            $account_number = !empty($bank['account_number']) ? ' - ' . $bank['account_number'] : null;
            $result[$bank['id']] = $bank_name . $account_number;
        }
        return $result;
    }
}
