<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\Http\Client;
use Cake\Cache\Cache;


class TranslateComponent extends AppComponent
{
    public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }    

    public function translate($list_text = [], $from = null, $to = null)
    {
        if(empty($list_text) || !is_array($list_text)) return [];
        if(empty($from) || empty($to)) return $list_text;

        $result = [];
        $http = new Client();
        foreach($list_text as $text){
            $params = [
                'dt' => 't',
                'client' => 'gtx',
                'dj' => 1,
                'sl' => $from,
                'tl' => $to,
                'q' => $text
            ];

            $url = 'https://translate.googleapis.com/translate_a/single?' . http_build_query($params);
            $translates = $http->get($url);
            $translates = $translates->getJson();
            $sentences = !empty($translates['sentences']) ? $translates['sentences'] : [];
            
            if(empty($sentences)){
                $result[] = $text;
                continue;
            }

            $text_tran = null;
            foreach($sentences as $item){
                $text_tran .= !empty($item['trans']) ? $item['trans'] : null;
            }

            $result[] = $text_tran;
        }

        return $result;
    }
}
