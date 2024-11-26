<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Firebase\JWT\JWT;
use Cake\Filesystem\Folder;
use UnexpectedValueException;

class AppController extends Controller {

    public $data_bearer = [];
    public $token = '';
    public $secret_key = '';

    public function beforeFilter(EventInterface $event)
    {
        $this->viewBuilder()->setClassName('Json');

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $language = $this->loadComponent('System')->getLanguageFrontend();        
        if(empty($language)){
            $this->responseErrorApi([
                MESSAGE => 'Không lấy được thông tin ngôn ngữ của Website'
            ]);
        }

        $currency_info = $this->loadComponent('System')->getCurrencyFrontend();
        if(empty($currency_info)){
            $this->responseErrorApi([
                MESSAGE => 'Chưa cài đặt đơn vị tiền tệ mặc định của Website'
            ]);
        }

        // get info mobile template
        $template_code = null;

        $plugins = TableRegistry::get('Plugins')->getList();
        if(!empty($plugins[MOBILE_APP]) && !defined('CODE_MOBILE_TEMPLATE')){
            $mobile_template = TableRegistry::get('MobileTemplate')->getTemplateDefault();
            $template_code = !empty($mobile_template['code']) ? $mobile_template['code'] : null;
            if(empty($template_code)){
                $this->responseErrorApi([
                    MESSAGE => 'Không lấy được thông tin giao diện'
                ]);
            }
        }

        $path_template = SOURCE_DOMAIN  . DS . 'templates' . DS . 'mobile_' . $template_code . DS;
        $url_template = '/templates/mobile_' . $template_code . '/';
        $folder_template = new Folder($path_template);

        if(empty($folder_template->path)){
            $this->responseErrorApi([
                MESSAGE => 'Không tìm thấy thư mục chứa giao diện'
            ]);
        }

        // set config path mobile template
        Configure::write('App.paths.templates', $path_template);
        Configure::write('App.paths.locales', $path_template . 'locales' . DS);

        define('LANGUAGE', $language);
        define('CURRENCY_CODE', !empty($currency_info['code']) ? $currency_info['code'] : null);
        define('CURRENCY_RATE', !empty($currency_info['exchange_rate']) ? $currency_info['exchange_rate'] : null);
        define('CURRENCY_UNIT', !empty($currency_info['unit']) ? $currency_info['unit'] : null);
        define('PATH_TEMPLATE', $path_template);
        define('URL_TEMPLATE', $url_template);
        define('CODE_TEMPLATE', $template_code);
        define('CODE_MOBILE_TEMPLATE', $template_code);

        if(!defined('CDN_URL')){
            if(!empty($settings['profile']['cdn_url'])){
                define('CDN_URL', $settings['profile']['cdn_url']);
            }else{
                define('CDN_URL', $this->request->scheme() . '://cdn.' . $this->request->host());
            }
        }


        // check auth api
        $api_setting = !empty($settings['api']) ? $settings['api'] : [];
        $this->secret_key = !empty($api_setting['secret_key']) ? $api_setting['secret_key'] : ''; 
        
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');

        $check_auth = true;
        if(in_array($controller, ['Website'])){
            $check_auth = false;
        }

        // bỏ qua check auth api nếu nhận kết quả thanh toán
        if($controller == 'Payment' && $action == 'returnPayment'){
            $check_auth = false;
        }

        $website_mode = !empty($settings['website_mode']['type']) ? $settings['website_mode']['type'] : null;
        if(in_array($action, ['generateBearerToken', 'getDataFromToken']) && $website_mode == DEVELOP){
            $check_auth = false;
        }

        if($check_auth){
            if(empty($this->secret_key)){
                $this->responseErrorApi([
                    MESSAGE => 'Chưa cấu hình mã bảo mật API'
                ]);
            }

            $this->data_bearer = $this->getBearerHeaderData();
        }
    }

    protected function getBearerHeaderData()
    {
        $header_auth = !empty($this->request->getHeader('Authorization')[0]) ? $this->request->getHeader('Authorization')[0] : null;
        if(empty($header_auth)){
            $this->responseErrorApi([
                STATUS => 401,
                MESSAGE => 'Auth Bearer chưa hợp lệ'
            ]);   
        };

        list($auth_type, $bearer_token) = explode(' ', $header_auth, 2);
        if($auth_type != 'Bearer' || empty($bearer_token)){
            $this->responseErrorApi([
                STATUS => 401,
                MESSAGE => 'Auth Bearer chưa hợp lệ'
            ]);
        }
        $this->token = $bearer_token;

        $jwt = new JWT();
        try{            
            $result = (array)$jwt->decode($bearer_token, $this->secret_key, ['HS256']);
        }catch (UnexpectedValueException $e) {
            $this->responseErrorApi([
                STATUS => 401,
                MESSAGE => $e->getMessage()
            ]);
        }

        return $result;
    }

    public function getDataFromToken()
    {
        $data = $this->getRequest()->getData();
        $token = !empty($data['token']) ? $data['token'] : '';

        $result = [];
        if(!empty($token)){
            $jwt = new JWT();
            try{            
                $result = (array)$jwt->decode($token, $this->secret_key, ['HS256']);
            }catch (UnexpectedValueException $e) {
                $this->responseErrorApi([
                    STATUS => 401,
                    MESSAGE => $e->getMessage()
                ]);
            }
        }

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }

    public function generateBearerToken()
    {
        $data = $this->getRequest()->getData();
        $jwt = new JWT();
        try{
            $token = $jwt->encode($data, $this->secret_key);
        }catch (Exception $e) {
            $this->responseErrorApi([
                STATUS => 401,
                MESSAGE => $e->getMessage()
            ]);
        }

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $token
        ]);
    }

	public function notFound()
    {
        $this->responseErrorApi([
            MESSAGE => 'Đường dẫn API không hợp lệ'
        ]);
    }

    protected function responseApi($params = [])
    {
        $code = SUCCESS;
        $status = !empty($params[STATUS]) ? intval($params[STATUS]) : 200;
        $message = !empty($params[MESSAGE]) ? $params[MESSAGE] : null;
        $data = !empty($params[DATA]) ? $params[DATA] : [];
        $extend = [];
        if(!empty($params[EXTEND])){
            $extend = $params[EXTEND];
        }
        
        $extend['cdn_url'] = CDN_URL;
        $extend[LANG] = LANGUAGE;

        if(!empty($this->request->getSession()->read(MEMBER))) {
            $extend['logged'] = true;
        } else {
            $extend['logged'] = false;
        }
        
        $result = [
            CODE => $code,
            STATUS => $status,
            MESSAGE => $message,
            EXTEND => $extend,
            DATA => $data
        ];        

        $this->set(compact(CODE, STATUS, MESSAGE, EXTEND, DATA));
        $this->viewBuilder()->setOption('serialize', [CODE, STATUS, MESSAGE, EXTEND, DATA]);
    }

    protected function responseErrorApi($params = [])
    {
        $result = [
            CODE => ERROR,
            STATUS => !empty($params[STATUS]) ? intval($params[STATUS]) : 200,
            MESSAGE => !empty($params[MESSAGE]) ? $params[MESSAGE] : null
        ];

        exit(json_encode($result));
    }

    protected function responseJson($params = []) 
    {
        $code = ERROR;
        if(!empty($params[CODE]) && in_array($params[CODE], [SUCCESS, ERROR])){
            $code = $params[CODE];
        }

        $message = !empty($params[MESSAGE]) ? $params[MESSAGE] : null;
        if(empty($params[MESSAGE]) && $code == ERROR){
            $message = __d('template', 'xu_ly_du_lieu_khong_thanh_cong');
        }

        if(empty($params[MESSAGE]) && $code == SUCCESS){
            $message = __d('template', 'xu_ly_du_lieu_thanh_cong');
        }
        
        $result = [
            CODE => $code,
            STATUS => !empty($params[STATUS]) ? intval($params[STATUS]) : 200,
            MESSAGE => $message
        ];

        if(isset($params[DATA])){
            $result[DATA] = !empty($params[DATA]) ? $params[DATA] : [];
        }

        if(isset($params[META])){
            $result[META] = !empty($params[META]) ? $params[META] : [];
        }

        exit(json_encode($result));
    }

}

