<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

class BlockAdminHelper extends Helper
{
    public function getAllTypeMenuDropdown()
    {
        $templates_table = TableRegistry::get('Templates');
        $page_table = TableRegistry::get('TemplatesPage');

        $template = $templates_table->getTemplateDefault();

        $list_page = Hash::combine($page_table->find()->where([
            'TemplatesPage.template_code' => !empty($template['code']) ? $template['code'] : null,
            'TemplatesPage.page_type' => PAGE
        ])->toArray(), '{n}.code', '{n}.name');

        $result = [];

        
        $result[__d('admin', 'trang')] = $list_page;
        $result[__d('admin', 'danh_muc')] = [
            CATEGORY_PRODUCT => __d('admin', 'danh_muc_san_pham'),
            CATEGORY_ARTICLE => __d('admin', 'danh_muc_bai_viet')
        ];
        
        $result[__d('admin', 'khac')] = [
            CUSTOM => __d('admin', 'tuy_bien'),
        ];

        return $result;
    }

    public function getAllTypeSubMenuDropdown()
    {
        $result = [];

        $result[__d('admin', 'danh_muc')] = [
            CATEGORY_PRODUCT => __d('admin', 'danh_muc_san_pham'),
            CATEGORY_ARTICLE => __d('admin', 'danh_muc_bai_viet')
        ];
        
        $result[__d('admin', 'khac')] = [
            CUSTOM => __d('admin', 'tuy_bien'),
        ];

        return $result;
    }

    public function getListViewBlock($type_block = null, $view = null)
    {
        $result = [];

        if(empty($type_block)) return $result;

        if(!empty($type_block) && $type_block != HTML){   
            $path_template = TableRegistry::get('Templates')->getPathTemplate();
            $path_view = $path_template . BLOCK . DS . $type_block;
            if(empty($path_template) || empty($path_view)) return null;

            $folder = new Folder($path_view, false);
            $list_files = $folder->find('.*\.tpl', true);

            if(!empty($list_files)){
                foreach ($list_files as $k => $file) {
                    switch ($view) {
                        case 'view':
                            if(strpos($file, 'view') > -1){
                                $result[$file] = $file;
                            }
                            break;

                        case 'sub':
                            if(strpos($file, 'sub') > -1){
                                $result[$file] = $file;
                            }
                            break;
                        
                        default:
                            $result[$file] = $file;
                            break;
                    }
                }
            }
        }

        return $result;
    }
}
