<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class Migration19 extends AbstractMigration
{

    /**
     * 
     * - cập nhật field search unicode của bảng customers
     * 
     * 
     */

    public function up()
    {
        $table = TableRegistry::get('Customers');
        $utilities = TableRegistry::get('Utilities');
        $loop = 50;
        for ($i = 1; $i <= $loop; $i++) { 
            $customers = $table->find()->select([
                'Customers.id', 
                'Customers.code',
                'Customers.full_name',
                'Customers.email',
                'Customers.phone'
            ])->limit(20)->page($i)->toList();
            if(empty($customers)) break;

            $data_save = [];
            foreach($customers as $customer){
                $code = !empty($customer['code']) ? $customer['code'] : null;
                $full_name = !empty($customer['full_name']) ? $customer['full_name'] : null;
                $email = !empty($customer['email']) ? $customer['email'] : null;
                $phone = !empty($customer['phone']) ? $customer['phone'] : null;

                $data_save[] = [
                    'id' => !empty($customer['id']) ? intval($customer['id']) : null,
                    'search_unicode' => strtolower($utilities->formatSearchUnicode([$code, $full_name, $email, $phone]))
                ];
            }

            $entities = $table->patchEntities($customers, $data_save, ['validate' => false]);

            try{
                $table->saveMany($entities);
            }catch (Exception $e) {
                break;
            }
        }
    }

    public function down()
    {
        
    }
}
