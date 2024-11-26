<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;

class WishlistComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'ReCaptcha'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function addProduct($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $record_id = !empty($data['record_id']) ? intval($data['record_id']) : [];
        $type = !empty($data['type']) ? $data['type'] : null;        
        if(empty($record_id) || empty($type) || !in_array($type, [PRODUCT, ARTICLE])){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);

        if(empty($member)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_dang_nhap_tai_khoan_de_su_dung_chuc_nang_nay')]);
        }

        $account_id = !empty($member['account_id']) ? intval($member['account_id']) : null;
        if(empty($account_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $table = TableRegistry::get('Wishlists');
        $wishlist_info = $table->find()->where([
            'customer_account_id' => $account_id
        ])->toArray();

        $wishlist_ids = !empty($wishlist_info) ? Hash::extract($wishlist_info, '{n}.record_id') : [];

        $data_wishlist = [];

        $messages = '';
        switch ($type) {
            case ARTICLE:
                $messages = __d('template', 'them_thanh_cong_bai_viet_yeu_thich');
            break;
            
            case PRODUCT:
                $messages = __d('template', 'them_thanh_cong_san_pham_yeu_thich');
            break;
        }   

        if(!in_array($record_id, $wishlist_ids)) {
            $data_wishlist[] = [
                'customer_account_id' => $account_id,
                'type' => $type,
                'record_id' => intval($record_id)
            ];
        } else {
            // nếu record id đã có trong list thì trả về kết quả luôn
            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => $messages
            ]);
        }
        
        if(empty($wishlist_info)) {
            $data_save = $table->newEntities($data_wishlist);
        } else {
            $data_save = $table->patchEntities($wishlist_info, $data_wishlist);
        }
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save = $table->saveMany($data_save);
            
            if (empty($save)){
                throw new Exception();
            }

            $conn->commit();

            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => $messages
            ]);
        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function removeProduct($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        // check recaptcha
        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $records_id = !empty($data['record_id']) ? explode(',', $data['record_id']) : [];
        $type = !empty($data['type']) ? $data['type'] : null;
        if(empty($records_id) || empty($type) || !in_array($type, [PRODUCT, ARTICLE])){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        if(empty($member)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_dang_nhap_tai_khoan_de_su_dung_chuc_nang_nay')]);
        }

        $account_id = !empty($member['account_id']) ? intval($member['account_id']) : null;
        if(empty($account_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $table = TableRegistry::get('Wishlists');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($records_id as $record_id){
                $wishlist_info = $table->find()->where([
                    'customer_account_id' => $account_id,
                    'type' => $type,
                    'record_id' => $record_id
                ])->first();

                if(empty($wishlist_info)){
                    return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_yeu_thich')]);
                }

                $delete = $table->delete($wishlist_info);

                if (empty($delete)){
                    throw new Exception();
                }
            }
       
            $conn->commit();

            $messages = '';
            switch ($type) {
                case ARTICLE:
                    $messages = __d('template', 'xoa_thanh_cong_bai_viet_yeu_thich');
                break;
                
                case PRODUCT:
                    $messages = __d('template', 'xoa_thanh_cong_san_pham_yeu_thich');
                break;
            }  

            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => $messages
            ]);
        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }
}
