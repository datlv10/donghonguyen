<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use ZipArchive;

class TemplateComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities', 'Admin.Plugin'];
    public $table_template = [];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();

        $this->table_template = [
            'templates_page',
            'templates_page_content',
            'templates_row',
            'templates_column',
            'templates_block'
        ];
    }

    public function exportTemplate($params = [])
    {
        // validate params 
        $name = !empty($params['name']) ? trim($params['name']) : null;
        $code = !empty($params['code']) ? trim($params['code']) : null;
        $author = !empty($params['author']) ? trim($params['author']) : null;
        $version = !empty($params['version']) ? trim($params['version']) : null;
        $description = !empty($params['description']) ? trim($params['description']) : null;
        $image_file = !empty($params['image_avatar']) ? $params['image_avatar'] : null;
        $template_id = !empty($params['template_id']) ? intval($params['template_id']) : null;
        
        if(empty($name)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_ten_giao_dien')]);
        }

        if(empty($code)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_ma_giao_dien')]);
        }

        if(empty($author)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_ten_tac_gia_giao_dien')]);
        }

        if(empty($template_id)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }

        // get code of template export
        $template_export = TableRegistry::get('Templates')->find()->where([
            'Templates.id' => $template_id
        ])->first();

        $template_code_export = !empty($template_export['code']) ? $template_export['code'] : null;
        if(empty($template_code_export)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }

        $template_info = [
            'name' => $name,
            'code' => $code,
            'author' => $author,
            'plugins' => !empty($template_export['plugins']) ? $template_export['plugins'] : null,
            'version' => $version,
            'description' => $description,
        ];

        $dir_template = SOURCE_DOMAIN . DS . 'templates' . DS . $template_code_export;
        $dir_tmp = TMP . 'templates' . DS;
        $dir_new = $dir_tmp . $code;

        // check folder template export exist
        $folder_template = new Folder($dir_template, false);
        if(empty($folder_template->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'thu_muc_chua_giao_dien_mau_khong_ton_tai')]);
        }        

        // create folder tmp
        $folder_tmp = new Folder($dir_tmp, true, 0755);
        if(empty($folder_tmp->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_khoi_tao_thu_muc_chua_giao_dien')]);
        }

        // check exist folder copy
        $folder_copy = new Folder($dir_new, false);
        if(!empty($folder_copy->path)){
            $folder_copy->delete();
        }        

        // copy template
        $folder_template->copy($dir_new);

        // check file template after copy
        $folder_new = new Folder($dir_new, false);
        if(empty($folder_new->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'nhan_ban_giao_dien_mau_khong_thanh_cong')]);
        }

        // upload file screenshot
        $image_error = $image_file->getError();
        $image_type = $image_file->getClientMediaType();
        if(empty($image_error) && !empty($image_type) && in_array($image_type, ['image/png', 'image/jpeg'])){
            $image_file->moveTo($dir_new . DS . 'screenshot.png');
        }

        // edit file config
        $file_config = new File($dir_new . DS . 'config.json', false);
        if(empty($file_config->path) || !$file_config->writable()){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_doc_duoc_tep_cau_hinh_cua_giao_dien')]);
        }
        $file_config->write(json_encode($template_info, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), 'w');
        $file_config->close();

        // export data template to folder
        $content_export = $this->exportDataTemplate($template_code_export, $code);
        $file_data = new File($dir_new . DS . 'data.sql', true, 0755);
        if(empty($file_data->path) || !$file_data->writable()){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_doc_duoc_tep_du_lieu_cua_giao_dien')]);
        }
        $file_data->write($content_export, 'w');
        $file_data->close();
        
        // zip file template
        $zip = new ZipArchive();
        $open_zip = $zip->open($dir_tmp . $code . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if($open_zip !== true){
            return $this->System->getResponse([MESSAGE => __d('admin', 'nen_thu_muc_giao_dien_khong_thanh_cong')]);
        }

        
        $files = $folder_new->findRecursive();
        if(empty($files)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'giai_nen_thu_muc_giao_dien_khong_thanh_cong')]);
        }

        foreach ($files as $key => $file) {
            if(strpos($file, 'config.rb') || strpos($file, '\sass\\')) continue;
            $zip->addFile($file, str_replace(DS, '/', str_replace(TMP . 'templates' . DS, '', $file)));
        }
        $zip->close();


        // check file zip created
        $file_zip = new File($dir_tmp . $code . '.zip', false);
        if(empty($file_zip->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_doc_duoc_tep_cau_hinh_cua_giao_dien')]);
        }
        $file_zip->close();

        // remove folder template tmp
        $delete = $folder_new->delete();

        // response
        $url_file = str_replace(DS, '/', str_replace(SOURCE_DOMAIN, '', $file_zip->path));
        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xuat_giao_dien_mau_thanh_cong'),
            DATA => [
                'template_file' => $url_file
            ]
        ]);        
    }

    private function exportDataTemplate($template_code = null, $new_code = null)
    {
        if(empty($template_code) || empty($new_code)) return null;

        $conn = ConnectionManager::get('default');

        $content = "SET sql_mode = '';\nSET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($this->table_template as $k => $table) {
            $rows = $conn->execute("SELECT * FROM `" . $table . "` WHERE `template_code` = \"" . $template_code . "\" ORDER BY `id` ASC")->fetchAll('assoc');            
            if(empty($rows)) continue;
            foreach ($rows as $key => $item) {
                $content .= "INSERT INTO " . $table . " (";

                foreach ($item as $field => $value) {
                    if($field == 'id') continue;

                    $content .= "`" . addslashes($field) . "`";
                    if(array_key_last($item) != $field) $content .= ", ";
                }
                
                $content .= ") VALUES (";
                foreach ($item as $field => $value) {
                    if($field == 'id') continue;

                    if($field == 'template_code'){
                        $content .= "'" . $new_code . "'";
                    }else{
                        if(is_null($value)) $value = '';
                        $content .= "'" . addslashes(str_replace($template_code, $new_code, $value)) . "'";
                    }

                    if(array_key_last($item) != $field) $content .= ", ";
                }
                $content .= ");\n";
            }
            $content .= "\n\n";
        }

        $content.= "SET FOREIGN_KEY_CHECKS = 1;";
        return $content;
    }
    
    public function installationTemplate($file_installation = null, $params = [])
    {
        if(empty($file_installation)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'tep_khoi_tao_cua_giao_dien_khong_hop_le')]);
        }
        
        $set_default = !empty($params['set_default']) ? 1 : 0;

        // check file template install
        $file_error = $file_installation->getError();
        $tmp_name = $file_installation->getStream()->getMetadata('uri');
        $file_name = $file_installation->getClientFilename();
        $file_type = $file_installation->getClientMediaType();
        $list_type_zip = ['application/x-zip-compressed', 'application/zip'];
        if(!empty($file_error) || empty($tmp_name) || empty($file_name) || !in_array($file_type, $list_type_zip)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tep_khoi_tao_cua_giao_dien_khong_hop_le')]);
        }

        $template_code = str_replace('.zip', '', $file_name);
        
        // create folder contain all templates if not exist
        $dir_template = SOURCE_DOMAIN . DS . 'templates' . DS;
        $folder_template = new Folder($dir_template, true, 0755);
        if(empty($folder_template->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_khoi_tao_thu_muc_chua_giao_dien')]);
        }
        
        // check folder template code exist and delete it  
        $dir_new = $dir_template . $template_code . DS;
        $folder_new = new Folder($dir_new, false);
        if(!empty($folder_new->path)){
            $folder_new->delete();
        }


        // unzip file to folder tmp
        $zip_archive = new ZipArchive();
        $open_file = $zip_archive->open($tmp_name);
        if($open_file !== true){
            return $this->System->getResponse([MESSAGE => __d('admin', 'tai_xuong_tep_khoi_tao_cua_giao_dien_khong_thanh_cong')]);
        }        

        if(PHP_OS == 'Linux'){
            $zip_archive->extractTo($dir_new);
            
            $folder_new = new Folder($dir_new, false);
            $files = $folder_new->findRecursive();

            if(!empty($files)){
                foreach($files as $path){
                    $file = new File($path, false);

                    $file_name = str_replace($dir_new, '', $path);
                    $file_name = str_replace('\\', '/', $file_name);
                    $file_name = str_replace("$template_code/", '', $file_name);

                    $file_copy = new File($dir_new . $file_name, true);
                    $copy = $file->copy($dir_new . $file_name, true);
                    $file->delete();
                    $file_copy->close();                    
                }
            }
        }else{
            $zip_archive->extractTo($dir_template);
        }

        $zip_archive->close();

        // read config file of template
        $file_config = new File($dir_new . 'config.json', false);
        if(!$file_config->exists()){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_tep_cau_hinh_cua_giao_dien')]);
        }

        $json_config = !empty($file_config->read()) ? trim($file_config->read()) : null;
        if(empty($json_config) || !$this->Utilities->isJson($json_config)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'thong_tin_cau_hinh_cua_giao_dien_khong_hop_le')]);
        }
        $file_config->close();

        $config = json_decode($json_config, true);
        $name = !empty($config['name']) ? $config['name'] : null;
        $template_code_config = !empty($config['code']) ? $config['code'] : null;
        $plugins = !empty($config['plugins']) ? $config['plugins'] : null;
        if(empty($name) || empty($template_code_config)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'thong_tin_cau_hinh_cua_giao_dien_khong_hop_le')]);
        }

        if($template_code_config != $template_code){
            return $this->System->getResponse([MESSAGE => __d('admin', 'ma_giao_dien_khong_chinh_xac')]);
        }

        // read data file of template
        $file_data = new File($dir_new . 'data.sql', false);
        if(!$file_data->exists()){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_tep_du_lieu_cua_giao_dien')]);
        }

        $query_data_template = !empty($file_data->read()) ? trim($file_data->read()) : null;
        if(empty($query_data_template)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_doc_duoc_thong_tin_du_lieu_giao_dien')]);
        }        
        $file_data->close();

        // excute data template
        $table = TableRegistry::get('Templates');

        $template = $table->newEntity([
            'code' => $template_code,
            'name' => $name,
            'plugins' => $plugins,
            'description' => !empty($config['description']) ? $config['description'] : null,
            'author' => !empty($config['author']) ? $config['author'] : null,
            'version' => !empty($config['version']) ? $config['version'] : null,
            'is_default' => $set_default
        ]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // clear old template
            $conn->execute("DELETE FROM `templates` WHERE `code` = \"" . $template_code . "\"");
            foreach ($this->table_template as $key => $table_name) {
                $conn->execute("DELETE FROM `" . $table_name . "` WHERE `template_code` = \"" . $template_code . "\"");
            }

            if(!empty($set_default)){
                $table->updateAll(['is_default' => 0], []);
            }
            
            // save info template
            $save = $table->save($template);
            if (empty($save->id)){
                throw new Exception();
            }

            // active plugin by template
            if(!empty($set_default)){
                $this->Plugin->activePluginByTemPlate();
            }

            $conn->commit();

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }

        $execute_query = $conn->execute($query_data_template);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'cai_dat_giao_dien_thanh_cong')
        ]);
    }

    public function deleteTemplate($template_code = null)
    {
        if(empty($template_code)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_ma_giao_dien')]);
        }

        $dir_new = SOURCE_DOMAIN . DS . 'templates' . DS . $template_code . DS;
        $folder_new = new Folder($dir_new, false);
        if(!empty($folder_new->path)){
            $folder_new->delete();
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // delete info template
            $conn->execute("DELETE FROM `templates` WHERE `code` = \"" . $template_code . "\"");
            foreach ($this->table_template as $key => $table_name) {
                $conn->execute("DELETE FROM `" . $table_name . "` WHERE `template_code` = \"" . $template_code . "\"");
            }

            $conn->commit();

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
        
        // clear cache website
        TableRegistry::get('App')->deleteAllCache();

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xoa_giao_dien_thanh_cong')
        ]);
    }
}
