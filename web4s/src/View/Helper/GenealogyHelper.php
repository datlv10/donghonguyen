<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;

class GenealogyHelper extends Helper
{
    public function getGenealogies($params = []) 
    {
        return TableRegistry::get('Genealogies')->getListTreeGenealogies();
    }
}
