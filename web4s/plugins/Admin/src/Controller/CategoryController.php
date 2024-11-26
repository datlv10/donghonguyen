<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class CategoryController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    // tách 2 function listCategoryProduct và listJsonCategoryArticle nhằm múc đích phân quyền cho các action này
    public function listCategoryProduct()
    {
        $type = PRODUCT;

        $this->js_page = '/assets/js/pages/list_category.js';
        $this->set('type', $type);
        $this->set('path_menu', 'category_' . $type);        
        $this->set('title_for_layout', __d('admin', 'danh_muc_san_pham'));
        $this->render('list');
    }

    public function listCategoryArticle()
    {
        $type = ARTICLE;

        $this->js_page = '/assets/js/pages/list_category.js';
        $this->set('type', $type);
        $this->set('path_menu', 'category_' . $type);    
        $this->set('title_for_layout', __d('admin', 'danh_muc_bai_viet'));
        $this->render('list');
    }

    public function listJsonCategoryProduct()
    {
        $this->listJson(PRODUCT);
    }

    public function listJsonCategoryArticle()
    {
        $this->listJson(ARTICLE);
    }

    public function listJson($type = null)
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $utilities = $this->loadComponent('Utilities');

        $data = $params = $categories = [];

        $limit = 1000;
        $page = 1;
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }
        
        $params[FILTER][TYPE] = $type;
        $params[FILTER][LANG] = $this->lang;

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $params['get_user'] = true;
        $params['get_empty_name'] = true;

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;
        $categories = TableRegistry::get('Categories')->queryListCategories($params)->all()->nest('id', 'parent_id')->toArray();
        
        if(!empty($categories)){
            $categories = TableRegistry::get('Categories')->parseDataCategories($categories, 0);
        }
        
        $categories = !empty($categories) ? array_values($categories) : [];

        // parse data before output
        $result = [];
        if(!empty($categories)){
            $languages = TableRegistry::get('Languages')->getList();
            foreach($categories as $k => $category){
                $result[$k] = $category;
                
                // check multiple language
                $mutiple_language = [];                
                if(!empty($languages)){
                    foreach($languages as $lang => $language){
                        if($lang == $this->lang && !empty($category['name'])){
                            $mutiple_language[$lang] = true;

                        }else{
                            $content = TableRegistry::get('CategoriesContent')->find()->where([
                                'category_id' => !empty($category['id']) ? intval($category['id']) : null,
                                'lang' => $lang
                            ])->select(['name'])->first();
                            
                            $mutiple_language[$lang] = !empty($content['name']) ? true : false;
                        }                        
                    }
                }

                $result[$k]['mutiple_language'] = $mutiple_language;
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $utilities->formatPaginationInfo()
        ]);
    }

    public function add($type = null)
    {
        if(!in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) {
            $this->showErrorPage();
        }

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes = !empty($all_attributes[CATEGORY]) ? $all_attributes[CATEGORY] : [];

        $all_options = [];
        if(!empty($all_attributes)){
            $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($this->lang), '{n}.id', '{n}.name','{n}.attribute_id');
        }

        $max_record = TableRegistry::get('Categories')->find()->select('id')->max('id');

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/category.js'
        ];
        $this->set('type', $type);
        $this->set('all_attributes', $all_attributes);
        $this->set('all_options', $all_options);
        $this->set('position', !empty($max_record->id) ? $max_record->id + 1 : 1);
        
        $this->set('path_menu', 'category_' . $type);
        $this->set('title_for_layout', __d('admin', 'them_danh_muc'));
        $this->render('update');
    }

    public function update($type = null, $id = null)
    {
        if(!in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) {
            $this->showErrorPage();
        }
                
        $category = TableRegistry::get('Categories')->getDetailCategory($type, $id, $this->lang, [
            'get_user' => true,
            'get_attributes' => true
        ]);

        $category = TableRegistry::get('Categories')->formatDataCategoryDetail($category);
        if(empty($category)){
            $this->showErrorPage();
        }

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes = !empty($all_attributes[CATEGORY]) ? $all_attributes[CATEGORY] : [];

        $all_options = [];
        if(!empty($all_attributes)){
            $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($this->lang), '{n}.id', '{n}.name','{n}.attribute_id');
        }
        $this->set('path_menu', 'category_' . $type);
        $this->set('id', $id);
        $this->set('all_attributes', $all_attributes);
        $this->set('all_options', $all_options);
        $this->set('position', !empty($category['position']) ? $category['position'] : 1);
        $this->set('category', $category);
        $this->set('type', $type);

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/category.js'            
        ];
        $this->set('title_for_layout', __d('admin', 'cap_nhat'));
    }

    public function detail($type = null, $id = null)
    {
        if(!in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) {
            $this->showErrorPage();
        }
        
        $table = TableRegistry::get('Categories');
        $category_detail = $table->getDetailCategory($type, $id, $this->lang, [
            'get_user' => true
        ]);

        $category = $table->formatDataCategoryDetail($category_detail);
        if(empty($category)){
            $this->showErrorPage();
        }

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-4.css',
            '/assets/plugins/global/lightbox/lightbox.css'
        ];
        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];

        $this->set('type', $type);
        $this->set('category', $category);
        $this->set('title_for_layout', __d('admin', 'chi_tiet_danh_muc'));
    }

    public function save($type = null, $id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($data) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $utilities = $this->loadComponent('Utilities');
        $attribute_component = $this->loadComponent('Admin.Attribute');
        $table = TableRegistry::get('Categories');
                
        if(!empty($id)){
            $category = $table->getDetailCategory($type, $id, $this->lang, ['get_attributes' => true]);
            if(empty($category)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : null;
        if(empty($link)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_duong_dan')]);
        }

        $link_id = !empty($category['Links']['id']) ? $category['Links']['id'] : null;
        if(TableRegistry::get('Links')->checkExist($link, $link_id)){
            $this->responseJson([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
        }

        // format data before save
        $list_keyword = !empty($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : null;
        $seo_keyword = !empty($list_keyword) ? implode(', ', $list_keyword) : null;

        $parent_id = !empty($data['parent_id']) ? intval($data['parent_id']) : null;
        $path_id = null;
        if(!empty($parent_id)){
            $parent_info = $table->get($parent_id);
            $parent_path_id = !empty($parent_info['path_id']) ? $parent_info['path_id'] : '';
            if(!empty($parent_path_id)){
                $path_id = $parent_path_id . $parent_id . '|';
            }else{
                $path_id = '|' . $parent_id . '|';
            }
        }
        
        $url_video = !empty($data['url_video']) ? $data['url_video'] : null;
        $type_video = null;
        if(!empty($url_video)){
            $type_video = !empty($data['type_video']) ? $data['type_video'] : null;
        }

        $data_save = [
            'type' => $type,
            'parent_id' => $parent_id,
            'path_id' => $path_id,
            'image_avatar' => !empty($data['image_avatar']) ? $data['image_avatar'] : null,
            'images' => !empty($data['images']) ? $data['images'] : null,
            'url_video' => $url_video,
            'type_video' => $type_video,
            'position' => !empty($data['position']) ? intval($data['position']) : 1,
        ];

        $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : null;
        $seo_title = !empty($data['seo_title']) ? trim(strip_tags($data['seo_title'])) : null;
        $seo_description = !empty($data['seo_description']) ? trim(strip_tags($data['seo_description'])) : null;

        $data_save['CategoriesContent'] = [
            'name' => $name,
            'description' => !empty($data['description']) ? trim($data['description']) : null,
            'content' => !empty($data['content']) ? trim($data['content']) : null,
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'seo_keyword' => $seo_keyword,
            'lang' => $this->lang,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
        ];

        $type_link = null;
        switch ($type) {
            case PRODUCT:
                $type_link = CATEGORY_PRODUCT;
                break;
            
            case ARTICLE:
                $type_link = CATEGORY_ARTICLE;
                break;
        }
        $data_save['Links'] = [
            'type' => $type_link,
            'url' => $link,
            'lang' => $this->lang,
        ];        
    
        $data_save['CategoriesAttribute'] = $attribute_component->formatDataAttributesBeforeSave($data, $this->lang, CATEGORY, $id);

        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');
            $category = $table->newEntity($data_save, [
                'associated' => ['CategoriesContent', 'Links', 'CategoriesAttribute']
            ]);
        }else{    
            $category = $table->patchEntity($category, $data_save);
        }

        // show error validation in model
        if($category->hasErrors()){        
            $list_errors = $utilities->errorModel($category->getErrors());
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            if(!empty($id)){
                $clear_attributes = TableRegistry::get('CategoriesAttribute')->deleteAll(['category_id' => $id]);
            }

            $save = $table->save($category);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            // dich tự động các ngôn ngữ khác khi thêm mới bản ghi
            $auto_translate = TableRegistry::get('Settings')->getSettingAutoTranslate();
            if(empty($id) && $auto_translate){
                $this->translateCategory($type, $save->id, $this->lang);
            }
            
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    private function translateCategory($type = null, $category_id = null, $lang_from = null)
    {
        if(empty($type) || empty($category_id) || empty($lang_from)) return false;

        $languages = TableRegistry::get('Languages')->getList();
        if(empty($languages)) return false;

        $utilities = $this->loadComponent('Utilities');
        $translate_component = $this->loadComponent('Admin.Translate');

        $table = TableRegistry::get('Categories');
        $links_table = TableRegistry::get('Links');
        $category_info = $table->getDetailCategory($type, $category_id, $lang_from);
        if(empty($category_info)) return false;

        foreach($languages as $lang => $language){
            if($lang == $lang_from) continue;

            $name = !empty($category_info['CategoriesContent']['name']) ? $category_info['CategoriesContent']['name'] : null;            
            $translates = $translate_component->translate([$name], $lang_from, $lang);
            $name_translate = !empty($translates[0]) ? $translates[0] : $name;
            $type_link = null;
            switch ($type) {
                case PRODUCT:
                    $type_link = CATEGORY_PRODUCT;
                    break;
                
                case ARTICLE:
                    $type_link = CATEGORY_ARTICLE;
                    break;
            }

            $link = $utilities->formatToUrl($name_translate);
            if(empty($link)) continue;

            $link = $links_table->getUrlUnique($link);
            $data_save = [
                'id' => $category_id,
                'CategoriesContent' => [
                    'name' => $name_translate,
                    'seo_title' => $name_translate,
                    'lang' => $lang,
                    'search_unicode' => strtolower($utilities->formatSearchUnicode([$name_translate]))
                ],
                'Links' => [
                    'type' => $type_link,
                    'url' => $link,
                    'lang' => $lang,
                ]
            ];

            $entity = $table->newEntity($data_save);
            if($entity->hasErrors()) continue;

            $save = $table->save($entity);
        }

        return true;
    }

    public function delete($type = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids) || empty($type)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $type = 'category_' . $type;

        $categories_table = TableRegistry::get('Categories');
        $links_table = TableRegistry::get('Links');

        try{
            foreach($ids as $id){

                // delete category
                $category = $categories_table->get($id);
                $category = $categories_table->patchEntity($category, ['id' => $id, 'deleted' => 1]);
                $delete_category = $categories_table->save($category);
                if (empty($delete_category)){
                    throw new Exception();
                }

                // delete link
                $delete_link = $links_table->updateAll(
                    [  
                        'deleted' => 1
                    ],
                    [  
                        'foreign_id' => $id,
                        'type' => $type
                    ]
                );
                
                //update parent_id and path_id of child category
                $child_categories = $categories_table->getCategoriesChild($id);
                $child_ids = Hash::extract($child_categories, '{n}.id');
                if(!empty($child_ids)){
                    $update_child = $categories_table->updateAll(
                        [  
                            'path_id' => null,
                            'parent_id' => null
                        ],
                        [  
                            'id IN' => $child_ids
                        ]
                    );
                }
            }
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Categories');

        $categories = $table->find()->where([
            'Categories.id IN' => $ids,
            'Categories.deleted' => 0
        ])->select(['Categories.id', 'Categories.status'])->toArray();
        
        if(empty($categories)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_danh_muc')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $category_id) {
            $patch_data[] = [
                'id' => $category_id,
                'status' => $status
            ];
        }
        $data_categories = $table->patchEntities($categories, $patch_data);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($data_categories);
            if (empty($change_status)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function duplicate($type = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || empty($type) || !is_array($ids) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $type = 'category_' . $type;

        $table = TableRegistry::get('Categories');

        $data_dulicate = [];
        foreach($ids as $id){          
            $category = $table->find()->contain([
                'ContentMutiple',
                'LinksMutiple' => function ($q) use ($type) {
                    return $q->where([
                        'LinksMutiple.type' => $type,
                        'LinksMutiple.deleted' => 0
                    ]);
                }
            ])
            ->where([
                'Categories.id' => $id,
                'Categories.deleted' => 0,
            ])->first()->toArray();

            if(empty($category)) continue;


            // format data before mere entity
            unset($category['id']);
            unset($category['created_by']);
            unset($category['created']);
            unset($category['updated']);
            if(!empty($category['ContentMutiple'])){
                foreach($category['ContentMutiple'] as $k_content => $content){
                    $name = $this->getNameUnique($content['name'], 1);
                    $category['ContentMutiple'][$k_content]['name'] = $name;

                    unset($category['ContentMutiple'][$k_content]['id']);
                    unset($category['ContentMutiple'][$k_content]['category_id']);
                }
            }

            if(!empty($category['LinksMutiple'])){
                foreach($category['LinksMutiple'] as $k_link => $link){
                    $category['LinksMutiple'][$k_link]['url'] = $this->getUrlUnique($link['url'], 1);

                    unset($category['LinksMutiple'][$k_link]['id']);
                    unset($category['LinksMutiple'][$k_link]['foreign_id']);
                }
            }

            $data_dulicate[] = $category;
        }

        $category_entities = $table->newEntities($data_dulicate, [
            'associated' => ['ContentMutiple', 'LinksMutiple']
        ]);

        try{
            // save data
            $save = $table->saveMany($category_entities);  
            
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'nhan_ban_du_lieu_thanh_cong')]);
        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }        
    }

    public function changePosition()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $id = !empty($data['id']) ? intval($data['id']) : null;
        $value = !empty($data['value']) ? $data['value'] : 0;
        $name = !empty($data['name']) ? $data['name'] : '';
        
        // validate data
        if (empty($id) || empty($name)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Categories');

        $data_save['position'] = $value;
        $category = $table->patchEntity($table->get($id), $data_save);

        try{
            // save data
            $save = $table->save($category);

            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function autoSuggest()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Categories');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        $filter[STATUS] = 1;
        $filter[TYPE] = PRODUCT;

        $categories = $table->queryListCategories([
            FILTER => $filter,
            FIELD => LIST_INFO,
        ])->limit(10)->toArray();

        $result = [];
        if(!empty($categories)){
            foreach ($categories as $key => $category) {
                $result[] = $table->formatDataCategoryDetail($category);
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
        ]);
    }

    private function getNameUnique($name = null, $index = 1)
    {
        $name_check = $name . ' ('. $index .')';
        if($index == 100){
            return $name_check;
        }   
        $check = TableRegistry::get('Categories')->checkNameExist($name_check);
        
        if($check){
            $index ++;
            $name_check = $this->getNameUnique($name, $index);
        }
        return $name_check;
    }

    private function getUrlUnique($url = null, $index = 1)
    {
        $url_check = $url . '-'. $index;
        if($index == 100){
            return $url_check;
        }

        $check = TableRegistry::get('Links')->checkExist($url_check);

        if($check){
            $index ++;
            $url_check = $this->getUrlUnique($url, $index);
        }
        return $url_check;
    }
}