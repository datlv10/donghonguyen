<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class ArticlesContentTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('articles_content');
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

    public function getSeoInfoArticle($params = [])
    {
        $lang = !empty($params['lang']) ? $params['lang'] : null;
        $article_id = !empty($params['article_id']) ? intval($params['article_id']) : null;

        if(empty($lang) || empty($article_id)) return;

        $result = TableRegistry::get('ArticlesContent')->find()->where([
            'article_id' => $article_id,
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