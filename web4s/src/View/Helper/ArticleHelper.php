<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;

class ArticleHelper extends Helper
{
    /** Lấy danh sách bài viết
     * 
     * $params['get_user']: lấy thông tin của nhân viên - ví dụ: 'true'| 'false'
     * $params['get_categories']: lấy thông tin của danh mục - ví dụ: 'true'| 'false'
     * $params['get_attributes']: lấy thông tin của thuộc tính bài viết - ví dụ: 'true'| 'false'
     * 
     * 
     * $params[{FIELD}]: Lấy các trường thông tin ví dụ: FULL_INFO | LIST_INFO | SIMPLE_INFO mặc định là SIMPLE_INFO
     * 
     * 
     * $params[{FILTER}]: lọc theo điều kiện truyền vào
     * 
     * 
     * $params[{FILTER}][{KEYWORD}]: lọc theo từ khóa
     * $params[{FILTER}]['has_album']: lọc theo bài viết có album - ví dụ 0 | 1
     * $params[{FILTER}]['has_video']: lọc theo bài viết có video - ví dụ 0 | 1
     * $params[{FILTER}]['has_file']: lọc theo bài viết có file - ví dụ 0 | 1
     * $params[{FILTER}]['featured']: lọc theo bài viết nổi bật - ví dụ: 0 | 1
     * $params[{FILTER}]['catalogue']: lọc theo bài viết có catelogue - ví dụ: 0 | 1
     * $params[{FILTER}]['seo_score']: lọc theo bài viết có điểm seo - ví dụ: success | warning | danger
     * $params[{FILTER}]['keyword_score']: lọc theo bài viết có điểm từ khóa - ví dụ: success | warning | danger | null
     * $params[{FILTER}]['ids']: lọc theo danh sách ID bài viết - ví dụ: [5, 6, 9, 10] - chú ý truyền dạng mảng "[]"
     * $params[{FILTER}]['not_ids']: lọc theo danh sách loại bỏ ID bài viết - ví dụ: [5, 6, 9, 10] - chú ý truyền dạng mảng "[]"
     * $params[{FILTER}]['id_categories']: lọc theo danh sách ID danh mục bài viết - ví dụ: [5, 6, 9, 10] - chú ý truyền dạng mảng "[]"
     * $params[{FILTER}]['tag_id']: lọc theo ID của tag bài viết (int)
     * 
     * $params[{SORT}][{FIELD}]: sắp xếp dữ liệu theo field - ví dụ: id | article_id | name | status | view | position | created | updated | featured | created_by -  mặc định id
     * $params[{SORT}][{SORT}]: sắp xếp tăng dần hoặc giảm dần - ví dụ DESC | ASC - mặc định DESC
     * 
     * 
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     * 
     * {assign var = data value = $this->Article->getArticles([
     *      'get_categories' => true,
     *      {FIELD} => FULL_INFO,
     *      {FILTER} => [
                'id_categories' => PAGE_CATEGORIES_ID
     *      ],
     *      {SORT} => [
     *          {FIELD} => 'name',
     *          {SORT} => ASC
     *      ]
     * ], {LANGUAGE})}
     * 
     * 
    */
    public function getArticles($params = [], $lang = null) 
    {
        $result = [];

        $languages = TableRegistry::get('Languages')->getList();
        if(empty($lang) || empty($languages[$lang])) $lang = LANGUAGE;
        $params[FILTER][LANG] = $lang;

        if(!isset($params[FILTER][STATUS])) $params[FILTER][STATUS] = 1;

        $limit = !empty($params['limit']) ? intval($params['limit']) : 10;
        $articles = TableRegistry::get('Articles')->queryListArticles($params)->limit($limit)->toArray();

        if(!empty($articles)){
            foreach($articles as $k => $article){
                $result[$k] = TableRegistry::get('Articles')->formatDataArticleDetail($article, $lang);
            }
        }
        
        return $result;
    }

    /** Lấy chi tiết bài biết thông qua article_id
     * 
     * $article_id (*): ID bài viết(int)
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     * $params['get_user']: lấy thông tin của nhân viên - ví dụ: 'true'| 'false'
     * $params['get_categories']: lấy thông tin của danh mục - ví dụ: 'true'| 'false'
     * $params['get_tags']: lấy thông tin của tag - ví dụ: 'true'| 'false'
     * $params['get_attributes']: lấy thông tin của thuộc tính bài viết - ví dụ: 'true'| 'false'
     * 
     * {assign var = data value = $this->Article->getDetailArticle({PAGE_RECORD_ID}, {LANGUAGE}, [
     *  'get_user' => true,
     *  'get_categories' => true
     * ])}
     * 
    */
    public function getDetailArticle($article_id = null, $lang = null, $params = [])
    {
        $result = [];
        if(empty($article_id)) return $result;
        $lang = !empty($lang) ? $lang : LANGUAGE;
        $params['status'] = 1;

        $table = TableRegistry::get('Articles');
        $article = $table->getDetailArticle($article_id, $lang, $params);
        
        if(!empty($article)){
            $result = $table->formatDataArticleDetail($article, $lang);
        }
        return $result;
    }
}
