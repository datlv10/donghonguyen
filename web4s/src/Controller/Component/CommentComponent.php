<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;

class CommentComponent extends Component
{
    public $controller = null;
    public $components = ['System', 'Utilities', 'PaginatorExtend', 'ReCaptcha', 'Upload'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function addComment($data = [], $options = [])
    {
        if(empty($data)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $api = !empty($options['api']) ? true : false;

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
               return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $full_name = !empty($data['full_name']) ? trim($data['full_name']) : null;
        $email = !empty($data['email']) ? trim($data['email']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;
        $type_comment = !empty($data['type_comment']) ? $data['type_comment'] : null;
        $rating = !empty($data['rating']) ? $data['rating'] : null;

        $content = !empty($data['content']) ? trim($data['content']) : null;
        $url = !empty($data['url']) ? str_replace('/', '', trim($data['url'])) : null;
        $parent_id = !empty($data['parent_id']) ? intval($data['parent_id']) : null;
        $account_id = !empty($data['account_id']) ? intval($data['account_id']) : null;
        $awaiting_approval = !empty($data['awaiting_approval']) ? true : false;
        $foreign_id = !empty($data['foreign_id']) ? intval($data['foreign_id']) : null;
        $type = !empty($data['type']) ? $data['type'] : null;

        if(empty($full_name)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ten_nguoi_binh_luan')]);  
        }

        if(empty($content)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_noi_dung_binh_luan')]);   
        }

        if(empty($url)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_duong_dan')]);
        }

        if(empty($type_comment)){
            return $this->System->getResponse([MESSAGE => __d('template', 'loai_binh_luan_khong_hop_le')]);
        }

        if(!empty($type_comment) && !in_array($type_comment, [COMMENT, RATING])){
            return $this->System->getResponse([MESSAGE => __d('template', 'loai_binh_luan_khong_hop_le')]);
        }

        if($type_comment == RATING && empty($parent_id) && empty($rating)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_chon_so_sao_danh_gia')]);
        }

        if(empty($foreign_id) || empty($type)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Comments');

        if(!empty($account_id) && $type_comment == RATING && empty($parent_id)) {
            $check_rating_exist = $table->find()->where([
                'customer_account_id' => $account_id,
                'type_comment' => RATING,
                'url' => $url,
                'parent_id IS' => null,
                'status' => 1,
                'deleted' => 0,
            ])->select(['id'])->first();
            
            if(!empty($check_rating_exist)) {
                return $this->System->getResponse([MESSAGE => __d('template', 'ban_da_danh_gia_san_pham_nay_roi')]);
            }
        }

        $images = [];
        if(!empty($data['images']) && is_array($data['images'])){
            foreach ($data['images'] as $key => $image) {
                $images[] = str_replace(CDN_URL , '', $image);
            }
        }
        $status = 2;  
        if(!$awaiting_approval){
            $status = 1;
        }
                      
        $data_save = [
            'foreign_id' => $foreign_id,
            'type' => $type,
            'customer_account_id' => $account_id,
            'parent_id' => !empty($data['parent_id']) ? intval($data['parent_id']) : null,
            'full_name' => $full_name,
            'type_comment' => $type_comment,
            'rating' => $rating,
            'email' => $email,
            'phone' => $phone,
            'content' => $content,
            'url' => $url,
            'parent_id' => $parent_id,
            'images' => !empty($images) ? json_encode($images) : null,
            'status' => $status,
            'is_admin' => !empty($data['is_admin']) ? 1 : null,
            'admin_user_id' => !empty($data['admin_user_id']) ? intval($data['admin_user_id']) : null,
            'ip' => $this->controller->getRequest()->clientIp(),
            'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$full_name, $email, $phone, $content]))
        ];
        
        $comment = $table->newEntity($data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                        
            $save = $table->save($comment);
            if (empty($save->id)){
                throw new Exception();
            }

            if(!$awaiting_approval && !empty($parent_id)){
                $update_reply = $this->updateNumberReply($parent_id);
                if (!$update_reply){
                    throw new Exception();
                }
            }
            
            if($data_save['type'] == PRODUCT_DETAIL){
                $update_product_comment = $this->updateInfoComment($data_save['foreign_id'], PRODUCT_DETAIL);
                if (!$update_product_comment){
                    throw new Exception();
                }
            }

            if($data_save['type'] == ARTICLE_DETAIL){
                $update_article_comment = $this->updateInfoComment($data_save['foreign_id'], ARTICLE_DETAIL);
                if (!$update_article_comment){
                    throw new Exception();
                }
            }
            
            $conn->commit();

            return $this->System->getResponse([
                CODE => SUCCESS, 
                DATA => $table->parseDetailComment($save),
                MESSAGE => __d('template', 'gui_binh_luan_thanh_cong')
            ]);
        }catch (Exception $e) {
            $conn->rollback();

            $message = !empty($e->getMessage()) ? $e->getMessage() : __d('template', 'dang_tai_binh_luan_khong_thanh_cong');
            return $this->System->getResponse([MESSAGE => $message]);
        }
    }

    public function shortRating($data = [], $options = [])
    {
        if(empty($data)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $api = !empty($options['api']) ? true : false;

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
               return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $rating = !empty($data['rating']) ? intval($data['rating']) : 0;

        $foreign_id = !empty($data['foreign_id']) ? intval($data['foreign_id']) : null;
        $type = !empty($data['type']) ? $data['type'] : null;

        if(empty($rating)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_chon_so_sao_danh_gia')]);
        }

        if(empty($foreign_id) || empty($type)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if(!in_array($type, [PRODUCT_DETAIL, ARTICLE_DETAIL])){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if($type == ARTICLE_DETAIL) {
            $table = TableRegistry::get('Articles');
        }

        if($type == PRODUCT_DETAIL) {
            $table = TableRegistry::get('Products');
        }

        $avg_rating = $rating_number = 0;
        $rating_info = $table->find()->where([
            'id' => $foreign_id,
            'deleted' => 0
        ])->select(['id', 'rating', 'rating_number'])->first();

        if(empty($rating_info)) return false;

        $avg_rating = !empty($rating_info['rating']) ? intval($rating_info['rating']) : 0;
        $rating_number = !empty($rating_info['rating_number']) ? intval($rating_info['rating_number']) : 0;

        $save_rating_number = $rating_number + 1;
        $save_avg_rating = (($avg_rating * $rating_number) + $rating) / $save_rating_number;

        $data_save = [
            'rating' => $save_avg_rating,
            'rating_number' => $save_rating_number
        ];

        $data_update = $table->patchEntity($rating_info, $data_save);        

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($data_update);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            return $this->System->getResponse([
                CODE => SUCCESS, 
                DATA => [
                    'rating' => !empty($save['rating']) ? intval($save['rating']) : 0,
                    'rating_number' => !empty($save['rating_number']) ? intval($save['rating_number']) : 0
                ],
                MESSAGE => __d('template', 'gui_danh_gia_thanh_cong')
            ]);
        }catch (Exception $e) {
            $conn->rollback();

            $message = !empty($e->getMessage()) ? $e->getMessage() : __d('template', 'gui_danh_gia_khong_thanh_cong');
            return $this->System->getResponse([MESSAGE => $message]);
        }
    }

    public function updateNumberReply($parent_id = null)
    {
        if(empty($parent_id)) return false;

        $table = TableRegistry::get('Comments');

        $parent_info = $table->find()->where([
            'id' => $parent_id,
            'parent_id IS' => null,
            'deleted' => 0
        ])->select(['id', 'number_reply'])->first();

        if(empty($parent_info)) return false;

        $data_update = $table->patchEntity($parent_info, [
            'number_reply' => $table->getNumberReply($parent_id)
        ]);        

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $update_reply = $table->save($data_update);
            if (empty($update_reply->id)){
                throw new Exception();
            }

            $conn->commit();
        }catch (Exception $e) {
            $conn->rollback();
            return false;
        }

        return true;
    }

    public function updateInfoComment($id = null, $type = null)
    {
        if(empty($id) || empty($type) || !in_array($type, [ARTICLE_DETAIL, PRODUCT_DETAIL])) return false;

        $comment_table = TableRegistry::get('Comments');

        if($type == ARTICLE_DETAIL) {
            $table = TableRegistry::get('Articles');
        }

        if($type == PRODUCT_DETAIL) {
            $table = TableRegistry::get('Products');
        }

        $query_info = $table->find()->where([
            'id' => $id,
            'deleted' => 0
        ])->select(['id', 'comment', 'rating', 'rating_number'])->first();

        if(empty($query_info)) return false;
        
        $rating_info = $comment_table->getInfoRating([
            'foreign_id' => $id,
            'type' => $type            
        ]);

        $rating = !empty($rating_info['avg_rating']) ? $rating_info['avg_rating'] : null;
        $number_rating = !empty($rating_info['number_rating']) ? intval($rating_info['number_rating']) : null;
        $number_comment = $comment_table->getNumberComment($id, $type, COMMENT);

        $data_update = $comment_table->patchEntity($query_info, [
            'comment' => !empty($number_comment) ? intval($number_comment) : null,
            'rating' => !empty($rating) ? round(floatval($rating), 1) : null,
            'rating_number' => !empty($number_rating) ? intval($number_rating) : null,
        ]);        

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($data_update);
            if (empty($save->id)){
                throw new Exception();
            }
            
            $conn->commit();
        }catch (Exception $e) {
            $conn->rollback();
            return false;
        }

        return true;
    }

    public function list($data = null, $options = [])
    {
        $type_comment = !empty($data['type_comment']) ? $data['type_comment'] : null;
        $foreign_id = !empty($data['foreign_id']) ? intval($data['foreign_id']) : null;
        $type = !empty($data['type']) ? $data['type'] : null;

        if(empty($type_comment) || empty($type)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $page = !empty($data['page']) ? intval($data['page']) : 1;
        $number_record = !empty($data['number_record']) ? intval($data['number_record']) : 20;
        $sort_field = !empty($data['sort_field']) ? $data['sort_field'] : null;
        $sort_type = !empty($data['sort_type']) ? $data['sort_type'] : null;

        $table = TableRegistry::get('Comments');        

        $parent_id = !empty($data['parent_id']) ? intval($data['parent_id']) : null;
        $params = [
            FILTER => [
                STATUS => 1,
                TYPE => $type,
                'foreign_id' => $foreign_id,
                'type_comment' => $type_comment
            ],
            SORT => []
        ];

        if(!empty($parent_id)){
            $params[FILTER]['parent_id'] = $parent_id;
        }else{
            $params['get_only_parent'] = true;
            
            if(empty($foreign_id)){
                return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
            }
        }

        $customer_account_id = !empty($data['customer_account_id']) ? $data['customer_account_id'] : null;
        $params['check_liked'] = true;
        if($customer_account_id) {
            $params['customer_account_id'] = $customer_account_id;
        }
        
        if(!empty($sort_field)){
            $params[SORT][FIELD] = $sort_field;
        }

        if(!empty($sort_type)){
            $params[SORT][SORT] = $sort_type;
        }

        try {
            $comments = $this->PaginatorExtend->paginate($table->queryListComments($params), [
                'limit' => $number_record,
                'page' => $page
            ])->toArray();
        } catch (Exception $e) {
            $comments = [];
        }

        $pagination_info = !empty($this->controller->getRequest()->getAttribute('paging')['Comments']) ? $this->controller->getRequest()->getAttribute('paging')['Comments'] : [];
        $meta_info = $this->Utilities->formatPaginationInfo($pagination_info);

        $result = [];
        if(!empty($comments)){
            foreach ($comments as $k => $comment) {
                $result[] = $table->parseDetailComment($comment);
            }
        }

        return $this->System->getResponse([
            CODE => SUCCESS, 
            DATA => [
                PAGINATION => $meta_info,
                'comments' => $result
            ]
        ]);
    }

    public function like($data = [], $options = [])
    {
        if(empty($data)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $customer_account_id = !empty($data['customer_account_id']) ? intval($data['customer_account_id']) : null;
        $comment_id = !empty($data['comment_id']) ? intval($data['comment_id']) : null;
        $type = !empty($data['type']) ? $data['type'] : null;

        if(empty($comment_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_binh_luan')]);
        }

        $table = TableRegistry::get('Comments');
        $like_table = TableRegistry::get('CommentsLike');

        $comment_info = $table->find()->where(['id' => $comment_id, 'deleted' => 0])->first();
        if(empty($comment_info)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_binh_luan')]);
        }

        $like = true;
        $like_entity = [];
        if(!empty($customer_account_id)){
            $liked = $like_table->find()->where([
                'customer_account_id' => $customer_account_id,
                'comment_id' => $comment_id
            ])->first();

            $like = !empty($liked) ? false : true;

            if(!empty($liked)){
                $like_entity = $liked;
            }else{
                $like_entity = $like_table->newEntity([
                    'customer_account_id' => $customer_account_id,
                    'comment_id' => $comment_id
                ]);
            }

            if($like){
                $like_entity = $like_table->newEntity([
                    'customer_account_id' => $customer_account_id,
                    'comment_id' => $comment_id
                ]);
            }
        }else{
            $like = !empty($type) && $type == 'dislike' ? $like = false : true;
        }

        $number_like = !empty($comment_info['number_like']) ? intval($comment_info['number_like']) : null;
        if($like){
            $number_like ++;
        }else{
            $number_like --;
        }
        $data_save = [
            'number_like' => $number_like,
        ];

        $comment_entity = $table->patchEntity($comment_info, ['number_like' => $number_like]);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                        
            $save = $table->save($comment_entity);
            if (empty($save->id)){
                throw new Exception();
            }

            if(!empty($like_entity)){
                if($like){
                    $save_like = $like_table->save($like_entity);
                    if (empty($save_like->id)){
                        throw new Exception();
                    }
                }else{
                    $delete_like = $like_table->delete($like_entity);
                }
            }

            $conn->commit();

            return $this->System->getResponse([CODE => SUCCESS, 
                DATA => [
                    'id' => $comment_id,
                    'type' => $type,
                    'number_like' => !empty($number_like) ? $number_like : null
                ]
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);  
        }
    }

    public function uploadImage($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if(!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $file = !empty($data['file']) ? $data['file'] : [];
        $path = !empty($data['path']) ? $data['path'] : COMMENT;
        if(empty($file)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if(empty($file['type']) || !in_array($file['type'], ['image/png', 'image/jpeg', 'image/jpg'])){
            return $this->System->getResponse([MESSAGE => __d('template', 'tep_dang_tai_khong_hop_le')]);
        }

        $result_upload = $this->Upload->uploadToCdn($file, $path);

        if(empty($result_upload[CODE]) || $result_upload[CODE] != SUCCESS){
            return $this->System->getResponse([
                MESSAGE => !empty($result_upload[MESSAGE]) ? $result_upload[MESSAGE] : null
            ]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS, 
            DATA => $result_upload[DATA] ? $result_upload[DATA] : []
        ]);      
    }
}
