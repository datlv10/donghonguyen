<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;

class CategoryHelper extends Helper
{
    /** Lấy chi tiết danh mục thông qua id
     * 
     * $id (*): ID danh mục(int) - ví dụ: {PAGE_RECORD_ID}
     * $type (*): loại danh mục(string) - ví dụ: PRODUCT | ARTICLE
     * $params['get_user']: lấy thông tin của nhân viên - ví dụ: 'true'| 'false'
     * $params['get_attributes']: lấy thông tin của thuộc tính danh mục - ví dụ: 'true'| 'false'
     * $params['lang'] (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     * 
     * {assign var = data value = $this->Category->getInfoCategory({PAGE_RECORD_ID}, {PRODUCT}, [
     *      'get_user' => true,
     *      'get_categories' => true,
     *      {LANG} => LANGUAGE
     * ])}
     * 
    */
    public function getInfoCategory($id = null, $type = null, $params = [])
    {
        if(empty($id)) return [];
        if(empty($type) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) return [];

        $table = TableRegistry::get('Categories');
        $lang = !empty($params[LANG]) ? $params[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
    
        $category_product = $table->getDetailCategory($type, $id, $lang, $params);

        if(empty($category_product)) return [];

        return $table->formatDataCategoryDetail($category_product);
    }


    /** Số lượng bài viết hoặc sản phẩm của danh mục
     * 
     * $id (*): ID danh mục(int) - ví dụ: {PAGE_RECORD_ID}
     * $type (*): loại danh mục(string) - ví dụ: PRODUCT | ARTICLE
     * 
     * {assign var = data value = $this->Category->countPostByCatId({PAGE_RECORD_ID}, {PRODUCT})}
     * 
    */
    public function countPostByCatId($id = null, $type = null) {

        if(empty($id)) return 0;
        if(empty($type) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) return [];

        $parent_category = TableRegistry::get('Categories')->getAllChildCategoryId($id);
        $result = 0;

        if($type == PRODUCT) {
        	$result = TableRegistry::get('Products')->find()->contain('CategoryProduct')->where([
        		'Products.deleted' => 0,
        		'Products.status' => ENABLE,
        		'CategoryProduct.category_id IN' => $parent_category
        	])->select('Products.id')->group('Products.id')->count();

            if(empty($result)) return 0;
        }
        
        if($type == ARTICLE) {
            $result = TableRegistry::get('Articles')->find()->contain('CategoryArticle')->where([
                'Articles.deleted' => 0,
                'Articles.status' => ENABLE,
                'CategoryArticle.category_id IN' => $parent_category
            ])->select('Articles.id')->group('Articles.id')->count();

            if(empty($result)) return 0;
        }

    	return $result;
    }

    private function parseDataCategoryDropdown($categories = [], $loop = 0)
    {
        $result = [];
        if(empty($categories)) return $result;

        $loop ++;
        $char = '- ';
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


    /** Danh sách danh mục
     * 
     * $type (*): loại danh mục(string) - ví dụ: PRODUCT | ARTICLE
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     * $params['get_user']: lấy thông tin của nhân viên - ví dụ: 'true'| 'false'
     * $params['get_parent']: Chỉ lấy danh mục cha - ví dụ: 'true'| 'false'
     * $params['get_attributes']: Lấy thông tin thuộc tính của danh mục - ví dụ: 'true'| 'false'
     * 
     * $params[{FILTER}]: lọc theo điều kiện truyền vào
     * 
     * $params[{FILTER}][{KEYWORD}]: lọc theo từ khóa
     * $params[{FILTER}][{NOT_ID}]: Loại bỏ ID danh mục(string)
     * $params[{FILTER}]['ids']: lọc theo danh sách ID danh mục - ví dụ: [5, 6, 9, 10] - chú ý truyền dạng mảng "[]"
     * $params[{FILTER}]['created_by']: lọc theo nhân viên tạo (int)
     * $params[{FILTER}]['parent_id']: Lọc lấy danh sách các danh mục con theo ID của danh mục cha(int)
     * 
     * $params[{SORT}][{FIELD}]: sắp xếp dữ liệu theo field - ví dụ: id | category_id | name | status | position | created | updated | created_by -  mặc định position
     * $params[{SORT}][{SORT}]: sắp xếp tăng dần hoặc giảm dần - ví dụ DESC | ASC - mặc định DESC
     * 
     *
     * {assign var = data value = $this->Category->getCategories({PRODUCT}, {LANGUAGE}, [
     *      'get_user' => true,
     *      {FILTER} => [
                'parent_id' => PAGE_CATEGORY_ID
     *      ],
     *      {SORT} => [
     *          {FIELD} => 'name',
     *          {SORT} => ASC
     *      ]
     * ])}
     * 
    */
    public function getCategories($type = null, $lang = null, $params = []) 
    {
        $result = [];

        if(empty($type) || empty($lang) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))){
            return $result;
        }

        $get_parent = !empty($params['get_parent']) ? true : false;

        $params['get_parent'] = $get_parent;
        $params[FILTER][STATUS] = 1;
        $params[FILTER][LANG] = $lang;
        $params[FILTER][TYPE] = $type;

        $categories = TableRegistry::get('Categories')->queryListCategories($params)->all()->nest('id', 'parent_id')->toArray();
        if(!empty($categories)){
            foreach($categories as $k => $category){
                $result[$k] = TableRegistry::get('Categories')->formatDataCategoryDetail($category, $lang);
            }
        }
        
        return $result;
    }

    /** Danh sách danh mục kiểu định dạng dropdown
     * 
     * $type (*): loại danh mục(string) - ví dụ: PRODUCT | ARTICLE
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     * $params['get_user']: lấy thông tin của nhân viên - ví dụ: 'true'| 'false'
     * $params['get_parent']: Chỉ lấy danh mục cha - ví dụ: 'true'| 'false'
     * $params['get_attributes']: Lấy thông tin thuộc tính của danh mục - ví dụ: 'true'| 'false'
     * 
     * $params[{FILTER}]: lọc theo điều kiện truyền vào
     * 
     * $params[{FILTER}][{KEYWORD}]: lọc theo từ khóa
     * $params[{FILTER}][{NOT_ID}]: Loại bỏ ID danh mục(string)
     * $params[{FILTER}]['ids']: lọc theo danh sách ID danh mục - ví dụ: [5, 6, 9, 10] - chú ý truyền dạng mảng "[]"
     * $params[{FILTER}]['created_by']: lọc theo nhân viên tạo (int)
     * $params[{FILTER}]['parent_id']: Lọc lấy danh sách các danh mục con theo ID của danh mục cha(int)
     * 
     * $params[{SORT}][{FIELD}]: sắp xếp dữ liệu theo field - ví dụ: id | category_id | name | status | position | created | updated | created_by -  mặc định position
     * $params[{SORT}][{SORT}]: sắp xếp tăng dần hoặc giảm dần - ví dụ DESC | ASC - mặc định DESC
     * 
     *
     * {assign var = data value = $this->Category->getCategoriesForDropdown({PRODUCT}, {LANGUAGE}, [
     *      'get_user' => true,
     *      {FILTER} => [
     *          'parent_id' => PAGE_CATEGORY_ID
     *      ],
     *      {SORT} => [
     *          {FIELD} => 'name',
     *          {SORT} => ASC
     *      ]
     * ])}
     * 
    */
    public function getCategoriesForDropdown($type = null, $lang = null, $params = [])
    {
        $result = [];

        if(empty($type) || empty($lang) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))){
            return $result;
        }

        $get_parent = !empty($params['get_parent']) ? true : false;

        $params['get_parent'] = $get_parent;
        $params[FIELD] = LIST_INFO;
        $params[FILTER][STATUS] = 1;
        $params[FILTER][LANG] = $lang;
        $params[FILTER][TYPE] = $type;

        $categories = TableRegistry::get('Categories')->queryListCategories($params)->all()->nest('id', 'parent_id')->toArray();

        if(!empty($categories)){
            $result = $this->parseDataCategoryDropdown($categories, 0);
        }

        return $result;
    }

    /** Bỏ dùng function getCategories
    */
    public function getListCategories($type = null, $lang = null, $params = []) 
    {

        $result = [];

        if(empty($type) || empty($lang) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))){
            return $result;
        }

        $get_parent = !empty($params['get_parent']) ? true : false;

        $params['get_parent'] = $get_parent;
        $params[FILTER][STATUS] = 1;
        $params[FILTER][LANG] = $lang;
        $params[FILTER][TYPE] = $type;

        $categories = TableRegistry::get('Categories')->queryListCategories($params)->all()->nest('id', 'parent_id')->toArray();
        if(!empty($categories)){
            foreach($categories as $k => $category){
                $result[$k] = TableRegistry::get('Categories')->formatDataCategoryDetail($category, $lang);
            }
        }
        
        return $result;
    }
}
