<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Text;

class TagController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->js_page = '/assets/js/pages/list_tag.js';

        $this->set('path_menu', 'tag');
        $this->set('title_for_layout', __d('admin', 'danh_sach_the_bai_viet'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Tags');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = [];

        $limit = PAGINATION_LIMIT_ADMIN;
        $page = 1;
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        $params[FILTER][LANG] = !empty($params[FILTER][LANG]) ? $params[FILTER][LANG] : null;

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [FIELD => 'id', SORT => DESC];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $tags = $this->paginate($table->queryListTags($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $tags = $this->paginate($table->queryListTags($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Tags']) ? $this->request->getAttribute('paging')['Tags'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $tags, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/tag.js',
        ];

        $this->set('path_menu', 'tag');
        $this->set('title_for_layout', __d('admin', 'them_the_bai_viet'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $tag = TableRegistry::get('Tags')->find()->where(['Tags.id' => $id])->first();
        if(empty($tag)){
            $this->showErrorPage();
        }
        
        $this->set('path_menu', 'tag');
        $this->set('id', $id);
        $this->set('tag', $tag);

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/tag.js',
        ];

        $this->set('title_for_layout', __d('admin', 'cap_nhat_the_bai_viet'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
       
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $tag_component = $this->loadComponent('Admin.Tag');

        $result = $tag_component->saveTag($data, $id);
        exit(json_encode($result));        
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

        $table = TableRegistry::get('Tags');
        $tags_relation_table = TableRegistry::get('TagsRelation');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach ($ids as $key => $id) {
                $tag_info = $table->find()->where(['id' => $id])->first();
                if(empty($tag_info)) continue;

                $delete = $table->delete($tag_info);
                $delete_relation = $tags_relation_table->deleteAll([
                    'tag_id' => $id
                ]);
            }            

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function autoSuggest()
    {
        $this->layout = false;
        $this->autoRender = false;
        
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
        
        $data = $this->getRequest()->getData();

        $params = [
            FIELD => LIST_INFO,
            FILTER => [
                LANG => $this->lang,
                KEYWORD => !empty($data['keyword']) ? $data['keyword'] : null
            ]
        ];
        
        $tags = TableRegistry::get('Tags')->queryListTags($params)->limit(7)->toArray();

        $results = [];
        if(!empty($tags)){
            foreach($tags as $item){
                $results[] = [
                    'value' => !empty($item['name']) ? $item['name'] : null
                ];
            }
        }
        
        exit(json_encode($results));
    }
}