<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;

class CategoryController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $data = $this->data_bearer;

        $type = !empty($data[TYPE]) ? $data[TYPE] : null;
        if(empty($type) || !in_array($type, [PRODUCT, ARTICLE])){
            $this->responseErrorApi([
                MESSAGE => __d('template', 'tham_so_api_chua_hop_le')
            ]);
        }

        $sort_field = !empty($data[SORT_FIELD]) ? $data[SORT_FIELD] : null;
        $sort_type = !empty($data[SORT_TYPE]) ? $data[SORT_TYPE] : null;        
        
        $lang = !empty($data[LANG]) ? $data[LANG] : LANGUAGE;
        $params = [
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ],
            FILTER => [
                LANG => $lang,
                STATUS => 1,
                TYPE => $type,
                'ids' => !empty($data['ids']) ? $data['ids'] : []
            ]
        ];

        $categories = TableRegistry::get('Categories')->queryListCategories($params)->all()->nest('id', 'parent_id')->toArray();
        $result = $this->loadComponent('Block')->parseDataCategories($categories);
        $max_level = Hash::maxDimensions($categories);

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $result,
            EXTEND => !empty($result) ? ['max_level' => $max_level] : []
        ]);
    }

    public function detail($id = null)
    {
        $data = $this->data_bearer;

        $id = !empty($data['id']) ? intval($data['id'] ) : null;
        if(empty($id)){
            $this->responseErrorApi([
                MESSAGE => __d('template', 'khong_nhan_duoc_id_danh_muc')
            ]);
        }        

        $type = !empty($data[TYPE]) ? $data[TYPE] : null;
        if(empty($type) || !in_array($type, [PRODUCT, ARTICLE])){
            $this->responseErrorApi([
                MESSAGE => __d('template', 'tham_so_api_chua_hop_le')
            ]);
        }
        $lang = !empty($data[LANG]) ? $data[LANG] : LANGUAGE;

        $category = TableRegistry::get('Categories')->getDetailCategory($type, $id, $lang);
        $result = TableRegistry::get('Categories')->formatDataCategoryDetail($category);        

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }

}