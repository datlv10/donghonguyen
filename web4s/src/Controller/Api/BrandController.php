<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class BrandController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $data = $this->data_bearer;

        $has_pagination = !empty($data[HAS_PAGINATION]) ? true : false;
        $number_record = !empty($data[NUMBER_RECORD]) ? intval($data[NUMBER_RECORD]) : 12;
        $page = !empty($data[PAGE]) ? intval($data[PAGE]) : 1;

        $sort_field = !empty($data[SORT_FIELD]) ? $data[SORT_FIELD] : null;
        $sort_type = !empty($data[SORT_TYPE]) ? $data[SORT_TYPE] : null;        

        $lang = !empty($data[LANG]) ? $data[LANG] : LANGUAGE;
        $params = [
            'get_user' => true,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ],
            FILTER => [
                STATUS => 1,
                'ids' => !empty($data['ids']) ? $data['ids'] : []
            ]
        ];

        $table = TableRegistry::get('Brands');

        if(!$has_pagination){
            $brands = $table->queryListBrands($params)->limit($number_record)->toArray();
        }else{
            $paginator = $this->loadComponent('PaginatorExtend');
            try {
                $brands = $paginator->paginate($table->queryListBrands($params), [
                    'limit' => $number_record,
                    'page' => $page
                ])->toArray();
            } catch (Exception $e) {
                $brands = [];
            }

            $pagination_info = !empty($this->getRequest()->getAttribute('paging')['Brands']) ? $this->getRequest()->getAttribute('paging')['Brands'] : [];
            $pagination = $this->loadComponent('Utilities')->formatPaginationInfo($pagination_info);       
        }        

        $result = [];
        if(!empty($brands)){            
            foreach ($brands as $brand) {
                $result[] = $table->formatDataBrandDetail($brand, $lang);
            }
        }

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $result,
            EXTEND => !empty($result) && !empty($pagination) ? [PAGINATION => $pagination] : []
        ]);
    }

    public function detail()
    {
        $data = $this->data_bearer;     
        $id = !empty($data['id']) ? intval($data['id'] ) : null;
        if(empty($id)){
            $this->responseErrorApi([
                MESSAGE => __d('template', 'khong_nhan_duoc_id_thuong_hieu')
            ]);
        }
        $lang = !empty($data[LANG]) ? $data[LANG] : LANGUAGE;

        $brand = TableRegistry::get('Brands')->getDetailBrand($id, $lang, ['get_user' => true]);
        
        $result = [];
        if(!empty($brand)){
            $result = TableRegistry::get('Brands')->formatDataBrandDetail($brand, $lang);
        }
        
        $this->responseApi([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }
}