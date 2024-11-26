<?php

namespace App\Lib\SmsBrandname;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use App\Lib\SmsBrandname\SmsBrandnameUtilities;
use App\Lib\SmsBrandname\NhSmsBrandname;
use App\Lib\SmsBrandname\TechAPI\Exception;

class Esms
{
    protected $config = [];

    public function __construct($params = [])
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $default_partner = !empty($settings['sms_brandname']['default_partner']) ? $settings['sms_brandname']['default_partner'] : null;

        $this->config = !empty($settings['sms_brandname'][$default_partner]) ? json_decode($settings['sms_brandname'][$default_partner], true) : null;
        $mode = !empty($this->config['mode']) ? $this->config['mode'] : null;
    }

    public function sendOtp($params = []) 
    {
        $phone = !empty($params['phone']) ? $params['phone'] : null;
        $message = !empty($params['message']) ? $params['message'] : null;
        $status = !empty($this->config['status']) ? 1 : 0;
        $brandname = !empty($this->config['brandname']) ? $this->config['brandname'] : null;
        $client_id = !empty($this->config['client_id']) ? $this->config['client_id'] : null;
        $client_secret = !empty($this->config['client_secret']) ? $this->config['client_secret'] : null;

        $utilities = new SmsBrandnameUtilities();

        if(empty($status)) {
            return $utilities->getResponse([
                CODE => ERROR,
                MESSAGE => __d('template', 'trang_thai_khong_hoat_dong')
            ]);
        }

        $message = urlencode($message);
        $data = "http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_get?Phone=$phone&ApiKey=$client_id&SecretKey=$client_secret&Content=$message&Brandname=$brandname&SmsType=2";
        
        $curl = curl_init($data); 
        curl_setopt($curl, CURLOPT_FAILONERROR, true); 
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($curl); 
            
        $obj = json_decode($result,true);

        if ($obj['CodeResult'] == 100) {
            return $utilities->getResponse([
                CODE => SUCCESS,
                DATA => $obj
            ]);
        } else {
            return $utilities->getResponse([
                CODE => ERROR,
                MESSAGE => $obj['ErrorMessage']
            ]);
        }
    }
}

?>