<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;
use Cake\Cache\Cache;

class PromotionsCouponTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('promotions_coupon');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);

        $this->hasOne('Promotions', [
            'className' => 'Promotions',
            'foreignKey' => 'id',
            'bindingKey' => 'promotion_id',
            'propertyName' => 'Promotions'
        ]);
    }

    public function queryListPromotionsCoupon($params = []) 
    {
        $table = TableRegistry::get('PromotionsCoupon');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_user = !empty($params['get_user']) ? $params['get_user'] : false;
        $get_empty_name = !empty($params['get_empty_name']) ? true : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $promotion_id = !empty($filter['promotion_id']) ? $filter['promotion_id'] : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $ids = !empty($filter['ids']) ? $filter['ids'] : [];
        $tag_id = !empty($filter['tag_id']) ? intval($filter['tag_id']) : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['PromotionsCoupon.id', 'PromotionsCoupon.code'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['PromotionsCoupon.id', 'PromotionsCoupon.promotion_id', 'PromotionsCoupon.code', 'PromotionsCoupon.used', 'PromotionsCoupon.number_use', 'PromotionsCoupon.status', 'PromotionsCoupon.created', 'Promotions.name'];
            break;
        }

        $where = [
            'PromotionsCoupon.promotion_id IS NOT' => NULL,
        ];

        $contain = ['Promotions'];  

        if (!empty($promotion_id)) {
            $where = [
                'PromotionsCoupon.promotion_id' => $promotion_id,
            ];   
        }

        // filter by conditions  
        if(!empty($keyword)){
            $where['PromotionsCoupon.code LIKE'] = '%' . $keyword . '%';
        }

        if(!empty($ids)){
            $where['PromotionsCoupon.id IN'] = $ids;
        }

        if(!is_null($status)){
            $where['PromotionsCoupon.status'] = $status;
        } 

        // sort by
        $sort_string = 'PromotionsCoupon.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'promotion_id':
                    $sort_string = 'PromotionsCoupon.id '. $sort_type;
                break;

                case 'code':
                    $sort_string = 'PromotionsCoupon.code '. $sort_type .', PromotionsCoupon.id DESC';
                break;

                case 'status':
                    $sort_string = 'PromotionsCoupon.status '. $sort_type .', PromotionsCoupon.id DESC';
                break;           
            }
        }        

        return $table->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    } 

    public function getDetailPromotionCoupon($id = null,$params = [])
    {
        $result = [];
        if(empty($id)) return [];        

        $status = !empty($params['status']) ? intval($params['status']) : null;

        $where = [
            'PromotionsCoupon.id' => $id,
        ];

        if(!is_null($status)) {
            $where['PromotionsCoupon.status'] = $status;
        }

        $result = TableRegistry::get('PromotionsCoupon')->find()->where($where)->first();
        return $result;
    }

    public function formatPromotionCouponDetail($data = [])
    {
        if(empty($data)) return [];
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'promotion_id' => !empty($data['promotion_id']) ? intval($data['promotion_id']) : null,
            'promotion_name' => !empty($data['Promotions']['name']) ? $data['Promotions']['name'] : '',
            'code' => !empty($data['code']) ? $data['code'] : null,
            'used' => !empty($data['used']) ? intval($data['used']) : 0,
            'number_use' => !empty($data['number_use']) ? intval($data['number_use']) : 0,
            'status' => isset($data['status']) ? intval($data['status']) : null,
            'created' => !empty($data['created']) ? date('H:i - d/m/Y', $data['created']) : null,
        ];

        return $result;
    }

    public function checkExist($data = null)
    {
        if(empty($data)) return false;
        foreach ($data as $key => $code) {
            $where = [
                'status'   => 1,
                'code'  => $code,
            ];

            $coupon = TableRegistry::get('PromotionsCoupon')->find()->where($where)->first();
            return !empty($coupon->id) ? true : false;
        }

        return false;
    }
}