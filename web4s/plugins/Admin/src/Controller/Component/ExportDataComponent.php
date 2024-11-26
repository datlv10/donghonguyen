<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Http\Client;
use ZipArchive;

class ExportDataComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities', 'Admin.Plugin'];
    public $list_tables_export = [];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();

        $this->list_tables_export = [
            'articles', 
            'articles_content',
            'attributes',
            'attributes_content',
            'attributes_options',
            'attributes_options_content',
            'brands', 
            'brands_content', 
            'categories', 
            'categories_article', 
            'categories_content', 
            'categories_product',
            'links',
            'products',
            'products_content',
            'products_item',
            'products_item_attribute',
            'tags',
            'tags_relation'
        ];

        $this->structre_tables = [
            'articles' => [
                'id' => 'int',
                'image_avatar' => 'varchar',
                'images' => 'text',
                'url_video' => 'varchar',
                'type_video' => 'varchar',
                'files' => 'text',
                'view' => 'int',
                'like' => 'int',
                'main_category_id' => 'int',
                'has_album' => 'int',
                'has_file' => 'int',
                'has_video' => 'int',
                'comment' => 'int',
                'created_by' => 'int',
                'created' => 'int',
                'updated' => 'int',
                'position' => 'int',
                'featured' => 'int',
                'seo_score' => 'varchar',
                'keyword_score' => 'varchar',
                'status' => 'int',
                'catalogue' => 'int',
                'draft' => 'int',
                'deleted' => 'int'
            ],
            'articles_content' => [
                'article_id' => 'int',
                'name' => 'varchar',
                'description' => 'text',
                'content' => 'text',
                'seo_title' => 'varchar',
                'seo_description' => 'varchar',
                'seo_keyword' => 'varchar',
                'search_unicode' => 'text',
                'lang' => 'varchar'
            ],
            'brands' => [
                'id' => 'int',
                'image_avatar' => 'varchar',
                'images' => 'text',
                'url_video' => 'varchar',
                'type_video' => 'varchar',
                'files' => 'text',
                'created_by' => 'int',
                'created' => 'int',
                'updated' => 'int',
                'position' => 'int',
                'status' => 'int',
                'deleted' => 'int'
            ],
            'brands_content' => [
                'brand_id' => 'int',
                'name' => 'varchar',
                'content' => 'text',
                'seo_title' => 'varchar',
                'seo_description' => 'varchar',
                'seo_keyword' => 'varchar',
                'search_unicode' => 'text',
                'lang' => 'varchar'
            ],
            'categories' => [
                'id' => 'int',
                'type' => 'varchar',
                'parent_id' => 'int',
                'path_id' => 'varchar',
                'image_avatar' => 'varchar',
                'images' => 'text',
                'url_video' => 'varchar',
                'type_video' => 'varchar',
                'created_by' => 'int',
                'created' => 'int',
                'updated' => 'int',
                'position' => 'int',
                'status' => 'int',
                'deleted' => 'int'
            ],
            'categories_article' => [
                'article_id' => 'int',
                'category_id' => 'int',
            ],
            'categories_content' => [
                'category_id' => 'int',
                'name' => 'varchar',
                'search_unicode' => 'text',
                'description' => 'text',
                'content' => 'text',
                'seo_title' => 'varchar',
                'seo_description' => 'varchar',
                'seo_keyword' => 'varchar',
                'lang' => 'varchar'
            ],
            'categories_product' => [
                'product_id' => 'int',
                'category_id' => 'int'
            ],
            'links' => [
                'foreign_id' => 'int',
                'type' => 'varchar',
                'url' => 'varchar',
                'lang' => 'varchar',
                'created' => 'int',
                'updated' => 'int'
            ],
            'products' => [
                'id' => 'int',
                'brand_id' => 'int',
                'url_video' => 'varchar',
                'type_video' => 'varchar',
                'files' => 'text',
                'width' => 'decimal',
                'length' => 'decimal',
                'height' => 'decimal',
                'weight' => 'decimal',
                'width_unit' => 'varchar',
                'length_unit' => 'varchar',
                'height_unit' => 'varchar',
                'weight_unit' => 'varchar',
                'view' => 'int',
                'like' => 'int',
                'main_category_id' => 'int',
                'rating' => 'float',
                'rating_number' => 'int',
                'comment' => 'int',
                'featured' => 'int',
                'seo_score' => 'varchar',
                'keyword_score' => 'varchar',
                'position' => 'int',
                'status' => 'int',
                'created' => 'int',
                'updated' => 'int',
                'created_by' => 'int',
                'catalogue' => 'int',
                'draft' => 'int',
                'deleted' => 'int'
            ],
            'products_content' => [
                'product_id' => 'int',
                'name' => 'varchar',
                'search_unicode' => 'text',
                'description' => 'text',
                'content' => 'text',
                'sets' => 'text',
                'tags' => 'text',
                'seo_title' => 'varchar',
                'seo_description' => 'varchar',
                'seo_keyword' => 'varchar',
                'lang' => 'varchar'
            ],
            'products_item' => [
                'id' => 'int',
                'product_id' => 'int',
                'code' => 'varchar',
                'barcode' => 'varchar',
                'price' => 'decimal',
                'discount_percent' => 'decimal',
                'price_special' => 'decimal',
                'time_start_special' => 'int',
                'time_end_special' => 'int',
                'images' => 'text',
                'quantity_available' => 'int',
                'position' => 'int',
                'status' => 'int',
                'deleted' => 'int'
            ],
            'tags' => [
                'id' => 'int',
                'name' => 'varchar',
                'url' => 'varchar',
                'content' => 'text',
                'seo_title' => 'varchar',
                'seo_description' => 'varchar',
                'seo_keyword' => 'varchar',
                'lang' => 'varchar',
                'created' => 'int',
                'updated' => 'int',
                'search_unicode' => 'text'
            ],
            'tags_relation' => [
                'foreign_id' => 'int',
                'type' => 'varchar',
                'tag_id' => 'int'
            ],
            'attributes' => [
                'id' => 'int',
                'attribute_type' => 'varchar',
                'code' => 'varchar',
                'input_type' => 'varchar',
                'has_image' => 'int',
                'required' => 'int',
                'position' => 'int',
                'status' => 'int',
                'created_by' => 'int',
                'created' => 'int',
                'updated' => 'int',
                'deleted' => 'int'
            ],
            'attributes_content' => [
                'attribute_id' => 'int',
                'name' => 'varchar',
                'lang' => 'varchar',
                'search_unicode' => 'varchar'
            ],
            'attributes_options' => [
                'id' => 'int',
                'attribute_id' => 'int',
                'code' => 'varchar',
                'position' => 'int'
            ],
            'attributes_options_content' => [
                'attribute_option_id' => 'int',
                'name' => 'varchar',
                'lang' => 'varchar',
                'search_unicode' => 'varchar'
            ],
            'products_item_attribute' => [
                'product_id' => 'int',
                'product_item_id' => 'int',
                'attribute_id' => 'int',
                'value' => 'varchar'
            ]
        ];
    }

    public function readDataSyned($tables = null)
    {
        if(empty($tables) || !in_array($tables, $this->list_tables_export)) return [];

        $file_data = new File(TMP . 'export/data' . DS . $tables .'.json', false);
        if(empty($file_data->exists())) return [];
        $content = $file_data->read();
        $file_data->close();

        return $this->Utilities->isJson($content) ? json_decode($content, true) : [];
    }

    public function writeDataSyned($tables = null, $data = [])
    {

        if(empty($tables) || !in_array($tables, $this->list_tables_export)) return false;

        $file_data = new File(TMP . 'export/data' . DS . $tables .'.json', false);

        if(empty($file_data->exists())) return false;

        if(!is_array($data)) $data = [];
        
        $file_data->write(json_encode($data), 'w');
        $file_data->close();

        return true;
    }

    public function writeDataSql($tables = null, $content = null)
    {
        if(empty($tables) || (!in_array($tables, $this->list_tables_export) && $tables != 'all')) return false;

        $file_sql = new File(TMP . 'export/data' . DS . $tables .'.sql', false);

        if(empty($file_sql->exists())) return false;

        if(empty($content)) $content = '';

        $file_sql->write($content, 'w');
        $file_sql->close();

        return true;
    }

    public function exportDataSql($tables = null)
    {
        if(empty($tables)) return false;

        $list_tables = [$tables];
        if($tables == 'all'){
            $list_tables = $this->list_tables;
        }
        $content = '';
        $structure = $this->structre_tables;
        foreach($this->list_tables_export as $table){
            $data = $this->readDataSyned($table);
            if(empty($data)){
                $this->writeDataSql($table, '');
                continue;
            }

            foreach($data as $k => $item){
                $content .= "INSERT INTO " . $table . " (";

                foreach ($item as $field => $value) {
                    $content .= "`" . addslashes($field) . "`";
                    if(array_key_last($item) != $field) $content .= ", ";
                }
                
                $content .= ") VALUES (";
                foreach ($item as $field => $value) {
                    $type_data = null;
                    if(!empty($structure[$table][$field])){
                        $type_data = $structure[$table][$field];
                    }

                    switch($type_data){
                        case 'int':
                        case 'float':
                        case 'decimal':
                            $content .= is_null($value) ? 'NULL' : $value;
                        break;

                        case 'varchar':
                        case 'text':
                            $content .= !empty($value) ? "'" . addslashes($value) . "'" : 'NULL';
                        break;

                        default:
                            if(is_int($value) || is_numeric($value)){
                                $content .= $value;
                            }else{
                                $content .= "'" . addslashes($value) . "'";
                            } 
                        break;
                    }

                    if(array_key_last($item) != $field) $content .= ", ";
                }
                
                $content .= ");\n";
            }
        }
        
        if(strlen($content) > 0){
            $content = "SET sql_mode = '';\nSET FOREIGN_KEY_CHECKS = 0;\n\n" . $content;
            $content.= "SET FOREIGN_KEY_CHECKS = 1;";
        }

        $result = $this->writeDataSql($tables, $content);
        return $result;
    }

    public function readMigrateDataExportInfo()
    {
        $migrate_info = [];
        $migrate_file = new File(TMP . 'export/migrate.json', false);
        if($migrate_file->exists()){
            $content = $migrate_file->read();
            $migrate_info = $this->Utilities->isJson($content) ? json_decode($content, true) : [];
        }

        return $migrate_info;
    }

    public function initializeExportData()
    {
        // khởi tạo thư mục chứa dữ liệu export
        $dir_export_tmp = TMP . 'export' . DS;
        $folder_export_tmp = new Folder($dir_export_tmp, true, 0755);
        if(empty($folder_export_tmp->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_khoi_tao_thu_muc_chua_du_lieu_export')]);
        }

        // khởi tạo thư mục chứa dữ liệu
        $dir_data_tmp = TMP . 'export/data' . DS;
        $folder_data_tmp = new Folder($dir_data_tmp, true, 0755);
        if(empty($folder_data_tmp->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_khoi_tao_thu_muc_chua_giao_dien')]);
        }

        $dir_media_tmp = TMP . 'export/media' . DS;
        $folder_media_tmp = new Folder($dir_media_tmp, true, 0755);
        if(empty($folder_media_tmp->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_khoi_tao_thu_muc_chua_giao_dien')]);
        }

        $dir_thumbs_tmp = TMP . 'export/thumbs' . DS;
        $dir_thumbs_tmp = new Folder($dir_thumbs_tmp, true, 0755);
        if(empty($dir_thumbs_tmp->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_khoi_tao_thu_muc_chua_giao_dien')]);
        }

        // khởi tạo file chứa dữ liệu
        foreach($this->list_tables_export as $table){
            $file = new File(TMP . 'export/data' . DS . $table .'.json', true);
            if(!$file->exists()){
                return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_khoi_tao_tep_du_lieu_{0}', [$table])]);
            }
            $file->write('', 'w');
            $file->close();

            $file = new File(TMP . 'export/data' . DS . $table .'.sql', true);
            if(!$file->exists()){
                return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_khoi_tao_tep_du_lieu_{0}', [$table])]);
            }

            $file->write('', 'w');
            $file->close();
        }

        $file = new File(TMP . 'export/data' . DS . 'all.sql', true);
        if(!$file->exists()){
            $this->responseJson([MESSAGE => "Không thể khởi tạo tệp dữ liệu all.sql'"]);
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_khoi_tao_tep_du_lieu_all_sql')]);
        }

        $file->write('', 'w');
        $file->close();

        $file = new File(TMP . 'export/data.zip', false);
        if($file->exists()){
            $file->delete();
            $file->close();
        }

        $file = new File(TMP . 'export/media.zip', false);
        if($file->exists()){
            $file->delete();
            $file->close();
        }

        $file = new File(TMP . 'export/thumbs.zip', false);
        if($file->exists()){
            $file->delete();
            $file->close();
        }

        // xóa dữ liệu trong folder media
        $media_dir = TMP . 'export/media';
        $folder_media = new Folder($media_dir, true);
        if(empty($folder_media->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_khoi_tao_thu_muc_media')]);
        }

        // khởi tạo thư mục chứa ảnh
        $folder_media = new Folder($media_dir . DS . 'categories', true);
        $folder_media = new Folder($media_dir . DS . 'articles', true);
        $folder_media = new Folder($media_dir . DS . 'products', true);
        $folder_media = new Folder($media_dir . DS . 'brands', true);
        $folder_media = new Folder($media_dir . DS . 'videos', true);
        $folder_media = new Folder($media_dir . DS . 'files', true);

        $thumbs_dir = TMP . 'export/thumbs';
        $folder_thumbs = new Folder(TMP . 'export/thumbs', true);
        if(empty($folder_thumbs->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_khoi_tao_thu_muc_thumbs')]);
        }

        // khởi tạo thư mục chứa ảnh thumbs
        $folder_media = new Folder($thumbs_dir . DS . 'categories', true);
        $folder_media = new Folder($thumbs_dir . DS . 'articles', true);
        $folder_media = new Folder($thumbs_dir . DS . 'products', true);
        $folder_media = new Folder($thumbs_dir . DS . 'brands', true);
        $folder_media = new Folder($thumbs_dir . DS . 'videos', true);
        $folder_media = new Folder($thumbs_dir . DS . 'files', true);

        // khởi tạo file migrate.json
        $data_update = [
            'initialization' => ['status' => SUCCESS, MESSAGE => __d('admin', 'khoi_tao_thanh_cong')]
        ];
        $update_file_migrate = $this->updateFileMiragte('initialization', $data_update, true);

        if(!$update_file_migrate) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'cap_nhat_file_migrate_json_khong_thanh_cong')]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'khoi_tao_quy_trinh_thanh_cong')
        ]);
    }

    public function updateFileMiragte($step = null, $data = [], $init = false)
    {
        if(empty($step) || empty($data) || !is_array($data)) return false;

        $migrate_info = [];
        $migrate_file = new File(TMP . 'export/migrate.json', true);
        if($migrate_file->exists()){
            $content = $migrate_file->read();
            $migrate_info = $this->Utilities->isJson($content) ? json_decode($content, true) : [];
        }

        if($init){
            $migrate_info = [ 
                'initialization' => [
                    'initialization' => null,
                    'read_database' => null,
                    'config_data' => null,
                    'config_id' => null,
                    'config_cdn' => null,
                    'done' => false
                ],
                'categories_article' => [
                    'total_record' => 0,
                    'migrated' => 0,
                    'done' => false
                ],
                'categories_product' => [
                    'total_record' => 0,
                    'migrated' => 0,
                    'done' => false
                ],
                'articles' => [
                    'total_record' => 0,
                    'migrated' => 0,
                    'done' => false
                ],
                'products' => [
                    'total_record' => 0,
                    'migrated' => 0,
                    'done' => false
                ],
                'brands' => [
                    'total_record' => 0,
                    'migrated' => 0,
                    'done' => false
                ],
                'attributes' => [
                    'total_record' => 0,
                    'migrated' => 0,
                    'done' => false
                ],
                'tags' => [
                    'total_record' => 0,
                    'migrated' => 0,
                    'done' => false
                ],
                'success' => [
                    'export' => false,
                    'done' => false
                ]
            ];
        }

        if(empty($migrate_info) || empty($migrate_info[$step])) return false;

        foreach($data as $k => $item){
            $migrate_info[$step][$k] = $item;
        }

        // kiểm tra các bước khởi tạo đã xong chưa
        if($step == 'initialization'){
            $done = true;
            foreach($migrate_info[$step] as $key => $item){
                if($key == 'done') continue;
                if(!isset($item['status']) || $item['status'] != SUCCESS){
                    $done = false;
                }
            }
            $migrate_info[$step]['done'] = $done;
        }

        $migrate_file->write(json_encode($migrate_info, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), 'w');
        $migrate_file->close();

        return true;
    }

    public function readDatabase()
    {
        // tổng số danh mục bài viết
        $number_category_article = TableRegistry::get('Categories')->find()->where([
            'deleted' => 0, 
            'type' => ARTICLE
        ])->count();

        // tổng số bài viết
        $number_article = TableRegistry::get('Articles')->find()->where([
            'deleted' => 0
        ])->count();

        // tổng số thẻ tag bài viết
        $number_tag_article = TableRegistry::get('TagsRelation')->find()->contain(['Tags'])->where([
            'TagsRelation.type' => ARTICLE_DETAIL,
        ])->count();

        // tổng số thuộc tính mở rộng bài viết
        $number_attribute_article = TableRegistry::get('Attributes')->find()->where([
            'deleted' => 0,
            'attribute_type' => ARTICLE
        ])->count();

        // tổng số danh mục sản phẩm
        $number_category_product = TableRegistry::get('Categories')->find()->where([
            'deleted' => 0, 
            'type' => PRODUCT
        ])->count();

        // tổng số sản phẩm
        $number_product = TableRegistry::get('Products')->find()->where([
            'deleted' => 0
        ])->count();

        // tổng số thương hiệu
        $number_brand = TableRegistry::get('Brands')->find()->where([
            'deleted' => 0
        ])->count();

        // tổng số thẻ tag sản phẩm
        $number_tag_product = TableRegistry::get('TagsRelation')->find()->contain(['Tags'])->where([
            'TagsRelation.type' => PRODUCT_DETAIL,
        ])->count();

        // tổng số thuộc tính mở rộng sản phẩm
        $number_attribute_product = TableRegistry::get('Attributes')->find()->where([
            'deleted' => 0,
            'attribute_type' => PRODUCT
        ])->count();

        // tổng số thuộc tính mở rộng sản phẩm
        $number_attribute_product_item = TableRegistry::get('Attributes')->find()->where([
            'deleted' => 0,
            'attribute_type' => PRODUCT_ITEM
        ])->count();

        $number_category = $number_category_article + $number_category_product;
        $number_tag = $number_tag_article + $number_tag_product;
        $number_attribute = $number_attribute_article + $number_attribute_product + $number_attribute_product_item;

        $this->updateFileMiragte('initialization', [
            'read_database' => [
                STATUS => SUCCESS,
                MESSAGE => __d('admin', 'doc_thong_tin_du_lieu_thanh_cong'),
                DATA => [
                    'number_category_article' => $number_category_article,
                    'number_article' => $number_article,
                    'number_tag_article' => $number_tag_article,
                    'number_attribute_article' => $number_attribute_article,
                    'number_category_product' => $number_category_product,
                    'number_product' => $number_product,
                    'number_brand' => $number_brand,
                    'number_tag_product' => $number_tag_product,
                    'number_attribute_product' => $number_attribute_product,
                    'number_attribute_product_item' => $number_attribute_product_item
                ]
            ]
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'doc_thong_tin_du_lieu_thanh_cong')
        ]);
    }

    public function loadConfigAdvanced($data = [], $lang = null)
    {
        $type = !empty($data['type']) ? $data['type'] : null;
        if (empty($type) || empty($lang)) return [];

        $result = [];
        switch ($type) {
            case 'categories_product':
            case 'categories_article':

                $result = $this->loadListCategory(str_replace('categories_', '', $type), $lang);
                break;

            case 'attributes_article':
            case 'attributes_product':
            case 'attributes_product_item':

                $result = $this->loadListAttributes(str_replace('attributes_', '', $type), $lang);
                break;
        }

        return $result;
    }

    public function saveConfigAdvanced($data = [])
    {
        $ids = !empty($data['ids']) ? json_encode($data['ids']) : null;
        $type = !empty($data['type']) ? $data['type'] : null;

        if (empty($data) || empty($type)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $migrate_info = $this->readMigrateDataExportInfo();
        $config_data = !empty($migrate_info['initialization']['config_data'][DATA]) ? $migrate_info['initialization']['config_data'][DATA] : [];

        $config_data[$type] = $ids;

        $this->updateFileMiragte('initialization', [
            'config_data' => [
                DATA => $config_data
            ]
        ]);

        $number_category = !empty($config_data[$type]) ? count(json_decode($config_data[$type], true)) : 0;
        $this->updateFileMiragte($type, [
            'total_record' => intval($number_category)
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'luu_cau_hinh_mo_rong_thanh_cong')
        ]);
    }

    public function loadListCategory($type = null, $lang = null)
    {
        if (empty($type) || empty($lang)) return [];

        $params[FILTER][TYPE] = $type;
        $params[FILTER][LANG] = $lang;
        $params['get_empty_name'] = true;

        $categories = TableRegistry::get('Categories')->queryListCategories($params)->all()->nest('id', 'parent_id')->toArray();
        
        if(!empty($categories)){
            $categories = TableRegistry::get('Categories')->parseDataCategories($categories, 0);
        }
        
        $result = !empty($categories) ? array_values($categories) : [];

        return $result;
    }

    public function loadListAttributes($type = null, $lang = null)
    {
        if (empty($type) || empty($lang)) return [];

        $params[FILTER]['attribute_type'] = $type;
        $params[FILTER][LANG] = $lang;

        // Danh sách thuộc tính mở rộng của sản phẩm
        $table = TableRegistry::get('Attributes');
        $attributes = $table->queryListAttributes($params)->toArray();

        return !empty($attributes) ? $attributes : [];
    }

    public function configDataExport($data = [])
    {
        if (empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        // validate dữ liệu cấu hình
        $validate = $this->validationData($data);
        if (!empty($validate) && !$validate['check']) {
            $message = !empty($validate['message']) ? $validate['message'] : __d('admin', 'du_lieu_khong_hop_le');

            return $this->System->getResponse([MESSAGE => $message]);
        }

        $articles = 0;
        if (!empty($data['articles']) && !empty($data['articles']['check']) && !empty($data['articles']['record'])) {
            $articles = $data['articles']['record'];
        }

        $products = 0;
        if (!empty($data['products']) && !empty($data['products']['check']) && !empty($data['products']['record'])) {
            $products = $data['products']['record'];
        }

        $tag_article = 0;
        if (!empty($data['tags']) && !empty($data['tags']['article_check']) && !empty($data['tags']['article_record'])) {
            $tag_article = $data['tags']['article_record'];
        }

        $tag_product = 0;
        if (!empty($data['tags']) && !empty($data['tags']['product_check']) && !empty($data['tags']['product_record'])) {
            $tag_product = $data['tags']['product_record'];
        }

        $brands = 0;
        if (!empty($data['brands']) && !empty($data['brands']['check']) && !empty($data['brands']['record'])) {
            $brands = $data['brands']['record'];
        }

        $migrate_info = $this->readMigrateDataExportInfo();
        $config_data = !empty($migrate_info['initialization']['config_data'][DATA]) ? $migrate_info['initialization']['config_data'][DATA] : [];

        $config_data['articles'] = intval($articles);
        $config_data['products'] = intval($products);
        $config_data['tag_article'] = intval($tag_article);
        $config_data['tag_product'] = intval($tag_product);
        $config_data['brands'] = intval($brands);
        $config_data['languages'] = !empty($data['languages']) ? $data['languages'] : null;

        $this->updateFileMiragte('initialization', [
            'config_data' => [
                STATUS => SUCCESS,
                MESSAGE => __d('admin', 'doc_thong_tin_du_lieu_thanh_cong'),
                DATA => $config_data
            ]
        ]);

        $this->updateTotalRecordExport();

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'cau_hinh_du_lieu_thanh_cong')
        ]);
    }

    public function configIdExport($data = [])
    {
        if (empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $category_id_start = !empty($data['category_id_start']) ? $data['category_id_start'] : null;
        $article_id_start = !empty($data['article_id_start']) ? $data['article_id_start'] : null;
        $product_id_start = !empty($data['product_id_start']) ? $data['product_id_start'] : null;
        $brand_id_start = !empty($data['brand_id_start']) ? $data['brand_id_start'] : null;
        $tag_id_start = !empty($data['tag_id_start']) ? $data['tag_id_start'] : null;
        $attribute_id_start = !empty($data['attribute_id_start']) ? $data['attribute_id_start'] : null;
        $customer_id_start = !empty($data['customer_id_start']) ? $data['customer_id_start'] : null;

        if(empty($category_id_start) || empty($article_id_start) || empty($product_id_start) || empty($brand_id_start) || empty($tag_id_start) || empty($attribute_id_start)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $this->updateFileMiragte('initialization', [
            'config_id' => [
                STATUS => SUCCESS,
                MESSAGE => __d('admin', 'cau_hinh_id_du_lieu_thanh_cong'),
                DATA => [
                    'category_id_start' => intval($category_id_start),
                    'article_id_start' => intval($article_id_start),
                    'product_id_start' => intval($product_id_start),
                    'brand_id_start' => intval($brand_id_start),
                    'tag_id_start' => intval($tag_id_start),
                    'attribute_id_start' => intval($attribute_id_start)
                ]
            ]
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'cau_hinh_id_du_lieu_thanh_cong')
        ]);
    }

    public function configCdnExport($data = [])
    {
        if (empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $url_cdn = !empty($data['url_cdn']) ? $data['url_cdn'] : null;
        $url_cdn_new = !empty($data['url_cdn_new']) ? $data['url_cdn_new'] : null;
        
        if(empty($url_cdn) || empty($url_cdn_new)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $this->updateFileMiragte('initialization', [
            'config_cdn' => [
                STATUS => SUCCESS,
                MESSAGE => __d('admin', 'cau_hinh_id_du_lieu_thanh_cong'),
                DATA => [
                    'url_cdn' => $url_cdn,
                    'url_cdn_new' => $url_cdn_new
                ]
            ]
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'cau_hinh_id_du_lieu_thanh_cong')
        ]);
    }

    public function validationData($data = [])
    {
        // đọc thông tin dữ liệu để check xem số dữ liệu cần export có vượt quá số dữ liệu hiện tại
        $migrate_info = $this->readMigrateDataExportInfo();
        $read_database = !empty($migrate_info['initialization']['read_database'][DATA]) ? $migrate_info['initialization']['read_database'][DATA] : [];
        $number_article = !empty($read_database['number_article']) ? intval($read_database['number_article']) : 0;
        $number_tag_article = !empty($read_database['number_tag_article']) ? intval($read_database['number_tag_article']) : 0;
        $number_product = !empty($read_database['number_product']) ? intval($read_database['number_product']) : 0;
        $number_brand = !empty($read_database['number_brand']) ? intval($read_database['number_brand']) : 0;
        $number_tag_product = !empty($read_database['number_tag_product']) ? intval($read_database['number_tag_product']) : 0;

        $result = [
            'check' => false,
            'message' => ''
        ];

        // validate
        if (!empty($data['articles']) && !empty($data['articles']['check']) && !empty($data['articles']['record']) && $data['articles']['record'] > $number_article) {

            $result['message'] = __d('admin', 'so_ban_ghi_bai_viet_vuot_qua_du_lieu_hien_tai');
            return $result;
        }

        if (!empty($data['products']) && !empty($data['products']['check']) && !empty($data['products']['record']) && $data['products']['record'] > $number_product) {

            $result['message'] = __d('admin', 'so_ban_ghi_san_pham_vuot_qua_du_lieu_hien_tai');
            return $result;
        }

        if (!empty($data['tags']) && !empty($data['tags']['article_check']) && !empty($data['tags']['article_record']) && $data['tags']['article_record'] > $number_tag_article) {

            $result['message'] = __d('admin', 'so_ban_ghi_tag_bai_viet_vuot_qua_du_lieu_hien_tai');
            return $result;
        }

        if (!empty($data['tags']) && !empty($data['tags']['product_check']) && !empty($data['tags']['product_record']) && $data['tags']['product_record'] > $number_tag_product) {

            $result['message'] = __d('admin', 'so_ban_ghi_tag_san_pham_vuot_qua_du_lieu_hien_tai');
            return $result;
        }

        if (!empty($data['brands']) && !empty($data['brands']['check']) && !empty($data['brands']['record']) && $data['brands']['record'] > $number_brand) {

            $result['message'] = __d('admin', 'so_ban_ghi_thuong_hieu_vuot_qua_du_lieu_hien_tai');
            return $result;
        }

        $result['check'] = true;
        return $result;
    }

    public function updateTotalRecordExport()
    {
        $migrate_info = $this->readMigrateDataExportInfo();
        $config_data = !empty($migrate_info['initialization']['config_data'][DATA]) ? $migrate_info['initialization']['config_data'][DATA] : [];

        $articles = !empty($config_data['articles']) ? intval($config_data['articles']) : 0;
        $products = !empty($config_data['products']) ? intval($config_data['products']) : 0;
        $tag_article = !empty($config_data['tag_article']) ? intval($config_data['tag_article']) : 0;
        $tag_product = !empty($config_data['tag_product']) ? intval($config_data['tag_product']) : 0;
        $brands = !empty($config_data['brands']) ? intval($config_data['brands']) : 0;
        $attribute_article = !empty($config_data['attributes_article']) ? json_decode($config_data['attributes_article'], true) : [];
        $attribute_product = !empty($config_data['attributes_product']) ? json_decode($config_data['attributes_product'], true) : [];
        $attributes_product_item = !empty($config_data['attributes_product_item']) ? json_decode($config_data['attributes_product_item'], true) : [];

        $number_tags = intval($tag_article) + intval($tag_product);
        $number_attribute = count($attribute_article) + count($attribute_product) + count($attributes_product_item);

        $this->updateFileMiragte('brands', [
            'total_record' => intval($brands)
        ]);

        $this->updateFileMiragte('articles', [
            'total_record' => intval($articles)
        ]);

        $this->updateFileMiragte('products', [
            'total_record' => intval($products)
        ]);

        $this->updateFileMiragte('tags', [
            'total_record' => intval($number_tags)
        ]);

        $this->updateFileMiragte('attributes', [
            'total_record' => intval($number_attribute)
        ]);
    }

    public function migrateData($type = null)
    {
        if(empty($type) || !in_array($type, ['categories_article', 'categories_product', 'articles', 'brands', 'products', 'attributes', 'tags'])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $migrate_info = $this->readMigrateDataExportInfo();
        if(empty($migrate_info['initialization']['done'])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'chua_hoan_thanh_buoc_khoi_tao')]);
        }

        switch($type){
            case 'categories_article';
            case 'categories_product';
                $result = $this->migrateCategories($type);
            break;

            case 'articles';
                $result = $this->migrateArticles();
            break;

            case 'brands';
                $result = $this->migrateBrands();
            break;

            case 'products';
                $result = $this->migrateProducts();
            break;

            case 'attributes';
                $result = $this->migrateAttributes();
            break;

            case 'tags';
                $result = $this->migrateTags();
            break;
        }

        return $result;
    }

    private function migrateCategories($type = null)
    {
        if(empty($type)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        // đọc thông tin cấu hình migrate
        $migrate_info = $this->readMigrateDataExportInfo();
        $config_id = !empty($migrate_info['initialization']['config_id'][DATA]) ? $migrate_info['initialization']['config_id'][DATA] : [];
        $config_data = !empty($migrate_info['initialization']['config_data'][DATA]) ? $migrate_info['initialization']['config_data'][DATA] : [];
        $config_cdn = !empty($migrate_info['initialization']['config_cdn'][DATA]) ? $migrate_info['initialization']['config_cdn'][DATA] : [];
        
        $languages = !empty($config_data['languages']) ? explode('-', $config_data['languages']) : [];
        $url_cdn = !empty($config_cdn['url_cdn']) ? $config_cdn['url_cdn'] : null;
        $url_cdn_new = !empty($config_cdn['url_cdn_new']) ? $config_cdn['url_cdn_new'] : null;

        $categories_migrated = $this->readDataSyned('categories');
        $id_start = !empty($config_id['category_id_start']) ? intval($config_id['category_id_start']) : 0;
        $last_id = !empty($categories_migrated) ? intval(end($categories_migrated)['id']) : $id_start;

        // danh sách id danh mục được cấu hình migrate
        $category_ids = !empty($config_data[$type]) ? json_decode($config_data[$type], true) : [];

        $migrate_status = $migrate_info[$type]['done'] ? true : false;
        if($migrate_status){
            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'chuyen_doi_du_lieu_thanh_cong')
            ]);
        }

        // validate languages
        if (empty($languages)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_cau_hinh_ngon_ngu')]);
        }

        // lấy danh sách thông tin danh mục
        $where = ['Categories.deleted' => 0];  

        if(!empty($category_ids)){
            $where['Categories.id IN'] = $category_ids;
        }

        $type_link = str_replace('categories_', '', $type);
        $type_link = 'category_' . $type_link;

        $contain = [
            'ContentMutiple', 
            'LinksMutiple' => function ($q) use ($type_link) {
                return $q->where([
                    'LinksMutiple.type' => $type_link,
                    'LinksMutiple.deleted' => 0
                ]);
            }
        ];

        $categories = TableRegistry::get('Categories')->find()->contain($contain)->where($where)->select()->group('Categories.id')->order('Categories.id')->toArray();

        $data_category = $data_content = $data_link = [];
        foreach($categories as $k => $category) {
            $last_id ++;
            $category_id = $last_id;
            $old_id = !empty($category['id']) ? $category['id'] : null;

            // ----- data category
            $item_category = [
                'id' => $category_id,
                'type' => !empty($category['type']) ? $category['type'] : null,
                'parent_id' => !empty($category['parent_id']) ? intval($category['parent_id']) : null,
                'path_id' => !empty($category['path_id']) ? $category['path_id'] : null,
                'image_avatar' => null,
                'images' => null,
                'url_video' => !empty($category['url_video']) ? $category['url_video'] : null,
                'type_video' => !empty($category['type_video']) ? $category['type_video'] : null,
                'created_by' => !empty($category['created_by']) ? intval($category['created_by']) : null,
                'created' => !empty($category['created']) ? intval($category['created']) : null,
                'updated' => !empty($category['updated']) ? intval($category['updated']) : null,
                'position' => !empty($category['position']) ? intval($category['position']) : null,
                'status' => !empty($category['status']) ? intval($category['status']) : null,
                'deleted' => !empty($category['deleted']) ? 1 : 0
            ];

            // download ảnh đại diện
            if (!empty($category['image_avatar'])) {
                $item_category['image_avatar'] = $this->downloadImage($category['image_avatar'], 'categories');
            }

            // download album ảnh
            $images = !empty($category['images']) ? json_decode($category['images'], true) : [];
            if (!empty($images)) {
                $list_images = [];
                foreach ($images as $key => $image) {
                    $list_images[] = $this->downloadImage($image, 'categories');
                }

                $item_category['images'] = !empty($list_images) ? json_encode($list_images) : null;
            }

            $category_content = !empty($category['ContentMutiple']) ? Hash::combine($category['ContentMutiple'], '{n}.lang', '{n}') : [];
            $category_link = !empty($category['LinksMutiple']) ? Hash::combine($category['LinksMutiple'], '{n}.lang', '{n}') : [];


            foreach ($languages as $key => $lang) {
                if (empty($lang)) continue;

                $item_content = !empty($category_content[$lang]) ? $category_content[$lang] : [];
                $item_link = !empty($category_link[$lang]) ? $category_link[$lang] : [];

                // xóa bỏ giá trị id cũ đi
                unset($item_content['id']);
                unset($item_link['id']);

                // update giá trị category_id mới cho nội dung là đường dẫn
                $item_content['category_id'] = $category_id;
                $item_link['foreign_id'] = $category_id;

                // chuyển đổi ảnh trong nội dung bài viết
                $content = !empty($item_content['content']) ? $item_content['content'] : null;
                $content = $this->migrateImageInContent('categories', $url_cdn, $url_cdn_new, $content);
                $item_content['content'] = $content;

                $data_content[] = $item_content;
                $data_link[] = $item_link;
            }

            $data_category[$old_id] = $item_category;
        }

        // -------------- lưu thông tin vào file data json

        // categories.json
        $categories_migrated += $data_category;
        $this->writeDataSyned('categories', $categories_migrated);

        // categories_content.json
        $content_migrated = $this->readDataSyned('categories_content');
        $content_migrated = array_merge($content_migrated, $data_content);
        $this->writeDataSyned('categories_content', $content_migrated);


        // links.json
        $links_migrated = $this->readDataSyned('links');
        $links_migrated = array_merge($links_migrated, $data_link);
        $this->writeDataSyned('links', $links_migrated);

        // cập nhật lại số danh mục đã migrate
        $this->updateFileMiragte($type, [
            'migrated' => count($data_category),
            'done' => true
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'chuyen_doi_du_lieu_thanh_cong')
        ]);
    }

    public function migrateArticles()
    {
        // đọc thông tin cấu hình migrate
        $migrate_info = $this->readMigrateDataExportInfo();
        $config_id = !empty($migrate_info['initialization']['config_id'][DATA]) ? $migrate_info['initialization']['config_id'][DATA] : [];
        $config_data = !empty($migrate_info['initialization']['config_data'][DATA]) ? $migrate_info['initialization']['config_data'][DATA] : [];
        $config_cdn = !empty($migrate_info['initialization']['config_cdn'][DATA]) ? $migrate_info['initialization']['config_cdn'][DATA] : [];
        
        $languages = !empty($config_data['languages']) ? explode('-', $config_data['languages']) : [];
        $url_cdn = !empty($config_cdn['url_cdn']) ? $config_cdn['url_cdn'] : null;
        $url_cdn_new = !empty($config_cdn['url_cdn_new']) ? $config_cdn['url_cdn_new'] : null;

        $id_start = !empty($config_id['article_id_start']) ? intval($config_id['article_id_start']) : 0;

        $migrate_status = $migrate_info['articles']['done'] ? true : false;
        if($migrate_status){
            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'chuyen_doi_du_lieu_thanh_cong')
            ]);
        }

        // validate languages
        if (empty($languages)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_cau_hinh_ngon_ngu')]);
        }

        // lấy danh sách thông tin bài viết
        $where['Articles.deleted'] = 0;
        $contain = ['CategoriesArticle', 'ContentMutiple', 'LinksMutiple'];

        $articles = TableRegistry::get('Articles')->find()->contain($contain)->where($where)->select()->group('Articles.id')->order('Articles.id')->toArray();

        // format dữ liệu
        $data_article = $data_content = $data_link = $data_article_category = [];
        $categories_migrated = $this->readDataSyned('categories');
        foreach($articles as $k => $article) {
            $id_start ++;
            $new_id = $id_start;
            $old_id = !empty($article['id']) ? $article['id'] : null;

            // ----- data article
            $article_content = !empty($article['ContentMutiple']) ? Hash::combine($article['ContentMutiple'], '{n}.lang', '{n}') : [];
            $article_link = !empty($article['LinksMutiple']) ? Hash::combine($article['LinksMutiple'], '{n}.lang', '{n}') : [];
            $article_categories = !empty($article['CategoriesArticle']) ? $article['CategoriesArticle'] : [];

            // xóa dữ liệu content | link | category
            unset($article['ContentMutiple']);
            unset($article['LinksMutiple']);
            unset($article['CategoriesArticle']);

            $item_article = $article; 

            // cập nhật id mới cho bài viết
            $item_article['id'] = $new_id;

            // download ảnh đại diện
            if (!empty($item_article['image_avatar'])) {
                $item_article['image_avatar'] = $this->downloadImage($item_article['image_avatar'], 'articles');
            }

            // download album ảnh
            $images = !empty($item_article['images']) ? json_decode($item_article['images'], true) : [];
            if (!empty($images)) {
                $list_images = [];
                foreach ($images as $key => $image) {
                    $list_images[] = $this->downloadImage($image, 'articles');
                }

                $item_article['images'] = !empty($list_images) ? json_encode($list_images) : null;
            }

            // format dữ liệu content và link
            foreach ($languages as $key => $lang) {
                if (empty($lang)) continue;

                $item_content = !empty($article_content[$lang]) ? $article_content[$lang] : [];
                $item_link = !empty($article_link[$lang]) ? $article_link[$lang] : [];

                // xóa bỏ giá trị id cũ đi
                unset($item_content['id']);
                unset($item_link['id']);

                // update giá trị category_id mới cho nội dung là đường dẫn
                $item_content['article_id'] = $new_id;
                $item_link['foreign_id'] = $new_id;

                // chuyển đổi ảnh trong nội dung bài viết
                $content = !empty($item_content['content']) ? $item_content['content'] : null;
                $content = $this->migrateImageInContent('articles', $url_cdn, $url_cdn_new, $content);
                $item_content['content'] = $content;

                $data_content[] = $item_content;
                $data_link[] = $item_link;
            }

            // format lại dữ liệu danh mục bài viết
            foreach ($article_categories as $k_category => $category) {
                $category_old_id = !empty($category['category_id']) ? intval($category['category_id']) : null;
                $category_new_id = !empty($categories_migrated[$category_old_id]) && !empty($categories_migrated[$category_old_id]['id']) ? intval($categories_migrated[$category_old_id]['id']) : null;

                $data_article_category[] = [
                    'article_id' => $new_id,
                    'category_id' => $category_new_id,
                ];
            }

            $data_article[$old_id] = $item_article;
        }

        // -------------- lưu thông tin vào file data json

        // articles.json
        $this->writeDataSyned('articles', $data_article);

        // categories_article.json
        $this->writeDataSyned('categories_article', $data_article_category);

        // categories_content.json
        $this->writeDataSyned('articles_content', $data_content);
        
        // links.json
        $links_migrated = $this->readDataSyned('links');
        $links_migrated = array_merge($links_migrated, $data_link);
        $this->writeDataSyned('links', $links_migrated);

        // cập nhật lại số bài viết đã migrate
        $this->updateFileMiragte('articles', [
            'migrated' => count($data_article),
            'done' => true
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'chuyen_doi_bai_viet_thanh_cong'),
            DATA => [
                'continue' => true,
                'migrated' => count($data_article)
            ]
        ]);
    }

    public function migrateBrands()
    {
        // đọc thông tin cấu hình migrate
        $migrate_info = $this->readMigrateDataExportInfo();
        $config_id = !empty($migrate_info['initialization']['config_id'][DATA]) ? $migrate_info['initialization']['config_id'][DATA] : [];
        $config_data = !empty($migrate_info['initialization']['config_data'][DATA]) ? $migrate_info['initialization']['config_data'][DATA] : [];
        $config_cdn = !empty($migrate_info['initialization']['config_cdn'][DATA]) ? $migrate_info['initialization']['config_cdn'][DATA] : [];
        
        $languages = !empty($config_data['languages']) ? explode('-', $config_data['languages']) : [];
        $url_cdn = !empty($config_cdn['url_cdn']) ? $config_cdn['url_cdn'] : null;
        $url_cdn_new = !empty($config_cdn['url_cdn_new']) ? $config_cdn['url_cdn_new'] : null;

        $id_start = !empty($config_id['brand_id_start']) ? intval($config_id['brand_id_start']) : 0;

        $migrate_status = $migrate_info['brands']['done'] ? true : false;
        if($migrate_status){
            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'chuyen_doi_du_lieu_thanh_cong')
            ]);
        }

        // validate languages
        if (empty($languages)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_cau_hinh_ngon_ngu')]);
        }

        // lấy danh sách thông tin thương hiệu
        $brands = TableRegistry::get('Brands')->find()->contain(['ContentMutiple', 'LinksMutiple'])->where(['Brands.deleted' => 0])->select()->group('Brands.id')->order('Brands.id')->toArray();

        // format dữ liệu
        $data_brand = $data_content = $data_link = [];
        foreach($brands as $k => $brand) {
            $id_start ++;
            $new_id = $id_start;
            $old_id = !empty($brand['id']) ? $brand['id'] : null;

            // ----- data brand
            $brand_content = !empty($brand['ContentMutiple']) ? Hash::combine($brand['ContentMutiple'], '{n}.lang', '{n}') : [];
            $brand_link = !empty($brand['LinksMutiple']) ? Hash::combine($brand['LinksMutiple'], '{n}.lang', '{n}') : [];

            // xóa dữ liệu content | link
            unset($brand['ContentMutiple']);
            unset($brand['LinksMutiple']);

            $item_brand = $brand; 

            // cập nhật id mới cho thương hiệu
            $item_brand['id'] = $new_id;

            // download ảnh đại diện
            if (!empty($item_brand['image_avatar'])) {
                $item_brand['image_avatar'] = $this->downloadImage($item_brand['image_avatar'], 'articles');
            }

            // download album ảnh
            $images = !empty($item_brand['images']) ? json_decode($item_brand['images'], true) : [];
            if (!empty($images)) {
                $list_images = [];
                foreach ($images as $key => $image) {
                    $list_images[] = $this->downloadImage($image, 'brands');
                }

                $item_brand['images'] = !empty($list_images) ? json_encode($list_images) : null;
            }

            // format dữ liệu content và link
            foreach ($languages as $key => $lang) {
                if (empty($lang)) continue;

                $item_content = !empty($brand_content[$lang]) ? $brand_content[$lang] : [];
                $item_link = !empty($brand_link[$lang]) ? $brand_link[$lang] : [];

                // xóa bỏ giá trị id cũ đi
                unset($item_content['id']);
                unset($item_link['id']);

                // update giá trị category_id mới cho nội dung là đường dẫn
                $item_content['brand_id'] = $new_id;
                $item_link['foreign_id'] = $new_id;

                // chuyển đổi ảnh trong nội dung bài viết
                $content = !empty($item_content['content']) ? $item_content['content'] : null;
                $content = $this->migrateImageInContent('brands', $url_cdn, $url_cdn_new, $content);
                $item_content['content'] = $content;

                $data_content[] = $item_content;
                $data_link[] = $item_link;
            }

            $data_brand[$old_id] = $item_brand;
        }

        // -------------- lưu thông tin vào file data json

        // brands.json
        $this->writeDataSyned('brands', $data_brand);

        // brands_content.json
        $this->writeDataSyned('brands_content', $data_content);
        
        // links.json
        $links_migrated = $this->readDataSyned('links');
        $links_migrated = array_merge($links_migrated, $data_link);
        $this->writeDataSyned('links', $links_migrated);

        // cập nhật lại số thương hiệu đã migrate
        $this->updateFileMiragte('brands', [
            'migrated' => count($data_brand),
            'done' => true
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'chuyen_doi_thuong_hieu_thanh_cong'),
            DATA => [
                'continue' => true,
                'migrated' => count($data_brand)
            ]
        ]);
    }

    public function migrateProducts()
    {
        // đọc thông tin cấu hình migrate
        $migrate_info = $this->readMigrateDataExportInfo();
        $config_id = !empty($migrate_info['initialization']['config_id'][DATA]) ? $migrate_info['initialization']['config_id'][DATA] : [];
        $config_data = !empty($migrate_info['initialization']['config_data'][DATA]) ? $migrate_info['initialization']['config_data'][DATA] : [];
        $config_cdn = !empty($migrate_info['initialization']['config_cdn'][DATA]) ? $migrate_info['initialization']['config_cdn'][DATA] : [];
        
        $languages = !empty($config_data['languages']) ? explode('-', $config_data['languages']) : [];
        $url_cdn = !empty($config_cdn['url_cdn']) ? $config_cdn['url_cdn'] : null;
        $url_cdn_new = !empty($config_cdn['url_cdn_new']) ? $config_cdn['url_cdn_new'] : null;

        $id_start = !empty($config_id['product_id_start']) ? intval($config_id['product_id_start']) : 0;

        $migrate_status = $migrate_info['products']['done'] ? true : false;
        if($migrate_status){
            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'chuyen_doi_du_lieu_thanh_cong')
            ]);
        }

        // validate languages
        if (empty($languages)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_cau_hinh_ngon_ngu')]);
        }

        // lấy danh sách thông tin bài viết
        $where['Products.deleted'] = 0;
        $contain = ['CategoriesProduct', 'ProductsItem', 'ContentMutiple', 'LinksMutiple'];

        $products = TableRegistry::get('Products')->find()->contain($contain)->where($where)->select()->group('Products.id')->order('Products.id')->toArray();

        // format dữ liệu
        $data_product = $data_product_item = $data_content = $data_link = $data_product_category = [];
        $categories_migrated = $this->readDataSyned('categories');
        $brands_migrated = $this->readDataSyned('brands');
        foreach($products as $k => $product) {
            $id_start ++;
            $new_id = $id_start;
            $old_id = !empty($product['id']) ? $product['id'] : null;

            // ----- data product
            $product_items = !empty($product['ProductsItem']) ? $product['ProductsItem'] : [];
            $product_content = !empty($product['ContentMutiple']) ? Hash::combine($product['ContentMutiple'], '{n}.lang', '{n}') : [];
            $product_link = !empty($product['LinksMutiple']) ? Hash::combine($product['LinksMutiple'], '{n}.lang', '{n}') : [];
            $product_categories = !empty($product['CategoriesProduct']) ? $product['CategoriesProduct'] : [];

            // xóa dữ liệu content | link | category
            unset($product['ProductsItem']);
            unset($product['ContentMutiple']);
            unset($product['LinksMutiple']);
            unset($product['CategoriesProduct']);

            $item_product = $product; 

            // cập nhật id mới cho sản phẩm
            $item_product['id'] = $new_id;

            // cập nhật main_category_id mới cho sản phẩm
            $main_category_old_id = !empty($product['main_category_id']) ? intval($product['main_category_id']) : null;
            $main_category_new_id = !empty($categories_migrated[$main_category_old_id]) && !empty($categories_migrated[$main_category_old_id]['id']) ? intval($categories_migrated[$main_category_old_id]['id']) : null;

            $item_product['main_category_id'] = $main_category_new_id;

            // cập nhật brand_id mới cho sản phẩm
            $brand_old_id = !empty($product['brand_id']) ? intval($product['brand_id']) : null;
            $brand_new_id = !empty($brands_migrated[$brand_old_id]) && !empty($brands_migrated[$brand_old_id]['id']) ? intval($brands_migrated[$brand_old_id]['id']) : null;

            $item_product['brand_id'] = $brand_new_id;

            // format dữ liệu phiên bản sản phẩm
            foreach ($product_items as $key => $items) {
                unset($items['id']);
                $item_product_items = $items;

                // update giá trị product_id mới cho phiên bản sản phẩm
                $item_product_items['product_id'] = $new_id;

                // download ảnh phiên bản
                $images = !empty($items['images']) ? json_decode($items['images'], true) : [];
                if (!empty($images)) {
                    $list_images = [];
                    foreach ($images as $key => $image) {
                        $list_images[] = $this->downloadImage($image, 'products');
                    }

                    $item_product_items['images'] = !empty($list_images) ? json_encode($list_images) : null;
                }

                $item_product_items['deleted'] = $item_product_items['deleted'] ? 1 : 0;

                $data_product_item[] = $item_product_items;
            }

            // format dữ liệu content và link
            foreach ($languages as $key => $lang) {
                if (empty($lang)) continue;

                $item_content = !empty($product_content[$lang]) ? $product_content[$lang] : [];
                $item_link = !empty($product_link[$lang]) ? $product_link[$lang] : [];

                // xóa bỏ giá trị id cũ đi
                unset($item_content['id']);
                unset($item_link['id']);

                // update giá trị category_id mới cho nội dung là đường dẫn
                $item_content['product_id'] = $new_id;
                $item_link['foreign_id'] = $new_id;

                // chuyển đổi ảnh trong nội dung bài viết
                $content = !empty($item_content['content']) ? $item_content['content'] : null;
                $content = $this->migrateImageInContent('products', $url_cdn, $url_cdn_new, $content);
                $item_content['content'] = $content;

                $data_content[] = $item_content;
                $data_link[] = $item_link;
            }

            // format lại dữ liệu danh mục sản phẩm
            foreach ($product_categories as $k_category => $category) {
                $category_old_id = !empty($category['category_id']) ? intval($category['category_id']) : null;
                $category_new_id = !empty($categories_migrated[$category_old_id]) && !empty($categories_migrated[$category_old_id]['id']) ? intval($categories_migrated[$category_old_id]['id']) : null;

                $data_product_category[] = [
                    'product_id' => $new_id,
                    'category_id' => $category_new_id,
                ];
            }

            $data_product[$old_id] = $item_product;
        }

        // -------------- lưu thông tin vào file data json

        // products.json
        $this->writeDataSyned('products', $data_product);

        // products_item.json
        $this->writeDataSyned('products_item', $data_product_item);

        // categories_product.json
        $this->writeDataSyned('categories_product', $data_product_category);

        // products_content.json
        $this->writeDataSyned('products_content', $data_content);
        
        // links.json
        $links_migrated = $this->readDataSyned('links');
        $links_migrated = array_merge($links_migrated, $data_link);
        $this->writeDataSyned('links', $links_migrated);

        // cập nhật lại số bài viết đã migrate
        $this->updateFileMiragte('products', [
            'migrated' => count($data_product),
            'done' => true
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'chuyen_doi_san_pham_thanh_cong'),
            DATA => [
                'continue' => true,
                'migrated' => count($data_product)
            ]
        ]);
    }

    public function migrateAttributes()
    {
        // đọc thông tin cấu hình migrate
        $migrate_info = $this->readMigrateDataExportInfo();
        $config_id = !empty($migrate_info['initialization']['config_id'][DATA]) ? $migrate_info['initialization']['config_id'][DATA] : [];
        $config_data = !empty($migrate_info['initialization']['config_data'][DATA]) ? $migrate_info['initialization']['config_data'][DATA] : [];
        $config_cdn = !empty($migrate_info['initialization']['config_cdn'][DATA]) ? $migrate_info['initialization']['config_cdn'][DATA] : [];
        
        $languages = !empty($config_data['languages']) ? explode('-', $config_data['languages']) : [];
        $url_cdn = !empty($config_cdn['url_cdn']) ? $config_cdn['url_cdn'] : null;
        $url_cdn_new = !empty($config_cdn['url_cdn_new']) ? $config_cdn['url_cdn_new'] : null;

        // đọc thông tin id thuộc tính được cấu hình export
        $ids_attribute_article = !empty($config_data['attributes_article']) ? json_decode($config_data['attributes_article'], true) : [];
        $ids_attribute_product = !empty($config_data['attributes_product']) ? json_decode($config_data['attributes_product'], true) : [];
        $ids_attribute_product_item = !empty($config_data['attributes_product_item']) ? json_decode($config_data['attributes_product_item'], true) : [];

        $ids_attribute = array_merge($ids_attribute_article, $ids_attribute_product, $ids_attribute_product_item); 

        $id_start = $id_option_start = !empty($config_id['attribute_id_start']) ? intval($config_id['attribute_id_start']) : 0;
        $migrate_status = $migrate_info['attributes']['done'] ? true : false;
        if($migrate_status){
            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'chuyen_doi_du_lieu_thanh_cong')
            ]);
        }

        // validate languages
        if (empty($languages)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_cau_hinh_ngon_ngu')]);
        }

        // lấy danh sách thông tin thuộc tính
        $where = ['Attributes.deleted' => 0];  

        if(!empty($ids_attribute)){
            $where['Attributes.id IN'] = $ids_attribute;
        }

        $attributes = TableRegistry::get('Attributes')->find()->contain(['ContentMutiple'])->where($where)->select()->group('Attributes.id')->order('Attributes.id')->toArray();

        // format dữ liệu thông tin thuộc tính
        foreach($attributes as $k => $attribute) {
            $id_start ++;
            $new_id = $id_start;
            $old_id = !empty($attribute['id']) ? $attribute['id'] : null;

            // ----- data attributes_content
            $attribute_content = !empty($attribute['ContentMutiple']) ? Hash::combine($attribute['ContentMutiple'], '{n}.lang', '{n}') : [];

            // xóa dữ liệu content
            unset($attribute['ContentMutiple']);

            $item_attribute = $attribute; 

            // cập nhật id mới cho thương hiệu
            $item_attribute['id'] = $new_id;

            // format dữ liệu content
            foreach ($languages as $key => $lang) {
                if (empty($lang)) continue;

                $item_content = !empty($attribute_content[$lang]) ? $attribute_content[$lang] : [];

                // xóa bỏ giá trị id cũ đi
                unset($item_content['id']);

                // update giá trị attribute_id mới cho nội dung
                $item_content['attribute_id'] = $new_id;

                $data_content[] = $item_content;
            }

            $data_attribute[$old_id] = $item_attribute;   
        }

        // attributes.json
        $this->writeDataSyned('attributes', $data_attribute);
        // attributes_content.json
        $this->writeDataSyned('attributes_content', $data_content);

        // migrate thông tin options
        $data_option = $data_option_content = [];
        foreach ($data_attribute as $attribute_old_id => $attribute) {
            $attribute_new_id = !empty($attribute['id']) ? intval($attribute['id']) : null;

            if (empty($attribute_old_id) || empty($attribute_new_id)) continue;

            // lấy thông tin tùy chọn của thuộc tính
            $options = TableRegistry::get('AttributesOptions')->find()->contain(['ContentMutiple'])->where([
                'AttributesOptions.attribute_id' => $attribute_old_id,
                'AttributesOptions.deleted' => 0
            ])->select()->group('AttributesOptions.id')->order('AttributesOptions.id')->toArray();

            if (empty($options)) continue;
            
            foreach ($options as $k_option => $option) {
                $id_option_start ++;
                $new_option_id = $id_option_start;
                $old_option_id = !empty($option['id']) ? $option['id'] : null;

                // ----- data option_content
                $option_content = !empty($option['ContentMutiple']) ? Hash::combine($option['ContentMutiple'], '{n}.lang', '{n}') : [];

                // xóa dữ liệu content
                unset($option['ContentMutiple']);

                $item_option = $option; 

                // cập nhật id mới cho tùy chọn
                $item_option['id'] = $new_option_id;
                $item_option['attribute_id'] = $attribute_new_id;

                // format dữ liệu content
                foreach ($languages as $key => $lang) {
                    if (empty($lang)) continue;

                    $item_option_content = !empty($option_content[$lang]) ? $option_content[$lang] : [];

                    // xóa bỏ giá trị id cũ đi
                    unset($item_option_content['id']);

                    // update giá trị attribute_option_id mới cho nội dung
                    $item_option_content['attribute_option_id'] = $new_option_id;

                    $data_option_content[] = $item_option_content;
                }

                $data_option[$old_option_id] = $item_option;
            }
        }

        // -------------- lưu thông tin vào file data json

        // attributes.json
        $this->writeDataSyned('attributes_options', $data_option);
        // attributes_content.json
        $this->writeDataSyned('attributes_options_content', $data_option_content);
        

        // cập nhật lại số thuộc tính đã migrate
        $this->updateFileMiragte('attributes', [
            'migrated' => count($data_attribute),
            'done' => true
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'chuyen_doi_thuoc_tinh_thanh_cong'),
            DATA => [
                'continue' => true,
                'migrated' => count($data_attribute)
            ]
        ]);
    }

    public function migrateTags()
    {
        // đọc thông tin cấu hình migrate
        $migrate_info = $this->readMigrateDataExportInfo();
        $config_id = !empty($migrate_info['initialization']['config_id'][DATA]) ? $migrate_info['initialization']['config_id'][DATA] : [];
        $config_data = !empty($migrate_info['initialization']['config_data'][DATA]) ? $migrate_info['initialization']['config_data'][DATA] : [];
        $config_cdn = !empty($migrate_info['initialization']['config_cdn'][DATA]) ? $migrate_info['initialization']['config_cdn'][DATA] : [];
        
        $languages = !empty($config_data['languages']) ? explode('-', $config_data['languages']) : [];
        $url_cdn = !empty($config_cdn['url_cdn']) ? $config_cdn['url_cdn'] : null;
        $url_cdn_new = !empty($config_cdn['url_cdn_new']) ? $config_cdn['url_cdn_new'] : null;

        $id_start = !empty($config_id['tag_id_start']) ? intval($config_id['tag_id_start']) : 0;
        $limit_tag_article = !empty($config_data['tag_article']) ? intval($config_data['tag_article']) : 0;
        $limit_tag_product = !empty($config_data['tag_product']) ? intval($config_data['tag_product']) : 0;

        $migrate_status = $migrate_info['tags']['done'] ? true : false;
        if($migrate_status){
            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'chuyen_doi_du_lieu_thanh_cong')
            ]);
        }

        // validate languages
        if (empty($languages)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_cau_hinh_ngon_ngu')]);
        }

        // lấy danh sách thông tin tags_article | tags_product
        $ids_tag = $ids_tag_article = $ids_tag_product = [];
        if ($limit_tag_article > 0) {

            $tags_article = TableRegistry::get('TagsRelation')->find()->limit($limit_tag_article)->where([
                'TagsRelation.type' => ARTICLE_DETAIL
            ])->select()->group('TagsRelation.id')->order('TagsRelation.tag_id')->toArray();

            $ids_tag_article = array_column($tags_article, 'tag_id');
        }

        if ($limit_tag_product > 0) {

            $tags_product = TableRegistry::get('TagsRelation')->find()->limit($limit_tag_product)->where([
                'TagsRelation.type' => PRODUCT_DETAIL
            ])->select()->group('TagsRelation.id')->order('TagsRelation.tag_id')->toArray();

            $ids_tag_product = array_column($tags_product, 'tag_id');
        }

        if (empty($ids_tag_article) && empty($ids_tag_product)) {
            $this->updateFileMiragte('tags', [
                'done' => true
            ]);

            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'chuyen_doi_du_lieu_thanh_cong')
            ]);
        }

        $ids_tag = array_unique(array_merge($ids_tag_article, $ids_tag_product));

        // lấy danh sách thông tin thẻ tags
        $where = [];  

        if(!empty($ids_tag)){
            $where['Tags.id IN'] = $ids_tag;
        }

        $tags = TableRegistry::get('Tags')->find()->contain(['TagsRelation'])->where($where)->select()->group('Tags.id')->order('Tags.id')->toArray();

        // format dữ liệu tags
        $data_tag = $data_tag_relation = [];
        foreach($tags as $k => $tag) {
            $id_start ++;
            $new_id = $id_start;
            $old_id = !empty($tag['id']) ? $tag['id'] : null;

            $tags_relation = !empty($tag['TagsRelation']) ? $tag['TagsRelation'] : [];
            unset($tag['TagsRelation']);

            // ----- data tag
            $item_tag = $tag; 

            // cập nhật id mới cho thẻ bài viết
            $item_tag['id'] = $new_id;

            // chuyển đổi ảnh trong nội dung thẻ bài viết
            $content = !empty($item_tag['content']) ? $item_tag['content'] : null;
            $content = $this->migrateImageInContent('tags', $url_cdn, $url_cdn_new, $content);
            $item_tag['content'] = $content;

            $data_tag[$old_id] = $item_tag;

            // format dữ liệu tags_relation
            if (empty($tags_relation)) continue;

            foreach ($tags_relation as $k_relation => $value) {
                if (empty($value)) continue;

                $item_tag_relation = $value;

                // xóa dữ liệu id cũ đi
                unset($item_tag_relation['id']);

                // cập nhật tag_id mới
                $item_tag_relation['tag_id'] = $new_id;

                $data_tag_relation[] = $item_tag_relation;
            }
        }

        // -------------- lưu thông tin vào file data json

        // tags.json
        $this->writeDataSyned('tags', $data_tag);

        // tags_relation.json
        $this->writeDataSyned('tags_relation', $data_tag_relation);

        // cập nhật lại số thương hiệu đã migrate
        $this->updateFileMiragte('tags', [
            'migrated' => count($data_tag),
            'done' => true
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'chuyen_doi_the_tag_thanh_cong'),
            DATA => [
                'continue' => true,
                'migrated' => count($data_tag)
            ]
        ]);
    }

    public function exportData()
    {
        // đọc thông tin cấu hình migrate
        $migrate_info = $this->readMigrateDataExportInfo();

        $migrate_status = $migrate_info['success']['done'] ? true : false;
        if($migrate_status){
            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'chuyen_doi_du_lieu_thanh_cong')
            ]);
        }

        foreach($this->list_tables_export as $table){
            $export = $this->exportDataSql($table);

            if(!$export){
                return $this->System->getResponse([MESSAGE => __d('admin', 'xuat_file_sql_khong_thanh_cong_{0}', [$table])]);
            }
        }

        $export = $this->exportDataSql('all');
        if(!$export){
            return $this->System->getResponse([MESSAGE => __d('admin', 'xuat_file_sql_khong_thanh_cong_all_sql')]);
        }

        // zip file data.zip
        $folder_data = new Folder(TMP . 'export/data', false);
        if(empty($folder_data->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thu_muc_chua_file_du_lieu')]);
        }

        $zip = new ZipArchive();
        $open_zip = $zip->open(TMP . 'export/data.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if($open_zip !== true){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tao_file_nen_data_zip_khong_thanh_cong')]);
        }
        
        $files = $folder_data->findRecursive();
        if(empty($files)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_co_file_du_lieu_trong_thu_muc_data')]);
        }

        foreach ($files as $key => $file) {
            if(!strpos($file, '.sql')) continue;
            $zip->addFile($file, str_replace(DS, '/', str_replace($folder_data->path . DS, '', $file)));
        }
        $zip->close();
        

        // zip file media.zip
        $folder_media = new Folder(TMP . 'export/media', false);
        if(empty($folder_media->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thu_muc_chua_media')]);
        }

        $zip = new ZipArchive();
        $open_zip = $zip->open(TMP . 'export/media.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if($open_zip !== true){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tao_file_nen_media_zip_khong_thanh_cong')]);
        }
        
        $files = $folder_media->findRecursive();
        foreach ($files as $key => $file) {
            $zip->addFile($file, str_replace(DS, '/', str_replace($folder_media->path . DS, '', $file)));
        }
        $zip->close();


        // zip file thumb.zip
        $folder_thumbs = new Folder(TMP . 'export/thumbs', false);
        if(empty($folder_thumbs->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thu_muc_chua_thumbs')]);
        }

        $zip = new ZipArchive();
        $open_zip = $zip->open(TMP . 'export/thumbs.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if($open_zip !== true){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tao_file_nen_thumbs_zip_khong_thanh_cong')]);
        }
        
        $files = $folder_thumbs->findRecursive();
        foreach ($files as $key => $file) {
            $zip->addFile($file, str_replace(DS, '/', str_replace($folder_thumbs->path . DS, '', $file)));
        }
        $zip->close();

        $this->updateFileMiragte('success', [
            'export' => true,
            'done' => true
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xuat_du_lieu_thanh_cong')
        ]);
    }

    public function downloadImage($url_media = null, $path = null)
    {
        // đọc thông tin cấu hình migrate
        $migrate_info = $this->readMigrateDataExportInfo();
        $config_cdn = !empty($migrate_info['initialization']['config_cdn'][DATA]) ? $migrate_info['initialization']['config_cdn'][DATA] : [];
        
        $pathinfo = !empty($url_media) ? pathinfo($url_media) : null;
        $dirname = !empty($pathinfo['dirname']) ? $pathinfo['dirname'] : null;
        $dir_thumb_name = !empty($dirname) ? str_replace('media', 'thumbs', $dirname) : null;
        $basename = !empty($pathinfo['basename']) ? $pathinfo['basename'] : null;
        $filename = !empty($pathinfo['filename']) ? $pathinfo['filename'] : null;
        $extension = !empty($pathinfo['extension']) ? $pathinfo['extension'] : null;

        $url_cdn = !empty($config_cdn['url_cdn']) ? $config_cdn['url_cdn'] : null;
        $url_media = !empty($url_media) ? $url_cdn . $url_media : null;

        // thực hiện tải ảnh về thư mục media
        $folder_media = TMP . 'export/media' . DS . $path . DS . str_replace('%20', ' ', $basename);
        $folder_thumbs = TMP . 'export/thumbs' . DS . $path . DS . str_replace('%20', ' ', $basename);

        if(! @(file_get_contents($url_media))) return '';
        file_put_contents($folder_media, file_get_contents($url_media));
        file_put_contents($folder_thumbs, file_get_contents($url_media));

        // thực hiện tải ảnh thumb về thư mục thumbs
        $list_thumbs = ['50', '150', '250', '350', '500', '720'];

        if (!empty($list_thumbs)) {
            foreach ($list_thumbs as $key => $thumb) {
                $url_thumb_media = $url_cdn . $dir_thumb_name . '/' . $filename . '_thumb_' . $thumb . '.' . $extension;
                $folder_thumbs = TMP . 'export/thumbs' . DS . $path . DS . str_replace('%20', ' ', $filename) . '_thumb_' . $thumb . '.' . $extension;
                
                if(! @(file_get_contents($url_thumb_media))) continue;

                file_put_contents($folder_thumbs, file_get_contents($url_thumb_media));
            }
        }

        $url_media_new = '/media/' . $path . '/' . $basename;
        return $url_media_new;
    }

    private function migrateImageInContent($root_folder = null, $url_cdn = null, $url_cdn_new = null, $content = null)
    {
        if(empty($content) || empty($root_folder) || empty($url_cdn)) return $content;
        if(!in_array($root_folder, ['categories', 'products', 'articles', '
            '])) return $content;
        if(strpos($content, '<img') == false) return $content;

        $matches = [];
        preg_match_all( '/<img[\s\r\n]+.*?>/is', $content, $matches);
        if(empty($matches[0])) return $content;

        $images = $replace = [];

        foreach($matches[0] as $image){
            $urls = [];
            preg_match_all('~<img.*?src=["\']+(.*?)["\']+~', $image, $urls);
            $url = !empty($urls[1][0]) ? $urls[1][0] : null;

            if(empty($url)) continue;

            $new_url = null;
            if(strpos($url, $url_cdn) > -1){
                $url = str_replace($url_cdn, '', $url);
                $new_url = $this->downloadImageContent($url, 'categories');
            }

            if(!empty($new_url)){
                $replaceImage = preg_replace( '/<img(.*?)src=/is', '<img$1src="' . $url_cdn_new . $new_url . '"', $image);
                $images[] = $image;
                $replace[] = $replaceImage;
            }                        
        }

        $content = str_replace($images, $replace, $content);

        return $content;            
    }

    public function downloadImageContent($url_media = null, $path = null)
    {
        // đọc thông tin cấu hình migrate
        $migrate_info = $this->readMigrateDataExportInfo();
        $config_cdn = !empty($migrate_info['initialization']['config_cdn'][DATA]) ? $migrate_info['initialization']['config_cdn'][DATA] : [];
        
        $pathinfo = !empty($url_media) ? pathinfo($url_media) : null;
        $dirname = !empty($pathinfo['dirname']) ? $pathinfo['dirname'] : null;
        $dir_thumb_name = !empty($dirname) ? str_replace('media', 'thumbs', $dirname) : null;
        $basename = !empty($pathinfo['basename']) ? $pathinfo['basename'] : null;
        $filename = !empty($pathinfo['filename']) ? $pathinfo['filename'] : null;
        $extension = !empty($pathinfo['extension']) ? $pathinfo['extension'] : null;

        $url_cdn = !empty($config_cdn['url_cdn']) ? $config_cdn['url_cdn'] : null;
        $url_media = !empty($url_media) ? $url_cdn . $url_media : null;

        if (!is_dir($path . DS . 'content')) {
            $folder_media = new Folder(TMP . 'export/media' . DS . $path . DS . 'content', true);
        }

        if (!is_dir($path . DS . 'content')) {
            $folder_media = new Folder(TMP . 'export/thumbs' . DS . $path . DS . 'content', true);
        }

        // thực hiện tải ảnh về thư mục media
        $folder_media = TMP . 'export/media' . DS . $path . DS . 'content' . DS . $basename;
        $folder_thumbs = TMP . 'export/thumbs' . DS . $path . DS . 'content' . DS . $basename;

        if(! @(file_get_contents($url_media))) return '';
        file_put_contents($folder_media, file_get_contents($url_media));
        file_put_contents($folder_thumbs, file_get_contents($url_media));

        // thực hiện tải ảnh thumb về thư mục thumbs
        $list_thumbs = ['50', '150', '250', '350', '500', '720'];

        if (!empty($list_thumbs)) {
            foreach ($list_thumbs as $key => $thumb) {
                $url_thumb_media = $url_cdn . $dir_thumb_name . '/' . $filename . '_thumb_' . $thumb . '.' . $extension;
                $folder_thumbs = TMP . 'export/thumbs' . DS . $path . DS . 'content' . DS . str_replace('%20', ' ', $filename) . '_thumb_' . $thumb . '.' . $extension;
                
                if(! @(file_get_contents($url_thumb_media))) continue;

                file_put_contents($folder_thumbs, file_get_contents($url_thumb_media));
            }
        }

        $url_media_new = '/media/' . $path . '/content/' . $basename;
        return $url_media_new;
    }
}
