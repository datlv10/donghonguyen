<?php

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;
use Cake\Filesystem\File;

class SystemController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function loadEmbed()
    {
        $this->layout = false;
        $this->autoRender = false;

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $embed_code = !empty($settings['embed_code']) ? $settings['embed_code'] : null;

        $result = [
            'head' => null,
            'top_body' => null,
            'bottom_body' => null,
        ];

        if(!empty($embed_code['head'])){
            $result['head'] = $embed_code['head'];
        }

        if(!empty($embed_code['top_body'])){
            $result['top_body'] = $embed_code['top_body'];
        }

        if(!empty($embed_code['bottom_body'])){
            $result['bottom_body'] = $embed_code['bottom_body'];
        }

        $this->responseJson([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }

    public function loadSdkSocial($type = null)
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if(empty($type) || !in_array($type, ['facebook', 'google'])) die;

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $social = !empty($settings['social']) ? $settings['social'] : [];


        $this->set('social', $social);

        if($type == 'facebook'){
            $this->render('/element/layout/facebook_sdk');
        }
        
        if($type == 'google'){
            $this->render('/element/layout/google_sdk');
        }
    }

    public function loadEmbedAttribute()
    {
        $view_builder = $this->viewBuilder();
        $view_builder->enableAutoLayout(false);
        $view_builder->setTemplatePath('embed_attribute');

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // kiểm tra dữ liệu 
        $embed = !empty($data['embed']) ? $data['embed'] : null;
        $page_type = !empty($data['page_type']) ? $data['page_type'] : null;
        $record_id = !empty($data['record_id']) ? $data['record_id'] : null;        
        if(empty($embed) || empty($page_type) || empty($record_id)) die;
        if(!in_array($page_type, [ARTICLE, PRODUCT])) die;

        $split = explode('||', $embed);
        $attribute_code = !empty($split[0]) ? $split[0] : null;
        $view = !empty($split[1]) ? $split[1] : null;        
        if(empty($attribute_code) || empty($view)) die;

        // kiểm tra view có tồn tại
        $file = new File(PATH_TEMPLATE . 'embed_attribute' . DS . $view, false);
        if(!$file->exists()) die;


        // lấy thông tin thuộc tính
        $table = TableRegistry::get('Attributes');
        $all_attributes = Hash::combine($table->getAll(LANGUAGE), '{n}.code', '{n}');
        $attribute_info = !empty($all_attributes[$attribute_code]) ? $all_attributes[$attribute_code] : [];
        $attribute_id = !empty($attribute_info['id']) ? intval($attribute_info['id']) : null;
        $input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;
        if(empty($attribute_id)) die;

        // lấy giá trị
        $value_attribute = [];
        switch ($page_type) {
            case ARTICLE:
                $value_attribute = TableRegistry::get('ArticlesAttribute')->find()->where([
                    'article_id' => $record_id, 
                    'attribute_id' => $attribute_id
                ])->select(['id', 'value'])->first();
                break;
            
            case PRODUCT:
                $value_attribute = TableRegistry::get('ProductsAttribute')->find()->where([
                    'product_id' => $record_id, 
                    'attribute_id' => $attribute_id
                ])->select(['id', 'value'])->first();
                break;
        }

        $value = !empty($value_attribute['value']) ? $value_attribute['value'] : null;        
        $value = $table->formatValueAttribute($input_type, $value, LANGUAGE);
        
        $this->set('value', $value);
        $this->render(str_replace('.tpl', '', $view));

    }

}