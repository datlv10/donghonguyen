<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration51 extends AbstractMigration
{
    /**
     * 
     * - thêm field `language_admin` trong bảng `users`
     * 
     * - Cập nhật độ dài kiểu dữ liệu field `group_setting` trong bảng `settings`
     * 
     * 
     */

    public function up()
    {
        // thêm  column `rating` vào bảng `users`
        $users_table = $this->table('users');        
        if (!$users_table->hasColumn('language_admin')) {
            $users_table->addColumn('language_admin', 'string', [
                'after' => 'address',
                'limit' => 20,                
                'null' => true,
                'comment' => 'Ngôn ngữ quản trị của tài khoản'
            ])->update();
        }


        // cập nhật độ dài field `group_setting` từ varchar(20) -> varchar(50)
        if ($this->table('settings')->hasColumn('group_setting')) {
            $this->execute('ALTER TABLE `settings` MODIFY `group_setting` varchar(50) DEFAULT NULL');
        }
    }

    public function down()
    {
   
    }
}