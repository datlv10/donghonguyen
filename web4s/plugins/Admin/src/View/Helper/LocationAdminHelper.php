<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class LocationAdminHelper extends Helper
{
    public function getListCitiesForDropdown($country_id = null)
    {
        if(empty($country_id)){
            $country_id = 1;
        }
        
        $list_cities = Hash::combine(TableRegistry::get('Cities')->queryListCities([FILTER =>[
            'country_id' => $country_id
        ]])->toArray(), '{n}.id', '{n}.name');

        return $list_cities;
    }

    public function getListDistrictForDropdown($city_id = null)
    {
        $list_district = Hash::combine(TableRegistry::get('Districts')->queryListDistricts([FILTER =>[
            'city_id' => $city_id
        ]])->toArray(), '{n}.id', '{n}.name');

        return $list_district;
    }

    public function getListWardForDropdown($district_id = null)
    {        
        $list_ward = Hash::combine(TableRegistry::get('Wards')->queryListWards([FILTER =>[
            'district_id' => $district_id
        ]])->toArray(), '{n}.id', '{n}.name');

        return $list_ward;
    }

    public function getDetailCity($city_id = null)
    {
        $city = TableRegistry::get('Cities')->find()->where(['id' => $city_id])->select(['id', 'name'])->first();
        return !empty($city) ? $city : [];
    }
}
