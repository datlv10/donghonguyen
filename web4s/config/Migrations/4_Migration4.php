<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration4 extends AbstractMigration
{

    /**
     * - xóa bảng email_templates_content    
     */

    public function up()
    {
        // xóa bảng email_templates_content
        if ($this->hasTable('email_templates_content')) {
            $this->table('email_templates_content')->drop()->save();
        }
    }

    public function down()
    {

    }
}
