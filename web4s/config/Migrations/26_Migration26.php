<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration26 extends AbstractMigration
{

    /**
     * 
     * - Cập nhật lại kiểu dữ liệu column `width`, `length`, `height`, `weight` bảng `products`
     * 
     * 
     */

    public function up()
    {

        if ($this->table('products')->hasColumn('width')) {
            $this->execute('ALTER TABLE `products` MODIFY `width` decimal(11,2) DEFAULT NULL');
        }

        if ($this->table('products')->hasColumn('length')) {
            $this->execute('ALTER TABLE `products` MODIFY `length` decimal(11,2) DEFAULT NULL');
        }

        if ($this->table('products')->hasColumn('height')) {
            $this->execute('ALTER TABLE `products` MODIFY `height` decimal(11,2) DEFAULT NULL');
        }

        if ($this->table('products')->hasColumn('weight')) {
            $this->execute('ALTER TABLE `products` MODIFY `weight` decimal(11,2) DEFAULT NULL');
        }
    }

    public function down()
    {

    }
}
