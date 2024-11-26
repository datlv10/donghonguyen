<?php

namespace App\Lib\SmsBrandname;


class SmsBrandnameUtilities
{
    public function getResponse($params = []) 
    {
        $code = ERROR;
        if(!empty($params[CODE]) && in_array($params[CODE], [SUCCESS, ERROR])){
            $code = $params[CODE];
        }

        $message = !empty($params[MESSAGE]) ? $params[MESSAGE] : null;
        if(empty($params[MESSAGE]) && $code == ERROR){
            $message = __d('template', 'xu_ly_du_lieu_khong_thanh_cong');
        }

        if(empty($params[MESSAGE]) && $code == SUCCESS){
            $message = __d('template', 'xu_ly_du_lieu_thanh_cong');
        }
        
        $result = [
            CODE => $code,
            MESSAGE => $message
        ];

        if(isset($params[DATA])){
            $result[DATA] = !empty($params[DATA]) ? $params[DATA] : [];
        }

        return $result;
    }

    public function isJson($json_str = null)
    {
        return is_string($json_str) && is_array(json_decode($json_str, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

}

?>