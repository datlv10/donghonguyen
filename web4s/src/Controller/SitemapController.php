<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\ORM\TableRegistry;

class SitemapController extends AppController {

    public function initialize(): void
    {
        parent::initialize();

        $this->get_structure_layout = false;

        $response = $this->response->withType('application/xml');
        $this->setResponse($response);

        $this->viewBuilder()->setLayout(SITEMAP);
        
    }

    public function index($group = null)
    {
        $request = $this->request;
        $path = $request->getUri()->getPath();
        $url = !empty($path) ? substr($path, 1) : null;
        $year = null;
        $view = 'index';

        
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $sitemap_setting = !empty($settings['sitemap']) ? $settings['sitemap'] : [];

        $combine_sitemap = !empty($sitemap_setting['combine_sitemap']) ? true : false;
        $split_by_year = !empty($sitemap_setting['split_by_year']) ? true : false;
        if($split_by_year){
            $year = date('Y');
        }

        if(strpos($url, '.xml') === false) die;

        if(!empty($group)) $group = str_replace('.xml', '', $group);
        if(!empty($group) && strpos($group, '-') !== false){
            $split = explode('-', $group);
            $group = !empty($split[0]) ? $split[0] : null;
            $year = !empty($split[1]) ? intval($split[1]) : null;
        };

        if(!empty($group) && !in_array($group, [PAGE, CATEGORY_PRODUCT, CATEGORY_ARTICLE, PRODUCT, ARTICLE, TAG])) die;

        $sitemap = [];
        switch ($group) {
            case PAGE:
                    $sitemap = $this->loadComponent('Sitemap')->getSitemap(PAGE);
                break;
            case CATEGORY_PRODUCT:
                    $sitemap = $this->loadComponent('Sitemap')->getSitemap(CATEGORY_PRODUCT);
                break;
            case CATEGORY_ARTICLE:
                    $sitemap = $this->loadComponent('Sitemap')->getSitemap(CATEGORY_ARTICLE);
                break;
            case PRODUCT:
                    $sitemap = $this->loadComponent('Sitemap')->getSitemap(PRODUCT, $year);
                break;
            case ARTICLE:
                    $sitemap = $this->loadComponent('Sitemap')->getSitemap(ARTICLE, $year);
                break;
            case TAG:
                    $sitemap = $this->loadComponent('Sitemap')->getSitemap(TAG, $year);
                break;

            default:
                if($combine_sitemap){
                    $sitemap = $this->loadComponent('Sitemap')->getSiteMapGroup($split_by_year);
                    $view = 'group';
                }else{
                    $sitemap = $this->loadComponent('Sitemap')->getSitemap(ALL);
                }
                break;
        }

        $this->set('sitemap', $sitemap);
        $this->render($view);
    }

}