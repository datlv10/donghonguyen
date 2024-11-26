<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Utility\Security;
use Cake\I18n\FrozenTime;

class UtilitiesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
    }

    public function isJson($json_str = null)
    {
        return is_string($json_str) && is_array(json_decode($json_str, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public function isDateClient($str = null)
    {
        // check date d/m/Y 
        $matches = [];
        $pattern = '/^([0-9]{1,2})\\/([0-9]{1,2})\\/([0-9]{4})$/';
        if (!preg_match($pattern, $str, $matches)) return false;
        if (!checkdate($matches[2], $matches[1], $matches[3])) return false;
        return true;
    }

    public function isDateTimeClient($str = null)
    {
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

    public function convertIntgerToDateString($int = null)
    {
        if(empty($int)) return null;

        try{
            $result = date('d/m/Y', intval($int));
        }catch (Exception $e) {
            return null;
        }

        return $result;
    }

    public function convertIntgerToDateTimeString($int = null, $format = 'H:i - d/m/Y')
    {
        if(empty($int)) return null;
        if(empty($format)) $format = 'H:i - d/m/Y';

        try{
            $result = date(strval($format), intval($int));
        }catch (Exception $e) {
            return null;
        }

        return $result;
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
            if(!empty($item)){
                $str = str_replace('-', '', $this->formatUnicode($item));
                $str = str_replace('+', '', $str);
                $str = str_replace('(', '', $str);
                $str = str_replace(')', '', $str);
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '', $str);
                $str = str_replace('*', '', $str);
                $str = str_replace('_', '', $str);
                $result[] = $str;
            }            
        }

        return !empty($result) ?  implode(' | ', $result) : null;
    }

    public function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )), 1, $length);
    }

    public function generateRandomNumber($length = 10) {
        return substr(str_shuffle(str_repeat($x = '0123456789', ceil($length/strlen($x)) )), 1, $length);
    }

    public function parseFileSize($bytes = null, $decimals = 2)
    {
        if(empty($bytes)) return '0 bytes';
        
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, $decimals) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, $decimals) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, $decimals) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public function getSecureKeyCdn($domain = null, $domain_cdn = null, $auth_info = [])
    {
        if(empty($domain) || empty($domain_cdn) || empty($auth_info) || !is_array($auth_info)) return null;

        $auth_id = !empty($auth_info['id']) ? intval($auth_info['id']) : null;
        $full_name = !empty($auth_info['full_name']) ? $auth_info['full_name'] : null;
        $json_account = json_encode(['auth_id' => $auth_id, 'full_name' => $full_name]);
        
        $access_key_upload = Security::hash(implode(SEPARATOR_KEY_UPLOAD, [$domain, $domain_cdn, $json_account, ACCESS_KEY_UPLOAD]), 'md5', false);
        $string_encode = $domain . '|' . $domain_cdn . '|' . $json_account . '|' . $access_key_upload;
        return base64_encode($string_encode);
    }

    public function getSecureKeyFilemanagerTemplate($domain = null)
    {
        if(empty($domain)) return null;

        $access_key_upload = Security::hash(implode(SEPARATOR_KEY_UPLOAD, [$domain, ACCESS_KEY_UPLOAD]), 'md5', false);
        $string_encode = $domain . '|' . $access_key_upload;
        return base64_encode($string_encode);
    }

    public function dirToPath($dir = null)
    {
        if(empty($dir) || strpos($dir, SOURCE_DOMAIN) !== 0) return null;

        $path = str_replace(DS, '/', str_replace(SOURCE_DOMAIN, '', $dir));
        $path = rtrim($path, '/');

        return $path;
    }

    public function pathToDir($path = null)
    {
        if(empty($path)) return null;

        $dir = SOURCE_DOMAIN . str_replace('/', DS, $path);

        if(is_dir($dir)) $dir = rtrim($dir, DS) . DS;

        return $dir;
    }

    public function parseTimestampToLabelTime($time = null)
    {
        $result = [
            'time' => null,
            'full_time' => null,
            'diff_time' => null
        ];

        if(empty($time)) return $result;

        $time = date('Y-m-d H:i:s', $time);
        $time_input = new FrozenTime($time);
        $now = new FrozenTime();

        $interval = $now->diff($time_input);
        if (!empty($interval->format('%i'))) {
            $result['diff_time'] = 'i';
            $result['time'] = $interval->format('%i') . ' ' . __d('template', 'phut_truoc');
        }

        if (!empty($interval->format('%h'))) {
            $result['diff_time'] = 'h';
            $result['time'] = $interval->format('%h') . ' ' . __d('template', 'gio_truoc');
        }

        if (!empty($interval->format('%d'))) {
            $result['diff_time'] = 'd';
            $result['time'] = $interval->format('%d') . ' ' . __d('template', 'ngay_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }

        if (!empty($interval->format('%m'))) {
            $result['diff_time'] = 'm';
            $result['time'] = $interval->format('%m') . ' ' . __d('template', 'thang_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }


        if (!empty($interval->format('%y'))) {
            $result['diff_time'] = 'y';
            $result['time'] = $interval->format('%y') . ' ' . __d('template', 'nam_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }

        if (empty($result['time'])) {
            $result['diff_time'] = 's';
            $result['time'] = __d('template', 'vua_xong');
        }

        $result['full_time'] = str_replace('MONTH', __d('template', 'thang'), trim($result['full_time']));
        $result['full_time'] = str_replace('AT', __d('template', 'luc'), trim($result['full_time']));

        return $result;
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

}