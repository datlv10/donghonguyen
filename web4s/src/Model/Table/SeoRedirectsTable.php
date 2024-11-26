<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Cache\Cache;
use Cake\Utility\Hash;
use Cake\Utility\Text;

class SeoRedirectsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('seo_redirects');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->belongsTo('User', [
            'className' => 'Users',
            'foreignKey' => 'created_by',
            'propertyName' => 'User'
        ]);

    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function queryListSeoRedirects($params = []) 
    {
        $table = TableRegistry::get('SeoRedirects');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_user = !empty($params['get_user']) ? $params['get_user'] : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];        
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $ids = !empty($filter['ids']) ? $filter['ids'] : [];

        $fields = ['SeoRedirects.id', 'SeoRedirects.url', 'SeoRedirects.redirect'];
        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['SeoRedirects.id', 'SeoRedirects.url', 'SeoRedirects.redirect', 'SeoRedirects.created_by', 'SeoRedirects.created', 'SeoRedirects.updated', 'SeoRedirects.status'];
            break;

            case LIST_INFO:
                $fields = ['SeoRedirects.id', 'SeoRedirects.url'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['SeoRedirects.id', 'SeoRedirects.url', 'SeoRedirects.redirect', 'SeoRedirects.created_by', 'SeoRedirects.created', 'SeoRedirects.status'];
            break;
        }

        $sort_string = 'SeoRedirects.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'SeoRedirects.id '. $sort_type;
                break;

                case 'status':
                    $sort_string = 'SeoRedirects.status '. $sort_type .', SeoRedirects.id DESC';
                break;

                case 'created':
                    $sort_string = 'SeoRedirects.created '. $sort_type .', SeoRedirects.id DESC';
                break;

                case 'updated':
                    $sort_string = 'SeoRedirects.updated '. $sort_type .', SeoRedirects.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'SeoRedirects.created_by '. $sort_type .', SeoRedirects.id DESC';
                break;             
            }
        }

        // filter by conditions
        $where = [];    

        if(!empty($keyword)){
            $where['SeoRedirects.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($ids)){
            $where['SeoRedirects.id IN'] = $ids;
        }

        if(!is_null($status)){
            $where['SeoRedirects.status'] = $status;
        }
     
        $contain = [];
        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->group('SeoRedirects.id')->order($sort_string);
    }

    public function getRedirectUrl($url = null)
    {
        if(empty($url)) return null;

        $result = TableRegistry::get('SeoRedirects')->find()->where([
            'url' => $url,
            'status' => 1
        ])->select(['redirect'])->first();

        if(empty($result)) return null;
        
        return !empty($result['redirect']) ? $result['redirect'] : '';
    }
}