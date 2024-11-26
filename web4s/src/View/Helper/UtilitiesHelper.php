<?php
declare(strict_types=1);

namespace App\View\Helper;
use Cake\View\Helper;
use Cake\Core\Exception\Exception;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\View\Helper\UrlHelper;
use Cake\Routing\Router;
use Cake\I18n\FrozenTime;

class UtilitiesHelper extends Helper
{
    /** Chuyển đổi định dạng int sang định dạng ngày tháng năm
     * 
     * $int*: thời gian(int)
     * $format: định dạng ngày ngày tháng năm(string)
     * 
     * {$this->Utilities->convertIntgerToDateString($article.created)}
    */
    public function convertIntgerToDateString($int = null, $format = 'd/m/Y')
    {
        if(empty($int)) return null;
        if(empty($format)) $format = 'd/m/Y';

        try{
            $result = date(strval($format), intval($int));
        }catch (Exception $e) {
            return null;
        }
        return $result;
    }

    /** Chuyển đổi định dạng int sang định dạng giờ phút - ngày tháng năm
     * 
     * $int*: thời gian(int)
     * $format: định dạng giờ phút - ngày ngày tháng năm(string)
     * 
     * {$this->Utilities->convertIntgerToDateTimeString($article.created)}
    */
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

    /** Chuyển đổi định dạng int sang định dạng giờ phút - ngày tháng năm
     * 
     * $int*: thời gian(int)
     * $format: định dạng giờ phút - ngày ngày tháng năm(string)
     * 
     * {$this->Utilities->convertIntgerToDateTimeString($article.created)}
    */
    public function formatDateTimeClient($str_time = null, $format = 'H:i - d/m/Y')
    {
        if(empty($str_time)) return null;

        $int = $this->stringDateTimeClientToInt($str_time);

        if(empty($format)) $str_time;

        try{
            $result = date(strval($format), intval($int));
        }catch (Exception $e) {
            return null;
        }
        
        return $result;
    }

    /** Chuyển đổi định dạng DataTime sang định dạng Int
     * 
     * $str_date*: thời gian(string) ví dụ: H:i - d/m/Y 
     * 
     * {$this->Utilities->stringDateTimeClientToInt($str_date)}
    */
    public function stringDateTimeClientToInt($str_date = null)
    {
        // check datetime H:i - d/m/Y 
        if(!$this->isDateTimeClient($str_date)){
            return null;
        }

        $time = FrozenTime::createFromFormat('H:i - d/m/Y', $str_date, null);
        $time = $time->format('Y-m-d H:i:s');
        return strtotime($time);
    }

    /** Kiểm tra định dạng DateTime
     * 
     * $str*: thời gian(string) ví dụ: H:i - d/m/Y 
     * 
     * {$this->Utilities->isDateTimeClient($str)}
    */
    public function isDateTimeClient($str = null)
    {
        // check datetime H:i - d/m/Y
        $matches = [];
        $pattern = '/^([0-9]{1,2})\:([0-9]{1,2})\s\-\s([0-9]{1,2})\\/([0-9]{1,2})\\/([0-9]{4})$/';
        if (!preg_match($pattern, $str, $matches)) return false;

        return true;
    }

    /** Lấy thời gian hiện tại
     * 
     * $format: thời gian(string) ví dụ: d/m/Y 
     * 
     * {$this->Utilities->getCurrentDate($format)}
    */
    public function getCurrentDate($format = null)
    {
        if(empty($format)) $format = 'Y-m-d';

        try{
            $date = date(strval($format));
        }catch (Exception $e) {
            return null;
        }

        return $date;
    }

    /** Lấy mã ngẫu nhiên bao gồm cả số cả chữ
     * 
     * $length: độ dài(int) 
     * 
     * {assign var = data value = $this->Utilities->randomCode()}
    */
    public function randomCode($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyz', intval(ceil($length/strlen($x))))), 1, $length);
    }

    /** Lấy ảnh thumb
     * 
     * $url*: đường dẫn ảnh(str) 
     * $size*: kích thước ảnh thumb(str) - ví dụ: 50 | 150 | 250 | 350
     * $source: nguồn từ website hoặc cdn mặc định website - ví dụ: template
     * 
     * {CDN_URL}{$this->Utilities->getThumbs($url, 150)}
     * {$this->Utilities->getThumbs($url, 350, 'template')}
    */
    public function getThumbs($url = null, $size = null, $source = null)
    {
        $result = [];
        if(empty($url) || empty($size) || !in_array($size, [50, 150, 250, 350, 500, 720])) return $result;
        
        $path_info = pathinfo($url);
        $extension = !empty($path_info['extension']) ? $path_info['extension'] : '';
        $filename = !empty($path_info['filename']) ? $path_info['filename'] : '';
        
        if(empty($extension)) return $result;

        switch ($source) {
            case 'template':
                $url = $this->str_replace_first('media', 'media_thumbs', $url);
                $path = explode('/', $url);
                break;
            
            default:
                $path = explode('/', $url);
                $path[1] = 'thumbs';
                break;
        }

        $num_last =  count($path) - 1;
        $path[$num_last] = $filename . '_thumb_'. $size . '.'. $extension;
        return implode('/', $path);
    }

    /** thay thế string đầu tiên trong văn bản
     * 
     * $find_string*: Từ cần thay thế(str) 
     * $replace*: Từ được thay thế(str)
     * $content*: Nội dung văn bản được áp dụng
     * 
     * {$this->Utilities->str_replace_first('media', 'media_thumbs', $url)}
    */
    public function str_replace_first($find_string = null, $replace = null, $content = null)
    {
        $find_string = '/' . preg_quote($find_string, '/') . '/';

        return preg_replace($find_string, $replace, $content, 1);
    }

    /** Lấy tên file
     * 
     * $url_file*: đường dẫn file
     * 
     * {assign var = data value = $this->Utilities->getFileNameInUrl($url_file)}
    */
    public function getFileNameInUrl($url_file = null)
    {
        if(empty($url_file)) return null;
        return pathinfo($url_file, PATHINFO_BASENAME);
    }    

    /** Lấy đường dẫn nội bộ
     * 
     * $url*: đường dẫn
     * 
     * {$this->Utilities->checkInternalUrl($url)}
    */
    public function checkInternalUrl($url = null)
    {
        if(empty($url)) return '';

        if(strpos($url, $this->getUrlWebsite()) === 0){
            $url = str_replace($this->getUrlWebsite(), '', $url);
        }

        if(strpos($url, 'https://') === 0 || strpos($url, 'http://') === 0 || strpos($url, 'www') === 0 || strpos($url, 'https://www') === 0){
            return $url;
        }

        if(strpos($url, '/') === 0){
            return $url;
        }
        
        return '/' . $url;
    }

    /** Thêm tham số vào đường dẫn
     * 
     * $url*: đường dẫn
     * $add: option thêm tham số đường dẫn
     * $remove: option xóa tham số đường dẫn
     * $options: cho phép truyền nhiều giá trị cách nhau bởi "-"
     * 
     * {$this->Utilities->addParamsToUrl($this->Url->build(), ['item_color' => $option_id], [], ['merge' => true])}
     * {$this->Utilities->addParamsToUrl($this->Url->build(), [], ['item_color'])}
    */
    public function addParamsToUrl($url = null, $add = [], $remove = [], $options = [])
    {
        if(empty($url)){
            $url = '/';
        }

        if(empty($add) && empty($remove)) return $url;

        $url_data = parse_url($url);
        $path = !empty($url_data['path']) ? $url_data['path'] : '';
        $query = [];
        if(!empty($url_data['query'])){
            $tmp = [];
            parse_str($url_data['query'], $tmp);
            
            foreach ($tmp as $k => $value) {
                $k = str_replace('amp;', '', $k);
                $query[$k] = $value;
            }
        }

        if(!empty($add)){
            foreach ($add as $key => $value) {
                if(!empty($options['merge']) && !empty($query[$key])){
                    $value = $query[$key] . '-' . $value;
                    $list_value = array_unique(explode('-', $value));
                    $query[$key] = implode('-', $list_value);
                }else{
                    $query[$key] = $value;
                }                
            }
        }
        
        $query_result = [];
        foreach ($query as $k => $value) {
            $k = str_replace('amp;', '', $k);
            $query_result[$k] = $value;
        }

        if(!empty($remove)){
            foreach ($remove as $key) {
                unset($query_result[$key]);
            }
        }
        $query_result = http_build_query($query_result);         
        return !empty($query_result) ? $path . '?' . $query_result : $path;
    }

    public function toggleValueParamsToUrl($url = null, $key = null, $value = null, $options = [])
    {
        if(empty($url)){
            $url = '/';
        }
        if(empty($key) || empty($value)) return $url;
        $url_data = parse_url($url);

        $path = !empty($url_data['path']) ? $url_data['path'] : '';
        $query = [];
        if(!empty($url_data['query'])){
            $tmp = [];
            parse_str($url_data['query'], $tmp);
            
            foreach ($tmp as $k => $v_tmp) {
                $k = str_replace('amp;', '', $k);
                $query[$k] = $v_tmp;
            }
        }
        if(!empty($query[$key]) && !empty($options['merge'])){
            $list_value = array_unique(explode('-', $query[$key]));

            $key_remove = array_search($value, $list_value);
            if(isset($key_remove)) {
                unset($list_value[$key_remove]);
            }

            if(empty($list_value)) {
                unset($query[$key]);
            } else {
                $query[$key] = implode('-', $list_value);
            }
        }
        if(!empty($query[$key]) && $query[$key] == $value) {
            unset($query[$key]);
        }
        
        $query_result = [];
        foreach ($query as $k => $value) {
            $k = str_replace('amp;', '', $k);
            $query_result[$k] = $value;
        }
        $query_result = http_build_query($query_result);  

        return !empty($query_result) ? $path . '?' . $query_result : $path;
    }

    /** Đường dẫn hiện tại
     *
     * {$this->Utilities->getUrlCurrent()}
    */
    public function getUrlCurrent() 
    {
        return Router::url(null, true);
    }

    /** Đường dẫn hiện tại
     *
     * {$this->Utilities->getUrlPath()}
    */
    public function getUrlPath() 
    {
        $request = $this->getView()->getRequest();
        return $request->scheme() . '://' . $request->host() . $request->getPath();
    }

    /** tên miền
     *
     * {$this->Utilities->getUrlWebsite()}
    */
    public function getUrlWebsite()
    {
        $request = $this->getView()->getRequest();
        return $request->scheme() . '://' . $request->host();
    }

    /** Lấy value qua key trên url
     *
     * {assign var = data value = $this->Utilities->getParamsByKey('limit')}
    */
    public function getParamsByKey($key = null)
    {
        if(empty($key)) return null;
        return $this->getView()->getRequest()->getQuery($key);    
    }

    /** thay thế biến hệ thống
    */
    public function replaceVariableSystem($str = null)
    {
        if(empty($str)) return '';

        if(defined('URL_TEMPLATE') && !is_null(URL_TEMPLATE)) $str = str_replace('{URL_TEMPLATE}', URL_TEMPLATE, $str);
        if(defined('CDN_URL') && !is_null(CDN_URL)) $str = str_replace('{CDN_URL}', CDN_URL, $str);
        if(defined('PATH_TEMPLATE') && !is_null(PATH_TEMPLATE)) $str = str_replace('{PATH_TEMPLATE}', PATH_TEMPLATE, $str);
        if(defined('CODE_TEMPLATE') && !is_null(CODE_TEMPLATE)) $str = str_replace('{CODE_TEMPLATE}', CODE_TEMPLATE, $str);
        if(defined('PAGE_URL') && !is_null(PAGE_URL)) $str = str_replace('{PAGE_URL}', PAGE_URL, $str);
        if(defined('LANGUAGE') && !is_null(LANGUAGE)) $str = str_replace('{LANGUAGE}', LANGUAGE, $str);

        return $str;
    }

    /** Thay đổi định dạng thành danh sách id => value
     *
     * {assign var = data value = $this->Utilities->formatToList($data, 'id', 'name')}
    */
    public function formatToList($data = [], $key_id = 'id', $key_value = 'name')
    {
        if(empty($data) || !is_array($data)) return [];

        $result = [];
        foreach ($data as $item) {
            $id = !empty($item[$key_id]) ? $item[$key_id] : null;
            $value = !empty($item[$key_value]) ? $item[$key_value] : null;

            if(is_null($id) || is_null($value)) continue;
            $result[$id] = $value;
        }

        return $result;
    }
}
