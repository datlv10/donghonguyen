<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class TemplatesRowTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('templates_row');
        $this->setPrimaryKey('id');

        $this->hasMany('TemplatesColumn', [
            'className' => 'Publishing.TemplatesColumn',
            'foreignKey' => 'row_code',
			'bindingKey' => 'code',
            'joinType' => 'LEFT',
            'conditions' => [
                'TemplatesColumn.template_code' => CODE_TEMPLATE
            ],
            'sort' => ['TemplatesColumn.id' => 'ASC'],
            'propertyName' => 'TemplatesColumn'
        ]);
    }

    public function getStructureRowOfPage($page_code = null, $device = 0, $get_layout = false, $id_record = null)
    {
    	if(empty($page_code)) return [];

        $cache_key = PAGE . '_' . $page_code . '_' . $device . '_' . $get_layout;
        if(!empty($id_record)){
            $cache_key = PAGE . '_' . $page_code . '_' . $id_record . '_' . $device . '_' . $get_layout;
        }
        
        $result = Cache::read($cache_key);
        if(!is_null($result)) return $result;

        $structure = [
            HEADER => [],
            CONTENT => [],
            FOOTER => []
        ];

        $blocks = [];
        $is_layout = false;

        // get info page
        $page_info = TableRegistry::get('TemplatesPage')->getInfoPage(['code' => $page_code]);
        if(empty($page_info)){
            return [
                'structure' => $structure,
                'blocks' => []
            ];
        }

        if(!empty($page_info['type'] == LAYOUT)){
            $is_layout = true;
        }

        // get structure of layout in page
        $layout_code = !empty($page_info['layout_code']) ? $page_info['layout_code'] : null;
        if(!empty($layout_code)){
            if($get_layout){
                $this->getStructure($structure, $blocks, true, $layout_code, $device);
            }else{
                unset($structure[HEADER]);
                unset($structure[FOOTER]);
            }           
        }

        // get structure of page
        $this->getStructure($structure, $blocks, false, $page_code, $device);

        if($is_layout){
            unset($structure[CONTENT]);
        }

        $result = [
            'cache' => false,
            'layout_code' => $layout_code,
            'structure' => $structure,
            'blocks' => $blocks                
        ];

        Cache::write($cache_key, $result);
    	
    	return $result;
    }

    private function getStructure(&$structure = [], &$blocks = [], $is_layout = false, $page_code = null, $device = 0)
    {
    	// get list row of page
    	$where = [
            'TemplatesRow.template_code' => CODE_TEMPLATE,
            'TemplatesRow.page_code' => $page_code,
            'TemplatesRow.device' => $device
        ];

    	$data_row = TableRegistry::get('TemplatesRow')->find()->contain(['TemplatesColumn'])
        ->where($where)
        ->order('TemplatesRow.id ASC')
        ->toArray();

        $block_table = TableRegistry::get('TemplatesBlock');

		// format data before output
		if(!empty($data_row)){
			foreach ($data_row as $row) {
				$type = !empty($row['type']) ? $row['type'] : null;
				if(!isset($structure[$type])) continue;
                			
				$columns = [];
				if(!empty($row['TemplatesColumn'])){
					foreach ($row['TemplatesColumn'] as $column) {
						$list_block_code = !empty($column['block_code']) ? explode(',', $column['block_code']) : [];
						if(!empty($list_block_code)){
							foreach ($list_block_code as $block_code) {
								if(!in_array($block_code, $blocks)){
                                    $block_info = $block_table->getInfoBlock($block_code);

                                    // merge dữ liệu mở rộng từ field 'normal_data_extend' => 'data_extend'
                                    $data_extend = !empty($block_info['data_extend']) ? $block_info['data_extend'] : [];
                                    $normal_data_extend = !empty($block_info['normal_data_extend']) ? $block_info['normal_data_extend'] : [];
                                    $locales = !empty($normal_data_extend['locale']) ? $normal_data_extend['locale'] : [];
                                    // $normal = !empty($normal_data_extend['normal']) ? $normal_data_extend['normal'] : [];

                                    if(!empty($locales) && is_array($locales)){
                                        foreach($locales as $lang => $locale){
                                            if(empty($lang) || empty($locale) || !is_array($locale)) continue;
                                            if(empty($data_extend['locale'][$lang])) $data_extend['locale'][$lang] = [];

                                            foreach($locale as $key => $value){
                                                if(empty($key)) continue;

                                                $data_extend['locale'][$lang][$key] = $value;
                                            }
                                        }
                                    }

                                    // if(!empty($normal) && is_array($normal)){
                                    //     foreach($normal as $key => $value){
                                    //         if(is_null($key) || trim($key) == '') continue;
                                    //         $data_extend[$key] = $value;
                                    //     }
                                    // }

                                    
                                    $block_info['data_extend'] = $data_extend;
                                    $blocks[$block_code] = $block_info;
								}
							}
						}

						$columns[] = [
							'id' => !empty($column['id']) ? intval($column['id']) : null,
							'code' => !empty($column['code']) ? $column['code'] : null,
							'is_layout' => $is_layout,
							'row_code' => !empty($column['row_code']) ? $column['row_code'] : null,
							'column_value' => !empty($column['column_value']) ? intval($column['column_value']) : null,
							'blocks' => $list_block_code
						];
					}
				}

				$structure[$type][] = [
					'id' => !empty($row['id']) ? intval($row['id']) : null,
					'code' => !empty($row['code']) ? $row['code'] : null,
					'config' => !empty($row['config']) ? json_decode($row['config'], true) : [],
					'columns' => $columns
				];
			}
		}
    }
}