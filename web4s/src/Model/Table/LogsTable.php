<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Hash;
use Cake\Utility\Text;

class LogsTable extends Table
{
    private $lang = null;
    private $limit = 500;
    private $number_log_file = 20;
    private $dir_log = SOURCE_DOMAIN . DS . 'system_logs';

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('logs');
        $this->setPrimaryKey('id');
        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);

        $this->belongsTo('User', [
            'className' => 'Users',
            'foreignKey' => 'user_id',
            'propertyName' => 'user'
        ]);

        $this->lang = TableRegistry::get('Languages')->getDefaultLanguage();
    }

    public function writeLog(string $alias = null, string $action = null, int $id = null, $entity = [], $old_data = [])
    {
        if(empty($action) || !in_array($action, ['add', 'update', 'update_status', 'delete'])) return;
        if(empty($alias) || empty($id) || empty($entity)) return;

        // chỉ ghi log tài khoản trong quản trị, nên khi tồn tại AUTH_USER_ID mới thực hiện ghi log
        if(!defined('AUTH_USER_ID')) return;

        $log = [];
        $type = DATA;
        $sub_type = null;
        switch($alias){
            case 'Articles':
                $log = $this->_article($action, $id, $entity);
                $sub_type = ARTICLE;
            break;

            case 'Products':
                $log = $this->_product($action, $id, $entity);
                $sub_type = PRODUCT;
            break;

            case 'Brands':
                $log = $this->_brand($action, $id, $entity);
                $sub_type = BRAND;
            break;

            case 'Categories':
                $log = $this->_category($action, $id, $entity);
                $sub_type = CATEGORY;
            break;

            case 'Orders':
                $log = $this->_order($action, $id, $entity);
                $sub_type = ORDER;
            break;

            case 'TemplatesBlock':
                $log = $this->_block($action, $id, $entity);
                $sub_type = BLOCK;
            break;

            case 'TemplatesPage':
                $log = $this->_templatePage($action, $id, $entity);
                $sub_type = TEMPLATE_PAGE;
            break;
        }
        
        if(empty($log) || empty($log['action']) || empty($log['description'])) return;

        $log['user_id'] = AUTH_USER_ID;
        $log['record_id'] = $id;
        $log['type'] = $type;
        $log['sub_type'] = $sub_type;

        // lưu vào bảng log
        $log_entity = $this->newEntity($log);
        $save = $this->save($log_entity);
        if (empty($save->id)) return;

        // lưu old entity giao diện vào log
        if(in_array($alias, ['TemplatesPage', 'TemplatesBlock'])){
            $this->_writeDetailLog($save->id, $old_data);
        }

        return true;
    }

    public function writeLogChangeFile($action = null, $dir_file = null)
    {
        if(empty($action) || !in_array($action, ['add', 'update', 'delete'])) return false;
        if(empty($dir_file) || !file_exists($dir_file)) return false;
        if(strpos($dir_file, SOURCE_DOMAIN . DS . 'templates') === false) return false;
        if(is_dir($dir_file)) return;

        $filename = basename($dir_file);
        $dir_log_file_origin = str_replace(SOURCE_DOMAIN . DS . 'templates', SOURCE_DOMAIN . DS . 'system_logs', $dir_file);
        $dir_log_file = $dir_log_file_origin . '_' . time();

        // tạo thư mục chứ file log nếu chưa tồn tại
        if(!file_exists(dirname($dir_log_file))) {
            $split_dir = explode(DS, str_replace(SOURCE_DOMAIN . DS, '', dirname($dir_log_file)));

            if(empty($split_dir)) return false;

            $check_path = SOURCE_DOMAIN;
            foreach($split_dir as $k => $path){
                $check_path .= DS . $path;
                if(!file_exists($check_path)){
                    @mkdir($check_path, 0755);
                }
            }
        }

        $copy = @copy($dir_file, $dir_log_file);
        if(empty($copy)) return false;

        // kiểm tra số lượng file log của tệp (nếu vượt quá 20 tệp thì xóa tệp log cũ đi)
        $files = glob($dir_log_file_origin . '_*');
        if(count($files) > $this->number_log_file){
            $number_delete = count($files) - $this->number_log_file;
            $i = 0;
            foreach($files as $old_file){
                $i ++;
                if($i > $number_delete) continue;
                @unlink($old_file);                
            }
        }

        // lưu path file
        $path_file = TableRegistry::get('Utilities')->dirToPath($dir_file);
        $path_log = TableRegistry::get('Utilities')->dirToPath($dir_log_file);

        $description = __d('admin', 'cap_nhat_tep_{0}', [$filename]);
        if($action == 'add'){
            $description = __d('admin', 'them_moi_tep_{0}', [$filename]);
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_tep_{0}', [$filename]);
        }

        $data_save = [
            'action' => $action,
            'type' => TEMPLATE,
            'user_id' => AUTH_USER_ID,
            'description' => $description,
            'link' => null,
            'path_file' => $path_file,
            'path_log' => $path_log
        ];

        // lưu vào bảng log
        $entity = $this->newEntity($data_save);
        $save = $this->save($entity);
        if (empty($save->id)) return false;
        
        return true;
    }

    private function _isUpdateStatus($action = null, EntityInterface $entity)
    {
        if($action != 'update' || !isset($entity['status'])) return false;

        $dirty_fields = $entity->getDirty();        
        if(empty($dirty_fields)) return false;

        $dirty_fields = array_flip($dirty_fields);
        unset($dirty_fields['updated']);
        unset($dirty_fields['draft']);

        if(isset($dirty_fields['status']) && count($dirty_fields) == 1) return true;

        return false;
    }

    private function _article($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);

        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy tên bài viết (thêm mới hoặc cập nhật)
        $name = !empty($entity['ArticlesContent']['name']) ? $entity['ArticlesContent']['name'] : '';

        // lấy tên bài viết (nhân bản)
        if(empty($name)){
            $name = !empty($entity['ContentMutiple'][0]['name']) ? $entity['ContentMutiple'][0]['name'] : '';
        }

        // lấy tên bài viết trong bảng
        if(empty($name)){
            $content = TableRegistry::get('ArticlesContent')->find()->where([
                'article_id' => $id, 
                'lang' => $this->lang
            ])->select(['name'])->first();

            $name = !empty($content['name']) ? $content['name'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', [strtolower(__d('admin', 'bai_viet')), $name]);
            $link = ADMIN_PATH .'/article/update/' . $id;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', [strtolower(__d('admin', 'bai_viet')), $name]);
            $link = ADMIN_PATH .'/article/update/' . $id;
        }
        
        if($action == 'update_status'){
            if(!empty($entity['status'])){
                $description = __d('admin', 'kich_hoat_{0}_{1}', [strtolower(__d('admin', 'bai_viet')), $name]);
            }else{
                $description = __d('admin', 'ngung_hoat_dong_{0}_{1}', [strtolower(__d('admin', 'bai_viet')), $name]);
            }
            
            $link = ADMIN_PATH .'/article/update/' . $id;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', [strtolower(__d('admin', 'bai_viet')), $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];

        return $result;
    }

    private function _product($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);
        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy tên bài viết (thêm mới hoặc cập nhật)
        $name = !empty($entity['ProductsContent']['name']) ? $entity['ProductsContent']['name'] : '';

        // lấy tên bài viết (nhân bản)
        if(empty($name)){
            $name = !empty($entity['ContentMutiple'][0]['name']) ? $entity['ContentMutiple'][0]['name'] : '';
        }

        // lấy tên sản phẩm trong bảng
        if(empty($name)){
            $content = TableRegistry::get('ProductsContent')->find()->where([
                'product_id' => $id, 
                'lang' => $this->lang
            ])->select(['name'])->first();

            $name = !empty($content['name']) ? $content['name'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
            $link = ADMIN_PATH .'/product/update/' . $id;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
            $link = ADMIN_PATH .'/product/update/' . $id;
        }
        
        if($action == 'update_status'){
            $status = !empty($entity['status']) ? intval($entity['status']) : 0;
            if($status == 1){
                $description = __d('admin', 'kich_hoat_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
            }elseif ($status == 2){
                $description = __d('admin', 'ngung_kinh_doanh_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
            }else{
                $description = __d('admin', 'ngung_hoat_dong_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
            }

            $link = ADMIN_PATH .'/product/update/' . $id;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];

        return $result;
    }

    private function _brand($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);

        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy tên thương hiệu (thêm mới hoặc cập nhật)
        $name = !empty($entity['BrandsContent']['name']) ? $entity['BrandsContent']['name'] : '';
        if(empty($name)){
            $content = TableRegistry::get('BrandsContent')->find()->where([
                'brand_id' => $id, 
                'lang' => $this->lang
            ])->select(['name'])->first();

            $name = !empty($content['name']) ? $content['name'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', [strtolower(__d('admin', 'thuong_hieu')), $name]);
            $link = ADMIN_PATH .'/brand/update/' . $id;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', [strtolower(__d('admin', 'thuong_hieu')), $name]);
            $link = ADMIN_PATH .'/brand/update/' . $id;
        }
        
        if($action == 'update_status'){
            if(!empty($entity['status'])){
                $description = __d('admin', 'kich_hoat_{0}_{1}', [strtolower(__d('admin', 'thuong_hieu')), $name]);
            }else{
                $description = __d('admin', 'ngung_hoat_dong_{0}_{1}', [strtolower(__d('admin', 'thuong_hieu')), $name]);
            }
            
            $link = ADMIN_PATH .'/brand/update/' . $id;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', [strtolower(__d('admin', 'thuong_hieu')), $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];

        return $result;
    }

    private function _category($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);
        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy tên danh mục
        $name = !empty($entity['CategoriesContent']['name']) ? $entity['CategoriesContent']['name'] : '';
        if(empty($name)){
            $content = TableRegistry::get('CategoriesContent')->find()->where([
                'category_id' => $id, 
                'lang' => $this->lang
            ])->select(['name'])->first();

            $name = !empty($content['name']) ? $content['name'] : '';
        }

        // lấy loại danh mục
        $type = !empty($entity['type']) ? $entity['type'] : '';        
        if(empty($type)){
            $category = TableRegistry::get('Categories')->find()->where([
                'id' => $id, 
            ])->select(['id', 'type'])->first();

            $type = !empty($category['type']) ? $content['type'] : '';
        }
       
        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', [strtolower(__d('admin', 'danh_muc')), $name]);

            if(!empty($type)) $link = ADMIN_PATH . "/category/$type/update/" . $id;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', [strtolower(__d('admin', 'danh_muc')), $name]);
            if(!empty($type)) $link = ADMIN_PATH . "/category/$type/update/" . $id;
        }
        
        if($action == 'update_status'){
            if(!empty($entity['status'])){
                $description = __d('admin', 'kich_hoat_{0}_{1}', [strtolower(__d('admin', 'danh_muc')), $name]);
            }else{
                $description = __d('admin', 'ngung_hoat_dong_{0}_{1}', [strtolower(__d('admin', 'danh_muc')), $name]);
            }
            
            if(!empty($type)) $link = ADMIN_PATH . "/category/$type/update/" . $id;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', [strtolower(__d('admin', 'danh_muc')), $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];

        return $result;
    }

    private function _order($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);
        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy mã đơn hàng
        $code = !empty($entity['code']) ? $entity['code'] : '';
        if(empty($code)){
            $order = TableRegistry::get('Orders')->find()->where([
                'id' => $id,
            ])->select(['code'])->first();

            $code = !empty($order['code']) ? $order['code'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', [strtolower(__d('admin', 'don_hang')), $code]);
            $link = ADMIN_PATH .'/order/detail/' . $id;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', [strtolower(__d('admin', 'don_hang')), $code]);
            $link = ADMIN_PATH .'/order/detail/' . $id;
        }
        
        if($action == 'update_status'){
            $description = __d('admin', 'thay_doi_trang_thai_{0}_{1}', [strtolower(__d('admin', 'don_hang')), $code]);   
            $link = ADMIN_PATH .'/order/detail/' . $id;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', [strtolower(__d('admin', 'don_hang')), $code]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];
        
        return $result;
    }

    private function _block($action = null, $id = null, $entity = null)
    {

        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);
        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';

        // lấy code và tên block
        $code = !empty($entity['code']) ? $entity['code'] : '';
        $name = !empty($entity['name']) ? $entity['name'] : '';
        if(empty($code) || empty($name)){
            $block = TableRegistry::get('Blocks')->find()->where([
                'id' => $id,
            ])->select(['code', 'name'])->first();

            $code = !empty($block['code']) ? $block['code'] : '';
            $name = !empty($block['name']) ? $block['name'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', ['BLOCK', $name]);
            $link = ADMIN_PATH .'/template/block/update/' . $code;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', ['BLOCK', $name]);
            $link = ADMIN_PATH .'/template/block/update/' . $code;
        }
        
        if($action == 'update_status'){
            $description = __d('admin', 'thay_doi_trang_thai_{0}_{1}', ['BLOCK', $name]);
            $link = ADMIN_PATH .'/template/block/update/' . $code;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', ['BLOCK', $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];

        return $result;
    }

    private function _templatePage($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);
        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy tên trang
        $name = !empty($entity['name']) ? $entity['name'] : '';
        if(empty($code) || empty($name)){
            $page_info = TableRegistry::get('TemplatesPage')->find()->where(['id' => $id])->first();
            $name = !empty($page_info['name']) ? $page_info['name'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', ['TEMPLATE_PAGE', $name]);
            $link = ADMIN_PATH .'/template/customize';
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', ['TEMPLATE_PAGE', $name]);
            $link = ADMIN_PATH .'/template/customize';
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', ['TEMPLATE_PAGE', $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];
        
        return $result;
    }

    private function _writeDetailLog(int $log_id, $old_data = [])
    {
        if(empty($log_id)) return false;    
        if(!file_exists($this->dir_log)) $create_dir = @mkdir($this->dir_log, 0755, true);

        // mỗi file 500 log
        $round = ceil($log_id / $this->limit) * $this->limit;
        $dir_file = $this->dir_log. DS . $round . '.json';

        if(!file_exists($dir_file)) @fopen($dir_file, 'w');
        if(!file_exists($dir_file)) return;

        $content = @file_get_contents($dir_file);
        if(empty($content) || !TableRegistry::get('Utilities')->isJson($content)){
            $content = [];
        }else{
            $content = json_decode($content, true);
        }

        $content[$log_id] = $old_data;
        $write = @file_put_contents($dir_file, json_encode($content));

        return;
    }

    public function rollbackDataLog($log_id = null)
    {
        if(empty($log_id)) return false;

        $log_info = $this->find()->where([
            'id' => $log_id
        ])->select([
            'id', 'action', 'type', 'sub_type', 'record_id'
        ])->first();

        $action = !empty($log_info['action']) ? $log_info['action'] : null;
        $type = !empty($log_info['type']) ? $log_info['type'] : null;
        $sub_type = !empty($log_info['sub_type']) ? $log_info['sub_type'] : null;
        $record_id = !empty($log_info['record_id']) ? intval($log_info['record_id']) : null;
        if(empty($action) || empty($type) || empty($sub_type) || empty($record_id)) return false;

        // lấy tệp chứa thông tin log

        $round = ceil($log_id / $this->limit) * $this->limit;
        $dir_file = $this->dir_log. DS . $round . '.json';

        if(!file_exists($dir_file)) return false;
        $content = @file_get_contents($dir_file);
        if(empty($content) || !TableRegistry::get('Utilities')->isJson($content)){
            $content = [];
        }else{
            $content = json_decode($content, true);
        }

        $data_save = !empty($content[$log_id]) ? $content[$log_id] : [];
        if(empty($data_save)) return false;

        if($sub_type == BLOCK){
            $table = TableRegistry::get('TemplatesBlock');

            $block_info = $table->find()->where(['id' => $record_id])->first();
            if(empty($block_info)) return false;

            $entity = TableRegistry::get('TemplatesBlock')->patchEntity($block_info, $data_save);
            $save = $table->save($entity);
            if (empty($save->id)) return false;
        }


        return true;
    }

    public function getListLogChangeFile($dir_file = null)
    {
        if(empty($dir_file) || !file_exists($dir_file)) return [];
        if(strpos($dir_file, SOURCE_DOMAIN . DS . 'templates') === false) return [];
        if(is_dir($dir_file)) return [];

        $log_dir = str_replace(SOURCE_DOMAIN . DS . 'templates', SOURCE_DOMAIN . DS . 'system_logs', $dir_file);
        $files = glob($log_dir . '_*');

        if(empty($files)) return [];        
        $files = array_reverse($files);
        
        $utilities = TableRegistry::get('Utilities');
        $result = [];
        foreach($files as $file){
            $result[] = [
                'filename' => basename($file),
                'dir' => $file,
                'path' => $utilities->dirToPath($file)
            ];
        }

        return $result;
    }
}