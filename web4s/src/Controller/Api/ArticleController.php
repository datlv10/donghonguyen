<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class ArticleController extends AppController {

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
            'get_categories' => true,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ],
            FILTER => [
                LANG => $lang,
                STATUS => 1,
                'ids' => !empty($data['ids']) ? $data['ids'] : [],
                'id_categories' => !empty($data['id_categories']) ? $data['id_categories'] : [],
                'id_brands' => !empty($data['id_brands']) ? $data['id_brands'] : [],
                'featured' => isset($data['featured']) && $data['featured'] != '' ? intval($data['featured']) : null,
                'tag_id' => !empty($data['tag_id']) ? intval($data['tag_id']) : null,
                'discount' => isset($data['discount']) && $data['discount'] != '' ? intval($data['discount']) : null,
                'stocking' => isset($data['stocking']) && $data['stocking'] != '' ? intval($data['stocking']) : null,
                'price_from' => !empty($data['price_from']) ? floatval(str_replace(',', '', $data['price_from'])) : null,
                'price_to' => !empty($data['price_to']) ? floatval(str_replace(',', '', $data['price_to'])) : null
            ]
        ];

        $table = TableRegistry::get('Articles');

        if(!$has_pagination){
            $articles = $table->queryListArticles($params)->limit($number_record)->toArray();
        }else{
            $paginator = $this->loadComponent('PaginatorExtend');
            try {
                $articles = $paginator->paginate($table->queryListArticles($params), [
                    'limit' => $number_record,
                    'page' => $page
                ])->toArray();
            } catch (Exception $e) {
                $articles = [];
            }

            $pagination_info = !empty($this->getRequest()->getAttribute('paging')['Articles']) ? $this->getRequest()->getAttribute('paging')['Articles'] : [];
            $pagination = $this->loadComponent('Utilities')->formatPaginationInfo($pagination_info);       
        }        

        $result = [];
        if(!empty($articles)){            
            foreach ($articles as $article) {
                $format_article = $table->formatDataArticleDetail($article, $lang);
                if(!empty($format_article['description'])) {
                    $format_article['description'] = trim(strip_tags($format_article['description']));
                }
                $result[] = $format_article;
            }
        }

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $result,
            EXTEND => !empty($result) && !empty($pagination) ? [PAGINATION => $pagination] : []
        ]);
    }

    public function detail($id = null)
    {
        $data = $this->data_bearer;

        $id = !empty($data['id']) ? intval($data['id'] ) : null;    
        if(empty($id)){
            $this->responseErrorApi([
                MESSAGE => __d('template', 'khong_nhan_duoc_id_bai_viet')
            ]);
        }
     
        $lang = !empty($data[LANG]) ? $data[LANG] : LANGUAGE;

        $table = TableRegistry::get('Articles');
        $article = $table->getDetailArticle($id, $lang, [
            'get_categories' => true,
            'get_tags' => true
        ]);

        $result = [];
        if(!empty($article)){
            $result = $table->formatDataArticleDetail($article, $lang);
        }
        
        $this->responseApi([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }

}