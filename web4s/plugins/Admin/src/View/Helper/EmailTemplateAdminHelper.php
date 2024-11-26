<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

class EmailTemplateAdminHelper extends Helper
{   

    public function getListEmailTemplates()
    {
        $result = Hash::combine(TableRegistry::get('EmailTemplates')->find()->select(['code', 'name'])->toArray(), '{n}.code', '{n}.name');
        return !empty($result) ? $result : [];
    }

    public function getListFileViewEmail()
    {
    	$path_template = TableRegistry::get('Templates')->getPathTemplate();
        $folder = new Folder($path_template . DS . 'email' . DS . 'html', false);
        $files = $folder->find('.*\.tpl', true);

        $result = [];
        if(!empty($files)){
            foreach ($files as $key => $file) {
                $result[$file] = $file;
            }
        }
        return $result;
    }
}
