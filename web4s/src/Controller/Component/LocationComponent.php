<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;

class LocationComponent extends Component
{
	public $controller = null;

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function getFullAddress($params = [])
    {
        $country_id = !empty($params['country_id']) ? intval($params['country_id']) : 1;
        $city_id = !empty($params['city_id']) ? intval($params['city_id']) : null;
        $district_id = !empty($params['district_id']) ? intval($params['district_id']) : null;
        $ward_id = !empty($params['ward_id']) ? intval($params['ward_id']) : null;
        $address = !empty($params['address']) ? $params['address'] : null;

        $country_name = $city_name = $district_name = $ward_name = '';
        $full_address = [];
        if(!empty($address)) {
            $full_address[] = $address;
        }

        if(!empty($ward_id)){
            $ward_info = TableRegistry::get('Wards')->find()->where(['Wards.id' => $ward_id])->first();
            $ward_name = !empty($ward_info['name']) ? $ward_info['name'] : null;
            if(!empty($ward_name)){
                $full_address[] = $ward_name;
            }
        }        

        if(!empty($district_id)){
            $district_info = TableRegistry::get('Districts')->find()->where(['Districts.id' => $district_id])->first();
            $district_name = !empty($district_info['name']) ? $district_info['name'] : null;
            if(!empty($district_name)){
                $full_address[] = $district_name;
            }
        }

        if(!empty($city_id)){
            $city_info = TableRegistry::get('Cities')->find()->where(['Cities.id' => $city_id])->first();
            $city_name = !empty($city_info['name']) ? $city_info['name'] : null;
            if(!empty($city_name)){
                $full_address[] = $city_name;
            }
        }

        $country_info = TableRegistry::get('Countries')->find()->where(['Countries.id' => $country_id])->first();
        $country_name = !empty($country_info['name']) ? $country_info['name'] : null;

        $full_address = !empty($full_address) ? implode(', ', $full_address) : null;

        return [
            'country_name' => $country_name,
            'city_name' => $city_name,
            'district_name' => $district_name,
            'ward_name' => $ward_name,
            'full_address' => $full_address
        ];

    }
}
