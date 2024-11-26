<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\ORM\TableRegistry;

class CommentController extends AppController {

	public function addComment()
    {
        $data_bearer = $this->data_bearer;

        $data = $this->formatDataComment($data_bearer, COMMENT);

        $result = $this->loadComponent('Comment')->addComment($data, ['api' => true]);
        if(!empty($result[CODE]) && $result[CODE] == SUCCESS){
            $member_info = $this->request->getSession()->read(MEMBER);
            $avatar = !empty($member_info['avatar']) ? $member_info['avatar'] : null;
            $result[DATA]['avatar'] = $avatar;
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function addRating()
    {
        $data_bearer = $this->data_bearer;
        $data = $this->formatDataComment($data_bearer, RATING);
       
        $result = $this->loadComponent('Comment')->addComment($data, ['api' => true]);
        if(!empty($result[CODE]) && $result[CODE] == SUCCESS){
            $member_info = $this->request->getSession()->read(MEMBER);
            $avatar = !empty($member_info['avatar']) ? $member_info['avatar'] : null;
            $result[DATA]['avatar'] = $avatar;
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    private function formatDataComment($data_bearer = [], $type_comment = null)
    {
        $content = !empty($data_bearer['content']) ? $data_bearer['content'] : null;
        $parent_id = !empty($data_bearer['parent_id']) ? $data_bearer['parent_id'] : null;
        $product_id = !empty($data_bearer['product_id']) ? $data_bearer['product_id'] : null;
        $rating = !empty($data_bearer['rating']) ? $data_bearer['rating'] : null;
        $images = !empty($data_bearer['images']) ? json_decode($data_bearer['images'], true) : [];

        $link_info = TableRegistry::get('Links')->getInfoLink([
            'foreign_id' => $product_id,
            'lang' => LANGUAGE,
            TYPE => PRODUCT_DETAIL
        ]);
        $url = !empty($link_info['url']) ? $link_info['url'] : null;

        $member_info = $this->request->getSession()->read(MEMBER);
        $app_info = TableRegistry::get('MobileApp')->getMobileAppDefault();
        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : null;

        if(empty($member_info)){
            $this->responseErrorApi([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        return [
            'type_comment' => $type_comment,
            'full_name' =>  !empty($member_info['full_name']) ? $member_info['full_name'] : null,
            'email' =>  !empty($member_info['email']) ? $member_info['email'] : null,
            'phone' =>  !empty($member_info['phone']) ? $member_info['phone'] : null,
            'content' => $content,
            'url' => $url,
            'parent_id' => $parent_id,
            'account_id' => !empty($member_info['account_id']) ? $member_info['account_id'] : null,
            'awaiting_approval' => !empty($config['comment']['awaiting_approval']) ? $config['comment']['awaiting_approval'] : null,
            'foreign_id' => $product_id,
            'type' => PRODUCT_DETAIL,
            'rating' => $rating,
            'images' => $images
        ];

    }

    public function getReply()
    {
        $result = [
            DATA => [],
            PAGINATION => []
        ];

        $data_bearer = $this->data_bearer;

        $type_comment = !empty($data_bearer['type_comment']) ? $data_bearer['type_comment'] : null;
        $parent_id = !empty($data_bearer['parent_id']) ? $data_bearer['parent_id'] : null;

        $params_url_filter = [];
        if(!empty($data_bearer['params']) && $this->loadComponent('Utilities')->isJson($data_bearer['params'])) {
            $params_url_filter = json_decode($data_bearer['params'], true);
        }

        $data = [
        	'type_comment' => $type_comment,
        	TYPE => PRODUCT_DETAIL,
        	'parent_id' => $parent_id,
        	'params_url_filter' => $params_url_filter
        ];
       
        $result_reply = $this->loadComponent('Comment')->list($data, ['api' => true]);
        if(empty($result_reply[CODE]) || $result_reply[CODE] == ERROR) {
            $this->responseErrorApi($result_reply);
        }

        $list_comment = !empty($result_reply[DATA]['comments']) ? $result_reply[DATA]['comments'] : [];
    
        $this->responseApi([
            DATA => $list_comment,
        ]);
    }

    public function like()
    {
        $data_bearer = $this->data_bearer;

        $member_info = $this->request->getSession()->read(MEMBER);

        $customer_account_id = !empty($member_info['account_id']) ? $member_info['account_id'] : null;
        $comment_id = !empty($data_bearer['comment_id']) ? $data_bearer['comment_id'] : null;

        if(empty($member_info) || empty($customer_account_id) || empty($comment_id)){
            $this->responseErrorApi([MESSAGE => __d('template', 'vui_long_dang_nhap_tai_khoan_de_su_dung_chuc_nang_nay')]);
        }

        $like_table = TableRegistry::get('CommentsLike');
        $like_info = $like_table->find()->where([
            'customer_account_id' => $customer_account_id,
            'comment_id' => $comment_id
        ])->select('id')->first();

        $type = !empty($like_info) ? 'dislike' : 'like';

        $data = [
            'customer_account_id' => $customer_account_id,
            'comment_id' => $comment_id,
            'type' => $type
        ];

        $result = $this->loadComponent('Comment')->like($data, ['api' => true]);
        if(!empty($result[CODE]) && $result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function uploadImage()
    {
        $files = !empty($_FILES['file']) ? $_FILES['file'] : [];
        
        if(empty($files) || !is_array($files)){
            $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
    
        $data_files = [];
        foreach($files as $key => $file){
            if(!is_array($file)) continue;
            foreach($file as $i => $val){
                $data_files[$i][$key] = $val;   
            } 
        }

        if(empty($data_files)) {
            $this->responseErrorApi([MESSAGE => __d('template', 'dang_tai_tep_khong_thanh_cong')]);
        }

        $app_info = TableRegistry::get('MobileApp')->getMobileAppDefault();
        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : null;

        $max_upload = !empty($config['comment']['max_upload']) ? intval($config['comment']['max_upload']) : 5; 

        if(count($data_files) > $max_upload){
            $this->responseErrorApi([MESSAGE => __d('template', 'so_luong_dang_tai_vuot_qua_{0}_anh', $max_upload)]);
        }

        $image_url = []; 
        foreach ($data_files as $data_file) {
            $data_upload = [
                'path' => COMMENT,
                'file' => $data_file
            ];

            $result_upload = $this->loadComponent('Comment')->uploadImage($data_upload, ['api' => true]);
            if(empty($result_upload[CODE]) || $result_upload[CODE] == ERROR) continue;
            $image_url[] = $result_upload['data']['url'];
        }

        if(empty($image_url)) {
            $this->responseErrorApi([MESSAGE => __d('template', 'dang_tai_tep_khong_thanh_cong')]);
        }

        $this->responseApi([
            DATA => $image_url
        ]);
        
    }
}