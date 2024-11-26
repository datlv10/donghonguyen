<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class CommentController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

	public function add() 
	{
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $url = !empty($data['url']) ? $data['url'] : null;
        if(empty($url)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $block_code = !empty($data['block_code']) ? trim($data['block_code']) : null;     
        if(empty($block_code)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_block')]);
        }        
 
        // get info block
        $block_info = TableRegistry::get('TemplatesBlock')->find()->where([
            'template_code' => CODE_TEMPLATE,
            'code' => $block_code,
            'deleted' => 0
        ])->first();
        if(empty($block_info)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_block')]);
        }

        $config = !empty($block_info['config']) ? json_decode($block_info['config'], true) : [];
        $login_required = !empty($config['login_required']) ? true : false;
        $awaiting_approval = !empty($config['awaiting_approval']) ? true : false;

        $member = $this->request->getSession()->read(MEMBER);
        $account_id = !empty($member['account_id']) ? intval($member['account_id']) : null;

        if($login_required && empty($account_id)){
            $this->responseJson([MESSAGE => __d('template', 'vui_long_dang_nhap_tai_khoan_de_su_dung_chuc_nang_nay')]);
        }
        
        $page_info = $this->getPageByUrl($url);

        $data['login_required'] = $login_required;
        $data['awaiting_approval'] = $awaiting_approval;
        $data['account_id'] = $account_id;        
        $data['foreign_id'] = !empty($page_info['page_record_id']) ? intval($page_info['page_record_id']) : null;
        $data['type'] = !empty($page_info['type']) ? $page_info['type'] : null;
        
        $add_comment = $this->loadComponent('Comment')->addComment($data);
        die(json_encode($add_comment));
    }

    public function shortRating()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $url = !empty($data['url']) ? $data['url'] : null;
        $rating = !empty($data['rating']) ? $data['rating'] : null;
        $block_code = !empty($data['block_code']) ? trim($data['block_code']) : null; 

        if(empty($url)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if(empty($rating)){
            $this->responseJson([MESSAGE => __d('template', 'vui_long_chon_so_sao_danh_gia')]);
        }
            
        if(empty($block_code)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_block')]);
        }        
 
        // get info block
        $block_info = TableRegistry::get('TemplatesBlock')->find()->where([
            'template_code' => CODE_TEMPLATE,
            'code' => $block_code,
            'deleted' => 0
        ])->first();

        if(empty($block_info)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_block')]);
        }

        $config = !empty($block_info['config']) ? json_decode($block_info['config'], true) : [];
        $login_required = !empty($config['login_required']) ? true : false;

        $member = $this->request->getSession()->read(MEMBER);
        $account_id = !empty($member['account_id']) ? intval($member['account_id']) : null;

        if($login_required && empty($account_id)){
            $this->responseJson([MESSAGE => __d('template', 'vui_long_dang_nhap_tai_khoan_de_su_dung_chuc_nang_nay')]);
        }

        $page_info = $this->getPageByUrl($url);

        $data['foreign_id'] = !empty($page_info['page_record_id']) ? intval($page_info['page_record_id']) : null;
        $data['type'] = !empty($page_info['type']) ? $page_info['type'] : null;
        $data['rating'] = $rating;   
        
        $short_rating = $this->loadComponent('Comment')->shortRating($data);
        die(json_encode($short_rating));
    }

    public function like() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data_request = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post') || empty($data_request)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $data = [
            'customer_account_id' => !empty($data_request['customer_account_id']) ? $data_request['customer_account_id'] : null,
            'comment_id' => !empty($data_request['comment_id']) ? $data_request['comment_id'] : null,
            'type' => !empty($data_request['type']) ? $data_request['type'] : null
        ];

        $result = $this->loadComponent('Comment')->like($data);
        $this->responseJson($result);
    }

    public function load()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data_request = $this->getRequest()->getData();

        $url = !empty($data_request['url']) ? str_replace('/', '', trim($data_request['url'])) : null;
        if(!empty($url)){
            $link_info = $this->getPageByUrl($url);

            if(empty($link_info)){
                $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_duong_dan')]);
            }

            $foreign_id = !empty($link_info['page_record_id']) ? intval($link_info['page_record_id']) : null;
            $type = !empty($link_info['type']) ? $link_info['type'] : null;
        }

        $data = [
            'type' => $type,
            'foreign_id' => $foreign_id,
            'type_comment' => !empty($data_request['type_comment']) ? $data_request['type_comment'] : null,
            'parent_id' => !empty($data_request['parent_id']) ? $data_request['parent_id'] : null,
        ];

        $result = $this->loadComponent('Comment')->list($data);
        $this->responseJson($result);
    }

    public function uploadImage()
    {
        $this->layout = false;
        $this->autoRender = false;

        $file = !empty($_FILES['file']) ? $_FILES['file'] : [];
        if (!$this->getRequest()->is('post') || empty($file)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $data = [
            'path' => COMMENT,
            'file' => $file
        ];
        $result = $this->loadComponent('Comment')->uploadImage($data);
        $this->responseJson($result);
    }

}