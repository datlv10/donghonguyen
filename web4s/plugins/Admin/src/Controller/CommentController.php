<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\I18n\Time;
use Cake\Datasource\ConnectionManager;

class CommentController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $table = TableRegistry::get('Comments');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = [];

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : 10;
        
        // sort 
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $comment = $this->paginate($table->queryListComments($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $comment = $this->paginate($table->queryListComments($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Comments']) ? $this->request->getAttribute('paging')['Comments'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $comments = [];
        if(!empty($comment)){
            foreach($comment as $item){
                $comment = $table->parseDetailComment($item);
                $comments[] = $comment;
            }
        }
        

        $this->set('comment', $comments);
        $this->set('pagination', $meta_info);

        if($this->request->is('ajax')){
            $this->viewBuilder()->enableAutoLayout(false);
            $this->render('list_comment_element');
        }else{

            $this->set('first_comment', !empty($comments[0]) ? $comments[0] : []);

            $this->css_page = [
                '/assets/css/pages/todo/todo.css',
                '/assets/plugins/global/lightbox/lightbox.css'
            ];
            $this->js_page = [
                '/assets/js/pages/list_comment.js',
                '/assets/plugins/global/lightbox/lightbox.min.js'
            ];
            $this->set('path_menu', 'comment');
            $this->set('title_for_layout', __d('admin', 'danh_sach_binh_luan'));
            $this->render('list');            
        }      
    }

    public function viewComment($id = null)
    {
        if(empty($id)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $this->viewBuilder()->enableAutoLayout(false);
        $table = TableRegistry::get('Comments');

        $comment = $table->getDetailComment($id);
        $comment = $table->parseDetailComment($comment);

        $this->set('comment', $comment);
        $this->render('view_comment_element');
    }

    public function commentModal()
    {
        $data = $this->getRequest()->getData();

        $id = !empty($data['id']) ? $data['id'] : null;
        $parent_id = !empty($data['parent_id']) ? $data['parent_id'] : null;

        if(empty($id)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $this->viewBuilder()->enableAutoLayout(false);
        $table = TableRegistry::get('Comments');

        $where = ['deleted' => 0];

        if(!empty($parent_id)) {
            $where['OR'] = [
                'id' => $parent_id,
                'parent_id' => $parent_id
            ];
            $this->set('current_comment', $id);
        } else {
            $where['OR'] = [
                'id' => $id,
                'parent_id' => $id
            ];
        }

        $comments = $table->find()->where($where)->order('id ASC')->toArray();

        $comment_list = [];
        if(!empty($comments)){
            foreach($comments as $item){
                $comment_list[] = $table->parseDetailComment($item);
            }
        }
        $this->set('comment_list', $comment_list);
    }

    public function uploadFile()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $file = !empty($_FILES['file']) ? $_FILES['file'] : [];
        if(empty($file)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $result_upload = $this->loadComponent('Upload')->uploadToCdn($file, COMMENT);

        if(empty($result_upload[CODE]) || $result_upload[CODE] != SUCCESS){
            $this->responseJson([
                MESSAGE => !empty($result_upload[MESSAGE]) ? $result_upload[MESSAGE] : null
            ]);
        }

        $this->responseJson([
            CODE => SUCCESS, 
            DATA => $result_upload[DATA] ? $result_upload[DATA] : []
        ]);
    }

    public function adminReply()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        $id = !empty($data['id']) ? $data['id'] : null;
        $content = !empty($data['content']) ? $data['content'] : null;

        if(!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty($content)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_noi_dung_binh_luan')]);   
        }

        if(empty($id)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_binh_luan')]);   
        }

        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('Comments');

        $comment = $table->find()->where(['id' => $id, 'deleted' => 0])->first();
        if(empty($comment)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_binh_luan')]);   
        }

        $parent_id = !empty($comment['parent_id']) ? intval($comment['parent_id']) : null;        
        if(!empty($parent_id)){
            $parent_info = $table->find()->where([
                'id' => $parent_id, 
                'deleted' => 0
            ])->first();

            if(empty($parent_info)){
                $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_binh_luan')]);
            }
        }else{
            $parent_info = $comment;
            $parent_id = $id;
        }        

        $images = [];
        if(!empty($data['images'])){
            foreach (json_decode($data['images'], true) as $key => $image) {
                $images[] = str_replace(CDN_URL , '', $image);
            }
        }

        $info_user = $this->Auth->user();

        $data_comment = [
            'type_comment' => !empty($parent_info['type_comment']) ? $parent_info['type_comment'] : null,
            'type' => !empty($parent_info['type']) ? $parent_info['type'] : null,
            'foreign_id' => !empty($parent_info['foreign_id']) ? intval($parent_info['foreign_id']) : null,            
            'parent_id' => $parent_id,
            'full_name' => !empty($info_user['full_name']) ? $info_user['full_name'] : null,            
            'email' => !empty($info_user['email']) ? $info_user['email'] : null,
            'phone' => null,
            'content' => $content,
            'url' => !empty($parent_info['url']) ? $parent_info['url'] : null,
            'parent_id' => $parent_id,
            'images' => $images,
            'status' => 1,
            'is_admin' => 1,
            'admin_user_id' => !empty($info_user['id']) ? $info_user['id'] : null,
            'foreign_id' => !empty($parent_info['foreign_id']) ? intval($parent_info['foreign_id']) : null,
            'type' => !empty($parent_info['type']) ? $parent_info['type'] : null
        ];

        $add_comment = $this->loadComponent('Comment')->addComment($data_comment);
        die(json_encode($add_comment));        
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;
        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Comments');
        $comment_component = $this->loadComponent('Comment');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $comment = $table->find()->where([
                    'id' => $id,
                    'deleted' => 0
                ])->select(['id', 'type_comment', 'type', 'parent_id', 'foreign_id', 'status'])->first();
                if (empty($comment)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_binh_luan'));
                }

                $comment = $table->patchEntity($comment, ['id' => $id, 'status' => $status]);
                $save = $table->save($comment);
                if (empty($save->id)){
                    throw new Exception();
                }

                if(!empty($comment['parent_id'])) {
                    $update_reply = $comment_component->updateNumberReply($comment['parent_id']);
                    if (!$update_reply){
                        throw new Exception();
                    }
                }

                if($comment['type'] == PRODUCT_DETAIL){
                    $update_product_comment = $comment_component->updateInfoComment($comment['foreign_id'], PRODUCT_DETAIL);
                    if (!$update_product_comment){
                        throw new Exception();
                    }
                }

                if($comment['type'] == ARTICLE_DETAIL){
                    $update_article_comment = $comment_component->updateInfoComment($comment['foreign_id'], ARTICLE_DETAIL);
                    if (!$update_article_comment){
                        throw new Exception();
                    }
                }
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();

            $message = !empty($e->getMessage()) ? $e->getMessage() : __d('admin', 'cap_nhat_khong_thanh_cong');
            $this->responseJson([MESSAGE => $message]);  
        }
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Comments');
        $comment_component = $this->loadComponent('Comment');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $comment = $table->find()->where([
                    'id' => $id,
                    'deleted' => 0
                ])->select(['id', 'type_comment', 'type', 'parent_id', 'foreign_id'])->first();

                if (empty($comment)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin'));
                }

                $delete = $table->delete($comment);
                if (empty($delete)){
                    throw new Exception();
                }

                if(!empty($comment['parent_id'])) {
                    $update_reply = $comment_component->updateNumberReply($comment['parent_id']);
                    if (!$update_reply){
                        throw new Exception();
                    }
                }

                if($comment['type'] == PRODUCT_DETAIL){
                    $update_product_comment = $comment_component->updateInfoComment($comment['foreign_id'], PRODUCT_DETAIL);
                    if (!$update_product_comment){
                        throw new Exception();
                    }
                }

                if($comment['type'] == ARTICLE_DETAIL){
                    $update_product_comment = $comment_component->updateInfoComment($comment['foreign_id'], ARTICLE_DETAIL);
                    if (!$update_product_comment){
                        throw new Exception();
                    }
                }
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}