<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ProductAdminHelper extends Helper
{   

    public function getDetailProductItem($product_item_id = null, $lang = null, $params = [])
    {
        if(empty($product_item_id) || empty($lang)) return [];

        $result = TableRegistry::get('ProductsItem')->getDetailProductItem($product_item_id, $lang, $params);
        return $result;
    }

    public function getDetailProduct($product_id = null, $lang = null, $params = [])
    {
        if(empty($product_id) || empty($lang)) return [];
        $product = TableRegistry::get('Products')->getDetailProduct($product_id, $lang, $params);
        $result = TableRegistry::get('Products')->formatDataProductDetail($product, $lang);
        
        return $result;
    }

    public function getAllNameContent($product_id = null)
    {
        if(empty($product_id)) return [];
        $result = TableRegistry::get('Products')->getAllNameContent($product_id);
        return $result;
    }
}
