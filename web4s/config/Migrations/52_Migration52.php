<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration52 extends AbstractMigration
{
    /**
     * 
     * - cập nhật lại unicode field trong bảng `articles`
     * 
     */

    public function up()
    {
        $table = $this->table('articles');

        if ($table->hasColumn('images')) {
            $this->execute('ALTER TABLE `articles` MODIFY `images` text CHARACTER SET utf8 COLLATE utf8_general_ci  DEFAULT NULL;');
        }

        if ($table->hasColumn('url_video')) {
            $this->execute('ALTER TABLE `articles` MODIFY `url_video` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL');
        }

        if ($table->hasColumn('type_video')) {
            $this->execute('ALTER TABLE `articles` MODIFY `type_video` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci  DEFAULT NULL;');
        }

        if ($table->hasColumn('files')) {
            $this->execute('ALTER TABLE `articles` MODIFY `files` text CHARACTER SET utf8 COLLATE utf8_general_ci  DEFAULT NULL;');
        }
    }

    public function down()
    {
   
    }
}