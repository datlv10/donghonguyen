<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;


class NhNotificationController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function listNotifications()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('NhNotifications');
        $utilities = $this->loadComponent('Utilities');

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $group = !empty($data['group']) ? $data['group'] : null;
        $page = !empty($data['page']) ? intval($data['page']) : 1;
        $limit = 7;
        
        $notifications = [];
        $more_page = false;
        if(!empty($group)){
            $params = [
                FILTER => [
                    'group_notification' => $group
                ]
            ];

            try {
                $notifications = $this->paginate($table->queryListNhNotifications($params), [
                    'limit' => $limit,
                    'page' => $page,
                    'order' => [
                        'id' => 'DESC'
                    ]
                ])->toArray();
            } catch (Exception $e) {
                $page = 1;
                $notifications = $this->paginate($table->queryListNhNotifications($params), [
                    'limit' => $limit,
                    'page' => $page,
                    'order' => [
                        'id' => 'DESC'
                    ]
                ])->toArray();
            }

            $pagination_info = !empty($this->request->getAttribute('paging')['NhNotifications']) ? $this->request->getAttribute('paging')['NhNotifications'] : [];
            $meta_info = $utilities->formatPaginationInfo($pagination_info);

            
            $total_pages = !empty($meta_info['pages']) ? intval($meta_info['pages']) : 0;
            $more_page = $total_pages > $page ? true : false;
        }
        
        $this->set('notifications', $notifications);
        $this->set('more_page', $more_page);
        $this->set('page', $page);
    }













}