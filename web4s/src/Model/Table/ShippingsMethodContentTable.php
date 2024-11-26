<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class ShippingsMethodContentTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('shippings_method_content');
        $this->setPrimaryKey('id');
    }

}