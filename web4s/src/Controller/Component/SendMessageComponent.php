<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Http\Client;
use Cake\Log\Log;

class SendMessageComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities'];

    public function send($type = null, $id_record = null, $lang = null) 
    {
        if(empty($type) || empty($id_record)) {
            $this->log('Dữ liệu không hợp lệ');
            return false;
        }
        $lang = !empty($lang[LANG]) ? $lang[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();

        $website_info = TableRegistry::get('Settings')->getSettingWebsite();
        $send_message = !empty($website_info['send_message']) ? $website_info['send_message'] : [];

        $slack = !empty($send_message['slack']) ? json_decode($send_message['slack'], true) : [];
        $status_slack = !empty($slack['status']) ? $slack['status'] : null;

        $telegram = !empty($send_message['telegram']) ? json_decode($send_message['telegram'], true) : [];
        $status_telegram = !empty($telegram['status']) ? $telegram['status'] : null;
        $apply = !empty($send_message['apply']) ? json_decode($send_message['apply'], true) : null;

        $data = [];
        switch ($type) {
            case ORDER:
                if(empty($apply['order'])) return false;

                $order_info = TableRegistry::get('Orders')->getDetailOrder($id_record, [
                    'get_items' => true,
                    'get_contact' => true,
                    'get_payment' => true
                ]);

                $data = TableRegistry::get('Orders')->formatDataOrderDetail($order_info, $lang);
                $payment_gateway = TableRegistry::get('PaymentsGateway')->getList($lang);

                $payment_info = !empty($order_info['Payments']) ? end($order_info['Payments']) : null;

                $payment_name = null;
                $payment_status = __d('template', 'cho_thanh_toan');
                if(!empty($payment_info['payment_method']) && $payment_info['payment_method'] == COD) {
                    $payment_name = !empty($payment_gateway[$payment_info['payment_method']]['name']) ? $payment_gateway[$payment_info['payment_method']]['name'] : null;
                }

                if(!empty($payment_info['payment_method']) && $payment_info['payment_method'] == BANK) {
                    $payment_name = !empty($payment_gateway[$payment_info['payment_method']]['name']) ? $payment_gateway[$payment_info['payment_method']]['name'] : null;
                }

                if(!empty($payment_info['payment_gateway_code'])) {
                    $payment_name = !empty($payment_gateway[$payment_info['payment_gateway_code']]['name']) ? $payment_gateway[$payment_info['payment_gateway_code']]['name'] : null;
                }

                $data['payment_name'] = $payment_name;

                if(empty($data['debt'])){
                    $payment_status = __d('template', 'da_thanh_toan');
                }
                $data['payment_status'] = $payment_status;

                break;

            case CONTACT:
                if(empty($apply['contact'])) return false;
                
                $data = TableRegistry::get('Contacts')->find()->contain(['ContactsForm'])->where([
                    'Contacts.id' => $id_record,
                    'Contacts.deleted' => 0
                ])->first();
                $data['value'] = !empty($data['value']) ? json_decode($data['value'], true) : [];

                break;
        }

        if(!empty($status_slack)) {
            $this->slack([
                'text' => $this->formatTextSlack($data, $type)
            ]);
        }

        if(!empty($status_telegram)) {
            $this->telegram([
                'text' => $this->formatTextTelegram($data, $type)
            ]);
        }

    }

    public function slack($params = [])
    {
        $text = !empty($params['text']) ? $params['text'] : null;

        $website_info = TableRegistry::get('Settings')->getSettingWebsite();
        $send_message = !empty($website_info['send_message']) ? $website_info['send_message'] : [];

        $slack = !empty($send_message['slack']) ? json_decode($send_message['slack'], true) : [];
        $status = !empty($slack['status']) ? $slack['status'] : null;
        $webhook = !empty($slack['webhook']) ? $slack['webhook'] : null;

        if(empty($status)) {
            $this->log('Trạng thái không hoạt động');
            return false;
        }

        if(empty($webhook)){
            $this->log('Dữ liệu không hợp lệ');
            return false;
        }

        try{
            $http = new Client();
            $response = $http->post($webhook, 
                [
                    'payload' => json_encode(['text' => $text])
                ]
            );

            $result = $response->getStringBody();

            if(empty($result) || $result != 'ok'){
                $this->log('Gửi thông báo SLACK không thành công', 'error');
                return false;
            }

            return true;

        }catch (NetworkException $e) {
            $this->log('Gửi thông báo SLACK không thành công: ' . $e->getMessage() , 'error');
            return false;
        }
    }

    public function telegram($params = [])
    {
        $text = !empty($params['text']) ? $params['text'] : null;

        $website_info = TableRegistry::get('Settings')->getSettingWebsite();
        $send_message = !empty($website_info['send_message']) ? $website_info['send_message'] : [];

        $telegram = !empty($send_message['telegram']) ? json_decode($send_message['telegram'], true) : [];

        $status = !empty($telegram['status']) ? $telegram['status'] : null;
        $token = !empty($telegram['token']) ? $telegram['token'] : null;
        $chat_id = !empty($telegram['chat_id']) ?  str_replace('-', '', $telegram['chat_id']) : null;

        if(empty($status)){
            $this->log('Trạng thái không hoạt động');
            return false;
        }
        if(empty($token) || empty($chat_id)) {
            $this->log('Dữ liệu không hợp lệ');
            return false;
        }

        try{
            $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
            $http = new Client();
            $response = $http->post($url,
                ['chat_id' => '-' . $chat_id, 'text' => $text, 'parse_mode' => 'html'],
                ['type' => 'json']
            );

            $result = $response->getStringBody();
            if(empty($result) || $result != 'ok'){
                $this->log('Gửi thông báo Telegram không thành công', 'error');
                return false;
            }

            return true;

        }catch (NetworkException $e) {
            $this->log('Gửi thông báo Telegram không thành công: ' . $e->getMessage() , 'error');
            return false;
        }       
    }

    private function formatTextSlack($data = null, $type = null) {
        if(empty($data) && empty($type)) return false;
        $result = '';
        switch ($type) {
            case ORDER:
                $result = ":gem::diamond_shape_with_a_dot_inside::gem: \n";

                if(!empty($data)){
                    $items = !empty($data['items']) ? $data['items'] : [];
                    if (!empty($items)) {
                        foreach($items as $item) {
                            $name_extend = !empty($item['name_extend']) ? $item['name_extend'] : '';
                            $result .= "*$name_extend* \n";
                        }
                    }
                    $result .= __d('template', 'ma_don_hang') .": <". $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] ."/admin/order/detail/". $data['id']."|*". $data['code'] ."*> \n";
                    $result .= __d('template', 'so_tien') . ": *" . number_format($data['total'], 0, '.', ',') . CURRENCY_UNIT . "* \n";
                    $result .= __d('template', 'phuong_thuc_thanh_toan') . ": *" . $data['payment_name'] . "* (".$data['payment_status'].") \n";
                    $result .= __d('template', 'ten_khach_hang') . ": *" . $data['contact']['full_name'] . "* \n";
                    $result .= __d('template', 'dien_thoai') . ": *" . $data['contact']['phone'] . "* \n";
                    $result .= "```".__d('template', 'cac_dich_vu_dang_ky_qua_website') . " " . $_SERVER['HTTP_HOST']."```";
                }


                break;

            case CONTACT:
                if(!empty($data['ContactsForm']['name'])){
                    $result = ":white_check_mark::white_check_mark::white_check_mark: \n";
                    $result .= __d('template', 'co_thong_tin_lien_he_tu') .": <". $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] ."/admin/contact/detail/". $data['id']."|*". $data['ContactsForm']['name'] ."*> \n";

                    if(!empty($data['value']['full_name'])) {
                        $result .= __d('template', 'ho_va_ten') . ": *".$data['value']['full_name']."* \n";
                    }

                    if(!empty($data['value']['phone'])) {
                        $result .= __d('template', 'so_dien_thoai') . ": *".$data['value']['phone']."* \n";
                    }

                    if(!empty($data['value']['email'])) {
                        $result .= __d('template', 'so_dien_thoai') . ": *".$data['value']['email']."* \n";
                    }
                }

                break;
        }

        return $result;
    }

    private function formatTextTelegram($data = null, $type = null) {
        if(empty($data) && empty($type)) return false;
        $result = '';
        switch ($type) {
            case ORDER:
                $result = "\u{1F48E}\u{1F4A0}\u{1F48E}\n";

                if(!empty($data)){
                    $items = !empty($data['items']) ? $data['items'] : [];
                    if (!empty($items)) {
                        foreach($items as $item) {
                            $name_extend = !empty($item['name_extend']) ? $item['name_extend'] : '';
                            $result .= "<b>$name_extend</b> \n";
                        }
                    }
                    $result .= __d('template', 'ma_don_hang') .": <b><a href='". $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] ."/admin/order/detail/". $data['id']."'>". $data['code'] ."</a></b> \n";
                    $result .= __d('template', 'so_tien') . ": <b>" . number_format($data['total'], 0, '.', ',') . CURRENCY_UNIT . "</b> \n";
                    $result .= __d('template', 'phuong_thuc_thanh_toan') . ": <b>" . $data['payment_name'] . "</b> (".$data['payment_status'].") \n";
                    $result .= __d('template', 'ten_khach_hang') . ": <b>" . $data['contact']['full_name'] . "</b> \n";
                    $result .= __d('template', 'dien_thoai') . ": <b>" . $data['contact']['phone'] . "</b> \n";
                    $result .= "<code>".__d('template', 'cac_dich_vu_dang_ky_qua_website') . " " . $_SERVER['HTTP_HOST']."</code>";
                }


                break;

            case CONTACT:
                if(!empty($data['ContactsForm']['name'])){
                    $result = "\u{2705}\u{2705}\u{2705}\n";
                    $result .= __d('template', 'co_thong_tin_lien_he_tu') .": <b><a href='". $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] ."/admin/contact/detail/". $data['id']."'>". $data['ContactsForm']['name'] ."</a></b> \n";
                    
                    if(!empty($data['value']['full_name'])) {
                        $result .= __d('template', 'ho_va_ten') . ": <b>".$data['value']['full_name']."</b> \n";
                    }

                    if(!empty($data['value']['phone'])) {
                        $result .= __d('template', 'so_dien_thoai') . ": <b>".$data['value']['phone']."</b> \n";
                    }

                    if(!empty($data['value']['email'])) {
                        $result .= __d('template', 'so_dien_thoai') . ": <b>".$data['value']['email']."</b> \n";
                    }
                }

                break;
        }

        return $result;
    }
}