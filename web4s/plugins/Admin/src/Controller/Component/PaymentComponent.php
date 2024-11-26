<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class PaymentComponent extends AppComponent
{
	public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
  

    public function savePayment($data = [], $id = null)
    {
        $result = [];
        $payments_table = TableRegistry::get('Payments');

        // validate data
        if(!isset($data['type']) || (isset($data['type']) && !in_array($data['type'], [0, 1]))){
            return $this->System->getResponse([MESSAGE => __d('admin', 'loai_phieu_thanh_toan_khong_hop_le')]);
        }

        if(empty($data['object_type']) || (!empty($data['object_type']) && !in_array($data['object_type'], [CUSTOMER, SUPPLIER, EMPLOYEE, SHIPPER, OTHER]))){
            return $this->System->getResponse([MESSAGE => __d('admin', 'doi_tuong_thanh_toan_khong_hop_le')]);
        }

        if(empty($data['amount']) && !in_array($data['payment_method'], [BANK, COD]) ){
            return $this->System->getResponse([MESSAGE => __d('admin', 'so_tien_thanh_toan_khong_hop_le')]);
        }

        if(empty($data['payment_method']) || (!empty($data['payment_method']) && !in_array($data['payment_method'], [CASH, BANK, CREDIT, GATEWAY, VOUCHER, COD]))){
            return $this->System->getResponse([MESSAGE => __d('admin', 'phuong_thuc_thanh_toan_khong_hop_le')]);
        }

        $payment_time = !empty($data['payment_time']) ? $data['payment_time'] : null;
        if(empty($payment_time) || !$this->Utilities->isDateTimeClient($payment_time)){
            $payment_time = date('H:i - d/m/Y');
        }
        $payment_time = $this->Utilities->stringDateTimeClientToInt($payment_time);

        $order_id = !empty($data['order_id']) ? intval($data['order_id']) : null;
        $foreign_type = !empty($data['foreign_type']) ? $data['foreign_type'] : ORDER;
        $foreign_id = !empty($data['foreign_id']) ? intval($data['foreign_id']) : null;

        // trong trường hợp không truyền foreign_id và có tham số order_id thì sẽ lấy order_id gán vào
        if(empty($foreign_id) && $foreign_type == ORDER && !empty($order_id)){
            $foreign_id = $order_id ;
        }

        $code = !empty($data['code']) ? $data['code'] : 'PAY' . $this->Utilities->generateRandomNumber(10);
        $reference = !empty($data['reference']) ? $data['reference'] : null;
        $full_name = !empty($data['full_name']) ? $data['full_name'] : null;
        $data_save = [
            'id' => $id,
            'code' => $code,
            'foreign_id' => $foreign_id,
            'foreign_type' => $foreign_type,
            'type' => $data['type'],
            'type_payment_id' => !empty($data['type_payment_id']) ? intval($data['type_payment_id']) : null,
            'object_type' => $data['object_type'],
            'object_id' => !empty($data['object_id']) ? intval($data['object_id']) : null,
            'amount' => $this->Utilities->formatToDecimal($data['amount']),
            'payment_method' => $data['payment_method'],
            'sub_method' => !empty($data['sub_method']) ? $data['sub_method'] : null,
            'payment_gateway_code' => !empty($data['payment_gateway_code']) ? $data['payment_gateway_code'] : null,
            'payment_gateway_response' => !empty($data['payment_gateway_response']) ? $data['payment_gateway_response'] : null,
            'payment_time' => $payment_time,
            'reference' => $reference,
            'full_name' => $full_name,
            'description' => !empty($data['description']) ? $data['description'] : null,
            'counted' => !empty($data['counted']) ? 1 : 0,
            'status' => !empty($data['status']) ? intval($data['status']) : 0,
            'created_by' => !empty($data['created_by']) ? intval($data['created_by']) : null,
            'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$code, $reference, $full_name]))
        ];

        // merge data with entity
        if(empty($id)){
            $payment = $payments_table->newEntity($data_save);
        }else{
            $payment = $payments_table->get($id);
            $payment = $payments_table->patchEntity($payment, $data_save);
        }

        // show error validation in model
        if($payment->hasErrors()){
            $list_errors = $this->Utilities->errorModel($payment->getErrors());
            
            return $this->System->getResponse([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save = $payments_table->save($payment);

            if (empty($save->id)){
                throw new Exception();
            }
            if(!empty($foreign_id) && $foreign_type == ORDER){
                $update_order = TableRegistry::get('Orders')->updateAfterPayment($foreign_id, $data['type']);
                if (empty($update_order)){
                    throw new Exception();
                }
            }

            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS, DATA => $save]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }
}
