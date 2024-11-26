<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;

class SearchController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function suggest()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();
        $keyword = !empty($data['keyword']) ? $data['keyword'] : null;
        $limit = !empty($data['limit']) ? intval($data['limit']) : 5;
        $type = !empty($data['type']) ? $data['type'] : null;

        if (!$this->getRequest()->is('post') || empty($keyword)) die;

        $products = $articles = $compares = [];

        // search products
        if(empty($type) || $type == PRODUCT || $type == COMPARE || $type == ALL){
            $list_products = TableRegistry::get('Products')->queryListProducts([            
                FILTER => [
                    LANG => LANGUAGE,
                    STATUS => 1,
                    STATUS_ITEM => 1,
                    KEYWORD => $keyword
                ],
                'get_item' => true
            ])->limit($limit)->toArray();

            if(!empty($list_products)){
                foreach ($list_products as $key => $product) {
                    $data_product = [
                        'id' => !empty($product['id']) ? intval($product['id']) : null,
                        'name' => !empty($product['ProductsContent']['name']) ? $product['ProductsContent']['name'] : null,
                        'url' => !empty($product['Links']['url']) ? $product['Links']['url'] : null,
                        'price' => null,
                        'price_special' => null,
                        'apply_special' => false
                    ];

                    $data_items = TableRegistry::get('Products')->formatDataProductItems($product, LANGUAGE);
                    $first_item = !empty($data_items['items'][0]) ? $data_items['items'][0] : [];
                    
                    $price = !empty($first_item['price']) ? round(floatval($first_item['price'] / CURRENCY_RATE), 2) : null;
                    $price_special = !empty($first_item['price_special']) ? round(floatval($first_item['price_special'] / CURRENCY_RATE), 2) : null;
                    $apply_special = !empty($first_item['apply_special']) ? true : false;

                    $data_product['price'] = !empty($price) ? $price : null;
                    $data_product['price_special'] = !empty($price_special) ? $price_special : null;
                    $data_product['apply_special'] = $apply_special;
                    $data_product['image'] = !empty($data_items['all_images']) ? $data_items['all_images'][0] : null;
                    
                    $products[] = $data_product;
                }
            }
        }

        // search articles
        if(empty($type) || $type == ARTICLE || $type == ALL){
            $list_articles = TableRegistry::get('Articles')->queryListArticles([            
                FILTER => [
                    LANG => LANGUAGE,
                    STATUS => 1,
                    KEYWORD => $keyword
                ]
            ])->limit($limit)->toArray();

            if(!empty($list_articles)){
                foreach ($list_articles as $article) {
                    $articles[] = [
                        'id' => !empty($article['id']) ? intval($article['id']) : null,
                        'name' => !empty($article['ArticlesContent']['name']) ? $article['ArticlesContent']['name'] : null,                       
                        'image' => !empty($article['image_avatar']) ? $article['image_avatar'] : null,
                        'has_album' => !empty($article['has_album']) ? true : false,
                        'has_file' => !empty($article['has_file']) ? true : false,
                        'has_video' => !empty($article['has_video']) ? true : false,
                        'featured' => !empty($article['featured']) ? $article['featured'] : null,
                        'url' => !empty($article['Links']['url']) ? $article['Links']['url'] : null
                    ];
                }
            }
        }

        $this->set('articles', $articles);
        $this->set('products', $products);
        
        if($type == COMPARE) {
            $this->render('suggest_compare');
        }
    }
}