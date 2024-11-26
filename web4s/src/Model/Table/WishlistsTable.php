<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;

class WishlistsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('wishlists');
        $this->setPrimaryKey('id');

    }

    public function validationDefault(Validator $validator): Validator
    {

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function wishlistTotal($account_id = null)
    {
        $result = [];

        if(empty($account_id)) return [];

        $wishlist_info = TableRegistry::get('Wishlists')->find()->where([
            'customer_account_id' => $account_id
        ])->toArray();

        $wishlist_info = Hash::combine($wishlist_info, '{n}.id', '{n}.record_id', '{n}.type');

        return $result = [
            PRODUCT => !empty($wishlist_info[PRODUCT]) ? array_values($wishlist_info[PRODUCT]) : [],
            ARTICLE => !empty($wishlist_info[ARTICLE]) ? array_values($wishlist_info[ARTICLE]) : [],
        ];
   
        return $result;
    }

}