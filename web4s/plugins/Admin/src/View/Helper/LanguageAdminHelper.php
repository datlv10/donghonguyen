<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class LanguageAdminHelper extends Helper
{   
    public function getList()
    {
        return TableRegistry::get('Languages')->getList();
    }

    public function checkUseMultipleLanguage()
    {
        return TableRegistry::get('Languages')->checkUseMultipleLanguage();
    }

    public function getDefaultLanguage()
    {
        return TableRegistry::get('Languages')->getDefaultLanguage();    
    }
}
