<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;

class LogController extends AppController {
  
    public function initialize(): void
    {
        parent::initialize();        
    }

    public function list() 
    {
        $this->js_page = [
            '/assets/js/pages/list_log.js'
        ];

        $actions = [
            'add' => __d('admin', 'them_moi'),
            'update' => __d('admin', 'cap_nhat'),
            'update_status' => __d('admin', 'doi_trang_thai'),
            'delete' => __d('admin', 'xoa')
        ];

        $list_type = [
            'data' => __d('admin', 'thay_doi_du_lieu'),
            'template' => __d('admin', 'sua_tep_giao_dien')
        ];

        $users = TableRegistry::get('Users')->find()->where(['deleted' => 0])->select(['id', 'full_name'])->toList();
        $users = Hash::combine($users, '{n}.id', '{n}.full_name');

        $this->set('actions', $actions);
        $this->set('list_type', $list_type);
        $this->set('users', $users);

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'lich_su_cap_nhat')); 
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);

        
        $table = TableRegistry::get('Logs');
        $utilities = TableRegistry::get('Utilities');

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $filter = !empty($data[QUERY]) ? $data[QUERY] : [];
 
        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : 50;

        // filter 
        $action = !empty($filter['action']) ? $filter['action'] : null;
        $type = !empty($filter['type']) ? $filter['type'] : null;
        $user_id = !empty($filter['user_id']) ? intval($filter['user_id']) : null;
        $create_from = $create_to = null;
        if(!empty($filter['create_from']) && $utilities->isDateClient($filter['create_from'])){
            $create_from = strtotime(str_replace('/', '-', $filter['create_from']));
        }

        if(!empty($filter['create_to']) && $utilities->isDateClient($filter['create_to'])){
            $create_to = strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to']))));
        }

        $where = [];
        if(!empty($action)) $where['Logs.action'] = $action;
        if(!empty($type)) $where['Logs.type'] = $type;
        if(!empty($user_id)) $where['Logs.user_id'] = $user_id;
        if(!empty($create_from)) $where['Logs.created >='] = $create_from;
        if(!empty($create_to)) $where['Logs.created <'] = $create_to;

        $query = $table->find()->contain(['User'])->where($where)->select([
            'Logs.id', 'Logs.action', 'Logs.type', 'Logs.sub_type', 'Logs.user_id', 'Logs.description', 'Logs.created', 'User.full_name'
        ])->order('Logs.id DESC');

        try {
            $logs = $this->paginate($query, [
                'limit' => $limit,
                'page' => $page
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $logs = $this->paginate($query, [
                'limit' => $limit,
                'page' => $page
            ])->toArray();
        }            

        $pagination_info = !empty($this->request->getAttribute('paging')['Logs']) ? $this->request->getAttribute('paging')['Logs'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        // format data
        $result = [];
        if(!empty($logs)){
            foreach($logs as $log){
                $full_name = !empty($log['user']['full_name']) ? $log['user']['full_name'] : null;
                $created = !empty($log['created']) ? intval($log['created']) : null;

                $parse_label = $utilities->parseTimestampToLabelTime($created);
                $diff_time = !empty($parse_label['diff_time']) ? $parse_label['diff_time'] : null;
                $time_label =  !empty($parse_label['time']) ? $parse_label['time'] : null;                
                if(!in_array($diff_time, ['s', 'i', 'h']) || empty($time_label)) $time_label = date('H:i - d/m/Y', $created);

                $log['created_label'] = $time_label;
                $log['full_name'] = $full_name;

                $result[] = $log;
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }
}