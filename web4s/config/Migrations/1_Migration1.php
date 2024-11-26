<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration1 extends AbstractMigration
{
    /**
     * Xóa bảng branchs
     *
     */

    public function change()
    {
        // xóa bảng branchs
        if ($this->hasTable('branchs')) {
            $this->table('branchs')->drop()->save();
        }
    }
}
