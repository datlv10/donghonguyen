<?php
namespace Admin;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\Middleware\CsrfProtectionMiddleware;

class Plugin extends BasePlugin {

    public function middleware($middleware){
        // Add middleware here.
        $middleware = parent::middleware($middleware);

        $csrf = new CsrfProtectionMiddleware(); 
        $middleware->add($csrf);

        return $middleware;
    }

    public function console($commands){
        // Add console commands here.
        $commands = parent::console($commands);
        return $commands;
    }

    public function bootstrap(PluginApplicationInterface $app){

        // Add constants, load configuration defaults.
        // By default will load `config/bootstrap.php` in the plugin.
        parent::bootstrap($app);
    }

    public function routes($routes){
        // Add routes.
        // By default will load `config/routes.php` in the plugin.
        parent::routes($routes);
    }
}