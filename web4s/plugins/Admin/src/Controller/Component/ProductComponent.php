<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;

class ProductComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities', 'Admin.Tag'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
  

    public function saveProduct($data_save = [], $id = null, $product_old = [])
    {
        $result = [];
        $products_table = TableRegistry::get('Products');
        $products_item_table = TableRegistry::get('ProductsItem');        
        $tags_table = TableRegistry::get('Tags');

        $clear_items_id = [];

        // merge data with entity
        if(empty($id)){
            $product = $products_table->newEntity($data_save, [
                'associated' => ['ProductsContent', 'Links', 'CategoriesProduct', 'ProductsItem', 'ProductsAttribute' , 'TagsRelation']
            ]);

        }else{
            $product = $products_table->patchEntity($product_old, $data_save);

            // get old product_item_id
            $old_items_id = $products_item_table->find()->where(['product_id' => $id, 'deleted' => 0])->select('id')->toArray();

            // get new product_item_id save
            $new_items_id = [];
            if(!empty($data_save['ProductsItem'])){
                foreach($data_save['ProductsItem'] as $item){
                    if(!empty($item['id'])){
                        $new_items_id[] = intval($item['id']);
                    }                    
                }
            }
            
            foreach($old_items_id as $old){
                if(!in_array($old->id, $new_items_id)){
                    $clear_items_id[] = $old->id;
                }
            }
        }

        // show error validation in model
        if($product->hasErrors()){
            $list_errors = $this->Utilities->errorModel($product->getErrors());
            
            return $this->System->getResponse([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

        $products_item_attribute = !empty($data_save['products_item_attribute']) ? $data_save['products_item_attribute'] : [];
        if(isset($data_save['products_item_attribute'])){
            unset($data_save['products_item_attribute']);
        }
        
        $lang = !empty($data_save['ProductsContent']['lang']) ? $data_save['ProductsContent']['lang'] : null;
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            if(!empty($id)){
                TableRegistry::get('CategoriesProduct')->deleteAll(['product_id' => $id]);
                TableRegistry::get('ProductsAttribute')->deleteAll(['product_id' => $id]);
                TableRegistry::get('ProductsItemAttribute')->deleteAll(['product_id' => $id]);
                TableRegistry::get('TagsRelation')->deleteAll([
                    'foreign_id' => $id,
                    'type' => PRODUCT_DETAIL
                ]);

                if(!empty($clear_items_id)){
                    foreach($clear_items_id as $item_id){
                        $item_info = $products_item_table->find()->where(['ProductsItem.id' => $item_id])->first();
                        if(empty($item_info)) continue;

                        $exist_in_order = TableRegistry::get('OrdersItem')->checkItemProductExist($item_id);
                        if(!empty($exist_in_order)){
                            $entity_item = $products_item_table->patchEntity($item_info, ['deleted' => 1], ['validate' => false]);
                            $delete_item = $products_item_table->save($entity_item);
                        }else{
                            $delete_item = $products_item_table->delete($item_info);
                        }
                    }
                }
            }

            // save data
            $save = $products_table->save($product);
            if (empty($save->id)){
                throw new Exception();
            }

            $products_item_saved = !empty($save['ProductsItem']) ? $save['ProductsItem'] : [];

            $data_attribute = [];
            if(count($products_item_saved) == count($products_item_attribute)){
                $product_id = $save->id;
                foreach($products_item_saved as $k_item => $item){                   
                    $product_item_id = $item->id;
                    if(!empty($products_item_attribute[$k_item])){
                        foreach($products_item_attribute[$k_item] as $item_attribute){

                            $data_attribute[] = [
                                'product_id' => $product_id,
                                'product_item_id' => !empty($item_attribute['product_item_id']) ? intval($item_attribute['product_item_id']) : $product_item_id,
                                'attribute_id' => !empty($item_attribute['attribute_id']) ? intval($item_attribute['attribute_id']) : null,
                                'value' => !empty($item_attribute['value']) ? $item_attribute['value'] : null,
                            ];
                        }
                    }
                }
            }
            
            if(!empty($data_attribute)){
                $attributes_entities = TableRegistry::get('ProductsItemAttribute')->newEntities($data_attribute);
                $save_attribute = TableRegistry::get('ProductsItemAttribute')->saveMany($attributes_entities, ['associated' => false]);

                if (empty($save_attribute)){
                    throw new Exception();
                }
            }

            $conn->commit();            
            return $this->System->getResponse([
                CODE => SUCCESS, 
                DATA => $save
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function saveManyProduct($data_save = [], $product_old = [])
    {
        $result = [];
        $products_table = TableRegistry::get('Products');
        $products_item_table = TableRegistry::get('ProductsItem');
        $tags_table = TableRegistry::get('Tags');
        
        if(empty($data_save)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_co_du_lieu')]);
        }

        $products_item_attribute = [];
        foreach ($data_save as $k_data_save => $v_data_save) {
            $products_item_attribute[] = !empty($v_data_save['products_item_attribute']) ? $v_data_save['products_item_attribute'] : [];
            if(isset($v_data_save['products_item_attribute'])){
                unset($data_save[$k_data_save]['products_item_attribute']);
            }
        }
        
        if(!empty($data_save) && empty($product_old)) {
            $product = $products_table->newEntities($data_save, [
                'associated' => ['ProductsContent', 'Links', 'CategoriesProduct', 'ProductsItem', 'ProductsAttribute', 'ProductsItemAttribute', 'TagsRelation']
            ]);
        }


        if(!empty($data_save) && !empty($product_old)) {
            $product = $products_table->patchEntities($product_old, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            // save data
            $save = $products_table->saveMany($product);
            if (empty($save)){
                throw new Exception();
            }  

            $data_attribute = [];
            foreach ($save as $k_save => $v_save) {
                $products_item = !empty($v_save['ProductsItem']) ? $v_save['ProductsItem'] : [];
                
                if(count($products_item) == count($products_item_attribute[$k_save])){
                    if(!empty($products_item)) {
                        foreach ($products_item as $k_products_item => $product_item) {
                            $product_item_id = !empty($product_item['id']) ? $product_item['id'] : null;
                            $product_id = !empty($product_item['product_id']) ? $product_item['product_id'] : null;
                            if(!empty($products_item_attribute[$k_save][$k_products_item])) {
                                foreach ($products_item_attribute[$k_save][$k_products_item] as $k_item_attribute => $v_item_attribute) {
                                    $data_attribute[] = [
                                        'product_id' => $product_id,
                                        'product_item_id' => $product_item_id,
                                        'attribute_id' => !empty($v_item_attribute['attribute_id']) ? intval($v_item_attribute['attribute_id']) : null,
                                        'value' => isset($v_item_attribute['value']) ? $v_item_attribute['value'] : null
                                    ];
                                
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($data_attribute)){
                $attributes_entities = TableRegistry::get('ProductsItemAttribute')->newEntities($data_attribute);
                $save_attribute = TableRegistry::get('ProductsItemAttribute')->saveMany($attributes_entities, ['associated' => false]);

                if (empty($save_attribute)){
                    throw new Exception();
                }
            }


            $conn->commit();            
            return $this->System->getResponse([
                CODE => SUCCESS
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }
}
