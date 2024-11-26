<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

class PrintTemplateAdminHelper extends Helper
{   

    public function getListPrintTemplates()
    {
        $result = Hash::combine(TableRegistry::get('PrintTemplates')->find()->select(['code', 'name'])->toArray(), '{n}.code', '{n}.name');
        return !empty($result) ? $result : [];
    }

    public function getListFileViewPrint()
    {
    	$path_template = TableRegistry::get('Templates')->getPathTemplate();
        $folder = new Folder($path_template . DS . 'print', false);
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
