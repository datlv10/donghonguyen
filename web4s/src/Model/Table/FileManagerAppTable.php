<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class FileManagerAppTable extends Table
{
    public $base_path = '/media';
    public $default_dir = SOURCE_DOMAIN . DS . 'templates' . DS . CODE_TEMPLATE . DS . 'assets';

    public function initialize(array $config): void
    {        
        parent::initialize($config);
    }

    public function responseData($params = []) 
    {
        $code = ERROR;
        if(!empty($params[CODE]) && in_array($params[CODE], [SUCCESS, ERROR])){
            $code = $params[CODE];
        }

        $message = !empty($params[MESSAGE]) ? $params[MESSAGE] : null;
        if(empty($params[MESSAGE]) && $code == ERROR){
            $message = __d('filemanager', 'xu_ly_khong_thanh_cong');
        }

        if(empty($params[MESSAGE]) && $code == SUCCESS){
            $message = __d('filemanager', 'xu_ly_thanh_cong');
        }
        
        $result = [
            CODE => $code,
            STATUS => !empty($params[STATUS]) ? ($params[STATUS]) : 200,
            MESSAGE => $message
        ];

        if(isset($params[DATA])){
            $result[DATA] = !empty($params[DATA]) ? $params[DATA] : [];
        }

        if(isset($params[META])){
            $result[META] = !empty($params[META]) ? $params[META] : [];
        }

        return $result;
    }

    public function _dirToPathUrl($dir = null)
    {       
        if(empty($dir) || (strpos($dir, $this->default_dir . DS . MEDIA) !== 0 && strpos($dir, $this->default_dir . DS . MEDIA_THUMBS) !== 0)) return null;

        $path = str_replace(DS, '/', str_replace($this->default_dir, '', $dir));
        $path = rtrim($path, '/');

        return $path;
    }

    public function _pathUrlToDir($path = null)
    {        
        if(empty($path) || strpos($path, $this->base_path) !== 0) return null;
    
        $dir = $this->default_dir . str_replace('/', DS, $path);        
        if(is_dir($dir)) $dir = rtrim($dir, DS) . DS;

        return $dir;
    }

    public function _getTypeFileByExtention($extension = null)
    {
        $type = FILE;

        if($extension == FOLDER) $type = FOLDER;
        if(in_array($extension, ['png', 'jpg', 'jpeg', 'webp', 'svg', 'ico', 'gif']))  $type = IMAGE;
        if(in_array($extension, ['mp4', 'mov', 'wmv', 'avi', 'flv', 'swf', 'f4v', 'mkv', 'mpeg', 'mpeg-2']))  $type = VIDEO;
        if(in_array($extension, ['mp3', 'm4a', 'flac', 'wav', 'wma', 'aac']))  $type = AUDIO;
        if(in_array($extension, ['doc', 'docx', 'pdf', 'xls', 'xlsx', 'csv','txt', 'ppt', 'pptx']))  $type = DOCUMENT;
        if(in_array($extension, ['zip', 'rar', '7z']))  $type = ARCHIVE;

        return $type;
    }

    public function _getDirThumb($dir = null)
    {
        if(empty($dir) || strpos($dir, $this->default_dir . DS . MEDIA) !== 0) return null;
        $dir = str_replace($this->default_dir . DS . MEDIA, $this->default_dir . DS . MEDIA_THUMBS, $dir);
        $dir = rtrim($dir, DS) . DS;
        return $dir;
    }

    public function _getUniqueFolderName($folder_name = null, $dir = null, $loop = 1)
    {
        if(empty($folder_name) || empty($dir)) return null;

        $dir = rtrim($dir, DS) . DS;
        if(!file_exists($dir . $folder_name)) return $folder_name;

        $name = $folder_name .'_' . $loop;
        if(file_exists($dir . $name)){
            $loop++;
            $name = $this->_getUniqueFolderName($folder_name, $dir, $loop);
        }

        return $name;
    }

    public function _getUniqueFileName($basename = null, $extension = null, $dir = null, $loop = 1)
    {

        if(is_null($basename) || empty(strlen($basename)) || empty($extension) || empty($dir)) return null;

        $dir = rtrim($dir, DS) . DS;
        if(!file_exists($dir . $basename . '.' . $extension)) return $basename . '.' . $extension;

        $filename = $basename . '_' . $loop . '.' . $extension;
        if(file_exists($dir . $filename)){
            $loop++;
            $filename = $this->_getUniqueFileName($basename, $extension, $dir, $loop);
        }
        
        return $filename;
    }

    public function _getInfoFile($dir_file = null)
    {
        if(empty($dir_file)) return [];
        if(!file_exists($dir_file)) return [];

        $path = $this->_dirToPathUrl($dir_file);

        $is_dir = is_dir($dir_file) ? true : false;

        $dir_info = pathinfo($dir_file);
        $filename = !empty($dir_info['basename']) ? $dir_info['basename'] : null;
        $extension = !empty($dir_info['extension']) ? strtolower($dir_info['extension']) : null;

        if($is_dir){
            $folder_info = $this->_countDirSize();
            $size = !empty($folder_info['size']) ? intval($folder_info['size']) : 0;

            $extension = FOLDER;
        }else{
            $size = filesize($dir_file);
        }

        $type = $this->_getTypeFileByExtention($extension);

        $result = [
            'is_dir' => $is_dir,
            'type' => $type,
            'path' => $path,
            'filename' => $filename,
            'time' => filectime($dir_file),
            'size' => $size,
            'extension' => $extension
        ];

        return $result;
    }
}