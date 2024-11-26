<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration50 extends AbstractMigration
{
    /**
     * 
     * - cập nhật lại quyền của tài khoản "Quản trị viên" mặc có quyền cập nhật block
     * 
     * 
     */

    public function up()
    {
        // kiểm tra có quyền "Quản trị viên" mặc định (id = 1)
        $role_admin = $this->fetchAll('SELECT `id`, `name` FROM `roles` WHERE `id` = 1 AND `name` = "Quản trị viên"');

        // cập nhật quyền 
        if(!empty($role_admin)){
            $builder = $this->getQueryBuilder();
            $builder->update('roles')->set(
                'permission', 
                '{"Article":{"view":"1","add":"1","status":"1","update":"1","delete":"1"},"Product":{"view":"1","add":"1","status":"1","update":"1","delete":"1"},"Brand":{"all":"1"},"Order":{"view":"1","add":"1","update":"1"},"Payment":{"view":"1","update":"1"},"Shipment":{"all":"1"},"Report":{"all":"1"},"Category":{"view":"1","add":"1","update":"1","delete":"1"},"Media":{"all":"1"},"Contact":{"all":"1"},"Customer":{"view":"1","add":"1","update":"1","delete":"1"},"Template":{"all":"1"},"TemplateBlock":{"all":"1"},"Comment":{"all":"1"},"Seo":{"view":"1","update":"1"},"SeoRedirect":{"view":"1","add":"1","update":"1","delete":"1"},"Tag":{"view":"1","add":"1","update":"1","delete":"1"},"Setting":{"view":"1","update":"1"},"Language":{"view":"1","update":"1"},"PaymentGateway":{"view":"1","update":"1"},"Attribute":{"view":"1","add":"1","update":"1","delete":"1"},"Currency":{"view":"1","add":"1","update":"1","delete":"1"},"Role":{"view":"1","add":"1","update":"1","delete":"1"},"User":{"view":"1","add":"1","update":"1","delete":"1"}}'
            )->where(['id' => 1])->execute();
        }
    }

    public function down()
    {
   
    }
}