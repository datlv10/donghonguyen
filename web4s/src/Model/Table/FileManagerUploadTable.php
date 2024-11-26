<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Psr\Http\Message\UploadedFileInterface;
use App\Lib\Image\ImageMagician;
use Cake\Utility\Text;

class FileManagerUploadTable extends FileManagerAppTable
{
    public function upload($file = null, $path = null)
    {        
        // validate
        $validate = $this->_validateFile($file);
        if(empty($validate[CODE]) || $validate[CODE] != SUCCESS) return $validate;

        if(empty($path) || strpos($path, $this->base_path) !== 0){
            return $this->responseData([MESSAGE => __d('filemanager', 'duong_dan_khong_hop_le')]);
        }

        // kiểm tra đường dẫn thư mục
        $dir = $this->_pathUrlToDir($path);
        if(empty(file_exists($dir))) {
            return $this->responseData([ MESSAGE => __d('filemanager', 'thu_muc_khong_ton_tai')]);
        }

        $file_name = strtolower(trim($file->getClientFilename()));
        $file_info = pathinfo($file_name);
        
        if(!isset($file_info['filename']) || empty(strlen($file_info['filename']))) {
            return $this->responseData([MESSAGE => __d('filemanager', 'ten_tep_khong_hop_le')]);
        }

        $basename = $file_info['filename'];    
        $basename = Text::slug($basename, '_'); // rename file theo định dạng ko có unicode
        $extension = !empty($file_info['extension']) ? strtolower($file_info['extension']) : null;
        $name = $this->_getUniqueFileName($basename, $extension, $dir);
    
        if(empty($name)) {
            return $this->responseData([MESSAGE => __d('filemanager', 'ten_tep_khong_hop_le')]);
        }

        $dir_new_file = $dir . $name;

        //upload 
        try {
            $file->moveTo($dir_new_file);
        } catch (Exception $ex) {
            return $this->responseData([MESSAGE => __d('filemanager', 'dang_tai_tep_khong_thanh_cong')]);
        }

        $type = $this->_getTypeFileByExtention($extension);

        // kiểm tra dung lượng tệp ảnh
        if($type == IMAGE){
            $dir_thumb = $this->_getDirThumb($dir);

            foreach(LIST_THUMBS_SIZE as $size){

                $new_file_basename = pathinfo($name, PATHINFO_FILENAME);
                $thumb_file = $dir_thumb . $new_file_basename . '_thumb_' . $size . '.' . $extension;

                // chỉ tạo thumb vs ảnh png và jpg, những định dạng ảnh khác thì sẽ nhân bản ảnh thumb
                if(in_array($extension, ['png', 'jpg', 'jpeg'])){
                    try{
                        $magician_image = new ImageMagician($dir_new_file);

                        /*
                        $option:     
                            0 / exact = defined size;
                            1 / portrait = keep aspect set height;
                            2 / landscape = keep aspect set width;
                            3 / auto = auto;
                            4 / crop= resize and crop;
                        */
                        $magician_image->resizeImage($size, $size, 3); // option == 3

                        $quality = 100;
                        $magician_image->saveImage($thumb_file, $quality);
                    }catch (Exception $e){
                        return $this->responseData([MESSAGE => __d('filemanager', 'tao_anh_thumb_khong_thanh_cong')]);
                    }
                }else{

                    $paste_thumb = $this->_pasteThumbImage($dir_new_file, $thumb_file);
                    if(empty($paste_thumb[CODE]) || $paste_thumb[CODE] != SUCCESS){
                        return $this->responseData([MESSAGE => __d('filemanager', 'tao_anh_thumb_khong_thanh_cong')]);
                    }
                }
                
            }
        }

        $file_info = $this->_getInfoFile($dir_new_file);

        return $this->responseData([
            CODE => SUCCESS,
            MESSAGE => __d('filemanager', 'dang_tai_tep_thanh_cong'),
            DATA => [
                'file_info' => $file_info
            ]
        ]);
    }

    private function _validateFile($file = null)
    {
        $utilities = TableRegistry::get('Utilities');
        if (empty($file) || !($file instanceof UploadedFileInterface)) {
            return $this->responseData([MESSAGE => __d('filemanager', 'tep_dang_tai_khong_hop_le')]);
        }

        if ($file->getError() > 0) {
            return $this->responseData([MESSAGE => $file->getError()]);
        }

        if (empty($file->getStream())) {
            return $this->responseData([MESSAGE => __d('filemanager', 'tep_dang_tai_khong_hop_le')]);
        }

        $filename = $file->getClientFilename();

        $size = $file->getSize();
        $file_type = $file->getClientMediaType();
        $extension = pathinfo(strtolower(trim($filename)), PATHINFO_EXTENSION);
        
        if(empty($file_type) || empty($extension)){
            return $this->responseData([MESSAGE => __d('filemanager', 'tep_dang_tai_khong_hop_le')]);
        }
        
        // kiểm tra dung lượng tệp
        if(defined('MAX_FILE_SIZE') && $size > MAX_FILE_SIZE){
            $max_size_label = $utilities->parseFileSize(MAX_FILE_SIZE);
            return $this->responseData([MESSAGE => __d('filemanager', 'dung_luong_tep_dang_tai_qua_lon_{0}', [$max_size_label])]);
        }

        // kiểm tra loại tệp
        if(defined('LIST_MIME_TYPES') && LIST_MIME_TYPES != '*' && is_array(LIST_MIME_TYPES) && !in_array($file_type, LIST_MIME_TYPES)){
            return $this->responseData([MESSAGE => __d('filemanager', 'tep_dang_tai_khong_hop_le')]);
        }

        // kiểm tra mine type content file upload
        if(empty($file->getStream()->getMetadata('uri')) || @mime_content_type($file->getStream()->getMetadata('uri')) != $file_type){
            return $this->responseData([MESSAGE => __d('filemanager', 'tep_dang_tai_da_bi_doi_duoi_mo_rong_vui_long_dang_tai_tep_goc')]);
        }

        $file_type_content = mime_content_type($file->getStream()->getMetadata('uri'));

        // kiểm tra đuôi mở rộng của tệp    
        if(defined('LIST_EXTENSIONS') && LIST_EXTENSIONS != '*' && is_array(LIST_EXTENSIONS) && !in_array($extension, LIST_EXTENSIONS)){
            return $this->responseData([MESSAGE => __d('filemanager', 'tep_dang_tai_khong_hop_le')]);
        }

        return $this->responseData([
            CODE => SUCCESS,
            MESSAGE => __d('filemanager', 'tep_dang_tai_hop_le')
        ]);
    }

    private function _pasteThumbImage($from_dir_file = null, $to_dir_file = null)
    {
        if(empty($from_dir_file) || empty(file_exists($from_dir_file))) {
            return $this->responseData([MESSAGE => __d('filemanager', 'duong_dan_khong_hop_le')]);
        }

        $copy = @copy($from_dir_file, $to_dir_file);
        if(empty($copy)) {
            return $this->responseData([MESSAGE => __d('filemanager', 'dan_tep_khong_thanh_cong')]);
        }    

        return $this->responseData([
            CODE => SUCCESS,
            MESSAGE => __d('filemanager', 'dan_tep_thanh_cong')
        ]);
    }

}