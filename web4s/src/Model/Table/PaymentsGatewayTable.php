<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Cache\Cache;

class PaymentsGatewayTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('payments_gateway');
        $this->setPrimaryKey('id');

        $this->hasOne('PaymentsGatewayContent', [
            'className' => 'Publishing.PaymentsGatewayContent',
            'foreignKey' => 'payment_code',
            'bindingKey' => 'code',
            'joinType' => 'INNER',
            'propertyName' => 'PaymentsGatewayContent'
        ]);

        $this->hasMany('ContentMutiple', [
            'className' => 'PaymentsGatewayContent',
            'foreignKey' => 'payment_code',
            'bindingKey' => 'code',
            'joinType' => 'LEFT',
            'propertyName' => 'ContentMutiple'

        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('code')
            ->notEmptyString('code');

        return $validator;
    }

    public function getList($lang = null)
    {
        if(empty($lang)) return [];

        $cache_key = PAYMENT_GATEWAY . '_list_' . $lang;
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $contain = [
                'PaymentsGatewayContent' => function ($q) use ($lang) {
                    return $q->where([
                        'PaymentsGatewayContent.lang' => $lang
                    ]);
                }
            ];

            $fields = ['PaymentsGateway.id', 'PaymentsGateway.code', 'PaymentsGateway.config', 'PaymentsGateway.is_installment', 'PaymentsGatewayContent.name', 'PaymentsGatewayContent.content'];
            
            $payments_gateway = TableRegistry::get('PaymentsGateway')->find()->contain($contain)->where([
                'PaymentsGateway.status' => 1
            ])
            ->select($fields)
            ->toArray();

            $result = [];
            if(!empty($payments_gateway)){
                foreach ($payments_gateway as $k => $item) {
                    $code = !empty($item['code']) ? $item['code'] : null;
                    if(empty($code)) continue;

                    $result[$code] = [
                        'id' => !empty($item['id']) ? intval($item['id']) : null,
                        'code' => !empty($item['code']) ? $item['code'] : null,
                        'name' => !empty($item['PaymentsGatewayContent']['name']) ? $item['PaymentsGatewayContent']['name'] : null,
                        'config' => !empty($item['config']) ? json_decode($item['config'], true) : [],
                        'is_installment' => !empty($item['is_installment']) ? true : false,
                        'content' => !empty($item['PaymentsGatewayContent']['content']) ? $item['PaymentsGatewayContent']['content'] : null
                    ];
                }
            }
            Cache::write($cache_key, $result);
        }        
        return $result;
    }

    public function getConfig($code = null)
    {
        if(empty($code)) return [];

        $payments_gateway = TableRegistry::get('PaymentsGateway')->find()->where(['PaymentsGateway.code' => $code])->select(['PaymentsGateway.config'])->first();

        return !empty($payments_gateway['config']) ? json_decode($payments_gateway['config'], true)  : [];
    }

}