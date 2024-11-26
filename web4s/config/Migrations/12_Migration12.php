<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration12 extends AbstractMigration
{

    /**
     * 
     * - bỏ bắt buộc nhập ở các column mới thêm từ các phiên bản migrate trước đó
     * 
     * 
     */

    public function up()
    {

        if ($this->table('promotions')->hasColumn('public')) {
            $this->execute('ALTER TABLE `promotions` MODIFY `public` int(11) DEFAULT 0');
        }
        
        if ($this->table('payments')->hasColumn('foreign_type')) {
            $this->execute('ALTER TABLE `payments` MODIFY `foreign_type` varchar(20) DEFAULT NULL');
        }

        if ($this->table('payments')->hasColumn('sub_method')) {
            $this->execute('ALTER TABLE `payments` MODIFY `sub_method` varchar(20) DEFAULT NULL');
        }
    }

    public function down()
    {

    }
}
