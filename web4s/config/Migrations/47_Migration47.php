<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration47 extends AbstractMigration
{
    /**
     * 
     * - thêm  column `rating` vào bảng `articles`
     * - thêm  column `rating_number` vào bảng `articles`
     * 
     * 
     */

    public function up()
    {
        $article_table = $this->table('articles');
        // thêm  column `rating` vào bảng `articles`
        if (!$article_table->hasColumn('rating')) {
            $article_table->addColumn('rating', 'float', [
                'after' => 'main_category_id',
                'null' => true,
                'precision' => 2,
                'scale'=> 1
            ])->update();
        }
        
        // thêm  column `rating_number` vào bảng `articles`
        if (!$article_table->hasColumn('rating_number')) {
            $article_table->addColumn('rating_number', 'integer', [
                'limit' => 11,
                'after' => 'rating',
                'null' => true
            ])->update();
        }
    }

    public function down()
    {
        
    }
}
