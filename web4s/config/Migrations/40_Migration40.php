<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration40 extends AbstractMigration
{

    /**
     * 
     * - thêm  column `main_category_id` vào bảng `articles`
     * - thêm  column `main_category_id` vào bảng `products`
     * 
     * 
     */

    public function up()
    {
        $article_table = $this->table('articles');
        $product_table = $this->table('products');
        // thêm  column `main_category_id` vào bảng `articles`

        if (!$article_table->hasColumn('main_category_id')) {
            $article_table->addColumn('main_category_id', 'integer', [
                'limit' => 11,
                'after' => 'like',
                'null' => true
            ])->update();
        }

        // thêm  column `main_category_id` vào bảng `products`
        if (!$product_table->hasColumn('main_category_id')) {
            $product_table->addColumn('main_category_id', 'integer', [
                'limit' => 11,
                'after' => 'like',
                'null' => true
            ])->update();
        }
    }

    public function down()
    {
   
    }
}
