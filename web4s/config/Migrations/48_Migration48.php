<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration48 extends AbstractMigration
{
    /**
     * 
     * - thêm  index `foreign_id` vào bảng `links`
     * 
     * 
     */

    public function up()
    {
        if ($this->hasTable('links')) {
            $query = "
                ALTER TABLE `links` 
                DROP INDEX `type_2`,
                ADD INDEX `type_2`(`type`, `lang`, `deleted`, `foreign_id`) USING BTREE;
            ";
            $this->execute($query);
        }
    }

    public function down()
    {
   
    }
}
