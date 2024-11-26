<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;
use Cake\Event\EventInterface;

class WishlistController extends AppController {
    
    public function addProduct()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $result = $this->loadComponent('Wishlist')->addProduct($data);
        $this->responseJson($result);
    }

    public function removeProduct()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $result = $this->loadComponent('Wishlist')->removeProduct($data);
        $this->responseJson($result);
    }
}