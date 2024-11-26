<?php

namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;

class UtilitiesComponent extends Component
{
    public function errorModel($list_errors = [])
    {
        $result = [];
        if(empty($list_errors)) return [];
        foreach($list_errors as $key => $errors){
            if(is_array($errors)){
                foreach($errors as $k => $error){
                    if(is_array($error)){
                        foreach($error as $e){
                            @$result[] = $k . ' - ' . $e;
                        }
                    }else{
                        $result[] = $key . ' - ' . $error;    
                    }                    
                }
            }else{
                $result[] = $key . ' - ' . $error;
            }
        }
        return $result;
    }

    public function isJson($json_str = null)
    {
        return is_string($json_str) && is_array(json_decode($json_str, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public function isDateClient($str = null)
    {
        if(empty($str)) return false;

        // check date d/m/Y 
        $matches = [];
        $pattern = '/^([0-9]{1,2})\\/([0-9]{1,2})\\/([0-9]{4})$/';
        if (!preg_match($pattern, $str, $matches)) return false;
        if (!checkdate($matches[2], $matches[1], $matches[3])) return false;
        return true;
    }

    public function isDateTimeClient($str = null)
    {
        if(empty($str)) return false;

        // check datetime H:i - d/m/Y
        $matches = [];
        $pattern = '/^([0-9]{1,2})\:([0-9]{1,2})\s\-\s([0-9]{1,2})\\/([0-9]{1,2})\\/([0-9]{4})$/';
        if (!preg_match($pattern, $str, $matches)) return false;
        if (!checkdate($matches[4], $matches[3], $matches[5])) return false;
        return true;
    }

    public function stringDateClientToInt($str_date = null)
    {   
        // check datetime d/m/Y 
        if(!$this->isDateClient($str_date)){
            return null;
        }
        return strtotime(date('Y-m-d', strtotime(str_replace('/', '-', $str_date))));
    }

    public function stringDateTimeClientToInt($str_date = null)
    {
        // check datetime H:i - d/m/Y 
        if(!$this->isDateTimeClient($str_date)){
            return null;
        }

        $time = Time::createFromFormat('H:i - d/m/Y', $str_date, null);
        $time = $time->format('Y-m-d H:i:s');
        return strtotime($time);
    }

    public function stringDateToInt($str_date = null)
    {   
        // check date Y-m-d
        $matches = [];
        $pattern = '/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/';
        if(!preg_match($pattern, $str_date, $matches)) return null;
        if(!checkdate($matches[2], $matches[3], $matches[1])) return null;
        return strtotime(date('Y-m-d', strtotime($str_date)));
    }

    public function stringDateTimeToInt($str_date = null)
    {
        // check datetime Y-m-d H:i:s
        $matches = [];
        $pattern = '/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})\s([0-9]{1,2})\:([0-9]{1,2})\:([0-9]{1,2})$/';
        if(!preg_match($pattern, $str_date, $matches)) return null;
        if(!checkdate($matches[2], $matches[3], $matches[1])) return null;
        return strtotime(date('Y-m-d H:i:s', strtotime($str_date)));
    }

    public function formatUnicode($str = null)
    {
        if (empty($str)) {
            return '';
        }

        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd' => 'đ|Đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i' => 'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ'
        ];
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, trim($str));
        }
        return $str;
    }

    public function formatSearchUnicode($data = [])
    {
        $result = [];
        foreach ($data as $k => $item) {
            if(!empty($item) && gettype($item) == 'string'){
                $result[] = Text::slug(strtolower($item), ' ');
            }
        }


        return !empty($result) ?  implode(' | ', $result) : null;
    }

    public function formatToUrl($str = null)
    {
        if(empty($str)) return '';
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $url_setting = !empty($settings['url']) ? $settings['url'] : [];

        $disable_unicode = !empty($url_setting['disable_unicode']) ? true : false;

        if($disable_unicode){
            $str = $this->formatUnicode($str);        
            $str = preg_replace('`\[.*\]`U', '', $str);
            $str = preg_replace('`&(amp;)?#?[a-z0-9]+;`i', '-', $str);        
            $str = preg_replace('`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i', '\\1', $str);        
            
            return mb_strtolower($str, 'UTF-8');
        }else{
            return Text::slug(strtolower($str), '-');
        }        
    }

    public function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )), 1, $length);
    }

    public function generateRandomNumber($length = 10) {
        return substr(str_shuffle(str_repeat($x = '0123456789', ceil($length/strlen($x)) )), 1, $length);
    }

    public function formatPaginationInfo($pagination_info = [])
    {
        $result = [
            'field' => !empty($pagination_info['sort']) ? $pagination_info['sort'] : 'id',
            'sort' => !empty($pagination_info['direction']) ? $pagination_info['direction'] : DESC,
            'page' => !empty($pagination_info['page']) ? intval($pagination_info['page']) : 1,
            'pages' => !empty($pagination_info['pageCount']) ? intval($pagination_info['pageCount']) : 1,
            'perpage' => !empty($pagination_info['perPage']) ? intval($pagination_info['perPage']) : PAGINATION_LIMIT_ADMIN,
            'current' => !empty($pagination_info['current']) ? intval($pagination_info['current']) : 0,
            'total' => !empty($pagination_info['count']) ? intval($pagination_info['count']) : 0
        ];

        return $result;
    }

    public function formatToDecimal($number = 0, $decimal = 2)
    {
        if(empty($number)){
            return NUMBER_EMPTY;
        }

        return number_format((float)$number, $decimal, '.', '');
    }

    public function str_replace_first($find_string = null, $replace = null, $content = null)
    {
        $find_string = '/' . preg_quote($find_string, '/') . '/';

        return preg_replace($find_string, $replace, $content, 1);
    }
}
