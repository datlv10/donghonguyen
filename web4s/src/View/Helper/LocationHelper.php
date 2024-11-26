<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class LocationHelper extends Helper
{   
    /** Lấy danh sách quốc gia kiểu định dạng dropdown
     * 
     * 
     * {assign var = data value = $this->Location->getListCountriesForDropdown()}
     * 
     * 
    */
    public function getListCountriesForDropdown()
    {
        $list_countries = Hash::combine(TableRegistry::get('Countries')->queryListCountries()->toArray(), '{n}.id', '{n}.name');

        return $list_countries;
    }

    /** Lấy danh sách tỉnh thành theo ID quốc gia kiểu định dạng dropdown
     * 
     * $country_id: ID của quốc gia mặc định việt nam là 1
     * 
     * {assign var = data value = $this->Location->getListCitiesForDropdown()}
     * 
     * 
    */
    public function getListCitiesForDropdown($country_id = null)
    {
        if(empty($country_id)){
            $country_id = 1;
        }
        $abc = TableRegistry::get('Cities')->queryListCities([FILTER =>[
            'country_id' => $country_id
        ]])->toArray();
        $list_cities = Hash::combine(TableRegistry::get('Cities')->queryListCities([FILTER =>[
            'country_id' => $country_id
        ]])->toArray(), '{n}.id', '{n}.name');

        return $list_cities;
    }

    /** Lấy danh sách quận huyện theo ID tỉnh thành kiểu định dạng dropdown
     * 
     * $city_id: ID của tỉnh thành
     * 
     * {assign var = data value = $this->Location->getListDistrictForDropdown($city_id)}
     * 
     * 
    */
    public function getListDistrictForDropdown($city_id = null)
    {
        if(empty($city_id)) return [];
        $list_district = Hash::combine(TableRegistry::get('Districts')->queryListDistricts([FILTER =>[
            'city_id' => $city_id
        ]])->toArray(), '{n}.id', '{n}.name');

        return $list_district;
    }

    /** Lấy danh sách phường xã theo ID quận huyện kiểu định dạng dropdown
     * 
     * $district_id: ID của quận huyện
     * 
     * {assign var = data value = $this->Location->getListWardForDropdown($district_id)}
     * 
     * 
    */
    public function getListWardForDropdown($district_id = null)
    {
        if(empty($district_id)) return [];
        $list_ward = Hash::combine(TableRegistry::get('Wards')->queryListWards([FILTER =>[
            'district_id' => $district_id
        ]])->toArray(), '{n}.id', '{n}.name');

        return $list_ward;
    }
}
