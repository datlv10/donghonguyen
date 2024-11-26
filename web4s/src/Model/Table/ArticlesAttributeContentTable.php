<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class ArticlesAttributeContentTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('articles_attribute_content');
        $this->setPrimaryKey('id');

    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('article_attribute_id')
            ->allowEmptyString('article_attribute_id', 'create');

        $validator
            ->scalar('lang')
            ->maxLength('lang', 20)
            ->notEmptyString('lang');

        return $validator;
    }
}