<?php

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Log\Log;

class WebsiteController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function loadSettingBlock()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();
        $code = empty($data['code']) ? $data['code'] : '';

        $block_info = TableRegistry::get('TemplatesBlock')->find()->where([
            'template_code' => CODE_TEMPLATE,
            'code' => $code,
            'deleted' => 0
        ])->first();

        $config = !empty($block_info['config']) ? json_decode($block_info['config'], true) : [];
        $type = !empty($block_info['type']) ? $block_info['type'] : null;

        $this->set('config', $config);
        $this->set('code', $code);
        $this->set('type', $type);
        $this->set('block_info', $block_info);
        $this->render('setting_block');
    }

    public function webhooksKiotviet()
    {
        $this->layout = false;
        $this->autoRender = false;

        $params = $this->request->getQueryParams();

        if($this->request->is(['post','put'])){
            $data = file_get_contents('php://input');

            if($this->loadComponent('Utilities')->isJson($data)){
                $data = json_decode($data, true);
            }            
            
            if(!is_array($data)){
                $this->responseJson([
                    MESSAGE => __d('template', 'du_lieu_khong_hop_le')
                ]);
            }

            $params = $data;
        }
        // $params = json_decode('{"Id":"d11df09e-720d-4575-8f26-dcc80a09d7bd","Attempt":1,"Notifications":[{"Action":"product.update.500322281","Data":[{"__type":"KiotViet.OmniChannelCore.Api.Shared.Model.WebhookProductUpdateRes, KiotViet.OmniChannelCore.Api.Shared","Id":20354926,"RetailerId":500322281,"Code":"NAM026","Name":"dsfdsdsf","FullName":"dsfdsdsf - mausac1 vfd - option_01 dsc - option_02 - Loại 01","CategoryId":533803,"CategoryName":"Điện công nghiệp","AllowsSale":true,"Type":2,"HasVariants":true,"BasePrice":2565,"Unit":"","ConversionValue":1,"Description":"","ModifiedDate":"2023-09-07T22:15:22.3970000+07:00","isActive":true,"IsRewardPoint":false,"OrderTemplate":"","Attributes":[{"ProductId":20300931,"AttributeName":"MÀU SẮC","AttributeValue":"Màu xanh đen"},{"ProductId":20300931,"AttributeName":"KÍCH THƯỚC","AttributeValue":"16 CM"},{"ProductId":20300931,"AttributeName":"THUỘC TÍNH MỚI","AttributeValue":"Thuộc tính 02"},{"ProductId":20300931,"AttributeName":"THUỘC TÍNH MỚI 2","AttributeValue":"option_02"}],"Units":[],"Inventories":[{"ProductId":20300931,"ProductCode":"dsfdsfff","ProductName":"dsfdsdsf","BranchId":58831,"BranchName":"Chi nhánh trung tâm","Cost":0,"OnHand":0,"Reserved":0,"MinQuantity":0,"MaxQuantity":999999999,"isActive":true},{"ProductId":20300931,"ProductCode":"dsfdsfff","ProductName":"dsfdsdsf","BranchId":59759,"BranchName":"Chi nhánh HN","Cost":0,"OnHand":1525,"Reserved":0,"MinQuantity":0,"MaxQuantity":999999999,"isActive":true},{"ProductId":20300931,"ProductCode":"dsfdsfff","ProductName":"dsfdsdsf","BranchId":59761,"BranchName":"Chi nhánh HCM","Cost":0,"OnHand":0,"Reserved":0,"MinQuantity":0,"MaxQuantity":999999999,"isActive":true}],"PriceBooks":[],"Serials":[],"Images":[]}]}]}', true);
        

        Log::write('debug', json_encode($params));
        $this->loadComponent('Admin.StoreKiotViet')->webhook($params);
    }
}