<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class LanguageHelper extends Helper
{   
    /** Lấy danh sách ngôn ngữ
     * 
     * 
     * {assign var = data value = $this->Language->getList()}
     * 
    */
    public function getList()
    {
        return TableRegistry::get('Languages')->getList();
    }
}
