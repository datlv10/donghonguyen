<?php

namespace Admin\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\Utility\Security;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class MyFilemanagerController extends AppController {

    public $base_path = null;
    public $default_dir = null;

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->base_path = '/media';
        $this->default_dir = SOURCE_DOMAIN . DS . 'templates' . DS . CODE_TEMPLATE . DS . 'assets';
    }

    protected function responseData($params = []) 
    {
        $result = TableRegistry::get('FileManagerApp')->responseData($params);
        exit(json_encode($result));
    }

	public function index()
	{
        $this->viewBuilder()->enableAutoLayout(false);
        
        // đọc thông tin từ params url
        $params = $this->request->getQueryParams();

        $token = !empty($params['token']) ? $params['token'] : null;
        $field_id = !empty($params['field_id']) ? $params['field_id'] : null;
        $cross_domain = !empty($params['cross_domain']) ? $params['cross_domain'] : null;
        $multiple = !empty($params['multiple']) ? true : false;
        $type_file = !empty($params['type_file']) && in_array($params['type_file'], [IMAGE, DOCUMENT, VIDEO, AUDIO, ARCHIVE]) ? $params['type_file'] : null;

        // thêm dấu chấm trc extentions
        $extensions = [];
        foreach(LIST_EXTENSIONS as $extension){
            $extensions[] = '.'. $extension;
        }
        
        $this->set('extensions', $extensions);
        $this->set('max_file_size', MAX_FILE_SIZE);

        $this->set('token', $token);
        $this->set('cross_domain', $cross_domain);
        $this->set('field_id', $field_id);
        $this->set('multiple', $multiple);
        $this->set('type_file', $type_file);
        $this->set('base_path', $this->base_path);    
    }
    
    public function files()
    {
        $this->autoRender = false;        
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        if (!$this->getRequest()->is('post')) {
            $this->responseData([MESSAGE => __d('filemanager', 'du_lieu_khong_hop_le')]);
        }

        $file_manager = TableRegistry::get('FileManager');

        $dir = !empty($data['path']) ? $file_manager->_pathUrlToDir($data['path']) : $this->default_dir . DS . MEDIA;
        $page = !empty($data[PAGE]) ? $data[PAGE] : 1;
        
        $filter_keyword = !empty($data[FILTER_KEYWORD]) ? $data[FILTER_KEYWORD] : null;
        $filter_type = !empty($data[FILTER_TYPE]) ? $data[FILTER_TYPE] : null;

        $sort_type = !empty($data[SORT_TYPE]) ? $data[SORT_TYPE] : ASC;
        $sort_field = !empty($data[SORT_FIELD]) ? $data[SORT_FIELD] : null;

        $options = [
            LIMIT => 100, 
            PAGE => $page,
            FILTER => [
                KEYWORD => $filter_keyword,
                TYPE => $filter_type
            ],
            SORT => [
                TYPE => $sort_type,
                FIELD => $sort_field
            ]
        ];

        $result = $file_manager->getListFilesInDir($dir, $options);
        exit(json_encode($result));
    }
    
    public function createFolder()
    {
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $path = !empty($data['path']) ? trim($data['path']) : null;
        $name = !empty($data['name']) ? trim($data['name']) : null;

        if (!$this->getRequest()->is('post')) {
            $this->responseData([MESSAGE => __d('filemanager', 'du_lieu_khong_hop_le')]);
        }

        $result = TableRegistry::get('FileManager')->createFolder($name, $path);

        exit(!empty($result) ? json_encode($result) : '');
    }

    public function rename()
    {
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseData([MESSAGE => __d('filemanager', 'du_lieu_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $type = !empty($data['type']) ? trim($data['type']) : null;
        $name = !empty($data['name']) ? trim($data['name']) : null;
        $old_name = !empty($data['old_name']) ? trim($data['old_name']) : null;
        $path = !empty($data['path']) ? trim($data['path']) : null;
        
        $result = TableRegistry::get('FileManager')->rename($type, $name, $old_name, $path);
        exit(!empty($result) ? json_encode($result) : '');
    }

    public function delete()
    {
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseData([MESSAGE => __d('filemanager', 'du_lieu_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $file = !empty($data['file']) ? $data['file'] : null;
        
        $result = TableRegistry::get('FileManager')->deleteFile($file);
        exit(!empty($result) ? json_encode($result) : '');
    }

    public function paste()
    {
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseData([MESSAGE => __d('filemanager', 'du_lieu_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $file = !empty($data['file']) ? $data['file'] : null;
        $path = !empty($data['path']) ? $data['path'] : null;
        $cut = isset($data['cut']) ? boolval($data['cut']) : false;

        $result = TableRegistry::get('FileManager')->pasteFileAndDir($file, $path, $cut);

        exit(!empty($result) ? json_encode($result) : '');
    }

    public function upload()
    {
        $this->autoRender = false;
        if (!$this->getRequest()->is('post')) {
            $this->responseData([MESSAGE => __d('filemanager', 'du_lieu_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        
        $file = !empty($data['file']) ? $data['file'] : null;
        $path = !empty($data['path']) ? $data['path'] : null;

        $result = TableRegistry::get('FileManagerUpload')->upload($file, $path);
        exit(!empty($result) ? json_encode($result) : '');
    }

    public function navigation()
    {
        $this->autoRender = false;
        if (!$this->getRequest()->is('post')) {
            $this->responseData([MESSAGE => __d('filemanager', 'du_lieu_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];        
        $path = !empty($data['path']) ? $data['path'] : null;
        
        $result = TableRegistry::get('FileManager')->getDataNavigation($path);
        exit(!empty($result) ? json_encode($result) : '');
    }











}