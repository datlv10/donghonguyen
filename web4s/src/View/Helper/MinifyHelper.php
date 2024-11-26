<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use JSMin\JSMin;
use CssMin;

class MinifyHelper extends Helper
{
    /** Giảm thiểu css js 
    */
    public function compress($files = [], $ext = 'css', $params = [])
    {
        $result = '';
        if(empty($files) || !is_array($files) || !in_array($ext, ['css', 'js'])) return $result;
        $mode = !empty($params['mode']) ? $params['mode'] : null;
        $lazy = !empty($params['lazy']) ? true : false;
        // load file origin if website in develop mode
        if(WEBSITE_MODE == DEVELOP || $mode == DEVELOP){
            foreach($files as $url_file){
                if($ext == 'js'){
                    $result .= '<script src="'. $url_file .'" type="text/javascript"></script>' . "\n";
                }else{
                    $result .= '<link href="'. $url_file .'" rel="stylesheet" type="text/css" />' . "\n";
                }                
            }
            return $result;
        }

        // if file cache not exist -> combine minify
        $file_name = Security::hash(implode('_', $files), 'md5', false) . '.' . $ext;
        $dir_file = CACHE . 'minify' . DS . $file_name;
        $file_cache = new File($dir_file);

        if(!$file_cache->exists()) {
            $variables_css = '';
            foreach($files as $url_file){
                $file = new File(preg_replace('/(\/+|\\+)/', DS, SOURCE_DOMAIN . $url_file), false);
                if(!$file->exists()) continue;

                if($ext == 'js'){
                    $result .= JSMin::minify($file->read()) . ';';
                }else{
                    if($file->name == 'variable.css'){
                        $variables_css = str_replace(':root {', '@variables {', $file->read());
                    }
                    $result .= CssMin::minify($variables_css . $file->read()) ."\n";
                }
            }

            $file_cache = new File($dir_file, true);

            
            if($ext == 'css'){
                // be sure no duplicate charsets
                $result = preg_replace('/@charset\s+[\'"].+?[\'"];?/i', '', $result);
                $result = '@charset "utf-8";' . "\n". $result;
                // replace url image and fonts
                $result = str_replace('../img/', URL_TEMPLATE . 'assets/img/', $result);
                $result = str_replace('../fonts/', URL_TEMPLATE . 'assets/fonts/', $result);
            }

            $file_cache->write($result);
        }


        switch ($ext) {
            case 'js':
                if($lazy){
                    return '<script data-src="'. str_replace(SOURCE_DOMAIN, '', $file_cache->path) .'" nh-lazy="script" type="text/javascript"></script>';
                }else{
                    if(!empty(DEVICE)){
                        return '<script src="'. str_replace(SOURCE_DOMAIN, '', $file_cache->path) .'" type="text/javascript"></script>';    
                    }else{
                        return '<script type="text/javascript">'. $file_cache->read() .'</script>';
                    }
                }
            break;

            case 'css':
                return '<style type="text/css" rel="preload">'. $file_cache->read() .'</style>';
            break;

            default:
                return '';
            break;
        }
    }
}
