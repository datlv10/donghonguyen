<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class GenealogyController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function detail($id = null)
    {
        $table = TableRegistry::get('Genealogies');
        $genealogy = $table->getDetailGenealogy($id);

        $arr_sex = [
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
        ];

        $arr_relationship = [
            1 => 'Chồng',
            2 => 'Vợ',
            3 => 'Con',
        ];

        $arr_status = [
            0 => 'Đã mất',
            1 => 'Còn sống'
        ];

        $arr_education_level = Configure::read('education_level');

        $genealogy['sex_name'] = !empty($genealogy['sex']) && !empty($arr_sex[$genealogy['sex']]) ? $arr_sex[$genealogy['sex']] : null;
        $genealogy['status_name'] = isset($genealogy['status']) && !empty($arr_status[$genealogy['status']]) ? $arr_status[$genealogy['status']] : null;
        $genealogy['education_level_name'] = !empty($genealogy['education_level']) && !empty($arr_education_level[$genealogy['education_level']]) ? $arr_education_level[$genealogy['education_level']] : null;

        $genealogy['generation'] = !empty($genealogy['generation']) ? $table->romanNumerals($genealogy['generation']) : 'I';

        
        // genealogical: Thuộc phả đồ 0: không | 1: thuộc
        // generation: Thế hệ thứ
        // relationship: Mối quan hệ | Vợ | Chồng | Con
        // relationship_info: Thông tin mối quan hệ
        // relationship_position: Mối quan hệ thứ



        // lấy thông tin mối quan hệ
        $relationship = !empty($genealogy['relationship']) ? intval($genealogy['relationship']) : null;
        $relationship_position = !empty($genealogy['relationship_position']) ? intval($genealogy['relationship_position']) : 1;

        $relationship_name = '';
        switch ($relationship) {
            case 3:
                $relationship_name = '';
                $sex_name = 'trai';
                if (!empty($genealogy['sex']) && $genealogy['sex'] == 'female') {
                    $sex_name = 'gái';
                } elseif (!empty($genealogy['sex']) && $genealogy['sex'] == 'other') {
                    $sex_name = '';
                }

                $relationship_name = 'Con ' . $sex_name;

                break;
            case 2: 
                $relationship_name = 'Vợ';

                break;
            case 1: 
                $relationship_name = 'Chồng';
                
                break;
        }

        $relationship_info = !empty($genealogy['relationship_info']) ? intval($genealogy['relationship_info']) : null;
        $relationship_info = $table->getDetailGenealogy($relationship_info);

        $relationship_description = 'Nguồn Gốc: Đại Tổ';
        if (!empty($relationship_info)) {
            $relationship_info_name = !empty($relationship_info['full_name']) ? $relationship_info['full_name'] : '';
            $relationship_info_sex = !empty($relationship_info['sex']) ? $relationship_info['sex'] : '';

            $relationship_info_sex_name = '';
            if ($relationship_info_sex == 'male') {
                $relationship_info_sex_name = 'Ông';
            } elseif ($relationship_info_sex == 'female') {
                $relationship_info_sex_name = 'Bà';
            }

            $relationship_description = 'Nguồn Gốc: ' . $relationship_name . ' thứ ' . $relationship_position . ' của ' . $relationship_info_sex_name . ' ' . $relationship_info_name;
        }

        $genealogy['relationship_description'] = !empty($relationship_description) ? $relationship_description : null;

        // danh sách vợ/chồng
        $list_wife = $list_husband = [];
        if ($genealogy['sex'] == 'male') {
            $list_wife = $table->getListWife($id);
            if (!empty($list_wife)) {
                foreach ($list_wife as $key => $wife) {
                    $list_wife[$key] = $this->formatDataGenealogy($wife);
                }
            }
        }

        if ($genealogy['sex'] == 'female') {
            $list_husband = $table->getListHusband($id);
            if (!empty($list_husband)) {
                foreach ($list_husband as $key => $husband) {
                    $list_husband[$key] = $this->formatDataGenealogy($husband);
                }
            }
        }

        // danh sách con
        $list_child = $table->getListChild($id);
        if (!empty($list_child)) {
            foreach ($list_child as $key => $child) {
                $list_child[$key] = $this->formatDataGenealogy($child);
            }
        }

        $this->set('genealogy', $genealogy);
        $this->set('list_husband', $list_husband);
        $this->set('list_wife', $list_wife);
        $this->set('list_child', $list_child);
    }

    public function formatDataGenealogy($data = [])
    {
        if (empty($data)) return [];

        $arr_sex = [
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
        ];

        $arr_status = [
            0 => 'Đã mất',
            1 => 'Còn sống'
        ];

        $arr_education_level = Configure::read('education_level');

        $full_name = !empty($data['full_name']) ? $data['full_name'] : null;
        $image_avatar = !empty($data['image_avatar']) ? $data['image_avatar'] : null;
        $generation = !empty($data['generation']) ? $data['generation'] : null;

        $sex_name = !empty($data['sex']) && !empty($arr_sex[$data['sex']]) ? $arr_sex[$data['sex']] : null;
        $status_name = isset($data['status']) && !empty($arr_status[$data['status']]) ? $arr_status[$data['status']] : null;
        $education_level_name = !empty($data['education_level']) && !empty($arr_education_level[$data['education_level']]) ? $arr_education_level[$data['education_level']] : null;

        $generation = TableRegistry::get('Genealogies')->romanNumerals($generation);

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'full_name' => $full_name,
            'year_of_birth' => !empty($data['year_of_birth']) ? $data['year_of_birth'] : null,
            'year_of_death' => !empty($data['year_of_death']) ? $data['year_of_death'] : null,
            'relationship_position' => !empty($data['relationship_position']) ? $data['relationship_position'] : null,
            'generation' => $generation,
            'image_avatar' => $image_avatar,
            'education_level_name' => $education_level_name,
            'sex_name' => $sex_name,
            'status_name' => $status_name
        ];

        return $result;
    }
}