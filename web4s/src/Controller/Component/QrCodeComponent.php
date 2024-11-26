<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Utility\Hash;

use App\Lib\BankQR\Functions;
use App\Lib\BankQR\Field;
use App\Lib\BankQR\CRC;
use App\Lib\BankQR\InvalidBankIdException;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use Endroid\QrCode\Color\Color;

class QrCodeComponent extends Component
{
	public $controller = null;
    public $components = ['System' ,'Utilities'];
    public $type = null;
    public $fields = [];
    public $setting = [];

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->controller = $this->_registry->getController();
    }
   
    public function generateQrCode($fields = [], $type = null, $setting = [])
    {
        // kiểm tra tính hợp lệ của dữ liệu
        if(empty($type) || !in_array($type, [URL, TEXT, EMAIL, BANK_ACCOUNT, BANK_TRANSACTION])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty($fields)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        // đọc cấu hình mã QR 
        if(empty($setting)){
            $all_setting = TableRegistry::get('Settings')->getSettingWebsite();
            if(in_array($type, [BANK_ACCOUNT, BANK_TRANSACTION])){
                $setting = !empty($all_setting['qr_bank_transaction']['config']) ? json_decode($all_setting['qr_bank_transaction']['config'], true) : [];
            }else{
                $setting = !empty($all_setting['qr_normal']['config']) ? json_decode($all_setting['qr_normal']['config'], true) : [];
            }
        }

        $this->type = $type;
        $this->fields = $fields;
        $this->setting = $setting;

        $result = [];
        switch($type){
            case URL:
                $result = $this->_urlGenerate();
            break;

            case TEXT:
                $result = $this->_textGenerate();
            break;

            case EMAIL:
                $result = $this->_emailGenerate();
            break;

            case BANK_ACCOUNT:
                $result = $this->_bankAccountGenerate();
            break;

            case BANK_TRANSACTION:
                $result = $this->_bankTransactionGenerate();
            break;
        }

        return $this->System->getResponse($result);
    }

    private function _urlGenerate()
    {
        // kiểm tra dữ liệu tạo mã QR
        $url = !empty($this->fields['url']) && is_string($this->fields['url']) ? $this->fields['url'] : null;

        if(empty($url) || filter_var($url, FILTER_VALIDATE_URL) === false){            
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['url'])]);
        }

        if(strlen($url) > 200){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_qua_dai_do_dai_cho_phep_la_{1}_ky_tu', ['url', 200])]);
        }
        
        // tạo mã
        return $this->_createImageNormal($url);
    }

    private function _textGenerate()
    {
        // kiểm tra dữ liệu tạo mã QR
        $text = !empty($this->fields['text']) && is_string($this->fields['text']) ? $this->fields['text'] : null;

        if(empty($text)){            
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['text'])]);
        }

        if(strlen($text) > 200){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_qua_dai_do_dai_cho_phep_la_{1}_ky_tu', ['text', 200])]);
        }
        
        // tạo mã
        return $this->_createImageNormal($text);
    }

    private function _emailGenerate()
    {
        // kiểm tra dữ liệu tạo mã QR
        $email = !empty($this->fields['email']) && is_string($this->fields['email']) ? $this->fields['email'] : null;
        $subject = !empty($this->fields['subject']) && is_string($this->fields['subject']) ? $this->fields['subject'] : null;
        $body  = !empty($this->fields['body']) && is_string($this->fields['body']) ? $this->fields['body'] : null;

        // email
        if (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['email'])]);
        }

        if(strlen($email) > 200){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_qua_dai_do_dai_cho_phep_la_{1}_ky_tu', ['email', 200])]);
        }

        // subject
        if (empty($subject)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['subject'])]);
        }

        if(strlen($subject) > 200){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_qua_dai_do_dai_cho_phep_la_{1}_ky_tu', ['subject', 200])]);
        }

        // body
        if (empty($body)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['body'])]);
        }

        if(strlen($body) > 200){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_qua_dai_do_dai_cho_phep_la_{1}_ky_tu', ['body', 200])]);
        }

        $text = 'mailto:'.$email.'?subject='.urlencode($subject).'&body='.urlencode($body);

        // tạo mã
        return $this->_createImageNormal($text);
    }

    private function _bankAccountGenerate()
    {
        // kiểm tra dữ liệu tạo mã QR
        $bank = !empty($this->fields['bank']) && is_string($this->fields['bank']) ? $this->fields['bank'] : null;
        $account = !empty($this->fields['account']) && is_string($this->fields['account']) ? $this->fields['account'] : null;

        $banks = $this->getBanks();
        if(empty($bank) || empty($banks[$bank])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['bank'])]);
        }

        if(empty($account) || strlen($account) <= 5 || strlen($account) >= 50){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['account'])]);
        }        

        // xử lý dữ liệu tạo mã
        $str_generate = '';        
        $str_generate = Functions::addField($str_generate, Field::VERSION, '01');
        
        // loại thanh toán
        $payment_type = '11'; //(pay_type = 11 -> chỉ có thông tin chuyển khoản, ko có số tiền và mô tả giao dịch)
        $str_generate = Functions::addField($str_generate, Field::INITIATION_METHOD, $payment_type);

        // thông tin tài khoản 
        try{
            $is_card = false;
            $consumer_info = Functions::generateMerchantInfo($bank, $account, $is_card);
        }catch(InvalidBankIdException $ex){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['bank'])]);
        }        
        $str_generate = Functions::addField($str_generate, Field::CONSUMER_INFO, $consumer_info);

        // mã tiền tệ (tìm hiểu thêm trong QR_Format_T&C_v1.0_VN_092021.pdf)
        $str_generate = Functions::addField($str_generate, Field::CURRENCY_CODE, '704'); // 704 -> VND
        
        // mã quốc gia (tìm hiểu thêm trong QR_Format_T&C_v1.0_VN_092021.pdf)
        $str_generate = Functions::addField($str_generate, Field::COUNTRY_CODE, 'VN');

        // mã kiểm thử giá trị CRC (tìm hiểu thêm trong QR_Format_T&C_v1.0_VN_092021.pdf)
        $crc = CRC::crcChecksum($str_generate . Field::CRC . '04');
        $crc = str_pad($crc, 4, '0', STR_PAD_LEFT);
        $str_generate = Functions::addField($str_generate, Field::CRC, $crc);

        // tạo mã
        return $this->_createImageBank($str_generate);        
    }

    private function _bankTransactionGenerate()
    {
        // kiểm tra dữ liệu tạo mã QR
        $bank = !empty($this->fields['bank']) && is_string($this->fields['bank']) ? $this->fields['bank'] : null;
        $account = !empty($this->fields['account']) && is_string($this->fields['account']) ? $this->fields['account'] : null;
        $amount = !empty($this->fields['amount']) && is_string($this->fields['amount']) ? intval($this->fields['amount']) : null;
        $info = !empty($this->fields['info']) && is_string($this->fields['info']) ? Text::slug(strtolower($this->fields['info']), ' ') : null;

        $banks = $this->getBanks();
        if(empty($bank) || empty($banks[$bank])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['bank'])]);
        }

        if(empty($account) || strlen($account) <= 5 || strlen($account) >= 50){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['account'])]);
        }
        
        if(empty($amount) || !Functions::isValidAmount($amount)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['amount'])]);
        }

        if(empty($info) || strlen($info) >= 200){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tham_so_{0}_khong_hop_le', ['info'])]);
        }

        // xử lý dữ liệu tạo mã
        $str_generate = '';        
        $str_generate = Functions::addField($str_generate, Field::VERSION, '01');
        
        // loại thanh toán
        $payment_type = '12'; // chuyển khoản có số tiền (pay_type = 12 -> có số tiền và thông tin chuyển khoản)
        $str_generate = Functions::addField($str_generate, Field::INITIATION_METHOD, $payment_type);

        // thông tin tài khoản        
        try{
            $is_card = false;
            $consumer_info = Functions::generateMerchantInfo($bank, $account, $is_card);
        }catch(InvalidBankIdException $ex){
            $this->System->getResponse([MESSAGE => __d('locale', 'tham_so_{0}_khong_hop_le', ['bank'])]);
        }        
        $str_generate = Functions::addField($str_generate, Field::CONSUMER_INFO, $consumer_info);

        // mã tiền tệ (tìm hiểu thêm trong QR_Format_T&C_v1.0_VN_092021.pdf)
        $str_generate = Functions::addField($str_generate, Field::CURRENCY_CODE, '704'); // 704 -> VND
        
        // số tiền thanh toán
        $str_generate = Functions::addField($str_generate, Field::TRANSACTION_AMOUNT, $amount);

        // mã quốc gia (tìm hiểu thêm trong QR_Format_T&C_v1.0_VN_092021.pdf)
        $str_generate = Functions::addField($str_generate, Field::COUNTRY_CODE, 'VN');

        // nội dung thanh toán
        $ref = Functions::addField('', Field::ADDITION_REF, $info);
        $str_generate = Functions::addField($str_generate, Field::ADDITION, $ref);        

        // mã kiểm thử giá trị CRC (tìm hiểu thêm trong QR_Format_T&C_v1.0_VN_092021.pdf)
        $crc = CRC::crcChecksum($str_generate . Field::CRC . '04');
        $crc = str_pad($crc, 4, '0', STR_PAD_LEFT);
        $str_generate = Functions::addField($str_generate, Field::CRC, $crc);

        // tạo mã
        return $this->_createImageBank($str_generate);        
    }

    private function _createImageNormal($data = [])
    {
        $font_base_path = APP . 'Lib\BankQR\fonts' . DS;

        // lấy cấu hình
        $general_setting = !empty($this->setting['general']) ? $this->setting['general'] : [];
        $qrcode_setting = !empty($this->setting['qrcode']) ? $this->setting['qrcode'] : [];
        $info_setting = !empty($this->setting['info']) ? $this->setting['info'] : [];

        $setting = [
            'general' => [
                'height' => !empty($general_setting['height']) ? $general_setting['height'] : 400, // default 400
                'width' => !empty($general_setting['width']) ? $general_setting['width'] : 400, // default 400
                'background' => !empty($general_setting['background']) ? $general_setting['background'] : '#fff', // default #fff
                'color' => !empty($general_setting['color']) ? $general_setting['color'] : '#000', // default #000
                'font' => !empty($general_setting['font']) ? $general_setting['font'] : '',
                'font_size' => !empty($general_setting['font_size']) ? intval($general_setting['font_size']) : 12,
                'margin_bottom' => !empty($general_setting['margin_bottom']) ? intval($general_setting['margin_bottom']) : 10
            ],
            'qrcode' => [
                'background' => !empty($qrcode_setting['background']) ? $qrcode_setting['background'] : '',
                'color' => !empty($qrcode_setting['color']) ? $qrcode_setting['color'] : '',
                'logo' => !empty($qrcode_setting['logo']) ? trim($qrcode_setting['logo']) : '',
            ],            
            'info' => [
                'display' => !empty($info_setting['display']) ? true : false,
                'color' => !empty($info_setting['color']) ? $info_setting['color'] : '',
                'font_size' => !empty($info_setting['font_size']) ? intval($info_setting['font_size']) : 12
            ],
        ];    

        // đọc cấu hình chung
        $background_rgb = ['r' => 255, 'g' => 255, 'b' => 255];
        $color_rgb = ['r' => 0, 'g' => 0, 'b' => 0 ];
        
        $font_path = $font_base_path . 'Roboto-Medium.ttf';
        $font_size = 12;
        if(!empty($setting['general'])){
            $general_background = !empty($setting['general']['background']) ? $setting['general']['background'] : null;
            $general_color = !empty($setting['general']['color']) ? $setting['general']['color'] : null;
            $general_font = !empty($setting['general']['font']) ? $setting['general']['font'] : null;            
            $general_font_size = !empty($setting['general']['font_size']) ? $setting['general']['font_size'] : 12;            

            if(!empty($general_background)){
                $rgb = $this->_hexToRgb($general_background);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 255;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 255;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 255;

                $background_rgb = ['r' => $r, 'g' => $g, 'b' => $b];

            }

            if($general_color){
                $rgb = $this->_hexToRgb($general_color);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 0;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 0;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 0;

                $color_rgb = ['r' => $r, 'g' => $g, 'b' => $b];
            }

            if(!empty($general_font)) $font_path = $font_base_path . $general_font;
            if(!empty($general_font_size)) $font_size = $general_font_size;
        }

        $height = !empty($setting['general']['height']) ? intval($setting['general']['height']) : 400;
        $width = !empty($setting['general']['width']) ? intval($setting['general']['width']) : 400;
        $margin_bottom = !empty($setting['general']['margin_bottom']) ? intval($setting['general']['margin_bottom']) : 10;

        if($height > 2000) $height = 2000;
        if($width > 2000) $width = 2000;

        $position_y = 0; // vị trí chiều dọc

        // tạo ảnh nền frame_image
        $frame_image = imagecreatetruecolor($width, $height);


        // set màu ảnh nền
        $background_frame = imagecolorallocate($frame_image, $background_rgb['r'], $background_rgb['g'], $background_rgb['b']);
        imagefill($frame_image, 0, 0, $background_frame);

        
        // tạo ảnh mã QR
        $width_qrcode = $width;
        $qr_image = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($width_qrcode)
            ->margin(0)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->validateResult(false);


        // đọc cấu hình mã QR
        if(!empty($setting['qrcode'])){
            $qrcode_logo = !empty($setting['qrcode']['logo']) ? trim($setting['qrcode']['logo']) : null;
            // format url encode
            if(!empty($qrcode_logo)){
                $split_logo = explode('/', $qrcode_logo);
                if(!empty($split_logo)){
                    foreach($split_logo as $k => $path_item){
                        $split_logo[$k] = rawurlencode($path_item);
                    }
                }

                $qrcode_logo = implode('/', $split_logo);
                $qrcode_logo = CDN_URL . $qrcode_logo;
            }
            
            
            $qrcode_color = !empty($setting['qrcode']['color']) ? $setting['qrcode']['color'] : null;
            $qrcode_background = !empty($setting['qrcode']['background']) ? $setting['qrcode']['background'] : null;

            if(!empty($qrcode_logo) && file_exists($qrcode_logo)) $qr_image->logoPath($qrcode_logo)->logoResizeToWidth(50)->logoPunchoutBackground(true);

            if(!empty($qrcode_color)){
                $rgb = $this->_hexToRgb($qrcode_color);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 0;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 0;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 0;

                $qr_image->foregroundColor(new Color($r, $g, $b));
            }

            if(!empty($qrcode_background)){
                $rgb = $this->_hexToRgb($qrcode_background);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 255;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 255;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 255;

                $qr_image->backgroundColor(new Color($r, $g, $b));
            }
        }

        // tạo ảnh mã QR
        $qr_image = $qr_image->build();
        $qr_image = $qr_image->getImage();


        // ghép ảnh QR lên ảnh nền
        imagecopy($frame_image, $qr_image, 0, 0, 0, 0, $height, $width);
        $position_y = $width_qrcode + $margin_bottom; // $width_qrcode thay thế $height_qrcode
    
        //thêm text mô tả
        if(!empty($this->fields['info']) && !empty($setting['info']['display'])){
            // đọc cấu hình tên ngân hàng
            $label_font_size = !empty($setting['info']['font_size']) ? $setting['info']['font_size'] : $font_size;
            $label_rgb = $color_rgb;
            if(!empty($setting['info']['color'])){
                $rgb = $this->_hexToRgb($setting['info']['color']);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 0;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 0;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 0;

                $label_rgb = ['r' => $r, 'g' => $g, 'b' => $b];
            }

            // cài đặt màu chữ số tiền
            $label_color = imagecolorallocate($frame_image, $label_rgb['r'], $label_rgb['g'], $label_rgb['b']);

            // căn text đặt vào giữa 
            $box = imagettfbbox($label_font_size, 0, $font_path, $this->fields['info']);
            $text_width = abs($box[2] - $box[0]);
            $text_height = abs($box[5] - $box[1]);
            $x = ($width - $text_width) / 2;

            // thêm số tiền vào ảnh
            imagettftext($frame_image, $label_font_size, 0,  round($x), $position_y, $label_color, $font_path, $this->fields['info']);

            $position_y += $text_height + $margin_bottom;
        }

        // tạo ảnh base64
        ob_start(); 
        imagejpeg($frame_image);
        $contents = ob_get_contents();
        ob_end_clean();
        $url = "data:image/png;base64," . base64_encode($contents);


        // tạo ảnh ra dir
        // imagepng($frame_image, $image_dir, 0);die;
        // $url = str_replace(DS, '/', str_replace(SOURCE_DOMAIN, '', $image_dir));
        // $url = rtrim($url, '/');


        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'tao_ma_qr_thanh_cong'),
            DATA => [
                'url' => $url
            ]
        ]);
    }

    private function _createImageBank($data = [])
    {
        $font_base_path = APP . 'Lib\BankQR\fonts' . DS;
        $bank_image_base_path = APP . 'Lib\BankQR\images\banks' . DS;

        if(!empty($this->fields['bank']) && empty($this->fields['bank_name'])){
            $banks = $this->getBanks();
            $this->fields['bank_name'] = !empty($banks[$this->fields['bank']]) ? $banks[$this->fields['bank']] : null;
        }
        
        // lấy cấu hình
        $general_setting = !empty($this->setting['general']) ? $this->setting['general'] : [];
        $qrcode_setting = !empty($this->setting['qrcode']) ? $this->setting['qrcode'] : [];
        $bank_name_setting = !empty($this->setting['bank_name']) ? $this->setting['bank_name'] : [];
        $account_setting = !empty($this->setting['account']) ? $this->setting['account'] : [];
        $account_name_setting = !empty($this->setting['account_name']) ? $this->setting['account_name'] : [];        
        $amount_setting = !empty($this->setting['amount']) ? $this->setting['amount'] : [];
        $info_setting = !empty($this->setting['info']) ? $this->setting['info'] : [];

        $setting = [
            'general' => [
                'height' => !empty($general_setting['height']) ? $general_setting['height'] : 620, // default 620
                'width' => !empty($general_setting['width']) ? $general_setting['width'] : 400, // default 400
                'background' => !empty($general_setting['background']) ? $general_setting['background'] : '#fff', // default #fff
                'color' => !empty($general_setting['color']) ? $general_setting['color'] : '#000', // default #000
                'font' => !empty($general_setting['font']) ? $general_setting['font'] : '',
                'font_size' => !empty($general_setting['font_size']) ? intval($general_setting['font_size']) : 12,
                'margin_bottom' => !empty($general_setting['margin_bottom']) ? intval($general_setting['margin_bottom']) : 10
            ],
            'qrcode' => [
                'background' => !empty($qrcode_setting['background']) ? $qrcode_setting['background'] : '',
                'color' => !empty($qrcode_setting['color']) ? $qrcode_setting['color'] : '',
                'logo' => !empty($qrcode_setting['logo']) ? trim($qrcode_setting['logo']) : '',
            ],
            'bank_name' => [
                'display' => !empty($bank_name_setting['display']) ? true : false,
                'color' => !empty($bank_name_setting['color']) ? $bank_name_setting['color'] : '',
                'font_size' => !empty($bank_name_setting['font_size']) ? intval($bank_name_setting['font_size']) : 12
            ],
            'account' => [
                'display' => !empty($account_setting['display']) ? true : false,
                'color' => !empty($account_setting['color']) ? $account_setting['color'] : '',
                'font_size' => !empty($account_setting['font_size']) ? intval($account_setting['font_size']) : 12
            ],
            'account_name' => [
                'display' => !empty($account_name_setting['display']) ? true : false,
                'color' => !empty($account_name_setting['color']) ? $account_name_setting['color'] : '',
                'font_size' => !empty($account_name_setting['font_size']) ? intval($account_name_setting['font_size']) : 12
            ],
            'info' => [
                'display' => !empty($info_setting['display']) ? true : false,
                'color' => !empty($info_setting['color']) ? $info_setting['color'] : '',
                'font_size' => !empty($info_setting['font_size']) ? intval($info_setting['font_size']) : 12
            ],
            'amount' => [
                'display' => !empty($amount_setting['display']) ? true : false,
                'color' => !empty($amount_setting['color']) ? $amount_setting['color'] : '',
                'font_size' => !empty($amount_setting['font_size']) ? intval($amount_setting['font_size']) : 12
            ]
        ];    


        // đọc cấu hình chung
        $background_rgb = ['r' => 255, 'g' => 255, 'b' => 255];
        $color_rgb = ['r' => 0, 'g' => 0, 'b' => 0 ];
        
        $font_path = $font_base_path . 'Roboto-Medium.ttf';
        $font_size = 12;
        if(!empty($setting['general'])){
            $general_background = !empty($setting['general']['background']) ? $setting['general']['background'] : null;
            $general_color = !empty($setting['general']['color']) ? $setting['general']['color'] : null;
            $general_font = !empty($setting['general']['font']) ? $setting['general']['font'] : null;            
            $general_font_size = !empty($setting['general']['font_size']) ? $setting['general']['font_size'] : 12;            

            if(!empty($general_background)){
                $rgb = $this->_hexToRgb($general_background);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 255;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 255;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 255;

                $background_rgb = ['r' => $r, 'g' => $g, 'b' => $b];

            }

            if($general_color){
                $rgb = $this->_hexToRgb($general_color);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 0;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 0;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 0;

                $color_rgb = ['r' => $r, 'g' => $g, 'b' => $b];
            }

            if(!empty($general_font)) $font_path = $font_base_path . $general_font;
            if(!empty($general_font_size)) $font_size = $general_font_size;
        }

        $height = !empty($setting['general']['height']) ? intval($setting['general']['height']) : 620;
        $width = !empty($setting['general']['width']) ? intval($setting['general']['width']) : 400;
        $margin_bottom = !empty($setting['general']['margin_bottom']) ? intval($setting['general']['margin_bottom']) : 10;

        if($height > 2000) $height = 2000;
        if($width > 2000) $width = 2000;

        $position_y = 0; // vị trí chiều dọc

        // tạo ảnh nền frame_image
        $frame_image = imagecreatetruecolor($width, $height);


        // set màu ảnh nền
        $background_frame = imagecolorallocate($frame_image, $background_rgb['r'], $background_rgb['g'], $background_rgb['b']);
        imagefill($frame_image, 0, 0, $background_frame);

        
        // tạo ảnh mã QR
        $width_qrcode = $width;
        $qr_image = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($width_qrcode)
            ->margin(0)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->validateResult(false);


        // đọc cấu hình mã QR
        if(!empty($setting['qrcode'])){
            $qrcode_logo = !empty($setting['qrcode']['logo']) ? trim($setting['qrcode']['logo']) : null;
            // format url encode
            if(!empty($qrcode_logo)){
                $split_logo = explode('/', $qrcode_logo);
                if(!empty($split_logo)){
                    foreach($split_logo as $k => $path_item){
                        $split_logo[$k] = rawurlencode($path_item);
                    }
                }

                $qrcode_logo = implode('/', $split_logo);
                $qrcode_logo = CDN_URL . $qrcode_logo;
            }            

            $qrcode_color = !empty($setting['qrcode']['color']) ? $setting['qrcode']['color'] : null;
            $qrcode_background = !empty($setting['qrcode']['background']) ? $setting['qrcode']['background'] : null;

            if(!empty($qrcode_logo) && file_exists($qrcode_logo)) $qr_image->logoPath($qrcode_logo)->logoResizeToWidth(50)->logoPunchoutBackground(true);

            if(!empty($qrcode_color)){
                $rgb = $this->_hexToRgb($qrcode_color);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 0;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 0;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 0;

                $qr_image->foregroundColor(new Color($r, $g, $b));
            }

            if(!empty($qrcode_background)){
                $rgb = $this->_hexToRgb($qrcode_background);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 255;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 255;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 255;

                $qr_image->backgroundColor(new Color($r, $g, $b));
            }
        }

        // tạo ảnh mã QR
        $qr_image = $qr_image->build();
        $qr_image = $qr_image->getImage();


        // ghép ảnh QR lên ảnh nền
        imagecopy($frame_image, $qr_image, 0, 0, 0, 0, $height, $width);
        $position_y = $width_qrcode; // $width_qrcode thay thế $height_qrcode

        // đè ảnh napas và bank
        $width_bank = 187;
        $height_bank = 70;

        $position_y -= 10;
        $x_napas = ($width - (($width_bank * 2) )) / 2; // 10 là khoảng cách giữa 2 ảnh napas và bank
        $x_bank = $x_napas + $width_bank ; // 10 là khoảng cách giữa 2 ảnh napas và bank
        $image_napas = imagecreatefrompng($bank_image_base_path . 'NAPAS.png');
        if(!empty($image_napas)) imagecopy($frame_image, $image_napas, $x_napas, $position_y, 0, 0, $width_bank, $height_bank);        
        
        $image_bank = imagecreatefrompng($bank_image_base_path . $this->fields['bank'] . '.png');
        if(!empty($image_bank)) imagecopy($frame_image, $image_bank, $x_bank, $position_y, 0, 0, $width_bank, $height_bank);
        $position_y += $height_bank + $margin_bottom + 20;
        // debug($position_y);die;
        // thêm text tên ngân hàng
        if(!empty($this->fields['bank_name']) && !empty($setting['bank_name']['display'])){
            // đọc cấu hình tên ngân hàng 
            $label_font_size = !empty($setting['bank_name']['font_size']) ? $setting['bank_name']['font_size'] : $font_size;
            $label_rgb = $color_rgb;            
            if(!empty($setting['bank_name']['color'])){
                $rgb = $this->_hexToRgb($setting['bank_name']['color']);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 0;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 0;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 0;

                $label_rgb = ['r' => $r, 'g' => $g, 'b' => $b];
            }

            // cài đặt màu chữ tên ngân hàng
            $label_color = imagecolorallocate($frame_image, $label_rgb['r'], $label_rgb['g'], $label_rgb['b']);
            
            // căn text đặt vào giữa 
            $box = imagettfbbox($label_font_size, 0, $font_path, $this->fields['bank_name']);
            $text_width = abs($box[2] - $box[0]);
            $text_height = abs($box[5] - $box[1]);
            $x = ($width - $text_width) / 2;

            // thêm tên ngân hàng vào ảnh
            imagettftext($frame_image, $label_font_size, 0, round($x), $position_y, $label_color, $font_path, $this->fields['bank_name']);

            $position_y += $text_height + $margin_bottom;
        }
        

        //thêm text chủ tài khoản
        if(!empty($this->fields['account_name']) && !empty($setting['account_name']['display'])){
            // đọc cấu hình tên ngân hàng            
            $label_font_size = !empty($setting['account_name']['font_size']) ? $setting['account_name']['font_size'] : $font_size;
            $label_rgb = $color_rgb;
            if(!empty($setting['account_name']['color'])){
                $rgb = $this->_hexToRgb($setting['account_name']['color']);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 0;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 0;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 0;

                $label_rgb = ['r' => $r, 'g' => $g, 'b' => $b];
            }

            // cài đặt màu chữ chủ tài khoản
            $label_color = imagecolorallocate($frame_image, $label_rgb['r'], $label_rgb['g'], $label_rgb['b']);

            // căn text đặt vào giữa 
            $label = "Chủ tài khoản: " . $this->fields['account_name'];
            $box = imagettfbbox($label_font_size, 0, $font_path, $label);
            $text_width = abs($box[2] - $box[0]);
            $text_height = abs($box[5] - $box[1]);
            $x = ($width - $text_width) / 2;

            // thêm tên ngân hàng vào ảnh
            imagettftext($frame_image, $label_font_size, 0,  round($x), $position_y, $label_color, $font_path, $label);

            $position_y += $text_height + $margin_bottom;
        }
        


        //thêm text số tk
        if(!empty($this->fields['account']) && !empty($setting['account']['display'])){
            // đọc cấu hình tên ngân hàng            
            $label_font_size = !empty($setting['account']['font_size']) ? $setting['account']['font_size'] : $font_size;
            $label_rgb = $color_rgb;
            if(!empty($setting['account']['color'])){
                $rgb = $this->_hexToRgb($setting['account']['color']);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 0;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 0;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 0;

                $label_rgb = ['r' => $r, 'g' => $g, 'b' => $b];
            }

            // cài đặt màu chữ tài khoản
            $label_color = imagecolorallocate($frame_image, $label_rgb['r'], $label_rgb['g'], $label_rgb['b']);

            // căn text đặt vào giữa 
            $label = "Số tài khoản: " . $this->fields['account'];
            $box = imagettfbbox($label_font_size, 0, $font_path, $label);
            $text_width = abs($box[2] - $box[0]);
            $text_height = abs($box[5] - $box[1]);
            $x = ($width - $text_width) / 2;

            // thêm số tài khoản vào ảnh
            imagettftext($frame_image, $label_font_size, 0,  round($x), $position_y, $label_color, $font_path, $label);

            $position_y += $text_height + $margin_bottom;
        }
        


        //thêm text số tiền
        if(!empty($this->fields['amount']) && !empty($setting['amount']['display'])){
            // đọc cấu hình tên ngân hàng
            $label_font_size = !empty($setting['amount']['font_size']) ? $setting['amount']['font_size'] : $font_size;
            $label_rgb = $color_rgb;
            if(!empty($setting['amount']['color'])){
                $rgb = $this->_hexToRgb($setting['amount']['color']);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 0;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 0;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 0;

                $label_rgb = ['r' => $r, 'g' => $g, 'b' => $b];
            }

            // cài đặt màu chữ số tiền
            $label_color = imagecolorallocate($frame_image, $label_rgb['r'], $label_rgb['g'], $label_rgb['b']);

            // căn text đặt vào giữa 
            $label = 'Số tiền: ' . number_format(round($this->fields['amount'])) . ' VNĐ';
            $box = imagettfbbox($label_font_size, 0, $font_path, $label);
            $text_width = abs($box[2] - $box[0]);
            $text_height = abs($box[5] - $box[1]);
            $x = ($width - $text_width) / 2;

            // thêm số tiền vào ảnh
            imagettftext($frame_image, $label_font_size, 0,  round($x), $position_y, $label_color, $font_path, $label);

            $position_y += $text_height + $margin_bottom;
        }
        

        //thêm text nội dung chuyển khoản
        if(!empty($this->fields['info']) && !empty($setting['info']['display'])){
            // đọc cấu hình tên ngân hàng
            $label_font_size = !empty($setting['info']['font_size']) ? $setting['info']['font_size'] : $font_size;
            $label_rgb = $color_rgb;
            if(!empty($setting['info']['color'])){
                $rgb = $this->_hexToRgb($setting['info']['color']);
                $r = !empty($rgb['r']) ? intval($rgb['r']) : 0;
                $g = !empty($rgb['g']) ? intval($rgb['g']) : 0;
                $b = !empty($rgb['b']) ? intval($rgb['b']) : 0;

                $label_rgb = ['r' => $r, 'g' => $g, 'b' => $b];
            }

            // cài đặt màu chữ số tiền
            $label_color = imagecolorallocate($frame_image, $label_rgb['r'], $label_rgb['g'], $label_rgb['b']);

            // căn text đặt vào giữa 
            $label = 'Nội dung: ' . $this->fields['info'];
            $box = imagettfbbox($label_font_size, 0, $font_path, $label);
            $text_width = abs($box[2] - $box[0]);
            $text_height = abs($box[5] - $box[1]);
            $x = ($width - $text_width) / 2;

            // thêm số tiền vào ảnh
            imagettftext($frame_image, $label_font_size, 0,  round($x), $position_y, $label_color, $font_path, $label);

            $position_y += $text_height + $margin_bottom;
        }

        // tạo ảnh base64
        ob_start(); 
        imagejpeg($frame_image);
        $contents = ob_get_contents();
        ob_end_clean();
        $url = "data:image/png;base64," . base64_encode($contents);


        // tạo ảnh ra dir
        // imagepng($frame_image, $image_dir, 0);die;
        // $url = str_replace(DS, '/', str_replace(SOURCE_DOMAIN, '', $image_dir));
        // $url = rtrim($url, '/');


        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'tao_ma_qr_thanh_cong'),
            DATA => [
                'url' => $url
            ]
        ]);
    }

    private function _hexToRgb($hex = '', $alpha = false) 
    {

        if(empty($hex)) return [];

        $hex = str_replace('#', '', $hex);
        $length = strlen($hex);

        $rgb = [];
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ($alpha) $rgb['a'] = $alpha;

        return $rgb;
    }

    public function getBanks()
    {
        // $dir_file = APP . 'Lib\BankQR\config\banks.json';
        $dir_file = ROOT . '/database_initialization/banks.json';
        if(!file_exists($dir_file)) return [];

        $content = @file_get_contents($dir_file);
        if(empty($content) || !$this->Utilities->isJson($content)) return [];

        $content = !empty($content) ? json_decode($content, true) : [];
        $banks = !empty($content[DATA]) ? $content[DATA] : [];
        if(empty($banks)) return [];

        return !empty($banks) ? Hash::combine($banks, '{n}.code', '{n}.name') : [];
    }
}
