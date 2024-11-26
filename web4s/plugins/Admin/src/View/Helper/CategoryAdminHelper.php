<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Collection\Collection;

class CategoryAdminHelper extends Helper
{   

    public function getListCategoriesForDropdown($params = [])
    {
        $type = !empty($params[TYPE]) ? $params[TYPE] : null;
        if(empty($type) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) return [];

        $lang = !empty($params[LANG]) ? $params[LANG] : null;
        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();        

        $categories = TableRegistry::get('Categories')->getAll($type, $lang);
        if(empty($categories)) return [];        

        // danh mục đang hoạt động
        $collection = Collection($categories)->filter(function ($item, $key, $iterator) {
            return $item['status'] == 1;
        });
        
        // sắp xếp danh mục
        $collection = $collection->sortBy('position', SORT_DESC);

        // phân cha con cho danh mục
        $categories = $collection->nest('id', 'parent_id');
        $result = [];

        // format 
        if(!empty($categories)){
            $result = $this->parseDataCategoryDropdownCollecttion($categories, 0);
        }

        return $result;
    }

    private function parseDataCategoryDropdown($categories = [], $loop = 0)
    {
        $result = [];
        if(empty($categories)) return $result;

        $loop ++;
        $char = '---- ';
        $char_level = '';

        for ($i = 1; $i < $loop; $i++) {
            $char_level .= $char;
        }

        foreach($categories as $category){           
            if(empty($category['id']) || empty($category['CategoriesContent']->name)) continue;
            $result[$category['id']] = $char_level . $category['CategoriesContent']->name;            
            if(!empty($category['children'])){
                $result += $this->parseDataCategoryDropdown($category['children'], $loop);    
            }
        }

        return $result;
    }

    private function parseDataCategoryDropdownCollecttion($categories = [], $loop = 0)
    {
        $result = [];
        if(empty($categories)) return $result;

        $loop ++;
        $char = '---- ';
        $char_level = '';

        for ($i = 1; $i < $loop; $i++) {
            $char_level .= $char;
        }

        foreach($categories as $category){           
            if(empty($category['id']) || empty($category['name'])) continue;
            $result[$category['id']] = $char_level . $category['name'];            
            if(!empty($category['children'])){
                $result += $this->parseDataCategoryDropdownCollecttion($category['children'], $loop);    
            }
        }

        return $result;
    }

    public function getListCategoriesForCheckboxList($params = [])
    {
        $type = !empty($params[TYPE]) ? $params[TYPE] : null;
        $lang = !empty($params[LANG]) ? $params[LANG] : null;
        if(empty($type) || empty($lang)){
            return [];
        }

        if(!in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) {
            return [];
        }

        $categories = TableRegistry::get('Categories')->queryListCategories([
            FIELD => LIST_INFO,
            FILTER => [
                STATUS => 1,
                TYPE => $type,
                LANG => $lang,
                NOT_ID => !empty($params[NOT_ID]) ? $params[NOT_ID] : null
            ],
            SORT => [FIELD => 'position', SORT => DESC]
        ])->all()->nest('id', 'parent_id')->toArray();        
        $result = [];
        if(!empty($categories)){
            $result = $this->parseDataCategoryCheckBox($categories, 0);
        }
        return $result;
    }

    private function parseDataCategoryCheckBox($categories = [], $loop = 0)
    {
        $result = [];
        if(empty($categories)) return $result;

        $loop ++;
        foreach($categories as $category){           
            if(empty($category['id']) || empty($category['CategoriesContent']->name)) continue;
            $result[$category['id']] = [
                'name' => $category['CategoriesContent']->name,
                'level' => $loop
            ];
            if(!empty($category['children'])){
                $result += $this->parseDataCategoryCheckBox($category['children'], $loop);    
            }
        }

        return $result;
    }

    public function getAllNameContent($category_id = null)
    {
        if(empty($category_id)) return [];
        $result = TableRegistry::get('Categories')->getAllNameContent($category_id);
        return $result;
    }

    public function getDetailCategory($type = null, $category_id = null, $lang = null, $params = [])
    {
        $table = TableRegistry::get('Categories');
        $category = $table->getDetailCategory($type, $category_id, $lang);

        $result = [];
        if(!empty($category)){
            $result = $table->formatDataCategoryDetail($category, $lang);
        }
        
        return $result;
    }
}
