<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration33 extends AbstractMigration
{

    /**
     * 
     * - bảng `customers` thêm column `identity_card_name`, `identity_card_where`
     */

    public function up()
    {

        // - bảng `customers` thêm column `identity_card_name`, `identity_card_where`
        $customer_table = $this->table('customers');

        if (!$customer_table->hasColumn('identity_card_name')) {
            $customer_table->addColumn('identity_card_name', 'string', [
                'limit' => 100,
                'after' => 'identity_card_date',
                'null' => true
            ])->update();
        }

        if (!$customer_table->hasColumn('identity_card_where')) {
            $customer_table->addColumn('identity_card_where', 'string', [
                'limit' => 100,
                'after' => 'identity_card_name',
                'null' => true
            ])->update();
        }
    }

    public function down()
    {
   
    }
}
