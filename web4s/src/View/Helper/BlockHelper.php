<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

class BlockHelper extends Helper
{
    /** Dịch đa ngôn ngữ
     * 
     * $attribute_code (*): mã thuộc tính(string)
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     *
     * 
     * {$this->Block->getLocale('tieu_de', $data_extend)}
     * 
    */
    public function getLocale($key = null, $data_extend = [])
    {
        if(empty($data_extend['locale'][LANGUAGE][$key])) return $key;
        return $data_extend['locale'][LANGUAGE][$key];
    }

    /** Kiểm tra view của block có tồn tại
     * 
     * $block_type (*): loại block(string)
     * $view (*): view của block(string) ví dụ: view.tpl
     *
     * 
     * $this->Block->checkViewExist('product', 'view.tpl')
     * 
    */
    public function checkViewExist($block_type = null, $view = null)
    {
        $path_view = PATH_TEMPLATE . BLOCK . DS . $block_type . DS . $view;
        $file = new File($path_view, false);
        if(!$file->exists()){
            return false;
        }
        return true;
    }

}
