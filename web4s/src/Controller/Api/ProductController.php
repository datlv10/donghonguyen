<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;

class ProductController extends AppController {

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

        $data_type = !empty($data['data_type']) ? $data['data_type'] : null;  
        
        $lang = !empty($data[LANG]) ? $data[LANG] : LANGUAGE;

        $params = [
            'get_item' => true,
            'get_categories' => true,
            'get_item_attributes' => true,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ],
            FILTER => [
                LANG => $lang,
                STATUS => 1,
                STATUS_ITEM => 1,
                'ids' => !empty($data['ids']) ? $data['ids'] : [],
                'id_categories' => !empty($data['id_categories']) ? $data['id_categories'] : [],
                'id_brands' => !empty($data['id_brands']) ? $data['id_brands'] : [],
                'featured' => isset($data['featured']) && $data['featured'] != '' ? intval($data['featured']) : null,
                'discount' => isset($data['discount']) && $data['discount'] != '' ? intval($data['discount']) : null,
                'stocking' => isset($data['stocking']) && $data['stocking'] != '' ? intval($data['stocking']) : null,
                'price_from' => !empty($data['price_from']) ? floatval(str_replace(',', '', $data['price_from'])) : null,
                'price_to' => !empty($data['price_to']) ? floatval(str_replace(',', '', $data['price_to'])) : null
            ]
        ];

        if($data_type == WISHLIST){
            if(empty($this->request->getSession()->read(MEMBER))){
            	$this->responseErrorApi([
                    STATUS => 403,
	                MESSAGE => __d('template', 'vui_long_dang_nhap_tai_khoan_de_su_dung_chuc_nang_nay')
	            ]);
            }

            $member = $this->request->getSession()->read(MEMBER);

            if($this->loadComponent('Member')->memberDoesntExistLogout($member['customer_id'])){
                $this->responseErrorApi([
                    STATUS => 403,
                    MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')
                ]);
            }

            $wishlist_info = TableRegistry::get('Wishlists')->find()->where([
                'customer_account_id' => $member['account_id'],
                'type' => PRODUCT
            ])->select(['record_id'])->toArray();

            $wishlist_ids = Hash::extract($wishlist_info, '{n}.record_id');

            if(empty($wishlist_ids)){
	            return $this->responseApi([
                    CODE => SUCCESS,
                    DATA => [],
                    EXTEND => []
                ]);
	        }
            $params[FILTER]['ids'] = $wishlist_ids;
        }   

        $table = TableRegistry::get('Products');

        if(!$has_pagination){
            $products = $table->queryListProducts($params)->limit($number_record)->toArray();
        }else{
            $paginator = $this->loadComponent('PaginatorExtend');
            try {
                $products = $paginator->paginate($table->queryListProducts($params), [
                    'limit' => $number_record,
                    'page' => $page
                ])->toArray();
            } catch (Exception $e) {
                $products = [];
            }

            $pagination_info = !empty($this->getRequest()->getAttribute('paging')['Products']) ? $this->getRequest()->getAttribute('paging')['Products'] : [];
            $pagination = $this->loadComponent('Utilities')->formatPaginationInfo($pagination_info);       
        }      

        $result = [];
        if(!empty($products)){            
            foreach ($products as $product) {
                $result[] = $table->formatDataProductDetail($product, $lang);
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
                MESSAGE => __d('template', 'khong_nhan_duoc_id_san_pham')
            ]);
        }

        
        $lang = !empty($data[LANG]) ? $data[LANG] : LANGUAGE;
        $product = TableRegistry::get('Products')->getDetailProduct($id, $lang, [
            'get_categories' => true,
            'get_item_attributes' => true,
            'get_tags' => true,
            'not_status' => 0
        ]);

        $result = [];
        if(!empty($product)){
            $result = TableRegistry::get('Products')->formatDataProductDetail($product, $lang);
        }
        
        $this->responseApi([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }

}