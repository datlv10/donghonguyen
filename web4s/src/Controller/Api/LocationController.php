<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class LocationController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function cityList()
    {
        $this->responseApi([
            CODE => SUCCESS,
            DATA => TableRegistry::get('Cities')->getListCity()
        ]);
    }

    public function districtList()
    {
        $data = $this->data_bearer;

        $city_id = !empty($data['city_id']) ? intval($data['city_id'] ) : null;    
        if(empty($city_id)){
            $this->responseErrorApi([
                MESSAGE => __d('template', 'tham_so_api_chua_hop_le')
            ]);
        }

        $this->responseApi([
            CODE => SUCCESS,
            DATA => TableRegistry::get('Districts')->getListDistrict($city_id)
        ]);
    }

    public function wardList()
    {
        $data = $this->data_bearer;

        $district_id = !empty($data['district_id']) ? intval($data['district_id'] ) : null;    
        if(empty($district_id)){
            $this->responseErrorApi([
                MESSAGE => __d('template', 'tham_so_api_chua_hop_le')
            ]);
        }

        $this->responseApi([
            CODE => SUCCESS,
            DATA => TableRegistry::get('Wards')->getListWard($district_id)
        ]);
    }
}