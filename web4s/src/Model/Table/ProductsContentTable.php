<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class ProductsContentTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('products_content');
        $this->setPrimaryKey('id');

    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('product_id')
            ->requirePresence('product_id', 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name')
            ->notEmptyString('name');

        $validator
            ->scalar('lang')
            ->maxLength('lang', 20)
            ->notEmptyString('lang');

        return $validator;
    }
    
    public function getSeoInfoProduct($params = [])
    {
        $lang = !empty($params['lang']) ? $params['lang'] : null;
        $product_id = !empty($params['product_id']) ? intval($params['product_id']) : null;

        if(empty($lang) || empty($product_id)) return [];

        $result = TableRegistry::get('ProductsContent')->find()->where([
            'product_id' => $product_id,
            'lang' => $lang
        ])->select([
            'seo_title', 
            'seo_description',
            'seo_keyword'
        ])->first();

        if(empty($result['seo_title']) && empty($result['seo_title']) && empty($result['seo_title'])) return [];
        
        return $result;
    }
}