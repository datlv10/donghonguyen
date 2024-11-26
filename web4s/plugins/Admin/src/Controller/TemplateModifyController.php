<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

class TemplateModifyController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Admin.UploadFile');
    }

    public function save($type = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $content = !empty($data['content']) ? $data['content'] : '';
        $dir = PATH_TEMPLATE . 'assets' . DS . $type . DS . 'custom.' . $type;
        $file = new File($dir, false);

        if(!$file->exists()){
            $this->responseJson([MESSAGE => __d('admin', 'khong_doc_duoc_noi_dung_tep')]);
        }

        // so sánh với nội dung cũ của tệp nếu ko có thay đổi thì bỏ qua
        $old_content = @file_get_contents($dir);
        if($content == $old_content) $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        try {
            // lưu file log trước khi cập nhật nội dung mới
            TableRegistry::get('Logs')->writeLogChangeFile('update', $dir);

            $write = $file->write($content);
            if(empty($write)) $this->responseJson([MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);            
            $file->close();
                        
            TableRegistry::get('App')->deleteCacheTemplate(PAGE);

            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong')
            ]);

        } catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function saveFile() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $path = !empty($data['path']) ? $data['path'] : null;
        if (!$this->getRequest()->is('post') || empty($path)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $dir = TableRegistry::get('Utilities')->pathToDir($path);
        if(empty($dir)) $this->responseJson([MESSAGE => __d('admin', 'tep_nay_khong_ton_tai')]);
        
        $content = !empty($data['content_file']) ? $data['content_file'] : '';

        $file = new File($dir, false);
        if (!$file->exists()) {
            $this->responseJson([MESSAGE => __d('admin', 'tep_nay_khong_ton_tai')]);
        }

        $ext = strtolower(pathinfo($dir, PATHINFO_EXTENSION));
        $white_list = ['css', 'js', 'tpl', 'json', 'po'];
        if(!in_array($ext, $white_list)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_doc_duoc_tep_nay')]);
        }

        // so sánh với nội dung cũ của tệp nếu ko có thay đổi thì bỏ qua
        $old_content = @file_get_contents($dir);
        if($content == $old_content) $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        try {
            // lưu file log trước khi cập nhật nội dung mới
            TableRegistry::get('Logs')->writeLogChangeFile('update', $dir);

            // cập nhật nội dung mới vào tệp
            $write = $file->write($content);
            if(empty($write)) $this->responseJson([MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);
            $file->close();
            
            TableRegistry::get('App')->deleteCacheTemplate();
        } catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
        ]);
    }

    public function renameFile()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $new_name = !empty($data['new_name']) ? $data['new_name'] : null;
        $old_name = !empty($data['old_name']) ? $data['old_name'] : null;
        $path = !empty($data['path']) ? $data['path'] : null;
        if (!$this->getRequest()->is('post') || empty($new_name) || empty($old_name) || empty($path)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $dir = TableRegistry::get('Utilities')->pathToDir($path);
        if(empty($dir)) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
    
        $different = @strcmp($new_name, $old_name);
        if(!$different) {
            $this->responseJson([MESSAGE => __d('admin', 'ten_chua_duoc_thay_doi')]);
        }

        

        $dir = pathinfo($dir, PATHINFO_DIRNAME);        
        $path_new = $dir . DS . $new_name;
        $path_old = $dir . DS . $old_name;

        // lưu file log
        TableRegistry::get('Logs')->writeLogChangeFile('delete', $path_old);
        
        // rename file
        @rename($path_old, $path_new);
        
        // lưu file log
        TableRegistry::get('Logs')->writeLogChangeFile('add', $path_new);
        
        TableRegistry::get('App')->deleteCacheTemplate();

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'doi_ten_thanh_cong')
        ]);
    }

    public function deleteFile()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $type = !empty($data['type']) ? $data['type'] : null;
        $path = !empty($data['path']) ? $data['path'] : null;
        if (!$this->getRequest()->is('post') || empty($type) || empty($path)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $dir = TableRegistry::get('Utilities')->pathToDir($path);
        if(empty($dir) || !file_exists($dir)) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);

        // lưu file log trước khi xóa
        TableRegistry::get('Logs')->writeLogChangeFile('delete', $dir);

        if($type == 'file') {
            $file = new File($dir, false);
            if($file->exists()){
                $delete = $file->delete();
            }
            $message = __d('admin', 'file_da_duoc_xoa');
        } else {
            $folder = new Folder($dir, false);
            $delete = $folder->delete();

            $message = __d('admin', 'folder_da_duoc_xoa');
        }

        TableRegistry::get('App')->deleteCacheTemplate();

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => $message
        ]);
    }

    public function uploadFile()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $path = !empty($data['path']) ? $data['path'] : null;
        if (!$this->getRequest()->is('post') || empty($path)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $dir = TableRegistry::get('Utilities')->pathToDir($path);
        if(!file_exists($dir)) {
            $this->responseJson([MESSAGE => __d('admin', 'tep_nay_khong_ton_tai')]);
        }

        if(!is_dir($dir)) $dir = dirname($dir);
        
        $upload = $this->UploadFile->upload($data['file'], $dir);
        if (empty($upload) || ( !empty($upload) && $upload['code'] === ERROR)) {
            $this->responseJson([
                MESSAGE => !empty($upload['message']) ? $upload['message'] : __d('admin', 'tai_tep_len_khong_thanh_cong')
            ]);
        }
        
        // lưu log sau khi upload
        $dir_file = !empty($upload[DATA]['dir_file']) ? $upload[DATA]['dir_file'] : null;
        TableRegistry::get('Logs')->writeLogChangeFile('add', $dir_file);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'tai_tep_len_thanh_cong')
        ]);
    }

    public function cssCustom()
    {
        $this->js_page = [
            '/assets/plugins/global/ace/ace.js',
            '/assets/plugins/global/ace/theme-monokai.js',
            '/assets/plugins/global/ace/mode-css.js',
            '/assets/plugins/global/ace/ext-language_tools.js',
            '/assets/js/view_logs_file.js',
            '/assets/js/block_config.js',
            '/assets/js/pages/template_modify.js',
            '/assets/js/pages/template_custom_css.js'
        ];

        $file_dir = PATH_TEMPLATE . 'assets' . DS . 'css' . DS . 'custom.css';
        $file_css = new File($file_dir, false);
        $exist_file = $file_css->exists();

        $content = null;
        if ($exist_file) {
            $content = $file_css->read();
            $file_css->close();
        }

        $file_path = TableRegistry::get('Utilities')->dirToPath($file_dir);

        $this->set('exist_file', $exist_file);
        $this->set('content', $content);
        $this->set('type', 'css');
        $this->set('file_path', $file_path);

        $this->set('path_menu', 'template');
        $this->set('title_for_layout', __d('admin', 'tuy_chinh_css'));        
    }

    public function jsCustom()
    {
        $this->js_page = [
            '/assets/plugins/global/ace/ace.js',
            '/assets/plugins/global/ace/theme-monokai.js',
            '/assets/plugins/global/ace/mode-javascript.js',
            '/assets/plugins/global/ace/ext-language_tools.js',
            '/assets/js/view_logs_file.js',
            '/assets/js/pages/template_modify.js',
            '/assets/js/pages/template_custom_javascript.js'
        ];

        $file_dir = PATH_TEMPLATE . 'assets' . DS . 'js' . DS . 'custom.js';
        $file_js = new File($file_dir, false);
        $exist_file = $file_js->exists();

        $content = null;
        if ($exist_file) {
            $content = $file_js->read();
            $file_js->close();            
        }

        $file_path = TableRegistry::get('Utilities')->dirToPath($file_dir);

        $this->set('exist_file', $exist_file);
        $this->set('content', $content);
        $this->set('type', 'js');
        $this->set('file_path', $file_path);

        $this->set('path_menu', 'template');
        $this->set('title_for_layout', __d('admin', 'tuy_chinh_javascript'));        
    }

    public function modifyView()
    {
        $this->js_page = [
            '/assets/js/view_logs_file.js',
            '/assets/js/pages/template_modify.js',
            '/assets/plugins/global/jstree/jstree.bundle.js',
            '/assets/plugins/global/ace/ace.js',
            '/assets/plugins/global/ace/theme-monokai.js',
            '/assets/plugins/global/ace/mode-javascript.js',
            '/assets/plugins/global/ace/mode-css.js',
            '/assets/plugins/global/ace/mode-php.js',
            '/assets/plugins/global/ace/mode-html.js',
            '/assets/plugins/global/ace/mode-smarty.js',
            '/assets/plugins/global/ace/ext-language_tools.js'
        ];
        $this->css_page = [
            '/assets/plugins/global/jstree/jstree.bundle.css',
        ];

        $this->set('path_menu', 'template');
        $this->set('title_for_layout', __d('admin', 'chinh_sua_giao_dien'));
    }

    public function readFolder()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $structure = $this->getStructFolder(PATH_TEMPLATE);
        try{
            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
                DATA => $structure
            ]);
        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }   
    }

    private function getStructFolder($dir = null)
    {
        if(empty($dir) || !file_exists($dir)) return [];

        $dir = rtrim($dir, DS);

        $path = TableRegistry::get('Utilities')->dirToPath($dir);

        $result = [];
        $direct_folder = new Folder($dir, false);
        $data = $direct_folder->read();        

        // read folder
        if (!empty($data[0])) {
            foreach ($data[0] as $key => $folder) {
                $result[] = [
                    'text' => $folder,
                    'icon' => 'fa fa-folder kt-font-success',
                    'children' => $this->getStructFolder($dir . DS . $folder),
                    'a_attr' => [
                        'data_folder_path' => $path . '/' . $folder,
                        'type' => 'folder'
                    ]
                ];
            }
        }

        // read file
        if (!empty($data[1])) {
            $white_list = ['css', 'js', 'tpl', 'json', 'po', 'eot', 'ttf', 'woff', 'woff2', 'png', 'gif', 'svg', 'jpg', 'jpeg', 'webp'];
            foreach ($data[1] as $key => $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));            
                if(!in_array($ext, $white_list)) continue;

                $icon = 'fa fa-file';
                switch ($ext) {
                    case 'php':
                        $icon = 'fa fa-file  kt-font-warning';
                        break;

                    case 'tpl':
                        $icon = 'fa fa-file  kt-font-info';
                        break;

                    case 'css':
                        $icon = 'fa fa-file  kt-font-danger';
                        break;

                    case 'js':
                        $icon = 'fa fa-file  kt-font-dark';
                        break;
                }

                $result[] = [
                    'text' => $file,
                    'icon' => $icon,
                    'a_attr' => [
                        'data_file_path' => $path . '/' . $file,
                        'data_file_ext' => $ext,
                        'type' => 'file'
                    ]
                ];
            }
        }

        return $result;
    }

    public function loadFile()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $path = !empty($data['path']) ? $data['path'] : null;
        if (!$this->getRequest()->is('post') || empty($path)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $dir = TableRegistry::get('Utilities')->pathToDir($path);
        if(empty($dir)) $this->responseJson([MESSAGE => __d('admin', 'tep_nay_khong_ton_tai')]);

        $file = new File($dir, false);
        if (!$file->exists()) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $white_list = ['css', 'js', 'tpl', 'json', 'po'];
        if(!in_array($ext, $white_list)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_doc_duoc_tep_nay')]);
        }

        $content = $file->read();
        $file->close();

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'), 
            DATA => $content
        ]);
    }

    public function downloadFile()
    {
        $this->layout = false;
        $this->autoRender = false;
        $params = $this->request->getQuery();
        $path = !empty($params['path']) ? $params['path'] : null;
        if(empty($path)) die;

        $dir = TableRegistry::get('Utilities')->pathToDir($path);
        if(empty($dir)) die;

        $file = new File($dir, false);
        if (!$file->exists()) die;

        $file_name = pathinfo($path, PATHINFO_BASENAME);
        $response = $this->response->withFile($dir, [
            'download' => true, 
            'name' => $file_name
        ]);

        return $response;
    }

    public function logFile()
    {
        $utilities = TableRegistry::get('Utilities');

        // params
        $params = $this->request->getQuery();
        $path_base64 = !empty($params['file']) ? $params['file'] : null;
        $path = !empty($path_base64) ? base64_decode($path_base64) : null;
        
        $dir_file = $utilities->pathToDir($path);
        $files = TableRegistry::get('Logs')->getListLogChangeFile($dir_file);

        // lấy ds log
        $logs = [];
        if(!empty($files)){
            $origin_name = basename($dir_file);
            foreach($files as $file){
                $filename = !empty($file['filename']) ? $file['filename'] : null;
                if(empty($filename)) continue;

                $time = str_replace($origin_name . '_', '', $filename);
                if(empty($time)) continue;

                $parse_label = $utilities->parseTimestampToLabelTime($time);
                $diff_time = !empty($parse_label['diff_time']) ? $parse_label['diff_time'] : null;
                $time_label =  !empty($parse_label['time']) ? $parse_label['time'] : null;                
                if(!in_array($diff_time, ['s', 'i', 'h']) || empty($time_label)) $time_label = date('H:i - d/m/Y', $time);

                $file['time'] = intval($time);
                $file['time_label'] = $time_label;
                $logs[] = $file;
            }
        }
        
        //lấy nội dung file gốc
        $content = !empty($dir_file) && file_exists($dir_file) ? @file_get_contents($dir_file) : '';

        // lấy nội dung của file log đầu tiên
        $first_log = !empty($logs) ? reset($logs) : [];
        $firts_dir_log = !empty($first_log['dir']) ? $first_log['dir'] : null;
        $firt_content = !empty($firts_dir_log) && file_exists($firts_dir_log) ? @file_get_contents($firts_dir_log) : '';

        $this->css_page = [
            '/assets/plugins/global/ace-diff/ace-diff.min.css'
        ];

        $this->js_page = [
            '/assets/plugins/global/ace-diff/ace_1.3.3.js',
            '/assets/plugins/global/ace-diff/ace-diff.min.js',
            '/assets/plugins/global/ace/ace.js',
            '/assets/plugins/global/ace/theme-monokai.js',
            '/assets/plugins/global/ace/mode-javascript.js',
            '/assets/plugins/global/ace/mode-css.js',
            '/assets/plugins/global/ace/mode-php.js',
            '/assets/plugins/global/ace/mode-html.js',
            '/assets/plugins/global/ace/mode-smarty.js',
            '/assets/plugins/global/ace/ext-language_tools.js',
            '/assets/js/pages/template_modify_log.js'
        ];        

        $this->set('filename', basename($dir_file));
        $this->set('path_file', $path);
        $this->set('logs', $logs);
        $this->set('content', $content);
        $this->set('firt_content', $firt_content);

        $this->set('path_menu', 'template');
        $this->set('title_for_layout', __d('admin', 'lich_su_thay_doi_cua_tep'));
    }

    public function getLogFile()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        //params
        $data = $this->getRequest()->getData();
        $path = !empty($data['path']) ? $data['path'] : null;

        $dir_file = TableRegistry::get('Utilities')->pathToDir($path);
        $content = !empty($dir_file) && file_exists($dir_file) ? @file_get_contents($dir_file) : '';

        die($content);
    }
}
