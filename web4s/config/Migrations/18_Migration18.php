<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\ORM\TableRegistry;

class Migration18 extends AbstractMigration
{

    /**
     * 
     * - chuyển `phone` bên table customer_address -----> `phone` table customer
     * 
     * 
     */

    public function up()
    {
        $table = TableRegistry::get('Customers');
        $loop = 50;
        for ($i = 1; $i <= $loop; $i++) { 
            $customers = $table->find()->contain(['DefaultAddress'])->select([
                'Customers.id', 
                'DefaultAddress.phone'
            ])->limit(20)->page($i)->toList();
            if(empty($customers)) break;

            $data_save = [];
            foreach($customers as $customer){
                $data_save[] = [
                    'id' => !empty($customer['id']) ? intval($customer['id']) : null,
                    'phone' => !empty($customer['DefaultAddress']['phone']) ? $customer['DefaultAddress']['phone'] : null,
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
