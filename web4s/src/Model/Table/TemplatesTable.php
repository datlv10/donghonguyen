<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class TemplatesTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('templates');
        $this->setPrimaryKey('id');
    }

    public function getTemplateDefault()
    {
        $cache_key = TEMPLATE . '_default';

        $result = Cache::read($cache_key);
        if(is_null($result)){

            $result = $this->find()->where(['Templates.is_default' => 1])->first();

            Cache::write($cache_key, !empty($result) ? $result : []);
        }    	
    	return $result;
    }

    public function getPathTemplate()
    {
        $template = $this->getTemplateDefault();
        $template_code = !empty($template['code']) ? $template['code'] : null;
        if(empty($template_code)) return null;
        
        return SOURCE_DOMAIN  . DS . 'templates' . DS . $template_code . DS;
    }
    
}