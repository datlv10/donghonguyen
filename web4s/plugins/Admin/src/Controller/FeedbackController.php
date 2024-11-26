<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\Http\Client;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;

class FeedbackController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function index()
    {
        $this->js_page = [
            '/assets/js/pages/feedback.js'
        ];

        $this->set('title_for_layout', __d('admin', 'gui_yeu_cau'));
        $this->set('path_menu', 'feedback');
    }

    public function success()
    {
        $this->set('title_for_layout', __d('admin', 'gui_yeu_cau_thanh_cong'));
        $this->set('path_menu', 'feedback');
    }

    public function send()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
        $data = $this->getRequest()->getData();

        $full_name = !empty($data['full_name']) ? trim($data['full_name']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;
        $content = !empty($data['content']) ? trim($data['content']) : null;
        $files = !empty($data['files']) ? json_decode($data['files'], true) : [];

        if(empty($full_name)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ho_va_ten')]);
        }

        if(empty($phone)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_so_dien_thoai')]);
        }

        if(empty($content)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_noi_dung')]);
        }        

        $request = $this->getRequest();
        $scheme = $request->scheme();
        $host = $request->host();

        $send_email = $send_slack = false;        

        // gửi slack
        $website = "<$scheme://$host| $host>";
        $text = "*--------------------- YÊU CẦU HỖ TRỢ  * \n *Từ:* $website \n *Tên khách hàng:* $full_name \n *Số điện thoại:* $phone \n *Nội dung:* $content \n";

        if(!empty($files)){
            $text .= "*Tệp đính kèm:* \n";
            foreach($files as $file){
                $url = CDN_URL . $file;
                $path = parse_url($url, PHP_URL_PATH);
                $file_name = basename($path);
                $text .= "<$url| $file_name> \n";
            }
        }
        $http = new Client();

        $webhook = 'https://hooks.slack.com/services/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';
        $response = $http->post($webhook, [
            'payload' => json_encode(['text' => $text])
        ]);

        $result = $response->getStringBody();
        if($result == 'ok') $send_slack = true;

        // gửi email
        if(!$send_slack){
            $mail_content = "----------- YÊU CẦU HỖ TRỢ WEBSITE <br> Từ: $scheme://$host <br> Tên khách hàng: $full_name <br> Số điện thoại: $phone <br> Nội dung: $content <br>";
            if(!empty($files)){
                $mail_content .= "Tệp đính kèm: <br>";
                foreach($files as $file){
                    $url = CDN_URL . $file;
                    $path = parse_url($url, PHP_URL_PATH);
                    $file_name = basename($path);
                    $mail_content .= "<a href=\"$url\">$file_name</a><br>";
                }
            }

            $from_email = 'no-reply@web4s.vn';
            $password = 'xyUDCr8ZuD';
            TransportFactory::setConfig('umail', [
                'host' => 'ssl://smtp.umailsmtp.com',
                'port' => 465,
                'username' => $from_email,
                'password' => $password,
                'className' => 'Smtp'
            ]);

            try{
                $mailer = new Mailer();
                $mailer->setTransport('umail');
                $mailer->setTo('lydt@nhanhoa.com.vn');
                $mailer->addBcc(['sennt@nhanhoa.com.vn']);
                $mailer->setFrom($from_email, 'Web4s.vn');
                $mailer->setSubject('Yêu cầu hỗ Website');
                $mailer->setEmailFormat('html');
                $mailer->deliver($mail_content);

                $send_email = true;

            } catch (Exception $e) {
                $send_email = false;

                $this->responseJson([MESSAGE => __d('admin', 'gui_yeu_cau_khong_thanh_cong')]);
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'gui_yeu_cau_thanh_cong')
        ]);
    }

    public function uploadFiles()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        $data = $this->getRequest()->getData();
        $file = !empty($data['file']) ? $data['file'] : [];
        if(empty($file)) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        $file_upload = [
            'type' => $file->getClientMediaType(),
            'size' => $file->getSize(),
            'error' => $file->getError(),
            'name' => $file->getClientFilename(),
            'tmp_name' => $file->getStream()->getMetadata('uri')
        ];

        $result = $this->loadComponent('Upload')->uploadToCdn($file_upload, 'my-feedback');
        $this->responseJson($result);
    }
}