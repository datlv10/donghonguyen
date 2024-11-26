<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class MobileTemplateRowTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('mobile_template_row');
        $this->setPrimaryKey('id');
    }

    public function getStructureRowOfPage($page_code = null, $id_record = null)
    {
    	if(empty($page_code)) return [];

        $cache_key = MOBILE_PAGE . '_' . $page_code;
        if(!empty($id_record)){
            $cache_key = MOBILE_PAGE . '_' . $page_code . '_' . $id_record;
        }
        
        $result = Cache::read($cache_key);
        if(!is_null($result)) return $result;

        // get info page
        $page_info = TableRegistry::get('MobileTemplatePage')->getInfoPage(['code' => $page_code]);
        if(empty($page_info)) [];
        $result = $blocks = [];

        // get structure of page
        $this->getStructure($result, $blocks, $page_code);

        Cache::write($cache_key, $result);
    	
    	return $result;
    }

    private function getStructure(&$structure = [], &$blocks = [], $page_code = null)
    {
    	// get list row of page
    	$where = [
            'MobileTemplateRow.template_code' => CODE_MOBILE_TEMPLATE,
            'MobileTemplateRow.page_code' => $page_code
        ];

    	$rows = TableRegistry::get('MobileTemplateRow')->find()->where($where)->order('MobileTemplateRow.id ASC')->toArray();    
		if(empty($rows)) return [];

        $block_table = TableRegistry::get('MobileTemplateBlock');

        $structure = $blocks = [];
        foreach ($rows as $row) {
            $blocks = !empty($row['block_code']) ? array_filter(explode(',', $row['block_code'])) : null;
            if(empty($blocks)) continue;
            foreach($blocks as $block_code){
                $block_info = [];
                if(empty($blocks[$block_code])){
                    $block_info = $block_table->getInfoBlock($block_code);
                    $blocks[$block_code] = $block_info;
                }else{
                    $block_info = $blocks[$block_code];
                }

                $structure[] = $block_info;
            }                    
        }

        return $structure;
    }


    public function getListRowsContainBlock($block_code = null)
    {
        if(empty($block_code)) return [];

        $result = TableRegistry::get('MobileTemplateRow')->find()->where([
            'MobileTemplateRow.template_code' => CODE_MOBILE_TEMPLATE,
            'MobileTemplateRow.block_code LIKE' => '%' . $block_code . '%'
        ])->select([
            'MobileTemplateRow.id', 
            'MobileTemplateRow.page_code', 
            'MobileTemplateRow.block_code'
        ])->toArray();

        return $result;
    }
}