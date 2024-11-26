<?php

namespace Admin\Controller;

use Admin\Controller\AppController;

class ErrorController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function notFound(){
    	$this->render('404');
    }

    public function denied()
    {
        $this->render('denied');
    }

    public function error()
    {
        $this->render('error');
    }
}