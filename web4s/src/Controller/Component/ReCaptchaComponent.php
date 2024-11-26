<?php

namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Http\Client;

class ReCaptchaComponent extends Component
{
	public $controller = null;
    public $components = ['System'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function check($token = null)
    {
        $setting_table = TableRegistry::get('Settings');
        $settings = $setting_table->getSettingWebsite();

        $recaptcha = !empty($settings['recaptcha']) ? $settings['recaptcha'] : null;
        if(empty($recaptcha['use_recaptcha'])){
            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('template', 'he_thong_khong_cau_hinh_su_dung_recaptcha')
            ]);
        }

        $secret_key = !empty($recaptcha['secret_key']) ? $recaptcha['secret_key'] : null;
        if(empty($secret_key)) {
            return $this->System->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_ma_bao_mat_recaptcha')
            ]);
        }

        if(empty($token)) {
            return $this->System->getResponse([
                MESSAGE => __d('template', 'khong_nhan_duoc_thong_tin_ma_xac_nhan_recaptcha')
            ]);
        }

        $http = new Client();
        $response = $http->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => $secret_key,
                'response' => $token,
                'remoteip' => $this->controller->getRequest()->clientIp()
            ]
        );
        if($response->getStatusCode() != 200 || empty($response->getStringBody())){
            return $this->System->getResponse([
                MESSAGE => __d('template', 'khong_xac_nhan_duoc_thong_tin_recaptcha')
            ]);
        }
        $result = json_decode($response->getStringBody(), true);

        if(!empty($result['success'])){
            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('template', 'xac_nhan_recaptcha_thanh_cong')
            ]);
        }else{
            return $this->System->getResponse([
                MESSAGE => __d('template', 'khong_xac_nhan_duoc_thong_tin_recaptcha')
            ]);
        }      
    }    
}
