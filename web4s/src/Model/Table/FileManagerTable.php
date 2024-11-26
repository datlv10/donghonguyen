<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Utility\Text;
use Cake\Collection\Collection;

class FileManagerTable extends FileManagerAppTable
{
    public function getListFilesInDir($dir = [], $options = [])
    {
        if(empty($dir) || empty(file_exists($dir))) {
            return $this->responseData([ MESSAGE => __d('filemanager', 'khong_lay_duoc_thong_tin_duong_dan')]);
        }

        $data_result = [
            FILES => [],
            PAGINATION => [],
            EXTEND => []
        ];

        // thêm dấu / đằng sau dir (trong trường hợp không có)
        $dir = rtrim($dir, DS) . DS;
      
        // thêm item quay lại
        $is_root = $dir == $this->default_dir . DS . MEDIA . DS ? true : false;

        // lấy danh sách tệp
        $all = scandir($dir);
        if(empty($all) || count($all) <= 2){
            return $this->responseData([
                CODE => SUCCESS,
                DATA => $data_result
            ]);
        }

        // params
        $limit = !empty($options[LIMIT]) ? intval($options[LIMIT]) : PAGINATION_LIMIT_DEFAULT;
        $page = !empty($options[PAGE]) ? intval($options[PAGE]) : 1;

        $sort = !empty($options[SORT]) ? $options[SORT] : [];
        $sort_type = !empty($sort[TYPE]) && in_array($sort[TYPE], [ASC, DESC]) ? $sort[TYPE] : ASC;
        $sort_field = !empty($sort[FIELD]) && in_array($sort[FIELD], [NAME, SIZE, TIME, EXTENSION]) ? $sort[FIELD] : null;

        // filter
        $filter = !empty($options[FILTER]) ? $options[FILTER] : [];

        $keyword_filter = !empty($filter[KEYWORD]) ? strtolower($filter[KEYWORD]) : null;
        $type_filter = !empty($filter[TYPE]) && in_array($filter[TYPE], [IMAGE, VIDEO, AUDIO, DOCUMENT, ARCHIVE]) ? $filter[TYPE] : null;

        $files = $folders = [];
        $get_time = $get_size = false;
        if($sort_field == TIME) $get_time = true;
        if($sort_field == SIZE) $get_size = true;

        // duyệt tất cả tệp
        foreach($all as $k => $file){
            if ($file == '.' || $file == '..') continue;

            $full_path = $dir . $file;
            $time = $get_time ? filemtime($full_path) : null;
            $file_lcase = strtolower($file);
            $size = 0;
            $is_dir = false;
            $has_child_folder = false;
            if (is_dir($full_path)) {
                if($get_size){                    
                    $folder_info = $this->_countDirSize($full_path);
                    $size = !empty($folder_info['size']) ? intval($folder_info['size']) : 0;
                    $has_child_folder = !empty($folder_info['has_child_folder']) ? true : false;
                }

                $is_dir = true;
                $extension = FOLDER;

                $item = [
                    'is_dir' => true,
                    'full_path' => $full_path,
                    'file' => $file,
                    'file_lcase' => $file_lcase,
                    'size' => $size,
                    'time' => $time,
                    'extension' => $extension,
                    'has_child_folder' => $has_child_folder
                ];

            }else{

                $path_info = pathinfo($file);
                $filename = !empty($path_info['filename']) ? $path_info['filename'] : null;
                $extension = !empty($path_info['extension']) ? strtolower($path_info['extension']) : null;

                // bỏ qua những tệp ko có extension hoặc tên tệp
                if(empty($extension) || empty($filename)) continue;

                if($get_size) $size = filesize($full_path);
                
                $item = [
                    'is_dir' => false,
                    'full_path' => $full_path,
                    'file' => $file,
                    'file_lcase' => $file_lcase,
                    'size' => $size,
                    'time' => $time,
                    'extension' => $extension,
                    'has_child_folder' => false
                ];
            }

            //type
            $type = $this->_getTypeFileByExtention($extension);

            $item['type'] = $type;

            // lọc dữ liệu
            if(!empty($type_filter) && $type_filter != $type && $type != FOLDER) continue;
            if(!empty($keyword_filter) && strpos($file_lcase, $keyword_filter) === false) continue;

            // thêm vào item
            if($is_dir){
                $folders[] = $item;
            }else{
                $files[] = $item;
            }
        }

        if(empty($folders) && empty($files)){
            return $this->responseData([
                CODE => SUCCESS,
                DATA => $data_result
            ]);
        }

        $folders_collection = new Collection($folders);
        $files_collection = new Collection($files);

        // sắp xếp
        switch($sort_field){
            case TIME:

                $folders = $folders_collection->sortBy('time', $sort_type == ASC ? SORT_ASC :SORT_DESC, SORT_NATURAL)->toArray();
                $files = $files_collection->sortBy('time', $sort_type == ASC ? SORT_ASC :SORT_DESC, SORT_NATURAL)->toArray();
            break;

            case SIZE:

                $folders = $folders_collection->sortBy('size', $sort_type == ASC ? SORT_ASC :SORT_DESC, SORT_NATURAL)->toArray();
                $files = $files_collection->sortBy('size', $sort_type == ASC ? SORT_ASC :SORT_DESC, SORT_NATURAL)->toArray();
            break;

            case EXTENSION:
                $files = $files_collection->sortBy('extension', $sort_type == ASC ? SORT_ASC :SORT_DESC, SORT_STRING)->toArray();
            break;

            case NAME:
            default:
                $folders = $folders_collection->sortBy('file_lcase', $sort_type == ASC ? SORT_ASC :SORT_DESC, SORT_STRING)->toArray();
                $files = $files_collection->sortBy('file_lcase', $sort_type == ASC ? SORT_ASC :SORT_DESC, SORT_STRING)->toArray();
            break;
        }
        
        $sorted = array_merge($folders, $files);

        // lấy thông tin dữ liệu theo số trang hiện tại
        $start_split = $limit * ($page - 1);
        $files_pagination = array_slice($sorted, $start_split, $limit);

        if(empty($files_pagination)) {
            return $this->responseData([
                CODE => SUCCESS,
                DATA => $data_result
            ]);
        }
         
        foreach($files_pagination as $item){
            $is_dir = !empty($item['is_dir']) ? true : false;
            $type = !empty($item['type']) ? $item['type'] : null;
            $file = !empty($item['file']) ? $item['file'] : null;
            $full_path = !empty($item['full_path']) ? $item['full_path'] : null;
            $time = !empty($item['time']) ? intval($item['time']) : 0;
            $size = !empty($item['size']) ? intval($item['size']) : 0;
            $has_child_folder = !empty($item['has_child_folder']) ? true : false;
            $extension = !empty($item['extension']) ? $item['extension'] : null;

            if(empty($file) || empty($full_path) || empty($extension)) continue;

            // nếu chưa get_time ở trên ds tổng thì cần lấy lại filetime
            if(!$get_time) $time = filemtime($full_path);
            
            if($is_dir){
                // nếu chưa get_size ở trên ds tổng thì cần lấy lại filesize
                if(!$get_size){                    
                    $folder_info = $this->_countDirSize($full_path);
                    
                    $size = !empty($folder_info['size']) ? intval($folder_info['size']) : 0;
                    $has_child_folder = !empty($folder_info['has_child_folder']) ? true : false;
                }
            }else{
                // nếu chưa get_size ở trên ds tổng thì cần lấy lại filesize
                if(!$get_size) $size = filesize($full_path);
            }

            $path = $this->_dirToPathUrl($full_path);

            $files_result[] = [
                'is_dir' => $is_dir,
                'type' => $type,
                'path' => $path,
                'filename' => $file,
                'time' => $time,
                'size' => $size,
                'extension' => $extension,
                'has_child_folder' => $has_child_folder
            ];
        }

        if(empty($files_result)) {
            return $this->responseData([
                CODE => SUCCESS,
                DATA => $data_result
            ]);
        }

        // phân trang
        $total_file = count($sorted);
        if($total_file % $limit == 0){
            $number_page = $total_file / $limit;
        }else{
            $number_page = intdiv($total_file, $limit) + 1;
        }

        if($page > $number_page) {
            return $this->responseData([
                CODE => SUCCESS,
                DATA => $data_result
            ]);
        }

        $data_result[FILES] = $files_result;
        $data_result[EXTEND]['is_root'] = $is_root;
        $data_result[EXTEND]['number_folder'] = count($folders);
        $data_result[EXTEND]['number_file'] = count($files);

        $data_result[PAGINATION] = [
            'page' => $page,
            'limit' => $limit,
            'pages' => $number_page,
            'total' => $total_file,
            'next' => $page == $number_page ? false : true,
            'prev' => $page == 1 ? false : true
        ];

        return $this->responseData([
            CODE => SUCCESS,
            DATA => $data_result
        ]);
    }

    public function getDataNavigation($path = [], $options = [])
    {
        if(empty($path) || strpos($path, $this->base_path) !== 0){
            return $this->responseData([MESSAGE => __d('filemanager', 'thu_muc_khong_ton_tai')]);
        }

        // kiểm tra đường dẫn 
        $dir = $this->_pathUrlToDir($path);
        if(empty(file_exists($dir))) {
            return $this->responseData([ MESSAGE => __d('filemanager', 'thu_muc_khong_ton_tai')]);
        }

        // lấy danh sách thư mục
        $folders = [];
        $all = scandir($dir);
        if(empty($all) || count($all) <= 2){
            return $this->responseData([
                CODE => SUCCESS,
                DATA => $folders
            ]);
        }

        // duyệt tất cả thư mục
        foreach($all as $k => $file){
            if ($file == '.' || $file == '..') continue;

            $full_path = $dir . $file;

            if(!is_dir($full_path)) continue;
            $folders[] = [
                FOLDER => $file,
                PATH => $this->_dirToPathUrl($full_path),
                'has_child_folder' => $this->_checkDirHasChildFolder($full_path)
            ];
        }

        // sắp xếp theo tên thư mục
        $folders_collection = new Collection($folders);
        $folders = $folders_collection->sortBy(FOLDER, SORT_ASC, SORT_STRING)->toArray();

        return $this->responseData([
            CODE => SUCCESS,
            DATA => $folders
        ]);
    }

    public function createFolder($name = null, $current_path = null)
    {
        if(empty($name)){
            return $this->responseData([MESSAGE => __d('filemanager', 'vui_long_nhap_ten_thu_muc')]);
        }

        if(empty($current_path) || strpos($current_path, $this->base_path) !== 0){
            return $this->responseData([MESSAGE => __d('filemanager', 'khong_lay_duoc_thong_tin_duong_dan')]);
        }

        // cắt tên còn 250 ký tự
        $name = substr($name, 0, 250);

        // format tên thư mục về unicode
        $name = Text::slug(strtolower($name), '_');
        if(empty($name)){
            return $this->responseData([MESSAGE => __d('filemanager', 'ten_thu_muc_khong_hop_le')]);
        }

        // kiểm tra đường dẫn thư mục
        $dir = $this->_pathUrlToDir($current_path);
        if(empty(file_exists($dir))) {
            return $this->responseData([ MESSAGE => __d('filemanager', 'thu_muc_khong_ton_tai')]);
        }

        // lấy tên duy nhất
        $name = $this->_getUniqueFolderName($name, $dir);
        if(file_exists($dir . DS . $name)) {
            return $this->responseData([ MESSAGE => __d('filemanager', 'thu_muc_da_duoc_tao')]);
        }

        // tạo thư mục ở folder media
        $create_media_folder = $this->_creatDir($dir . DS . $name);
        if(empty($create_media_folder)){
            return $this->responseData([ MESSAGE => __d('filemanager', 'tao_thu_muc_khong_thanh_cong')]);
        }

        // tạo thư mục thumb
        $dir_thumb = $this->_getDirThumb($dir);
        $create_thumb_folder = $this->_creatDir($dir_thumb . $name);
        if(empty($create_thumb_folder)){
            return $this->responseData([ MESSAGE => __d('filemanager', 'tao_thu_muc_thumb_khong_thanh_cong')]);
        }

        return $this->responseData([
            CODE => SUCCESS,
            MESSAGE => __d('filemanager', 'tao_thu_muc_thanh_cong'),
            DATA => [
                'name' => $name,
                'path' => $current_path . '/' . $name,
                'time' => time()
            ]
        ]);
    }

    public function rename($type = null, $name = null, $old_name = null, $path = null)
    {
        if(empty($type) || !in_array($type, [FILE, FOLDER])){
            return $this->responseData([MESSAGE => __d('filemanager', 'du_lieu_khong_hop_le')]);
        }

        if($type == FOLDER){
            $result = $this->_renameFolder($name, $old_name, $path);
        }else{
            $result = $this->_renameFile($name, $old_name, $path);
        }

        return $result;
    }

    private function _renameFolder($name = null, $old_name = null, $path = null)
    {
        if(empty($name)){
            return $this->responseData([MESSAGE => __d('filemanager', 'vui_long_nhap_ten_thu_muc')]);
        }

        if(empty($old_name) || empty($path) || strpos($path, $this->base_path) !== 0){
            return $this->responseData([MESSAGE => __d('filemanager', 'thu_muc_khong_ton_tai')]);
        }

        // kiểm tra đường dẫn 
        $dir = $this->_pathUrlToDir($path);
        if(empty(file_exists($dir)) || empty(file_exists($dir . $old_name))) {
            return $this->responseData([ MESSAGE => __d('filemanager', 'thu_muc_khong_ton_tai')]);
        }

        // format tên thư mục       
        $name = Text::slug(strtolower($name), '_');

        // lấy tên thư mục không trùng với các thư mục khác
        $name = $this->_getUniqueFolderName($name, $dir);
        if(empty($name)){
            return $this->responseData([MESSAGE => __d('filemanager', 'ten_thu_muc_khong_hop_le')]);
        }
        
        // đổi tên thư mục ở folder media
        $rename = @rename(
            $dir . $old_name, 
            $dir . $name
        );

        if(empty($rename)){
            return $this->responseData([MESSAGE => __d('filemanager', 'doi_ten_thu_muc_khong_thanh_cong')]);
        }

        // đổi tên thư mục ở folder thumbs
        $dir_thumb = $this->_getDirThumb($dir);
        $rename_thumb = @rename(
            $dir_thumb . $old_name, 
            $dir_thumb . $name
        );

        if(empty($rename_thumb)){
            return $this->responseData([MESSAGE => __d('filemanager', 'doi_ten_thu_muc_khong_thanh_cong')]);
        }

        return $this->responseData([
            CODE => SUCCESS,
            MESSAGE => __d('filemanager', 'doi_ten_thu_muc_thanh_cong'),
            DATA => [
                'name' => $name,
                'path' => $path . '/' . $name,
                'time' => time(),
                'extension' => FOLDER
            ]
        ]);
    }

    private function _renameFile($name = null, $old_name = null, $path = null)
    {
        if(empty($name)){
            return $this->responseData([MESSAGE => __d('filemanager', 'vui_long_nhap_ten_tep')]);
        }

        if(empty($old_name) || empty($path) || strpos($path, $this->base_path) !== 0){
            return $this->responseData([MESSAGE => __d('filemanager', 'tep_khong_ton_tai')]);
        }

        // kiểm tra đường dẫn path
        $dir = $this->_pathUrlToDir($path);
        if(empty(file_exists($dir)) || empty(file_exists($dir . $old_name))) {
            return $this->responseData([ MESSAGE => __d('filemanager', 'tep_khong_ton_tai')]);
        }

        // format tên tệp
        $file_info = pathinfo($name);
        $extension = !empty($file_info['extension']) ? strtolower($file_info['extension']) : null;
        $basename = !empty($file_info['filename']) ? $file_info['filename'] : null;
        if(empty($basename)){
            return $this->responseData([MESSAGE => __d('filemanager', 'ten_tep_khong_hop_le')]);
        }

        $basename = Text::slug(strtolower($basename), '_');

        // lấy tên tệp không trùng với tệp khác
        $name = $this->_getUniqueFileName($basename, $extension, $dir);
        if(empty($name)){
            return $this->responseData([MESSAGE => __d('filemanager', 'ten_tep_khong_hop_le')]);
        }

        // đổi tên file ở folder media
        $rename = @rename(
            $dir . $old_name, 
            $dir . $name
        );

        if(empty($rename)){
            return $this->responseData([MESSAGE => __d('filemanager', 'doi_ten_tep_khong_thanh_cong')]);
        }

        // đổi tên file ảnh ở folder thumbs
        $type = $this->_getTypeFileByExtention($extension);
        
        if($type == IMAGE && in_array($extension, ['png', 'jpeg', 'jpg'])){
            $dir_thumb = $this->_getDirThumb($dir);
            foreach(LIST_THUMBS_SIZE as $size){
                $old_file_info = pathinfo($old_name);
                $new_file_info = pathinfo($name);

                $old_basename = !empty($old_file_info['filename']) ? $old_file_info['filename'] : null;
                $new_basename = !empty($file_info['filename']) ? $file_info['filename'] : null;
                $full_path_old_file = $dir_thumb . $old_basename . '_thumb_' . $size . '.' . $extension;
                $full_path_new_file = $dir_thumb . $new_basename . '_thumb_' . $size . '.' . $extension;
                if(!file_exists($full_path_old_file)) continue;
                
                @rename(
                    $full_path_old_file, 
                    $full_path_new_file
                );
            }
        }

        return $this->responseData([
            CODE => SUCCESS,
            MESSAGE => __d('filemanager', 'doi_ten_thu_muc_thanh_cong'),
            DATA => [
                'name' => $name,
                'path' => $path . '/' . $name,
                'time' => time(),
                'extension' => $extension
            ]
        ]);
    }

    public function deleteFile($file_url = null)
    {
        if(empty($file_url) || strpos($file_url, $this->base_path) !== 0){
            return $this->responseData([MESSAGE => __d('filemanager', 'tep_khong_ton_tai')]);
        }

        // kiểm tra đường dẫn tệp
        $dir = $this->_pathUrlToDir($file_url);
        if(empty(file_exists($dir))) {
            return $this->responseData([ MESSAGE => __d('filemanager', 'tep_khong_ton_tai')]);
        }

        // xóa tệp hoặc folder
        if (is_dir($dir))
        {           
            $delete = $this->_deleteDir($dir);
            if(empty($delete)){
                return $this->responseData([MESSAGE => __d('filemanager', 'xoa_thu_muc_khong_thanh_cong')]);
            }

            // xoá folder thumbs
            $dir_thumb = $this->_getDirThumb($dir);
            $delete = $this->_deleteDir($dir_thumb);
        }
        else
        {
            $delete = @unlink($dir);
            if(empty($delete)){
                return $this->responseData([MESSAGE => __d('filemanager', 'xoa_tep_khong_thanh_cong')]);
            }

            // xoá ảnh thumbs
            $path_info = pathinfo($dir);

            $dir_file = !empty($path_info['dirname']) ? $path_info['dirname'] : null;
            $basename = !empty($path_info['filename']) ? $path_info['filename'] : null;
            $extension = !empty($path_info['extension']) ? $path_info['extension'] : null;

            if(in_array($extension, ['png', 'jpg', 'jpeg'])){
                $dir_thumb = $this->_getDirThumb($dir_file);

                foreach(LIST_THUMBS_SIZE as $size){
                    $thumb_file = $dir_thumb . $basename . '_thumb_' . $size . '.' . $extension;

                    if(!file_exists($thumb_file)) continue;
                    @unlink($thumb_file);
                }                
            }
        }

        return $this->responseData([
            CODE => SUCCESS,
            MESSAGE => __d('filemanager', 'xoa_tep_thanh_cong'),
            DATA => [
                'path' => $file_url,
                'time' => time(),
            ]
        ]);
    }

    public function _deleteDir($dir = null)
    {
        if(empty($dir)) return false;

        $dir = rtrim($dir, DS) . DS;
        if(empty(file_exists($dir))) return false;

        $files = scandir($dir);

        // nếu không có file hoặc tệp bên trong thì thực hiện xóa folder
        if(empty($files) || count($files) <= 2) {
            return @rmdir($dir);
        }

        // xóa file và tệp bên trong thư mục trước
        foreach($files as $k => $file){
            // bỏ qua 2 value '.' và '..' ở đầu ds
            if($file == '.' || $file == '..') continue;

            $current_file = $dir . $file;
            
            if (is_dir($current_file))
            {
                $delete = $this->_deleteDir($current_file);
                if(!$delete) return false;
            }
            else
            {
                $delete = @unlink($current_file);
                if(!$delete) return false;   
            }
        }

        return @rmdir($dir);
    }

    public function pasteFileAndDir($file_url = null, $path = null, $cut = false)
    {
        if(empty($file_url) || strpos($file_url, $this->base_path) !== 0){
            return $this->responseData([MESSAGE => __d('filemanager', 'duong_dan_khong_hop_le')]);
        }

        if(empty($path) || strpos($path, $this->base_path) !== 0){
            return $this->responseData([MESSAGE => __d('filemanager', 'duong_dan_khong_hop_le')]);
        }

        // kiểm tra đường dẫn path
        $dir = $this->_pathUrlToDir($path);
        
        if(!file_exists($dir)) {
            return $this->responseData([ MESSAGE => __d('filemanager', 'duong_dan_khong_hop_le')]);
        }

        // kiểm tra đường dẫn file
        $dir_file = $this->_pathUrlToDir($file_url);
        if(!file_exists($dir_file)) {
            return $this->responseData([ MESSAGE => __d('filemanager', 'dan_tep_khong_thanh_cong')]);
        }

        if (is_dir($dir_file))
        {
            $paste_result = $this->_pasteDir($dir_file, $dir, $cut);
        }
        else
        {
            $paste_result = $this->_pasteFile($dir_file, $dir, $cut);
        }


        if(!empty($paste_result[CODE]) && $paste_result[CODE] == SUCCESS){
            $dir_file_pasted = !empty($paste_result[DATA]['file_pasted']) ? $paste_result[DATA]['file_pasted'] : null;
            $file_info = $this->_getInfoFile($dir_file_pasted);

            return $this->responseData([
                CODE => SUCCESS,
                MESSAGE => __d('filemanager', 'dan_tep_thanh_cong'),
                DATA => [
                    'file_info' => $file_info
                ]
            ]);
        }else{
            return $paste_result;
        }
    }

    public function _pasteDir($from_dir = null, $to_dir = null, $cut = false)
    {
        if(empty($from_dir) || empty(file_exists($from_dir)) || !is_dir($from_dir)){
            return $this->responseData([MESSAGE => __d('filemanager', 'duong_dan_khong_hop_le')]);
        }

        if(empty($to_dir) || empty(file_exists($to_dir)) || !is_dir($to_dir)){
            return $this->responseData([MESSAGE => __d('filemanager', 'duong_dan_khong_hop_le')]);
        }

        // ngắt copy dir từ thư mục cha vào thư mục con nhằm tránh việc nhân bản vô hạn
        if(strpos($to_dir, $from_dir) === 0) {
            return $this->responseData([MESSAGE => __d('filemanager', 'khong_the_dan_thu_muc_nguon_vao_thu_muc_con')]);
        }

        $from_dir = rtrim($from_dir, DS) . DS;
        $to_dir = rtrim($to_dir, DS) . DS;
        $thumb_from_dir = $this->_getDirThumb($from_dir);
        $thumb_to_dir = $this->_getDirThumb($to_dir);

        $path_info = pathinfo($from_dir);
        
        $dir = !empty($path_info['dirname']) ? $path_info['dirname'] : null;
        $name = !empty($path_info['basename']) ? $path_info['basename'] : null;

        $name = $this->_getUniqueFolderName($name, $to_dir);
        if(empty($name)) {
            return $this->responseData([MESSAGE => __d('filemanager', 'ten_thu_muc_khong_hop_le')]);
        }
        
        $new_dir = $to_dir . $name . DS;
        $new_thumb_dir = $thumb_to_dir . $name . DS;

        // ----------------- xử lý paste khi cut folder
        if($cut){
            $copy = @rename($from_dir, $new_dir);
            if(empty($copy)) if(empty($name)) {
                return $this->responseData([MESSAGE => __d('filemanager', 'dan_thu_muc_khong_thanh_cong')]);
            }

            $copy_thumb = @rename($thumb_from_dir, $thumb_to_dir);

            // return kết quả luôn khi cut
            return $this->responseData([
                CODE => SUCCESS,
                MESSAGE => __d('filemanager', 'dan_thu_muc_thanh_cong'),
                DATA => [
                    'file_pasted' => $new_dir
                ]
            ]);
        }

        // --------------------------- xử lý paste khi copy folder
        $create_folder = $this->_creatDir($new_dir);
        if(empty($create_folder)) {
            return $this->responseData([MESSAGE => __d('filemanager', 'dan_thu_muc_khong_thanh_cong')]);
        }
        

        // tạo folder thumbs
        $create_thumb_folder = $this->_creatDir($new_thumb_dir);        
        if(empty($create_thumb_folder)) {
            return $this->responseData([MESSAGE => __d('filemanager', 'dan_thu_muc_khong_thanh_cong')]);
        }
        

        // nếu có file hoặc tệp bên trong thì thực hiện copy qua
        $files = scandir($from_dir);
        if(empty($files) || count($files) <= 2) {
            return $this->responseData([
                CODE => SUCCESS,
                MESSAGE => __d('filemanager', 'dan_thu_muc_thanh_cong'),
                DATA => [
                    'file_pasted' => $new_dir
                ]
            ]);
        }

        foreach($files as $k => $file){
            // bỏ qua 2 value '.' và '..' ở đầu ds
            if($file == '.' || $file == '..') continue;

            $child_file = $from_dir . $file;            

            if (is_dir($child_file))
            {
                // dán folder child
                $paste_result = $this->_pasteDir($child_file, $new_dir, $cut);

                if(empty($paste_result[CODE]) || $paste_result[CODE] != SUCCESS) continue;
            }
            else
            {
                // dán file vào folder mới
                $paste_result = $this->_pasteFile($child_file, $new_dir, $cut);

                if(empty($paste_result[CODE]) || $paste_result[CODE] != SUCCESS) continue;
            }
        }

        // cut folder
        if(!empty($cut)){
            $this->_deleteDir($from_dir);
            $this->_deleteDir($thumb_from_dir);
        }

        return $this->responseData([
            CODE => SUCCESS,
            MESSAGE => __d('filemanager', 'dan_thu_muc_thanh_cong'),
            DATA => [
                'file_pasted' => $new_dir
            ]
        ]);
    }

    public function _pasteFile($from_dir_file = null, $to_dir = null, $cut = false)
    {
        if(empty($from_dir_file) || empty(file_exists($from_dir_file)) || is_dir($from_dir_file)) {
            return $this->responseData([MESSAGE => __d('filemanager', 'duong_dan_khong_hop_le')]);
        }

        if(empty($to_dir) || empty(file_exists($to_dir)) || !is_dir($to_dir)) {
            return $this->responseData([MESSAGE => __d('filemanager', 'duong_dan_khong_hop_le')]);
        }

        $to_dir = rtrim($to_dir, DS) . DS;

        $file_info = pathinfo($from_dir_file);
        
        $dir = !empty($file_info['dirname']) ? $file_info['dirname'] : null;
        $basename = !empty($file_info['filename']) ? $file_info['filename'] : null;
        $extension = !empty($file_info['extension']) ? strtolower($file_info['extension']) : null;

        if(empty($dir) || empty($basename) || empty($extension)) {
            return $this->responseData([MESSAGE => __d('filemanager', 'duong_dan_khong_hop_le')]);
        }

        $dir = rtrim($dir, DS) . DS;

        $name = $this->_getUniqueFileName($basename, $extension, $to_dir);
        if(empty($name)) {
            return $this->responseData([MESSAGE => __d('filemanager', 'ten_tep_khong_hop_le')]);
        }

        $to_dir_file = $to_dir . $name;

        if($cut){
            $copy = @rename($from_dir_file, $to_dir_file);
        }else{
            $copy = @copy($from_dir_file, $to_dir_file);
        }

        if(empty($copy)) {
            return $this->responseData([MESSAGE => __d('filemanager', 'dan_tep_khong_thanh_cong')]);
        }

        $type = $this->_getTypeFileByExtention($extension);

        // dán thumb image
        if($type == IMAGE){
            $new_basename = pathinfo($to_dir_file, PATHINFO_FILENAME);
            
            $dir_thumb = $this->_getDirThumb($dir);
            $to_dir_thumb = $this->_getDirThumb($to_dir);
         
            foreach(LIST_THUMBS_SIZE as $size){
                $thumb_file = $dir_thumb . $basename . '_thumb_' . $size . '.' . $extension;
                $to_thumb_file = $to_dir_thumb . $new_basename . '_thumb_' . $size . '.' . $extension;

                if(!file_exists($thumb_file)) continue;

                if($cut){
                    @rename($thumb_file, $to_thumb_file);                    
                }else{
                   @copy($thumb_file, $to_thumb_file);
                }                
            }
        }

        return $this->responseData([
            CODE => SUCCESS,
            MESSAGE => __d('filemanager', 'dan_tep_thanh_cong'),
            DATA => [
                'file_pasted' => $to_dir_file
            ]
        ]);
    }

    public function _countDirSize($dir = null)
    {
        if(empty($dir)) return [];

        $total_size = $files_count = $folders_count = 0;
        $dir = rtrim($dir, DS) . DS;

        $files = scandir($dir);
        if(empty($files) || count($files) <= 2) return [];

        $has_child_folder = false;
        foreach($files as $k => $file){
            // bỏ qua 2 value '.' và '..' ở đầu ds
            if($file == '.' || $file == '..') continue;

            $current_file = $dir . $file;
            if (is_dir($current_file))
            {
                $folder_info = $this->_countDirSize($current_file);
                $total_size += !empty($folder_info['size']) ? intval($folder_info['size']) : 0;
                $folders_count ++;
                $has_child_folder = true;
            }
            else
            {
                $size = filesize($current_file);
                $total_size += $size;
                $files_count++;
            }
        }

        return [
            'size' => $total_size,
            'files_count' => $files_count,
            'folders_count' => $folders_count,
            'has_child_folder' => $has_child_folder
        ];
    }

    public function _creatDir($dir = null)
    {
        // chỉ tạo thư mục trong media hoặc thumb
        if(empty($dir) || (strpos($dir, $this->default_dir . DS . MEDIA) !== 0 && strpos($dir, $this->default_dir . DS . MEDIA_THUMBS) !== 0)) return false;

        $result = @mkdir($dir, 0755, true);
        return !empty($result) ? true : false;
    }

    public function _checkDirHasChildFolder($dir = null)
    {
        if(empty($dir) || (strpos($dir, $this->default_dir . DS . MEDIA) !== 0 && strpos($dir, $this->default_dir . DS . MEDIA_THUMBS) !== 0)) return false;
        if(empty(file_exists($dir))) return false;

        $files = scandir($dir);
        if(empty($files) || count($files) <= 2) return false;

        $result = false;
        foreach($files as $k => $file){
            // bỏ qua 2 value '.' và '..' ở đầu ds
            if($file == '.' || $file == '..') continue;

            $current_file = $dir . DS . $file;
            if (is_dir($current_file))
            {
               $result = true;
               break;
            }            
        }
        return $result;
    }
}