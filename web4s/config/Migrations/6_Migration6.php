<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration6 extends AbstractMigration
{

    /**
     * - cập nhật giá trị column `foreign_type` = 'order' trong bảng payments
     * - thực hiện cập nhật column này vì mới được thêm từ version migrate 5
     * - migrate này có thể dẫn đến sai dữ liệu bảng payment nếu không chạy cùng với version migrate 5
     * 
     */

    public function up()
    {   
    
        // cập nhật giá trị column `foreign_type` = 'order'
        $this->execute("UPDATE `payments` SET `foreign_type` = 'order'");

    }

    public function down()
    {

    }
}
