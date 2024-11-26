<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class TagComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
    
    public function saveTag($data = [], $id = null)
    {
        if(empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Tags');

        $name = !empty($data['name']) ? trim($data['name']) : null;
        $link = !empty($data['link']) ? $this->Utilities->formatToUrl(trim($data['link'])) : null;
        $lang = !empty($data['lang']) ? trim($data['lang']) : null;

        // validate data
        if(empty($name)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_ten_the')]);
        }

        if(empty($link)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_duong_dan')]);
        }

        if(empty($lang)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_chon_ngon_ngu')]);
        }        

        if($table->checkTagExist($name, $id)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'ten_the_da_ton_tai_tren_he_thong')]);
        }

        if($table->checkUrlTagExist($link, $id)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
        }

        $languages = TableRegistry::get('Languages')->getList();
        if(empty($languages[$lang])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'ngon_ngu_khong_hop_le')]);
        }

        if(!empty($id)){
            $tag = $table->find()->where(['Tags.id' => $id])->first();
            if(empty($tag)){
                return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // format data before save
        $list_keyword = !empty($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : null;
        $seo_keyword = !empty($list_keyword) ? implode(', ', $list_keyword) : null;
        
        $seo_title = !empty($data['seo_title']) ? $data['seo_title'] : $name;
        if(empty($id)){
            $settings = TableRegistry::get('Settings')->getSettingByGroup(TAG);
            
            $prefix_seo_title = !empty($settings['prefix_seo_title']) ? $settings['prefix_seo_title'] : null;
            $suffixes_seo_title = !empty($settings['suffixes_seo_title']) ? $settings['suffixes_seo_title'] : null;
            $seo_title = $prefix_seo_title . $seo_title . $suffixes_seo_title;
        }        

        $data_save = [
            'name' => $name,
            'url' => $link,
            'content' => !empty($data['content']) ? $data['content'] : null,
            'seo_title' => $seo_title,
            'seo_description' => !empty($data['seo_description']) ? $data['seo_description'] : null,
            'seo_keyword' => $seo_keyword,
            'lang' => $lang,
            'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$name]))
        ];
        // merge data with entity 
        if(empty($id)){
            $tag = $table->newEntity($data_save);
        }else{            
            $tag = $table->patchEntity($tag, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($tag);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            return $this->System->getResponse([
                CODE => SUCCESS, 
                DATA => [
                    'id' => $save->id
                ]
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }
}
