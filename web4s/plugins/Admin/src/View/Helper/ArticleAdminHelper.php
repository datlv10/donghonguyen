<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ArticleAdminHelper extends Helper
{   

    public function getDetailArticle($article_id = null, $lang = null, $params = [])
    {
        $table = TableRegistry::get('Articles');
        $article = $table->getDetailArticle($article_id, $lang, $params);

        $result = [];
        if(!empty($article)){
        	$result = $table->formatDataArticleDetail($article, $lang);
        }
        return $result;
    }

    public function getAllNameContent($article_id = null)
    {
        if(empty($article_id)) return [];
        $result = TableRegistry::get('Articles')->getAllNameContent($article_id);
        return $result;
    }
}
