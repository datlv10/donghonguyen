<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class PluginComponent extends Component
{
	public $controller = null;

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function activePluginByTemPlate()
    {
        $template_default = TableRegistry::get('Templates')->find()->where([
            'Templates.is_default' => 1
        ])->first();
        if(empty($template_default)) return false;

        $list_code = !empty($template_default['plugins']) ? array_filter(explode(',', $template_default['plugins'])) : [];

        $table = TableRegistry::get('Plugins');
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // disable all plugins
            $disable_plugins = $table->updateAll([
                'status' => 0
            ],
            [
                'code <>' =>  MOBILE_APP
            ]);

            // active plugins by template
            if(!empty($list_code)){
                foreach($list_code as $code){                    
                    $plugin_info = $table->find()->where(['code' => trim($code)])->select(['id', 'code', 'status'])->first();
                    if(empty($plugin_info)) continue;

                    $active = $table->save($table->patchEntity($plugin_info, ['status' => 1], ['validate' => false]));
                    if (empty($active->id)){
                        throw new Exception();
                    }
                }
            }                    

            $conn->commit();

        }catch (Exception $e) {
            $conn->rollback();
            return false;
        }
        
        return true;   
    }
}
