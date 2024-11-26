<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class UploadFileComponent extends Component
{
    public function upload($file = null, $path = null, $params = [])
    {
        $result = [
            CODE => ERROR,
            MESSAGE => ''
        ];

        // check params
        $file_name = !empty($params['file_name']) ? $params['file_name'] : null;
        if(empty($file_name)){
            $file_name = $file->getclientFilename();
        }

        $white_list = !empty($params['white_list']) ? $params['white_list'] : [];
        if(empty($white_list)){
            $white_list = Configure::read('WHITE_LIST_EXTENSION');
        }
        
        $ext_file = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $error_file = $file->getError();
        $size_file = $file->getSize();
        $tmp_name = $file->getStream()->getMetadata('uri');

        if($size_file > MAX_SIZE_FILE_UPLOAD){
            $result[MESSAGE] = __d('admin', 'kich_thuoc_tep_tin_qua_lon');
            return $result;
        }

        if($error_file != 0){
            $result[MESSAGE] = __d('admin', 'tep_tin_khong_hop_le');
            return $result;
        }

        if(!in_array($ext_file, $white_list)) {
            $result[MESSAGE] = __d('admin', 'sai_dinh_dang');
            return $result;
        }

        $path = rtrim($path, '/');
        $path = rtrim($path, DS);

        $destination = $path . DS . $file_name;
        $counter = 1;
        if(file_exists($destination)) {
            while(file_exists($destination)) {
                $destination = $path . DS . substr($file_name, 0, strpos($file_name, '.')) . "({$counter})" . strstr($file_name, '.');
                $counter++;
            }
        }

        $moved = move_uploaded_file($tmp_name, $destination);
        
        $result = [
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'upload_thanh_cong'),
            DATA => [
                'file' => TableRegistry::get('Utilities')->dirToPath($destination),
                'dir_file' => $destination
            ]
        ];

        return $result;
    }
}