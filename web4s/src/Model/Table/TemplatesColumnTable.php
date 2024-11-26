<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class TemplatesColumnTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('templates_column');
        $this->setPrimaryKey('id');
    }

    public function getListRowsContainBlock($block_code = null)
    {
    	if(empty($block_code)) return [];

    	$result = TableRegistry::get('TemplatesColumn')->find()->where([
    		'TemplatesColumn.template_code' => CODE_TEMPLATE,
    		'TemplatesColumn.block_code LIKE' => '%' . $block_code . '%'
    	])->select([
    		'TemplatesColumn.id', 
    		'TemplatesColumn.page_code', 
    		'TemplatesColumn.row_code'
    	])->toArray();

    	return $result;
    }
}