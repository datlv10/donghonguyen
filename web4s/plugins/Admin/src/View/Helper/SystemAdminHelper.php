<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\Utility\Security;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Collection\Collection;
use Cake\Routing\Router;

class SystemAdminHelper extends Helper
{
    public function getUrlVars($var_name, $value)
    {
        $query_str = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        $url = $_SERVER['REDIRECT_URL'];
        $query_params = [];
        if($query_str) {
            parse_str($query_str, $query_params);
        }
        $query_params[$var_name] = $value;
        $url .= '?'.http_build_query($query_params);
        return $url;
    }
}
