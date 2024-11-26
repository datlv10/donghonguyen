<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class ProductsPartnerQuantityTable extends Table
{

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('products_partner_quantity');

        $this->setPrimaryKey('id');
        
    }

}