<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;
use Cake\Cache\Cache;
use Cake\Utility\Text;

class ProductsPartnerStoreTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('products_partner_store');

        $this->setPrimaryKey('id');
    }

    public function queryListStorePartner($params = []) 
    {
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // where
        $where = ['ProductsPartnerStore.deleted' => 0];

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['ProductsPartnerStore.id', 'ProductsPartnerStore.partner', 'ProductsPartnerStore.partner_store_id', 'ProductsPartnerStore.name', 'ProductsPartnerStore.phone', 'ProductsPartnerStore.email', 'ProductsPartnerStore.address', 'ProductsPartnerStore.is_default'];
            break;

            case LIST_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['ProductsPartnerStore.id', 'ProductsPartnerStore.partner', 'ProductsPartnerStore.partner_store_id', 'ProductsPartnerStore.name'];
            break;
        }
        
        return $this->find()->where($where)->select($fields);

    }

    public function getAllStore($type = null)
    {
        $cache_key = PARTNER_STORE . '_all_' . $type;

        $result = Cache::read($cache_key);
        
        if(is_null($result)){
            $stores = $this->find()->where(['partner' => $type, 'deleted' => 0])->toArray();

            $result = [];
            if(!empty($stores)){
                foreach ($stores as $k => $store_info) {
                    $partner_store_id = !empty($store_info['partner_store_id']) ? intval($store_info['partner_store_id']) : null;
                    if(empty($partner_store_id)) continue;

                    $result[] = [
                        'id' => !empty($store_info['id']) ? intval($store_info['id']) : null,
                        'code' => !empty($store_info['code']) ? $store_info['code'] : null,
                        'name' => !empty($store_info['name']) ? $store_info['name'] : null,
                        'phone' => !empty($store_info['phone']) ? $store_info['phone'] : null,
                        'email' => !empty($store_info['email']) ? $store_info['email'] : null,
                        'address' => !empty($store_info['address']) ? $store_info['address'] : null,
                        'is_default' => !empty($store_info['is_default']) ? true : false,
                        'partner_store_id' => $partner_store_id
                    ];
                }
            }

            Cache::write($cache_key, $result);
        }

        return $result;   
    }

    public function getKiotVietDefaultBranchId()
    {   
        $store = $this->find()->where([
            'partner' => KIOTVIET, 
            'is_default' => 1,
            'deleted' => 0
        ])->select(['partner_store_id'])->first();

        // nếu ko có mặc định thì lấy branch đầu tiên
        if(empty($store)){
            $store = $this->find()->where([
                'partner' => KIOTVIET, 
                'deleted' => 0
            ])->select(['partner_store_id'])->first();
        }

        return !empty($store['partner_store_id']) ? intval($store['partner_store_id']) : null;
    }

}