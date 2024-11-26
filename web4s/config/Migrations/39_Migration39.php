<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration39 extends AbstractMigration
{

    /**
     * 
     * - bỏ bắt buộc nhập ở các column mới thêm từ các phiên bản migrate 37
     */

    public function up()
    {
        if ($this->table('payments')->hasColumn('sub_method')) {
            $this->execute('ALTER TABLE `payments` MODIFY `sub_method` varchar(20) DEFAULT NULL');
        }
    }

    public function down()
    {
   
    }
}
