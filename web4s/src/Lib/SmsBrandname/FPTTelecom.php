<?php

namespace App\Lib\SmsBrandname;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

use App\Lib\SmsBrandname\SmsBrandnameUtilities;
use App\Lib\SmsBrandname\NhSmsBrandname;

use App\Lib\SmsBrandname\TechAPI\Exception;
use App\Lib\SmsBrandname\TechAPI\Auth\AccessToken;
use App\Lib\SmsBrandname\TechAPI\Api\SendBrandnameOtp;
use App\Lib\SmsBrandname\TechAPI\Constant;
use App\Lib\SmsBrandname\TechAPI\Client;
use App\Lib\SmsBrandname\TechAPI\Auth\ClientCredentials;



class FPTTelecom
{
    protected $config = [];

    public function __construct($params = [])
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $default_partner = !empty($settings['sms_brandname']['default_partner']) ? $settings['sms_brandname']['default_partner'] : null;

        $this->config = !empty($settings['sms_brandname'][$default_partner]) ? json_decode($settings['sms_brandname'][$default_partner], true) : null;
        $mode = !empty($this->config['mode']) ? $this->config['mode'] : null;

        // Constant::MODE_LIVE | MODE_SANDBOX
        if($mode == LIVE) {
            $mode = Constant::MODE_LIVE;
        } else {
            $mode = Constant::MODE_SANDBOX;
        }
    
        Constant::configs([
            'mode'            => $mode, 
            'connect_timeout' => 15,
            'enable_cache'    => false,
            'enable_log'      => true,
            'log_path'        => LOGS
        ]);
    }

    public function getTechAuthorization()
    {
        $client_id = !empty($this->config['client_id']) ? $this->config['client_id'] : null;
        $client_secret = !empty($this->config['client_secret']) ? $this->config['client_secret'] : null;

        $client = new Client(
            $client_id,
            $client_secret,
            ['send_brandname_otp']
        );

        return new ClientCredentials($client);
    }
    public function sendOtp($params = []) 
    {
        $phone = !empty($params['phone']) ? $params['phone'] : null;
        $message = !empty($params['message']) ? $params['message'] : null;
        $status = !empty($this->config['status']) ? 1 : 0;
        $brandname = !empty($this->config['brandname']) ? $this->config['brandname'] : null;
        $utilities = new SmsBrandnameUtilities();

        if(empty($status)) {
            return $utilities->getResponse([
                CODE => ERROR,
                MESSAGE => __d('template', 'trang_thai_khong_hoat_dong')
            ]);
        }

        try{
            // get post data
            $data = [
                'Phone'      => $phone,
                'BrandName'  => $brandname,
                'Message'    => $message
            ];

            // Lấy đối tượng Authorization để thực thi API
            $oGrantType = $this->getTechAuthorization();

             // Thực thi API
            $apiSendBrandname = new SendBrandnameOtp($data);
            $data = $oGrantType->execute($apiSendBrandname);

            if (!empty($data['error']))
            {
                // Xóa cache access token khi có lỗi xảy ra từ phía server
                AccessToken::getInstance()->clear();
                
                throw new Exception($data['error_description'], $data['error']);
            }

            return $utilities->getResponse([
                CODE => SUCCESS,
                DATA => $data
            ]);
        }catch (Exception $e) {
            return $utilities->getResponse([
                CODE => ERROR,
                MESSAGE => $e->getMessage()
            ]);
        }
    }
}

?>