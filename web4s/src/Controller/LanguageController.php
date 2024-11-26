<?php

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

class LanguageController extends SystemController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

    }

	public function activeLanguage() 
	{
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $lang = !empty($data['lang']) ? $data['lang'] : null;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $languages = TableRegistry::get('Languages')->getList();
        if (empty($lang) || empty($languages[$lang])) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $utilities = $this->loadComponent('Utilities'); 

        // lay link chuyen huong sau khi doi ngon ngu
        $referer = !empty($this->request->referer()) ? $utilities->str_replace_first('/', '', parse_url($this->request->referer())['path']) : '';
        $referer = !empty($referer) ? urldecode($referer) : '';

        $url_redirect = null;
        $page_url = TableRegistry::get('TemplatesPageContent')->find()->where([
            'url' => $referer
        ])->select(['page_code'])->first();
        
        if(!empty($page_url['page_code'])) {
            $url_link = TableRegistry::get('TemplatesPageContent')->find()->where([
                'page_code' => $page_url['page_code'],
                'lang' => $lang,
                'template_code' => CODE_TEMPLATE
            ])->select(['url'])->first();
            $url_redirect = !empty($url_link['url']) ? '/' . $url_link['url'] : '/';

        }else{
            $referer_info = TableRegistry::get('Links')->find()->where([
                'deleted' => 0,
                'url' => $referer
            ])->select(['foreign_id', 'type'])->first();

            if(!empty($referer_info)){
                $url_link = TableRegistry::get('Links')->find()->where([
                    'deleted' => 0,
                    'foreign_id' => !empty($referer_info['foreign_id']) ? $referer_info['foreign_id'] : null,
                    'type' => !empty($referer_info['type']) ? $referer_info['type'] : null,
                    'lang' => $lang
                ])->select(['url'])->first();

                $url_redirect = !empty($url_link['url']) ? '/' . $url_link['url'] : '/';
            }            
        }


        $session = $this->getRequest()->getSession();
        $session->write(LANG_FRONTEND, $lang);

        $this->responseJson([
            CODE => SUCCESS,
            DATA => [
                'url_redirect' => $url_redirect
            ]
        ]);
    }
}