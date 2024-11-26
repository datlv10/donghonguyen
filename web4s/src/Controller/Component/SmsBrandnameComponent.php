<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use App\Lib\SmsBrandname\NhSmsBrandname;

class SmsBrandnameComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }


    public function sendSms($params = [])
    {
        $phone = !empty($params['phone']) ? $params['phone'] : null;
        $message = !empty($params['message']) ? $params['message'] : null;

        if(empty($message) || empty($phone)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        //get email config
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting_sms = !empty($settings['sms_brandname']) ? $settings['sms_brandname'] : [];
        $default_partner = !empty($setting_sms['default_partner']) ? $setting_sms['default_partner'] : null;

        $sms_brandname_info = !empty($settings['sms_brandname'][$default_partner]) ? json_decode($settings['sms_brandname'][$default_partner], true) : null;
        $brandname = !empty($sms_brandname_info['brandname']) ? $sms_brandname_info['brandname'] : '';

        if(empty($default_partner) || !in_array($default_partner, [FPT_TELECOM])){ 
            return $this->System->getResponse([MESSAGE => __d('template', 'thong_tin_cau_hinh_chua_hop_le')]);
        }

        $result = [];
        switch ($default_partner) {
            case FPT_TELECOM:
                    $nh_smsbrandname = new NhSmsBrandname(FPT_TELECOM);
                    $result = $nh_smsbrandname->sendOtp([
                        'phone' => $phone, 
                        'message' => $brandname . ': ' . $message
                    ]);

                    if(!empty($result[CODE]) && $result[CODE] == ERROR) {
                        return $this->System->getResponse([MESSAGE => $result[MESSAGE]]);
                    }
                break;
        }

        return $this->System->getResponse([
            CODE => SUCCESS, 
            MESSAGE => __d('template', 'gui_ma_thanh_cong'),
            DATA => $params
        ]);
    }

    public function sendToken($params = [])
    {
        $type_token = !empty($params['type_token']) ? $params['type_token'] : null;
        $phone = !empty($params['phone']) ? $params['phone'] : null;

        if(empty($type_token) || empty($phone) || !in_array($type_token, Configure::read('TYPE_TOKEN'))) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $token = $this->createTokenPhone([
            'phone' => $phone,
            'type' => $type_token
        ]);

        if(empty($token)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_ma_xac_nhan')]);
        }

        return $this->sendSms([
            'phone' => $phone,
            'message' => __d('template', 'ma_xac_thuc_otp_cua_quy_khach_la_{0}', $token)
        ]);
    }

    private function createTokenPhone($params = [], $lenght = 5)
    {
        $phone = !empty($params['phone']) ? $params['phone'] : null;
        $type = !empty($params['type']) ? $params['type'] : null;

        // validate
        if(empty($phone) || empty($type)) return null;

        if(!in_array($type, Configure::read('TYPE_TOKEN'))) return null;

        $table = TableRegistry::get('EmailToken');

        $exits_token = $table->find()->where([
            'phone' => $phone,
            'type' => $type,
            'status' => 0,
            'end_time >=' => time()
        ])->select(['code'])->first();

        if(!empty($exits_token['code'])) {
            return $exits_token['code'];
        }

        // create token
        $token = $this->Utilities->generateRandomNumber($lenght);
        $data_entity = $table->newEntity([
            'phone' => $phone,
            'type' => $type,
            'code' => $token,
            'end_time' => time() + 30*60,
            'status' => 0
        ]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($data_entity);

            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            return $token;

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }
}
