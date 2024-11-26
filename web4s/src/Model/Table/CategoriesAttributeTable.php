<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class CategoriesAttributeTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('categories_attribute');
        $this->setPrimaryKey('id');
    }

}