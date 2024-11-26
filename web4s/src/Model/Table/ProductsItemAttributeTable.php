<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class ProductsItemAttributeTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('products_item_attribute');
        $this->setPrimaryKey('id');

        $this->hasOne('Attributes', [
            'className' => 'Publishing.Attributes',
            'foreignKey' => 'id',
            'bindingKey' => 'attribute_id',
            'joinType' => 'INNER',
            'propertyName' => 'Attributes'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

}