<?php

namespace App\Controller;
use Cake\Core\Configure;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

class PageController extends AppController {
    public function initialize(): void
    {
        parent::initialize();        
    }

	public function index()
	{
		$this->render('index');
    }

    public function notFound()
    {
        $this->response = $this->response->withStatus(404);
        $this->render('404');
    }

    public function error()
    {

        $this->response = $this->response->withStatus(403);

        $params = $this->request->getQueryParams();
        $message = !empty($params[MESSAGE]) ? $params[MESSAGE] : null;

        $this->set('message', $message);
        $this->render('error');
    }
}