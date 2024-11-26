<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;

class SitemapComponent extends Component
{
	public $controller = null;

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
  
    public function getSitemap($type = null, $year = null)
    {
        if(empty($type)) return [];
        
        $result = [];        
        $request = $this->controller->getRequest();
        $domain = $request->scheme() . '://' . $request->host() . '/';        
        $lastmod_default = date(DATE_ATOM, strtotime(date('Y-01-01')));
        $limit = 200;

        $page_table = TableRegistry::get('TemplatesPage');
        $link_table = TableRegistry::get('Links');
        $tag_table = TableRegistry::get('Tags');

        // get page url
        if($type == PAGE || $type == ALL){
            $pages_url = $page_table->find()->where([
                'TemplatesPage.template_code' => CODE_TEMPLATE,
                'TemplatesPage.page_type' => PAGE,
                'TemplatesPageContent.template_code' => CODE_TEMPLATE,
                'TemplatesPageContent.lang' => LANGUAGE,
                'OR' => [
                    'TemplatesPage.type' => HOME,
                    'AND' => [
                        'TemplatesPageContent.url !=' => '',
                        'TemplatesPageContent.url IS NOT' => null
                    ]
                ]
            ])->contain(['TemplatesPageContent'])->select(['TemplatesPage.code', 'TemplatesPage.created', 'TemplatesPageContent.url'])->limit($limit)->order('TemplatesPage.id ASC')->toArray();

            if(!empty($pages_url)){
                foreach ($pages_url as $key => $page) {
                    $loc = !empty($page['TemplatesPageContent']['url']) ? $domain . $page['TemplatesPageContent']['url'] : $domain;
                    $result[] = [
                        'loc' => $loc,
                        'lastmod' => !empty($page['created']) ? date(DATE_ATOM, intval($page['created'])) : $lastmod_default
                    ];
                }
            }
        }
        
        // get record url
        if(in_array($type, [CATEGORY_PRODUCT, CATEGORY_ARTICLE, PRODUCT, ARTICLE]) || $type == ALL){
            $where_link = [
                'Links.lang' => LANGUAGE,
                'Links.url !=' => '',
                'Links.url IS NOT' => null,
                'Links.deleted' => 0
            ];

            if(!empty($year)){
                $where_link['Links.created >='] = strtotime(date("$year-01-01"));
                $where_link['Links.created <='] = strtotime(date("$year-12-31"));
            }

            $contain = [];

            switch ($type) {
                case CATEGORY_PRODUCT:
                    $where_link['Links.type'] = CATEGORY_PRODUCT;
                    $where_link['Categories.status'] = 1;
                    $where_link['Categories.type'] = PRODUCT;

                    $contain = ['Categories'];
                    break;

                case CATEGORY_ARTICLE:
                    $where_link['Links.type'] = CATEGORY_ARTICLE;
                    $where_link['Categories.status'] = 1;
                    $where_link['Categories.type'] = ARTICLE;

                    $contain = ['Categories'];
                    break;

                case PRODUCT:
                    $where_link['Links.type'] = PRODUCT_DETAIL;
                    $where_link['Products.status'] = 1;

                    $contain = ['Products'];
                    break;

                case ARTICLE:
                    $where_link['Links.type'] = ARTICLE_DETAIL;
                    $where_link['Articles.status'] = 1;

                    $contain = ['Articles'];
                    break;
            }

            $total_link = $link_table->find()->contain($contain)->where($where_link)->count();
            if(!empty($total_link)){
                $page_number = ceil($total_link / $limit);
                if($page_number < 1)  $page_number = 1;

                for ($i = 0; $i < $page_number; $i++) {
                    $offset = $i * $limit;
                    $links = $link_table->find()->contain($contain)->where($where_link)->select(['url', 'updated'])->limit($limit)->offset($offset)->toArray();
                    if(empty($links)) continue;

                    foreach ($links as $key => $link) {
                        $result[] = [
                            'loc' => $domain . $link['url'],
                            'lastmod' => !empty($link['updated']) ? date(DATE_ATOM, intval($link['updated'])) : $lastmod_default
                        ];
                    }
                }
            }
        }
            
        // get tag url
        if($type == TAG || $type == ALL){
            $where_tag = ['Tags.lang' => LANGUAGE];
            if(!empty($year)){
                $where_tag['Tags.created >='] = strtotime(date("$year-01-01"));
                $where_tag['Tags.created <='] = strtotime(date("$year-12-31"));
            }

            $total_tag = $tag_table->find()->where($where_tag)->count();

            if(!empty($total_tag)){
                $page_number = ceil($total_tag / $limit);
                if($page_number < 1)  $page_number = 1;

                for ($i = 0; $i < $page_number; $i++) {
                    $offset = $i * $limit;
                    $tags = $tag_table->find()->where($where_tag)->select(['url', 'updated'])->limit($limit)->offset($offset)->toArray();
                    if(empty($tags)) continue;

                    foreach ($tags as $key => $tag) {
                        $result[] = [
                            'loc' => $domain . substr(TAG_PATH, 1) . '/' . $tag['url'],
                            'lastmod' => !empty($tag['updated']) ? date(DATE_ATOM, intval($tag['updated'])) : $lastmod_default
                        ];
                    }
                }
            }
        }

        return $result;
    }

    public function getSiteMapGroup($split_by_year = false)
    {
        $request = $this->controller->getRequest();
        $domain = $request->scheme() . '://' . $request->host() . '/';

        $lastmod_default = date(DATE_ATOM, strtotime(date('Y-01-01')));

        $table = TableRegistry::get('Links');
        $tags_table = TableRegistry::get('Tags');

        $where_category_product = $where_category_article = $where_article = $where_product = $where_link = [
            'Links.lang' => LANGUAGE,
            'Links.url !=' => '',
            'Links.url IS NOT' => null,
            'Links.deleted' => 0
        ];

        // count number product category
        $where_category_product['Links.type'] = CATEGORY_PRODUCT;
        $where_category_product['Categories.status'] = 1;
        $where_category_product['Categories.type'] = PRODUCT;
        $number_category_product = $table->find()->contain(['Categories'])->where($where_category_product)->count();

        // count number article category
        $where_category_article['Links.type'] = CATEGORY_ARTICLE;
        $where_category_article['Categories.status'] = 1;
        $where_category_article['Categories.type'] = PRODUCT;
        $number_category_article = $table->find()->contain(['Categories'])->where($where_category_article)->count();

        // count number article
        $where_article['Links.type'] = ARTICLE_DETAIL;
        $where_article['Articles.status'] = 1;
        $number_article = $table->find()->contain(['Articles'])->where($where_article)->count();

        // count number product
        $where_product['Links.type'] = PRODUCT_DETAIL;
        $where_product['Products.status'] = 1;
        $number_product = $table->find()->contain(['Products'])->where($where_product)->count();

        // count number tag
        $number_tag = $tags_table->find()->where([
            'Tags.lang' => LANGUAGE,
            'Tags.url !=' => '',
            'Tags.url IS NOT' => null
        ])->count();

        $last_created_page = TableRegistry::get('TemplatesPage')->find()->select(['created'])->order('created DESC')->first();
        $result = [
            [
                'loc' => $domain . 'sitemap-' . PAGE . '.xml',
                'lastmod' => !empty($last_created_page['created']) ? date(DATE_ATOM, $last_created_page['created']) : $lastmod_default
            ]
        ];

        if(!empty($number_category_product)){
            $last_update_record = $table->find()->contain(['Categories'])->where($where_category_product)->select('Links.updated')->order('Links.updated DESC')->first();
            $result[] = [
                'loc' => $domain . 'sitemap-' . CATEGORY_PRODUCT . '.xml',
                'lastmod' => !empty($last_update_record['updated']) ? date(DATE_ATOM, $last_update_record['updated']) : $lastmod_default
            ];
        }

        if(!empty($number_category_article)){
            $last_update_record = $table->find()->contain(['Categories'])->where($where_category_article)->select('Links.updated')->order('Links.updated DESC')->first();
            $result[] = [
                'loc' => $domain . 'sitemap-' . CATEGORY_ARTICLE . '.xml',
                'lastmod' => !empty($last_update_record['updated']) ? date(DATE_ATOM, $last_update_record['updated']) : $lastmod_default
            ];
        }

        if(!empty($number_product)){
            $last_update_record = $table->find()->contain(['Products'])->where($where_product)->select('Links.updated')->order('Links.updated DESC')->first();
            $result[] = [
                'loc' => $domain . 'sitemap-' . PRODUCT . '.xml',
                'lastmod' => !empty($last_update_record['updated']) ? date(DATE_ATOM, $last_update_record['updated']) : $lastmod_default
            ];
        }

        if(!empty($number_article)){
            $last_update_record = $table->find()->contain(['Articles'])->where($where_article)->select('Links.updated')->order('Links.updated DESC')->first();
            $result[] = [
                'loc' => $domain . 'sitemap-' . ARTICLE . '.xml',
                'lastmod' => !empty($last_update_record['updated']) ? date(DATE_ATOM, $last_update_record['updated']) : $lastmod_default
            ];
        }

        if(!empty($number_tag)){
            $last_update_record = $tags_table->find()->where(['lang' => LANGUAGE])->select('updated')->order('updated DESC')->first();

            $result[] = [
                'loc' => $domain . 'sitemap-' . TAG . '.xml',
                'lastmod' => !empty($last_update_record['updated']) ? date(DATE_ATOM, $last_update_record['updated']) : $lastmod_default
            ];
        }

        if($split_by_year){
            $year = date('Y');

            //split product sitemap by year
            $first_created_product = $table->find()->contain(['Products'])->where($where_product)->select('Links.created')->order('Links.created ASC')->first();
            $first_year_product = !empty($first_created_product['created']) ? date('Y', $first_created_product['created']) : null;
            
            if(!empty($first_year_product) && $first_year_product < $year){
                for ($i = $year - 1; $i >= $first_year_product; $i--) {
                    $result[] = [
                        'loc' => $domain . 'sitemap-' . PRODUCT . '-' . $i . '.xml',
                        'lastmod' => !empty($first_created_product['created']) ? date(DATE_ATOM, $first_created_product['created']) : $lastmod_default
                    ];
                }
            }
            
            //split article sitemap by year
            $first_created_article = $table->find()->contain(['Articles'])->where($where_article)->select('Links.created')->order('Links.created ASC')->first();
            $first_year_article = !empty($first_created_article['created']) ? date('Y', $first_created_article['created']) : null;
            if(!empty($first_year_article) && $first_year_article < $year){
                for ($i = $year - 1; $i >= $first_year_article; $i--) {
                    $result[] = [
                        'loc' => $domain . 'sitemap-' . ARTICLE . '-' . $i . '.xml',
                        'lastmod' => !empty($first_created_article['created']) ? date(DATE_ATOM, $first_created_article['created']) : $lastmod_default
                    ];
                }
            }

            //split tag sitemap by year
            $first_created_tag = $tags_table->find()->where(['lang' => LANGUAGE])->select('created')->order('created ASC')->first();
            $first_year_tag = !empty($first_created_tag['created']) ? date('Y', $first_created_tag['created']) : null;
            if(!empty($first_year_tag) && $first_year_tag < $year){
                for ($i = $year - 1; $i >= $first_year_tag; $i--) {
                    $result[] = [
                        'loc' => $domain . 'sitemap-' . TAG . '-' . $i . '.xml',
                        'lastmod' => !empty($first_created_tag['created']) ? date(DATE_ATOM, $first_created_tag['created']) : $lastmod_default
                    ];
                }
            }
        }

        return $result;
    }
}
