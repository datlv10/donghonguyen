<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration2 extends AbstractMigration
{

    /**
     * Thêm field `permission` trong bảng `roles` 
     * Thêm data khởi tạo cho field `permission`
     */

    public function up()
    {
        // kiểm tra bảng `roles` đã tồn tại field `permission` hay chưa
        $table = $this->table('roles');
        if (!$table->hasColumn('permission')) {
            // thêm field `permission`
            $table->addColumn('permission', 'text', [
                'after' => 'short_description'
            ])->update();

            $default_value = '{"Article":{"view":"1","add":"1","status":"1","update":"1","delete":"1"},"Product":{"view":"1","add":"1","status":"1","update":"1","delete":"1"},"Brand":{"all":"1"},"Order":{"view":"1","add":"1","update":"1"},"Payment":{"view":"1","update":"1"},"Shipment":{"all":"1"},"Report":{"all":"1"},"Category":{"view":"1","add":"1","update":"1","delete":"1"},"Media":{"all":"1"},"Contact":{"all":"1"},"Customer":{"view":"1","add":"1","update":"1","delete":"1"},"Template":{"all":"1"},"Comment":{"all":"1"},"Seo":{"view":"1","update":"1"},"SeoRedirect":{"view":"1","add":"1","update":"1","delete":"1"},"Tag":{"view":"1","add":"1","update":"1","delete":"1"},"Setting":{"view":"1","update":"1"},"Language":{"view":"1","update":"1"},"PaymentGateway":{"view":"1","update":"1"},"Attribute":{"view":"1","add":"1","update":"1","delete":"1"},"Currency":{"view":"1","add":"1","update":"1","delete":"1"},"Role":{"view":"1","add":"1","update":"1","delete":"1"},"User":{"view":"1","add":"1","update":"1","delete":"1"}}';

            // set giá trị mặc định
            $update = $this->execute("UPDATE `roles` SET `permission` = '$default_value'"); 
        }
    }

    /**
     * Xóa field `permission` trong bảng `roles` 
     */
    public function down()
    {
        // kiểm tra bảng `roles` đã tồn tại field `permission` hay chưa
        $table = $this->table('roles');
        if ($table->hasColumn('permission')) {
            // xóa field `permission`
            $table->removeColumn('permission')->save();
        }
    }
}
