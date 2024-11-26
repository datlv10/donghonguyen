<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class CategoriesContentTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('categories_content');
        $this->setPrimaryKey('id');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
            
        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name')
            ->notEmptyString('name');

        $validator
            ->scalar('lang')
            ->maxLength('lang', 20)
            ->requirePresence('lang')
            ->notEmptyString('lang');
            
        return $validator;
    }

    public function getSeoInfoCategory($params = [])
    {
        $lang = !empty($params['lang']) ? $params['lang'] : null;
        $category_id = !empty($params['category_id']) ? intval($params['category_id']) : null;

        if(empty($lang) || empty($category_id)) return;

        $result = TableRegistry::get('CategoriesContent')->find()->where([
            'category_id' => $category_id,
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