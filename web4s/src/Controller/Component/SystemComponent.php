<?php

namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Http\Client;

class SystemComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
  
	public function getLanguageAdmin()
	{
        $request = $this->controller->getRequest();

		$params = $request->getQueryParams();
		$session = $request->getSession();

		$table = TableRegistry::get('Languages');
		$list_languages = $table->getList();
		if(!empty($params[LANG]) && !empty($list_languages[$params[LANG]])){
			$session->write(LANG, $params[LANG]);
		}
		
		$lang = $session->read(LANG);
		if(empty($lang)){
			$lang = $table->getDefaultLanguage();
			$session->write(LANG, $lang);
		}
		
		return $lang;
	}

    public function getLanguageFrontend()
    {
        $request = $this->controller->getRequest();
        $lang = $request->getSession()->read(LANG_FRONTEND);        
        if(empty($lang)){
            $lang = TableRegistry::get('Languages')->getDefaultLanguage();
            $request->getSession()->write(LANG_FRONTEND, $lang);
        }
    
        return $lang;
    }

    public function getCurrencyFrontend()
    {
        $request = $this->controller->getRequest();
        $code = $request->getSession()->read(CURRENCY_PARAM);

        $result = [];
        $exchange_rate = 1;
        if(empty($code)){
            $currency_info = TableRegistry::get('Currencies')->getDefaultCurrency();    
        }else{
            $list_currencies = TableRegistry::get('Currencies')->getAll();
            $currency_info = !empty($list_currencies[$code]) ? $list_currencies[$code] : [];            
            if(empty($currency_info['is_default'])){
                $exchange_rate = !empty($currency_info['exchange_rate']) ? floatval($currency_info['exchange_rate']) : null;
            }            
        }

        if(empty($currency_info) || empty($currency_info['status'])) return [];

        return [
            'code' => !empty($currency_info['code']) ? $currency_info['code'] : null,
            'unit' => !empty($currency_info['unit']) ? $currency_info['unit'] : null,
            'exchange_rate' => $exchange_rate,
        ];
    }

	public function getNameUnique($class_name_table, $name = null, $index = 1)
    {
        $name_check = $name . ' ('. $index .')';
        if($index == 100){
            return $name_check;
        }

        $check = TableRegistry::get($class_name_table)->checkNameExist($name_check);
        if($check){
            $index ++;
            $name_check = $this->getNameUnique($class_name_table, $name, $index);
        }
        return $name_check;
    }

	public function getUrlUnique($url = null, $index = 1)
    {
        $url_check = $url . '-'. $index;
        if($index == 100){
            return $url_check;
        }

        $check = TableRegistry::get('Links')->checkExist($url_check);

        if($check){
            $index ++;
            $url_check = $this->getUrlUnique($url, $index);
        }
        return $url_check;
    }

    public function getResponse($params = []) 
    {
        $code = ERROR;
        if(!empty($params[CODE]) && in_array($params[CODE], [SUCCESS, ERROR])){
            $code = $params[CODE];
        }

        $message = !empty($params[MESSAGE]) ? $params[MESSAGE] : null;
        if(empty($params[MESSAGE]) && $code == ERROR){
            $message = __d('template', 'cap_nhat_khong_thanh_cong');
        }

        if(empty($params[MESSAGE]) && $code == SUCCESS){
            $message = __d('template', 'cap_nhat_thanh_cong');
        }
        
        $result = [
            CODE => $code,
            STATUS => !empty($params[STATUS]) ? intval($params[STATUS]) : 200,
            MESSAGE => $message
        ];

        if(isset($params[DATA])){
            $result[DATA] = !empty($params[DATA]) ? $params[DATA] : [];
        }

        if(isset($params[META])){
            $result[META] = !empty($params[META]) ? $params[META] : [];
        }

        return $result;
    }

    public function getProfileWebsite()
    {
        $profile = [
            'id' => 1,
            'full_name' => 'Lê Văn A',
            'created' => null,
            'expired_date' => null,
            'storage_cdn' => 10000
        ];

        return $profile;
    }
}
