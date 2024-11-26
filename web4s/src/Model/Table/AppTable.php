<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

class AppTable extends Table
{

    private $dir_data = CACHE . DATA . DS;
    private $dir_data_block = CACHE . DATA_BLOCK . DS;
    private $dir_template = CACHE . TEMPLATE . DS;

    private $old_data = [];

    public function initialize(array $config): void
    {
        parent::initialize($config);
    }

    public function beforeSave(Event $event, EntityInterface $entity)
    {
        $id = !empty($entity['id']) ? intval($entity['id']) : null;
        $is_new = $entity->isNew();
        if($is_new) return true;

        $alias = $event->getSubject()->getAlias();
        // lấy dữ liệu trước khi cập nhật để thêm vào log

        if($alias == 'TemplatesBlock'){
            $this->old_data = TableRegistry::get($alias)->find()->where(['id' => $id])->first();
        }

        if($alias == 'TemplatesPage'){
            $this->old_data = TableRegistry::get($alias)->find()->contain([
                'TemplatesRow', 
                'TemplatesColumn'
            ])->where(['id' => $id])->first();
        }        
        
        return true;
    }

    public function afterSave(Event $event, EntityInterface $entity)
    {
        $id = !empty($entity['id']) ? intval($entity['id']) : null;
        $is_new = $entity->isNew();

        // clear cache after model table save record
        $params = [];
        $alias = $event->getSubject()->getAlias();

        switch ($alias) {
            case 'ProductsItem':
                $item_info = TableRegistry::get($alias)->find()->where(['product_id' => $id])->first();
                $product_id = !empty($item_info['product_id']) ? intval($item_info['product_id']) : null;

                $params[] = [TYPE => PRODUCT];
                $params[] = [TYPE => TAB_PRODUCT];
                if(!empty($product_id)){
                    $params[] = [
                        TYPE => PRODUCT_DETAIL,
                        'id_record' => $product_id
                    ];
                }
                $this->deleteCacheWhenUpdateRecord($params);
            break;
            
            case 'Products':
                $params[] = [TYPE => PRODUCT];
                $params[] = [TYPE => TAB_PRODUCT];
                if(!empty($id) && !$is_new){
                    $params[] = [
                        TYPE => PRODUCT_DETAIL,
                        'id_record' => $id
                    ];
                }
                $this->deleteCacheWhenUpdateRecord($params);
            break;
            
            case 'Articles':
                $params[] = [TYPE => ARTICLE];
                $params[] = [TYPE => TAB_ARTICLE];
                if(!empty($id) && !$is_new){
                    $params[] = [
                        TYPE => ARTICLE_DETAIL,
                        'id_record' => $id
                    ];
                }
                $this->deleteCacheWhenUpdateRecord($params);
            break;

            case 'Tags':
                if(!empty($id)){
                    $product_ids = TableRegistry::get('TagsRelation')->find()->where([
                        'TagsRelation.tag_id' => $id,
                        'TagsRelation.type' => PRODUCT_DETAIL
                    ])->select(['TagsRelation.foreign_id'])->toArray();
                    $product_ids = Hash::extract($product_ids, '{n}.foreign_id');

                    $article_ids = TableRegistry::get('TagsRelation')->find()->where([
                        'TagsRelation.tag_id' => $id,
                        'TagsRelation.type' => ARTICLE_DETAIL
                    ])->select(['TagsRelation.foreign_id'])->toArray();
                    $article_ids = Hash::extract($article_ids, '{n}.foreign_id');

                    if(!empty($product_ids)){
                        foreach ($product_ids as $key => $product_id) {
                            $params = [
                                [
                                    TYPE => PRODUCT_DETAIL,
                                    'id_record' => $product_id
                                ]
                            ];
                            $this->deleteCacheWhenUpdateRecord($params);
                        }
                    }

                    if(!empty($article_ids)){
                        foreach ($article_ids as $key => $article_id) {
                            $params = [
                                [
                                    TYPE => ARTICLE_DETAIL,
                                    'id_record' => $article_id
                                ]
                            ];
                            $this->deleteCacheWhenUpdateRecord($params);
                        }
                    }                    
                }
                
            break;

            case 'Categories':
                $category_info = TableRegistry::get($alias)->find()->where(['id' => $id])->first();
                $type = !empty($category_info['type']) ? $category_info['type'] : null;
                if(!empty($type)){
                    $params[] = [TYPE => 'category_' . $type];
                    $this->deleteCacheWhenUpdateRecord($params);
                }

                if(!empty($id) && !$is_new){
                    switch($type){
                        case PRODUCT:
                            $product_ids = TableRegistry::get('CategoriesProduct')->getListProductIds($id);
                            if(!empty($product_ids)){
                                foreach($product_ids as $product_id){
                                    $this->deleteCacheWhenUpdateRecord([
                                        [
                                            TYPE => PRODUCT_DETAIL,
                                            'id_record' => $product_id
                                        ]
                                    ]);
                                }
                            }
                        break;

                        case ARTICLE:
                            $article_ids = TableRegistry::get('CategoriesArticle')->getListArticleIds($id);
                            if(!empty($article_ids)){
                                foreach($article_ids as $article_id){
                                    $this->deleteCacheWhenUpdateRecord([
                                        [
                                            TYPE => ARTICLE_DETAIL,
                                            'id_record' => $article_id
                                        ]
                                    ]);
                                }
                            }                            
                        break;
                    }     
                }

                $this->deleteCacheDataByKey(CATEGORY);
            break;

            case 'TemplatesBlock':
                $block_type = !empty($entity['type']) ? $entity['type'] : null;
                $code = !empty($entity['code']) ? $entity['code'] : null;

                if(!empty($block_type) && !empty($code) && !$is_new){
                    if($block_type == HTML){
                        $this->deleteViewTmpOfBlockHtml($code);
                    }

                    $this->deleteCacheBlock($code);
                }
            break;

            case 'Languages':
            case 'Currencies':
            case 'Templates': 
                $this->deleteAllCache();
            break;

            case 'TemplatesPage':
                $page_code = !empty($entity['code']) ? $entity['code'] : null;
                if(!empty($page_code) && !$is_new){
                    $this->deleteCacheDataByKey(PAGE . '_' . $page_code);
                }

                $this->deleteCacheTemplate(PAGE);
            break;

            case 'TemplatesRow':
                $page_code = !empty($entity['page_code']) ? $entity['page_code'] : null;
                if(!empty($page_code)){
                    $this->deleteCacheDataByKey(PAGE . '_' . $page_code);

                    $page_info = TableRegistry::get('TemplatesPage')->getInfoPage(['code' => $page_code]);
                    $page_type = !empty($page_info['page_type']) ? $page_info['page_type'] : null;

                    if($page_type == LAYOUT){
                        $this->deleteCacheDataByKey(PAGE);
                    }
                }

                $this->deleteCacheTemplate(PAGE);
            break;

            case 'MobileApp': 
                $this->deleteCacheDataByKey(MOBILE_APP);
            break;

            case 'MobileTemplate': 
                $this->deleteCacheDataByKey(MOBILE_TEMPLATE);
                $this->deleteCacheDataByKey(MOBILE_PAGE);
                $this->deleteCacheDataByKey(MOBILE_BLOCK);
            break;

            case 'MobileTemplatePage':
                $page_code = !empty($entity['code']) ? $entity['code'] : null;
                if(!empty($page_code) && !$is_new){
                    $this->deleteCacheDataByKey(MOBILE_PAGE . '_' . $page_code);
                }
            break;

            case 'MobileTemplateRow':
                $page_code = !empty($entity['page_code']) ? $entity['page_code'] : null;
                if(!empty($page_code)){
                    $this->deleteCacheDataByKey(MOBILE_PAGE . '_' . $page_code);
                }
            break;

            case 'MobileTemplateBlock':
                $code = !empty($entity['code']) ? $entity['code'] : null;
                if(!empty($code) && !$is_new){                    
                    $this->deleteCacheMobileBlock($code);
                }
            break;

            case 'Settings':
            case 'Plugins':
                $this->deleteAllCache();
            break;

            case 'Brands':
                $this->deleteCacheDataByKey(BRAND);
            break;

            case 'Attributes':
                $this->deleteCacheDataByKey(ATTRIBUTE);
            break;

            case 'AttributesOptions':
                $this->deleteCacheDataByKey(ATTRIBUTE_OPTION);
            break;

            case 'PaymentsGateway':
                $this->deleteCacheDataByKey(PAYMENT_GATEWAY);
            break;

            case 'ShippingsCarrier':
                $this->deleteCacheDataByKey(SHIPPING_CARRIER);
            break;

            case 'Roles':
                $this->deleteCacheDataByKey(ROLE);
            break;

            case 'Promotions':
                $this->deleteCacheDataByKey(PROMOTION);
            break;

            case 'ShippingsMethod':
                $this->deleteCacheDataByKey(SHIPPING_METHOD);
            break;

            case 'Notifications':
                $this->deleteCacheDataByKey(NOTIFICATION);
            break;

            // thêm thông báo mới khi có đơn hàng hoặc liên hệ mới
            case 'Orders':
            case 'Contacts':
                if($is_new){
                    $type_notification = null;
                    if($alias == 'Orders'){
                        $type_notification = 'order';
                    }

                    if($alias == 'Contacts'){
                        $type_notification = 'contact';   
                    }

                    TableRegistry::get('NhNotifications')->addMyNotificationCallback($id, $type_notification);
                    $this->deleteCacheDataByKey(NH_NOTIFICATION);
                }
            break;

            case 'NhNotifications':
                $this->deleteCacheDataByKey(NH_NOTIFICATION);
            break;

            case 'ProductsPartnerStore':
                $this->deleteCacheDataByKey(PARTNER_STORE);
            break;
        }

        //lưu log
        $action = $is_new ? 'add' : 'update';
        TableRegistry::get('Logs')->writeLog($alias, $action, $id, $entity, $this->old_data);

        return true;
    }

    public function afterDelete(Event $event, EntityInterface $entity)
    {
        $id = !empty($entity['id']) ? intval($entity['id']) : null;
        $alias = $event->getSubject()->getAlias();
        switch ($alias) {
            case 'TemplatesPage':
                $page_code = !empty($entity['code']) ? $entity['code'] : null;
                if(!empty($page_code)){
                    $this->deleteCacheDataByKey(PAGE . '_' . $page_code);
                }

                $this->deleteCacheTemplate(PAGE);
            break;

            case 'TemplatesRow':
                $page_code = !empty($entity['page_code']) ? $entity['page_code'] : null;
                if(!empty($page_code)){
                    $this->deleteCacheDataByKey(PAGE . '_' . $page_code);
                }

                $this->deleteCacheTemplate(PAGE);
            break;

            case 'NhNotifications':
                $this->deleteCacheDataByKey(NH_NOTIFICATION);
            break;            
        }

        //lưu log
        $action = 'delete';
        TableRegistry::get('Logs')->writeLog($alias, $action, $id, $entity);
      
        return true;
    }

    public function deleteCacheWhenUpdateRecord($options = [])
    {
        if(empty($options)) return false;

        foreach ($options as $k => $option) {
            $type = !empty($option[TYPE]) ? $option[TYPE] : null;
            $id_record = !empty($option['id_record']) ? $option['id_record'] : null;

            if(empty($type)) continue;

            $blocks = TableRegistry::get('TemplatesBlock')->queryListBlocks([
                FIELD => LIST_INFO,
                FILTER => [
                    TYPE => $type
                ]
            ])->select(['id', 'code'])->toArray();

            if(!empty($blocks)){
                foreach ($blocks as $k => $block) {
                    $code = !empty($block['code']) ? $block['code'] : null;
                    $this->deleteCacheBlock($code, [
                        'ignore_info' => true,
                        'id_record' => $id_record
                    ]);
                }
            }

            // xóa cache plugin mobile_app
            $plugins = TableRegistry::get('Plugins')->getList();
            if(empty($plugins[MOBILE_APP]) || !defined('CODE_MOBILE_TEMPLATE')) continue;
            $mobile_blocks = TableRegistry::get('MobileTemplateBlock')->queryListMobileBlocks([
                FIELD => LIST_INFO,
                FILTER => [
                    TYPE => $type
                ]
            ])->select(['id', 'code'])->toArray();

            if(!empty($mobile_blocks)){
                foreach ($mobile_blocks as $k => $block) {
                    $code = !empty($block['code']) ? $block['code'] : null;
                    $this->deleteCacheMobileBlock($code);
                }
            }            
        }

        return true;
    }

    public function deleteCacheBlock($code = null, $params = [])
    {
        if(empty($code)) return true;

        $id_record = !empty($params['id_record']) ? intval($params['id_record']) : null;
        $ignore_info = !empty($params['ignore_info']) ? true : false;
        $ignore_data = !empty($params['ignore_data']) ? true : false;
        $ignore_template = !empty($params['ignore_template']) ? true : false;

        $folder_data = new Folder($this->dir_data);
        $folder_data_block = new Folder($this->dir_data_block);
        $folder_template = new Folder($this->dir_template);
        
        //clear cache of block
        if(!$ignore_info){
            $key_block = BLOCK . '_' . $code . '.*';
            
            $files = !empty($folder_data->path) ? $folder_data->findRecursive($key_block) : [];
            $this->deleteListFiles($files);
        }

        if(!$ignore_data){
            $key_data_block = $code . '.*';
            if(!empty($id_record)){
                $key_data_block = $code . '_' . $id_record . '.*';
            }

            $files = !empty($folder_data_block->path) ? $folder_data_block->findRecursive($key_data_block) : [];
            $this->deleteListFiles($files);
        }
        
        if(!$ignore_template){
            $key_template = 'element_' . BLOCK . '_' . $code . '.*';
            if(!empty($id_record)){
                $key_template = 'element_' . BLOCK . '_' . $code . '_' . $id_record . '.*';
            }

            $files = !empty($folder_template->path) ? $folder_template->findRecursive($key_template) : [];
            $this->deleteListFiles($files);
        }

        // clear cache template page contain this block
        $rows = TableRegistry::get('TemplatesColumn')->getListRowsContainBlock($code);
        if(empty($rows)) return true;

        $pages = [];
        $has_layout = false;
        foreach ($rows as $key => $row) {
            $page_code = !empty($row['page_code']) ? $row['page_code'] : null;
            $row_code = !empty($row['row_code']) ? $row['row_code'] : null;
            if(empty($page_code) || empty($row_code)) continue;

            $key_template_row = 'element_row_' . $row_code . '.*';

            // clear cache template rows
            $files = !empty($folder_template->path) ? $folder_template->findRecursive($key_template_row) : [];
            $this->deleteListFiles($files);

            if(!in_array($page_code, $pages)){
                $pages[] = $page_code;

                $page_info = TableRegistry::get('TemplatesPage')->getInfoPage(['code' => $page_code]);
                $page_type = !empty($page_info['type']) ? $page_info['type'] : null;
                if($page_type == LAYOUT){
                    $has_layout = true;
                }
            }
        }

        // clear cache template page
        if(empty($pages)) return true;

        if($has_layout){
            $this->deleteCacheTemplate(PAGE);
            $this->deleteCacheDataByKey(PAGE);
            return true;
        }
        
        if(!$has_layout && !empty($pages)){
            foreach ($pages as $key => $page_code) {
                $this->deleteCacheDataByKey(PAGE . '_' . $page_code);
                
                $key_template_page = 'element_page_' . $page_code . '.*';
                $files = !empty($folder_template->path) ? $folder_template->findRecursive($key_template_page) : [];
                $this->deleteListFiles($files);
            }
        }

        return true;
    }

    public function deleteCacheMobileBlock($code = null)
    {
        if(empty($code)) return true;

        $folder_data = new Folder($this->dir_data);
        $folder_data_block = new Folder($this->dir_data_block);
        
        //xóa cache block
        $key_block = MOBILE_BLOCK . '_' . $code . '.*';            
        $files = !empty($folder_data->path) ? $folder_data->findRecursive($key_block) : [];
        $this->deleteListFiles($files);

        // xóa cache data
        $key_data_block = $code . '.*';            
        $files = !empty($folder_data_block->path) ? $folder_data_block->findRecursive($key_data_block) : [];
        $this->deleteListFiles($files);        

        $this->deleteCacheDataByKey(MOBILE_PAGE);

        return true;
    }

    public function deleteCacheTemplate($type = null)
    {
        if(empty($type) || !in_array($type, [ALL, PAGE, BLOCK])){
            $type = ALL;
        }

        $folder_template = new Folder($this->dir_template);
        if(empty($folder_template->path)) return true;

        switch ($type) {
            case ALL:
                Cache::clear(TEMPLATE);
            break;

            case PAGE:
                $files = $folder_template->findRecursive('element_page.*');
                $this->deleteListFiles($files);
            break;

            case BLOCK:
                $files = $folder_template->findRecursive('element_block.*');
                $this->deleteListFiles($files);
            break;
        }
    
        return true;
    }

    public function deleteCacheDataByKey($key = null)
    {
        if(empty($key)) return true;

        $folder = new Folder($this->dir_data, false);
        if(empty($folder->path)) return true;

        $files = $folder->findRecursive($key . '.*');
        if(empty($files)) return true;

        foreach ($files as $k => $path) {
            $file = new File($path, false);
            if($file->exists()){
                $delete = @$file->delete();                
            }
            $file->close();
        }

        return true;
    }

    public function deleteAllCache()
    {
        // delete cache system
        Cache::clearAll();

        // delete view tmp of block html
        $this->deleteViewTmpOfBlockHtml();

        // delete another folder cache
        $cache_folder = new Folder(CACHE);
        $cache_structure = $cache_folder->read(false);
        $cache_folders = !empty($cache_structure[0]) ? $cache_structure[0] : [];

        if(!empty($cache_folders)){
            foreach ($cache_folders as $k => $folder_name) {
                if(in_array($folder_name, ['models', 'persistent', 'data', 'data_block', 'template'])) continue;
                $folder = new Folder(CACHE . $folder_name, false);
                $delete = @$folder->delete();
            }
        }
        
        // delete another folder tmp
        $tmp_folder = new Folder(TMP);
        $tmp_structure = $tmp_folder->read(false);
        $tmp_folders = !empty($tmp_structure[0]) ? $tmp_structure[0] : [];
        $tmp_files = !empty($tmp_structure[1]) ? $tmp_structure[1] : [];

        if(!empty($tmp_files)){
            foreach ($tmp_files as $k => $file_name) {
                $file = new File(TMP . $file_name, false);
                if($file->exists()){
                    @$file->delete();
                }
                $file->close();
            }
        }

        if(!empty($tmp_folders)){
            foreach ($tmp_folders as $k => $folder_name) {
                if(in_array($folder_name, ['cache', 'sessions', 'logs'])) continue;
                $folder = new Folder(TMP . $folder_name, false);         
                @$folder->delete();
                
            }
        }

        return true;
    } 

    private function deleteListFiles($files = [])
    {
        if(empty($files)) return false;

        foreach ($files as $k => $path) {
            $file = new File($path, false);
            if($file->exists()){
                @$file->delete();
            }
            $file->close();
        }
        return true;
    }

    private function deleteViewTmpOfBlockHtml($code = '')
    {
        if(!defined('PATH_TEMPLATE')) return true;
        
        $folder = new Folder(PATH_TEMPLATE . BLOCK . DS . HTML . DS);
        if(empty($folder->path)) return true;

        $files = $folder->findRecursive($code . '.*\.tpl');
        $this->deleteListFiles($files);

        return true;
    }

}