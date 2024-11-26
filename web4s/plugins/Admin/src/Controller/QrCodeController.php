<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Core\Configure;

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

class QrCodeController extends AppController {

    public function index()
    {
        $this->set('title_for_layout', __d('admin', 'cau_hinh_ma_qr'));
        $this->set('path_menu', 'setting');
    }

    public function settingQrBankTransaction()
    {
        // lấy thông tin cấu hình
        $group = 'qr_bank_transaction';
        $setting = TableRegistry::get('Settings')->find()->where([
            'group_setting' => $group
        ])->toArray();
        $setting = Hash::combine($setting, '{n}.code', '{n}.value');

        $config = !empty($setting['config']) ? json_decode($setting['config'], true) : [];

        // ds fonts chữ
        $fonts = Configure::read('FONTS_QRCODE');
        $fonts_size = Configure::read('FONTS_SIZE_QRCODE');

        $this->css_page = [
            '/assets/plugins/jquery-minicolors/css/jquery.minicolors.css'
        ];

        $this->js_page = [
            '/assets/plugins/jquery-minicolors/js/jquery.minicolors.min.js',
            '/assets/js/pages/setting_qrcode.js'
        ];

        $this->set('config', $config);
        $this->set('fonts', $fonts);
        $this->set('fonts_size', $fonts_size);

        $this->set('group', $group);

        $this->set('title_for_layout', __d('admin', 'cau_hinh_ma_qr_giao_dich_don_hang'));
        $this->set('path_menu', 'setting');
    }

    public function previewSettingQrBank()
    {
        $this->autoRender = false;
        $data = $this->getRequest()->getData();

        $type = !empty($data['type']) ? $data['type'] : null;
        if(empty($type) || !in_array($type, [BANK_TRANSACTION, TEXT])) $type = TEXT;

        $setting = !empty($data['config']) ? $data['config'] : [];

        $fields = [];
        if($type == BANK_TRANSACTION){
            $fields = [
                'bank' => 'ACB',
                'bank_name' => 'Ngân hàng TMCP Á Châu',
                'account' => '9729317',
                'account_name' => 'LE VAN SI',
                'amount' => '10000',
                'info' => 'Thanh toán đơn hàng ORD00001'
            ];
        }
        
        if($type == TEXT){
            $fields = [
                'text' => 'Mã QR của tôi',
                'info' => 'SCAN ME'
            ];
        }

        $create_qr = $this->loadComponent('QrCode')->generateQrCode($fields, $type, $setting);

        $this->responseJson($create_qr);
    }

    public function settingQrNormal()
    {
        // lấy thông tin cấu hình
        $group = 'qr_normal';
        $setting = TableRegistry::get('Settings')->find()->where([
            'group_setting' => $group
        ])->toArray();
        $setting = Hash::combine($setting, '{n}.code', '{n}.value');

        $config = !empty($setting['config']) ? json_decode($setting['config'], true) : [];
        // ds fonts chữ
        $fonts = Configure::read('FONTS_QRCODE');
        $fonts_size = Configure::read('FONTS_SIZE_QRCODE');

        $this->css_page = [
            '/assets/plugins/jquery-minicolors/css/jquery.minicolors.css'
        ];

        $this->js_page = [
            '/assets/plugins/jquery-minicolors/js/jquery.minicolors.min.js',
            '/assets/js/pages/setting_qrcode.js'
        ];    

        $this->set('config', $config);
        $this->set('fonts', $fonts);
        $this->set('fonts_size', $fonts_size);

        $this->set('group', $group);

        $this->set('title_for_layout', __d('admin', 'cau_hinh_ma_qr'));
        $this->set('path_menu', 'setting');
    }

    public function generateQr()
    {
        $qr_image = 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';        
        $banks = $this->loadComponent('QrCode')->getBanks();

        $this->set('qr_image', $qr_image);
        $this->set('banks', $banks);

        $this->js_page = [
            '/assets/js/pages/setting_generate_qrcode.js'
        ];

        $this->set('title_for_layout', __d('admin', 'tao_ma_qr'));
        $this->set('path_menu', 'setting');
    }

    public function ajaxGenerateQr()
    {
        $this->autoRender = false;
        $data = $this->getRequest()->getData();

        $fields = !empty($data['fields']) ? $data['fields'] : [];
        $type = !empty($data['type']) ? $data['type'] : null;

        if(empty($type) || !in_array($type, [BANK_ACCOUNT, TEXT, URL])) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $create_qr = $this->loadComponent('QrCode')->generateQrCode($fields, $type);

        $this->responseJson($create_qr);
    }


}