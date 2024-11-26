<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Filesystem\File;

class ProductController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

	public function quickView($product_id = null) 
	{
        $this->viewBuilder()->enableAutoLayout(false);

        $product = TableRegistry::get('Products')->getDetailProduct($product_id, LANGUAGE, [
            'get_categories' => true,
            'get_item_attributes' => true
        ]);
        $product = TableRegistry::get('Products')->formatDataProductDetail($product, LANGUAGE);
        
        if(!empty($product['items'])){
            foreach ($product['items'] as $key => $item) {
                if(!empty($item['price'])){
                    $product['items'][$key]['price'] = !empty($item['price']) ? round(floatval($item['price'] / CURRENCY_RATE), 2) : null;
                }

                if(!empty($item['price_special'])){
                    $product['items'][$key]['price_special'] = !empty($item['price_special']) ? round(floatval($item['price_special'] / CURRENCY_RATE), 2) : null;
                }
            }
        }
        $this->set('product', $product);
    }

    public function compare() 
    {
        $this->viewBuilder()->enableAutoLayout(false);
        
        $compare_ids = !empty($this->request->getCookie(COMPARE)) ? json_decode($this->request->getCookie(COMPARE), true) : null;

        if(empty($compare_ids)) die;

        $params = [
            'get_item' => true,
            'get_categories' => true,
            'get_item_attributes' => true,
            SORT => [
                FIELD => 'order_field_id',
            ],
            FILTER => [
                LANG => LANGUAGE,
                STATUS => 1,
                STATUS_ITEM => 1
            ]
        ];
        $params[FILTER]['ids'] = $compare_ids;

        $table = TableRegistry::get('Products');
        $products = $table->queryListProducts($params)->toArray();

        $compare = [];
        if(!empty($products)) {
            foreach ($products as $product) {
                $compare[] = $table->formatDataProductDetail($product, LANGUAGE);
            }
        }

        $this->set('compare', $compare);
        $view_compare = new File(PATH_TEMPLATE . BLOCK . DS . PRODUCT . DS .'view_compare.tpl', false);
        if($view_compare->exists()){                        
            $this->render('/block/product/view_compare');
        }
    }
}