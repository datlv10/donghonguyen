<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Exception\Exception;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

class UtilitiesAdminHelper extends Helper
{    

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

    public function isJson($json_str = null)
    {
        return is_string($json_str) && is_array(json_decode($json_str, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public function getTypeFileByUrl($url_file = null)
    {
        $ext = pathinfo($url_file, PATHINFO_EXTENSION);

        $type = '';
        switch($ext){
            case 'jpg':
            case 'png':
            case 'gif':
            case 'jpeg':
            case 'svg':
            case 'bmp':
                $type = 'image';
            break;

            case 'xlsx':
            case 'xlsm':
            case 'xls':
                $type = 'excel';
            break;

            case 'doc':
            case 'docx':
                $type = 'word';
            break;

            case 'pdf':
                $type = 'pdf';
            break;

            case 'mp3':
            case 'flac':
            case 'm4a':
                $type = 'audio';
            break;

            case 'mp4':
            case 'swf':
            case 'avi':
            case '3gp':
            case 'mov':
            case 'wmv':
            case 'webm':
                $type = 'video';
            break;
        }

        return $type;
    }

    public function getArrayKeys($list = [])
    {
        if(!is_array($list)) return [];

        return array_keys($list);
    }

    public function getUrlWebsite()
    {
        $request = $this->getView()->getRequest();
        return $request->scheme() . '://' . $request->host();
    }

    public function parseFileSize($bytes = null, $decimals = 2)
    {
        return TableRegistry::get('Utilities')->parseFileSize($bytes, $decimals);
    }
}
