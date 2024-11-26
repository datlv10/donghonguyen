<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;
use Cake\Log\Log;
use Cake\Filesystem\File;
use Cake\Datasource\ConnectionManager;

class EmailComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function send($params = [])
    {
        $send_in_background = false;
        if (defined('SEND_EMAIL_BACKGROUND') && !empty(SEND_EMAIL_BACKGROUND) && PHP_OS === 'Linux') {
            $send_in_background = true;
        }

        if($send_in_background){
            $request = $this->controller->getRequest();
            $url = $request->scheme() . '://' . $request->host() . '/job/send-email';
            $cmd = "curl -X POST -d '" . http_build_query($params) . "' '" . $url . "'";
            if (!empty($_SERVER['HTTPS'])) {
                $cmd .= "'  --insecure";
            }
            $cmd .= " > /dev/null 2>&1 &"; //bỏ qua chờ phản hồi từ server

            // lưu ý kiểm function exec() hoặc shell_exec có thể bị chặn vì lý do bảo mật. Kiểm tra file log nếu không thể gửi email
            exec($cmd);
            
            return $this->System->getResponse([CODE => SUCCESS, MESSAGE => 'Đã gửi email dưới dạng JOB']);
        }else{
            return $this->sendEmail($params);
        }
    }

    public function sendEmail($params = [])
    {
        // params        
        $code = !empty($params['code']) ? $params['code'] : null;
        $id_record = !empty($params['id_record']) ? $params['id_record'] : null;
        $to_email = !empty($params['to_email']) ? $params['to_email'] : null;
        $send_try_content = !empty($params['send_try_content']) ? true : false;
        $generate_token = !empty($params['generate_token']) ? $params['generate_token'] : null;

        $from_website_template = !empty($params['from_website_template']) ? true : false;

        $title_email = 'EMAIL SEND TRY';
        $view = null;
        $token = null;            

        $path_template = defined('PATH_TEMPLATE') ? PATH_TEMPLATE : null;
        if($from_website_template){
            $path_template = TableRegistry::get('Templates')->getPathTemplate();            
        }

        // validate params
        if (empty($path_template)) {
            return $this->System->getResponse([MESSAGE => 'Không lấy được thông tin giao diện']);
        }

        if(empty($to_email)){
            $this->log('Không lấy được thông tin email nhận', 'error');
            return $this->System->getResponse([MESSAGE => 'Không lấy được thông tin email nhận']);
        }

        if($send_try_content){
            $content = !empty($params['content']) ? $params['content'] : null;
            if(empty($content)){
                $this->log('Vui lòng nhập nội dung gửi thử', 'error');
                return $this->System->getResponse([MESSAGE => 'Vui lòng nhập nội dung gửi thử']);
            }        
        }else{
            if(empty($code)){
                $this->log('Không lấy được thông tin mẫu Email', 'error');
                return $this->System->getResponse([MESSAGE => 'Không lấy được thông tin mẫu Email']);
            }

            $template_email = TableRegistry::get('EmailTemplates')->getEmailTemplateBycode($code);
            $title_email = !empty($template_email['title_email']) ? $template_email['title_email'] : null;
            $view = !empty($template_email['template']) ? $template_email['template'] : null;

            $cc_email = !empty($template_email['cc_email']) ? explode(', ', $template_email['cc_email']) : null;
            $bcc_email = !empty($template_email['bcc_email']) ? explode(', ', $template_email['bcc_email']) : null;
            
            if(empty($view)){
                $this->log('Không lấy được thông tin mẫu Email', 'error');
                return $this->System->getResponse([MESSAGE => 'Không lấy được thông tin mẫu Email']);
            }

            $file = new File($path_template . 'email' . DS . HTML . DS . $view, false);
            if(!$file->exists()){
                $this->log('Không lấy được thông tin mẫu Email', 'error');
                return $this->System->getResponse([MESSAGE => 'Không lấy được thông tin mẫu Email']);
            }

            $view = !empty($view) ? str_replace('.tpl', '', $view) : null;

            if(!empty($generate_token) && in_array($generate_token, Configure::read('TYPE_TOKEN'))) {
                $token = $this->createToken([
                    'email' => $to_email,
                    'type' => $generate_token
                ]);

                if(empty($token)){
                    return $this->System->getResponse([MESSAGE => 'Không lấy được mã xác nhận Email']);
                }
            }
        }
        
        //get email config
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting_website_info = !empty($settings['website_info']) ? $settings['website_info'] : [];

        $website_info = TableRegistry::get('Settings')->formatDataWebsiteInfoByLang($setting_website_info);
        $lang = LANGUAGE_DEFAULT;
        if(defined('LANGUAGE')){
            $lang = LANGUAGE;
        }
        $website_info = !empty($website_info[$lang]) ? $website_info[$lang] : [];
        
        $config = !empty($settings['email']) ? $settings['email'] : [];
        
        $smtp_host = !empty($config['smtp_host']) ? $config['smtp_host'] : null;
        $smtp = !empty($config['smtp']) ? $config['smtp'] : null;
        $port = !empty($config['port']) ? intval($config['port']) : null;
        $from_email = !empty($config['email']) ? $config['email'] : null;

        if(empty($from_email)){
            $this->log('Vui lòng cấu hình Email ứng dụng', 'error');
            return $this->System->getResponse([MESSAGE => 'Vui lòng cấu hình Email ứng dụng']);
        }

        switch ($smtp_host) {
            case 'gmail':
                if(empty(TransportFactory::getConfig('gmail'))){
                    TransportFactory::setConfig('gmail', [
                        'host' => 'ssl://smtp.gmail.com',
                        'port' => 465,
                        'username' => $from_email,
                        'password' => !empty($config['application_password']) ? $config['application_password'] : null,
                        'className' => 'Smtp'
                    ]);
                }
                break;
            case 'umail':
                if(empty(TransportFactory::getConfig('umail'))){
                    TransportFactory::setConfig('umail', [
                        'host' => 'ssl://smtp.umailsmtp.com',
                        'port' => 465,
                        'username' => $from_email,
                        'password' => !empty($config['application_password']) ? $config['application_password'] : null,
                        'className' => 'Smtp'
                    ]);
                }
                break;
            case 'other':
                if(empty(TransportFactory::getConfig('other'))){
                    TransportFactory::setConfig('other', [
                        'host' => $smtp,
                        'port' => $port,
                        'username' => $from_email,
                        'password' => !empty($config['application_password']) ? $config['application_password'] : null,
                        'className' => 'Smtp'
                    ]);
                }
                
                break;
        }
        
        // rewrite config template path
        Configure::write('App.paths.templates', $path_template);
        Configure::write('App.paths.locales', $path_template . 'locales' . DS);

        // send email by mailer
        try{
            $mailer = new Mailer();
            $mailer->setTransport($smtp_host);
            $mailer->setTo($to_email);

            if(!empty($cc_email)){
                $mailer->addCc($cc_email);
            }

            if(!empty($bcc_email)){
                $mailer->addBcc($bcc_email);
            }
            
            $mailer->setFrom($from_email, !empty($website_info['website_name']) ? $website_info['website_name'] : 'Web4s.vn');
            $mailer->setSubject($title_email);
            $mailer->setEmailFormat('html');

            if($send_try_content){
                $mailer->deliver($content);
            }else{
                $mailer->viewBuilder()
                ->setClassName('Smarty')
                ->setLayout('default') // dir layout --- /templates/[code]/layout/email/html/default
                ->setTemplatePath('email')
                ->setTemplate($view);

                $mailer->setViewVars([
                    'id_record' => $id_record,
                    'token' => $token
                ]);

                $mailer->deliver();
            }

            return $this->System->getResponse([CODE=> SUCCESS, MESSAGE => 'Gửi email thành công']);

        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    private function createToken($params = [], $lenght = 5)
    {
        $email = !empty($params['email']) ? $params['email'] : null;
        $type = !empty($params['type']) ? $params['type'] : null;

        // validate
        if(empty($email) || empty($type)) return null;

        if(!in_array($type, Configure::read('TYPE_TOKEN'))) return null;

        $table = TableRegistry::get('EmailToken');

        $email_token_info = $table->find()->where([
            'email' => $email,
            'type' => $type,
            'status' => 0,
            'end_time >=' => time()
        ])->select(['code'])->first();

        if(!empty($email_token_info['code'])) {
            return $email_token_info['code'];
        }


        // create token
        $token = $this->Utilities->generateRandomNumber($lenght);
        $data_entity = $table->newEntity([
            'email' => $email,
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

            return null;
        }
    }
}
