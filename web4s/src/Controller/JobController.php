<?php

namespace App\Controller;

class JobController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
        $this->get_structure_layout = false;
    }

    public function sendEmail()
    {
        $this->layout = false;
        $this->autoRender = false;

        $request = $this->getRequest();
        if ($this->getRequest()->is('post')) {
            $data = $request->getData();
        }else{
            $data = $request->getQuery();
        }

        $result = $this->loadComponent('Email')->sendEmail($data);
        exit(json_encode($result));
    }

}