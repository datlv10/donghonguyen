<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class LinksTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('links');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);

        $this->hasOne('Products', [
            'className' => 'Publishing.Products',
            'foreignKey' => 'id',
            'bindingKey' => 'foreign_id',
            'joinType' => 'INNER',
            'conditions' => [
                'Products.deleted' => 0
            ],
            'propertyName' => 'Products'
        ]);

        $this->hasOne('Articles', [
            'className' => 'Publishing.Articles',
            'foreignKey' => 'id',
            'bindingKey' => 'foreign_id',
            'joinType' => 'INNER',
            'conditions' => [
                'Articles.deleted' => 0
            ],
            'propertyName' => 'Articles'
        ]);

        $this->hasOne('Categories', [
            'className' => 'Publishing.Categories',
            'foreignKey' => 'id',
            'bindingKey' => 'foreign_id',
            'joinType' => 'INNER',
            'conditions' => [
                'Categories.deleted' => 0
            ],
            'propertyName' => 'Categories'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
            
        $validator
            ->scalar('type')
            ->maxLength('type', 20)
            ->requirePresence('type')
            ->notEmptyString('type');

        $validator
            ->scalar('url')
            ->maxLength('url', 255)
            ->requirePresence('url')
            ->notEmptyString('url');

        $validator
            ->scalar('lang')
            ->maxLength('lang', 20)
            ->requirePresence('lang')
            ->notEmptyString('lang');

        return $validator;
    }

    public function getLinkByUrl($url = null, $params = [])
    {
        if(empty($url)) return [];

        $type = !empty($params['type']) ? $params['type'] : null;
        $language = !empty($params[LANGUAGE]) ? $params[LANGUAGE] : null;
        
        $where = [
            'deleted' => 0,
            'url' => trim($url),
        ];

        if(!empty($type)){
            $where['type'] = $type;
        }

        if(!empty($language)){
            $where['lang'] = $language;
        }

        $result = $this->find()->where($where)->first();

        return $result;
    }

    public function getLanguageByUrl($url = null)
    {
        if(empty($url)) return null;

        $link = $this->find()->where([
            'deleted' => 0,
            'url' => trim($url)
        ])->select(['lang'])->first();

        if(empty($link)){
            $template = TableRegistry::get('Templates')->getTemplateDefault();
            $template_code = !empty($template['code']) ? $template['code'] : null;
            if(empty($template_code)) return null;

            $link = TableRegistry::get('TemplatesPageContent')->find()->where([
                'template_code' => $template_code,
                'url' => trim($url)
            ])->select(['lang'])->first();        
        }

        return !empty($link['lang']) ? $link['lang'] : null;
    }

    public function checkExist($url = null, $id = null)
    {
        if(empty($url)) return false;

        $where = [
            'deleted' => 0,
            'url' => trim($url),
        ];

        if(!empty($id)){
            $where['id <>'] = $id;
        }

        $link = $this->find()->where($where)->first();
        return !empty($link->id) ? true : false;
    }

    public function checkExistUrl($url = null, $foreign_id = null, $type = null)
    {
        if(empty($url)) return false;

        $where = [
            'deleted' => 0,
            'url' => trim($url),
        ];

        if(!empty($foreign_id)){
            $where['foreign_id !='] = $foreign_id;
        }

        if(!empty($type)){
            $where['type'] = $type;
        }

        $link = $this->find()->where($where)->first();
        return !empty($link->id) ? true : false;
    }

    public function getInfoLink($params = [])
    {
        $foreign_id = !empty($params['foreign_id']) ? intval($params['foreign_id']) : null;
        $lang = !empty($params['lang']) ? $params['lang'] : null;
        $type = !empty($params['type']) ? $params['type'] : null;

        if(empty($foreign_id) || empty($lang)) return [];
        

        $where = [
            'deleted' => 0,
            'lang' => $lang,
            'foreign_id' => intval($foreign_id),
        ];

        if(!empty($type)){
            $where['type'] = $type;
        }

        $link = $this->find()->where($where)->first();
        return $link;
    }

    public function getUrlUnique($url = null, $index = 0)
    {   
        if(empty($index)) $index = 0;

        $url_check = $url;
        if($index > 0){
            $url_check = $url . '-'. $index;
        }
        
        if($index >= 100){
            return $url_check;
        }

        $check = $this->checkExist($url_check);

        if($check){
            $index ++;
            $url_check = $this->getUrlUnique($url, $index);
        }
        return $url_check;
    }
}