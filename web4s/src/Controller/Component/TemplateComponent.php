<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

class TemplateComponent extends Component
{
    public $controller = null;
    public $components = ['PaginatorExtend', 'Utilities', 'Block'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
  
    
    protected function getDataForTemplate()
    {
        // get structure of page
        $structure_page = $this->getStructurePage(PAGE_CODE, DEVICE, true, PAGE_RECORD_ID);
        $blocks = !empty($structure_page['blocks']) ? $structure_page['blocks'] : [];
        $structure = !empty($structure_page['structure']) ? $structure_page['structure'] : [];
        $cache_page = !empty($structure_page['cache']) ? true :  false;

        // replace structure of desktop to mobile
        if(DEVICE == 1){
            $get_layout = $get_content = false;
            if(empty($structure['header']) && empty($structure['footer'])){
                $get_layout = true;
            }

            if(empty($structure['content'])){
                $get_content = true;
            }

            $structure_desktop = [];
            if($get_layout || $get_content){
                $structure_desktop = $this->getStructurePage(PAGE_CODE, 0, $get_layout, PAGE_RECORD_ID);
                $cache_page = !empty($structure_desktop['cache']) ? true :  false;
            }

            if($get_layout){
                $structure['header'] = !empty($structure_desktop['structure']['header']) ? $structure_desktop['structure']['header'] : [];
                $structure['footer'] = !empty($structure_desktop['structure']['footer']) ? $structure_desktop['structure']['footer'] : [];
                $blocks = array_merge($blocks, $structure_desktop['blocks']);
            }

            if($get_content){
                $structure['content'] = !empty($structure_desktop['structure']['content']) ? $structure_desktop['structure']['content'] : [];
                $blocks = array_merge($blocks, $structure_desktop['blocks']);
            }
        }

        return [
            'cache' => $cache_page,
            'structure' => $structure,
            'blocks' => $blocks
        ];
    }
    
    protected function getStructurePage($page_code = null, $device = 0, $get_layout = false, $id_record = null)
    {
        if(empty($page_code)) return [];
        $structure_page = TableRegistry::get('TemplatesRow')->getStructureRowOfPage($page_code, $device, $get_layout, $id_record);
        $list_block = !empty($structure_page['blocks']) ? $structure_page['blocks'] : [];

        // get list block used in website
        $blocks = [];        
        $cache = true;
        if(!empty($list_block)){
            foreach ($list_block as $block_code => $block_info) {
                $block_type = !empty($block_info['type']) ? $block_info['type'] : '';
                $config = !empty($block_info['config']) ? $block_info['config'] : [];

                if(empty($block_info) || empty($block_info['status']) ||empty($block_type)) continue;

                // generate view of block HTML
                if($block_type == HTML){
                    $view_file = new File(PATH_TEMPLATE . BLOCK . DS . HTML . DS . $block_code . '.tpl', false);


                    if(!$view_file->exists()){                        
                        $html_content = !empty($config['html_content']) ? '{strip}' . $config['html_content'] . '{/strip}' : '';
                        $view_file->write($html_content, 'w', true);
                    }
                    $view_file->close();
     
                }

                // get data of block
                $block_info['data_block'] = $this->Block->getDataBlock($block_info);

                // check block use cache                
                $cache_block = !empty($config['cache']) ? true : false;
                if(!$cache_block){
                    $cache = false;
                }

                // push block info to result
                $blocks[$block_code] = $block_info;
            }
        }

        return [
            'cache' => $cache,
            'structure' => !empty($structure_page['structure']) ? $structure_page['structure'] : [],
            'blocks' => $blocks
        ];
    }
}
