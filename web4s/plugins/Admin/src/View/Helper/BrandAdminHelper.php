<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class BrandAdminHelper extends Helper
{   
    public function getListBrands()
    {
        $result = TableRegistry::get('Brands')->getListBrands();
        return !empty($result) ? $result : [];
    }

    public function getBrandByMainCategory($category_id = null, $lang = null)
    {
        $result = TableRegistry::get('Brands')->getBrandByMainCategory($category_id, $lang);
        return !empty($result) ? $result : [];
    }

    public function getDetailBrand($brand_id = null, $lang = null, $params = [])
    {
        $result = TableRegistry::get('Brands')->getDetailBrand($brand_id, $lang, $params);
        return !empty($result) ? $result : [];
    }

    public function getAllNameContent($brand_id = null)
    {
        if(empty($brand_id)) return [];
        $result = TableRegistry::get('Brands')->getAllNameContent($brand_id);
        return $result;
    }
}