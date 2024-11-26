<?php
declare(strict_types=1);

namespace App\View\Helper;
use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class CurrencyHelper extends Helper
{   
    /** Lấy danh sách tiền tệ
     * 
     * 
     * {assign var = data value = $this->Currency->getList()}
     * 
     * 
    */
    public function getList()
    {
        return TableRegistry::get('Currencies')->getList();
    }

    /** Lấy mã tiền tệ mặc định
     * 
     * 
     * {assign var = data value = $this->Currency->getCurrencyCode()}
     * 
     * 
    */
    public function getCurrencyCode()
    {
        $currency = CURRENCY_CODE;
        if(empty(CURRENCY_CODE)){
        	$currency_default = TableRegistry::get('Currencies')->getDefaultCurrency();
        	$currency = !empty($currency_default['code']) ? $currency_default['code'] : null;
        }
        return $currency;
    }

    
    /** Bỏ dùng function getCurrencyCode
    */
    public function getCurrencyDefault()
    {
        $currency_default = TableRegistry::get('Currencies')->getDefaultCurrency();
        if(empty($currency_default)) return [];
        
        $currency = !empty($currency_default['code']) ? $currency_default['code'] : null;
        return $currency;
    }
}
