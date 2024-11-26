<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;

class BrandController extends AppController {

    public function initialize(): void
    {
        parent::initialize();        
    }

    public function list() 
    {
        $this->css_page = [
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];
        $this->js_page = [
            '/assets/js/pages/list_brand.js',
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'brand');
        $this->set('title_for_layout', __d('admin', 'thuong_hieu'));   
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Brands');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $brands = [];

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

        $params[FILTER][LANG] = !empty($params[FILTER][LANG]) ? $params[FILTER][LANG] : TableRegistry::get('Languages')->getDefaultLanguage();

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $params['get_user'] = true;

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $brands = $this->paginate($table->queryListBrands($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $brands = $this->paginate($table->queryListBrands($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        // parse data before output
        $result = [];
        if(!empty($brands)){
            $languages = TableRegistry::get('Languages')->getList();
            foreach($brands as $k => $brand){
                $result[$k] = $table->formatDataBrandDetail($brand, $this->lang);
                
                // check multiple language
                $mutiple_language = [];
                if(!empty($languages)){
                    foreach($languages as $lang => $language){
                        if($lang == $this->lang && !empty($article['name'])){
                            $mutiple_language[$lang] = true;

                        }else{
                            $content = TableRegistry::get('BrandsContent')->find()->where([
                                'brand_id' => !empty($brand['id']) ? intval($brand['id']) : null,
                                'lang' => $lang
                            ])->select(['name'])->first();
                            
                            $mutiple_language[$lang] = !empty($content['name']) ? true : false;
                        }                        
                    }
                }


                $result[$k]['mutiple_language'] = $mutiple_language;
            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Brands']) ? $this->request->getAttribute('paging')['Brands'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $max_record = TableRegistry::get('Brands')->find()->select('id')->max('id');

        $this->set('position', !empty($max_record->id) ? $max_record->id + 1 : 1);
        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/brand.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];
        $this->set('path_menu', 'brand');
        $this->set('title_for_layout', __d('admin', 'them_thuong_hieu'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $brand = TableRegistry::get('Brands')->getDetailBrand($id, $this->lang, ['get_user' => true]);        
        $brand = TableRegistry::get('Brands')->formatDataBrandDetail($brand, $this->lang);

        if(empty($brand)){
            $this->showErrorPage();
        }

        $this->set('position', !empty($brand['position']) ? $brand['position'] : 1);
        $this->set('id', $id);
        $this->set('brand', $brand);

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/brand.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];
        $this->set('path_menu', 'brand');
        $this->set('title_for_layout', __d('admin', 'cap_nhat_thuong_hieu'));
    }

    public function detail($id = null)
    {
        if(empty($id)){
            $this->showErrorPage();
        }

        $table = TableRegistry::get('Brands');

        $brand_detail = $table->getDetailBrand($id, $this->lang, ['get_user' => true]);
        if(empty($brand_detail)){
            $this->showErrorPage();
        }

        $brand = $table->formatDataBrandDetail($brand_detail, $this->lang);

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-4.css',
            '/assets/plugins/global/lightbox/lightbox.css'
        ];
        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];

        $this->set('brand', $brand);
        $this->set('title_for_layout', __d('admin', 'chi_tiet_thuong_hieu'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('Brands');        

        if(!empty($id)){
            $brand = $table->getDetailBrand($id, $this->lang, [
                'get_user' => false
            ]);

            if(empty($brand)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_tieu_de')]);
        }

        $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : null;
        if(empty($link)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_duong_dan')]);
        }

        $link_id = !empty($brand['Links']) ? $brand['Links']['id'] : null;
        if(TableRegistry::get('Links')->checkExist($link, $link_id)){
            $this->responseJson([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
        }

        // format data before save
        $list_keyword = !empty($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : null;
        $seo_keyword = !empty($list_keyword) ? implode(', ', $list_keyword) : null;

        $url_video = !empty($data['url_video']) ? $data['url_video'] : null;
        $type_video = null;
        if(!empty($url_video)){
            $type_video = !empty($data['type_video']) ? $data['type_video'] : null;
        }

        $files = [];
        if(!empty($data['files'])){
            foreach (json_decode($data['files'], true) as $key => $file) {
                $files[] = str_replace(CDN_URL , '', $file);
            }
        }

        $status = isset($brand['status']) ? intval($brand['status']) : 1;
        
        $data_save = [
            'image_avatar' => !empty($data['image_avatar']) ? $data['image_avatar'] : null,
            'images' => !empty($data['images']) ? $data['images'] : null,
            'url_video' => $url_video,
            'type_video' => $type_video,
            'files' => !empty($files) ? json_encode($files) : null,          
            'position' => !empty($data['position']) ? intval($data['position']) : 1,
            'status' => $status
        ];

        $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : null;
        $seo_title = !empty($data['seo_title']) ? trim(strip_tags($data['seo_title'])) : null;
        $seo_description = !empty($data['seo_description']) ? trim(strip_tags($data['seo_description'])) : null;
        
        $data_save['BrandsContent'] = [
            'name' => $name,
            'content' => !empty($data['content']) ? trim($data['content']) : null,
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'seo_keyword' => $seo_keyword,
            'lang' => $this->lang,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
        ];

        $data_save['Links'] = [
            'type' => BRAND_DETAIL,
            'url' => $link,
            'lang' => $this->lang,
        ];

        // merge data with entity 
        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');
            $entity = $table->newEntity($data_save, [
                'associated' => ['BrandsContent', 'Links']
            ]);
        }else{            
            $entity = $table->patchEntity($brand, $data_save);
        }

        // show error validation in model
        if($entity->hasErrors()){
            $list_errors = $utilities->errorModel($entity->getErrors());            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            // dich tự động các ngôn ngữ khác khi thêm mới bản ghi
            $auto_translate = TableRegistry::get('Settings')->getSettingAutoTranslate();
            if(empty($id) && $auto_translate){
                $this->translateBrand($save->id, $this->lang);
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    private function translateBrand($brand_id = null, $lang_from = null)
    {
        if(empty($brand_id) || empty($lang_from)) return false;

        $languages = TableRegistry::get('Languages')->getList();
        if(empty($languages)) return false;

        $utilities = $this->loadComponent('Utilities');
        $translate_component = $this->loadComponent('Admin.Translate');

        $table = TableRegistry::get('Brands');
        $links_table = TableRegistry::get('Links');
        $brand_info = $table->getDetailBrand($brand_id, $lang_from);
        if(empty($brand_info)) return false;

        foreach($languages as $lang => $language){
            if($lang == $lang_from) continue;

            $name = !empty($brand_info['BrandsContent']['name']) ? $brand_info['BrandsContent']['name'] : null;
            $translates = $translate_component->translate([$name], $lang_from, $lang);
            
            $name_translate = !empty($translates[0]) ? $translates[0] : $name;

            $link = $utilities->formatToUrl($name_translate);
            if(empty($link)) continue;

            $link = $links_table->getUrlUnique($link);
            $data_save = [
                'id' => $brand_id,
                'BrandsContent' => [
                    'name' => $name_translate,
                    'seo_title' => $name_translate,
                    'lang' => $lang,
                    'search_unicode' => strtolower($utilities->formatSearchUnicode([$name_translate]))
                ],
                'Links' => [
                    'type' => BRAND_DETAIL,
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

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Brands');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                
                $brand = $table->get($id);
                if (empty($brand)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_thuong_hieu'));
                }

                $brand = $table->patchEntity($brand, ['id' => $id, 'deleted' => 1], ['validate' => false]);
                $delete = $table->save($brand);
                if (empty($delete)){
                    throw new Exception();
                }

                // delete link
                $delete_link = TableRegistry::get('Links')->updateAll(
                    [  
                        'deleted' => 1
                    ],
                    [  
                        'foreign_id' => $id,
                        'type' => BRAND_DETAIL
                    ]
                );
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
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

        $table = TableRegistry::get('Brands');

        $brands = $table->find()->where([
            'Brands.id IN' => $ids,
            'Brands.deleted' => 0
        ])->select(['Brands.id', 'Brands.status'])->toArray();
        
        if(empty($brands)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_thuong_hieu')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $brand_id) {
            $patch_data[] = [
                'id' => $brand_id,
                'status' => $status
            ];
        }

        $entities = $table->patchEntities($brands, $patch_data, ['validate' => false]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($entities);            
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

    public function changePosition()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $id = !empty($data['id']) ? intval($data['id']) : null;
        $value = !empty($data['value']) ? $data['value'] : 0;

        if(!$this->getRequest()->is('post') || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Brands');
        $brand = $table->get($id);
        if(empty($brand)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $brand = $table->patchEntity($brand, ['position' => $value], ['validate' => false]);

        try{
            $save = $table->save($brand);

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

        $table = TableRegistry::get('Brands');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        $filter[STATUS] = 1;
        
        $brands = $table->queryListBrands([
            FILTER => $filter,
            FIELD => LIST_INFO
        ])->limit(10)->toArray();

        // parse data before output
        $result = [];
        if(!empty($brands)){
            foreach($brands as $k => $brand){
                $result[$k] = $table->formatDataBrandDetail($brand, $this->lang);
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
        ]);
    }
}