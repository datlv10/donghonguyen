<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\Utility\Hash;
use Cake\Filesystem\File;

class SystemController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function getTinymceTemplates()
    {
        $templates_file_json = ROOT . '/plugins/Admin/webroot/assets/plugins/custom/tinymce6/templates/templates.json';
        $file_config = new File($templates_file_json, false);

        if(empty($file_config->path)) die(json_encode([]));
        $file_content = $file_config->read();

        if(!$this->loadComponent('Utilities')->isJson($file_content)) die(json_encode([]));

        $files = json_decode($file_content, true);

        $result = [];

        foreach($files as $item){
            $title = !empty($item['title']) ? trim($item['title']) : null;
            $description = !empty($item['description']) ? trim($item['description']) : null;
            $file = !empty($item['file']) ? trim($item['file']) : null;

            $url = ADMIN_PATH . '/assets/plugins/custom/tinymce6/templates/' . $file . '?' . time();
            if(empty($title) || empty($file)) continue;

            $result[] = [
                'title' => $title,
                'description' => $description,
                'url' => $url
            ];
        }

        die(json_encode($result));
    }
}