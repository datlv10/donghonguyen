<?php

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

class CurrencyController extends SystemController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

    }

	public function activeCurrency()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $currency = !empty($data['currency']) ? $data['currency'] : null;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $currencies = TableRegistry::get('Currencies')->getList();
        if (empty($currency) || empty($currencies[$currency])) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->getRequest()->getSession();
        $session->write(CURRENCY_PARAM, $currency);

        // update cart info
        if(!empty($session->read(CART))){
            $this->loadComponent('Cart')->resetSessionCart($currency);
        }


        $this->responseJson([CODE => SUCCESS]);
    }

}