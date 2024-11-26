<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;

class CategoriesArticleTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('categories_article');
        $this->setPrimaryKey('id');

    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function getListArticleIds($category_id = null)
    {
        if(empty($category_id)) return [];
        $table = TableRegistry::get('CategoriesArticle');


        $article_ids = $table->find()->where(['category_id' => $category_id])->select(['article_id'])->toArray();
        $result = !empty($article_ids) ? Hash::extract($article_ids, '{n}.article_id') : [];
        return $result;
    }
}