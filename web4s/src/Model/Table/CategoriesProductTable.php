<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;

class CategoriesProductTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('categories_product');
        $this->setPrimaryKey('id');

    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function getListProductIds($category_id = null)
    {
        if(empty($category_id)) return [];
        $table = TableRegistry::get('CategoriesProduct');

        $product_ids = $table->find()->where(['category_id' => $category_id])->select(['product_id'])->toArray();
        $result = !empty($product_ids) ? Hash::extract($product_ids, '{n}.product_id') : [];
        return $result;
    }

    public function getListCategoryIds($product_id = null)
    {
        if(empty($product_id)) return [];

        $table = TableRegistry::get('CategoriesProduct');

        $category_ids = $table->find()->where(['product_id' => $product_id])->select(['category_id'])->toArray();

        $result = !empty($category_ids) ? Hash::extract($category_ids, '{n}.category_id') : [];
        return $result;
    }
}