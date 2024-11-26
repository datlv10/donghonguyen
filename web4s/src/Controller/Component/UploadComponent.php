<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Exception\Exception;
use Cake\Utility\Security;
use Cake\Http\Client;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;

class UploadComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities', 'Auth'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    // function này chỉ dùng với khách hàng đăng tải
    public function uploadToCdn($file = [], $path_folder = null, $options = [])
    {
        if(empty($file) || !is_array($file)){
            return $this->System->getResponse([MESSAGE => __d('template', 'tep_dang_tai_khong_hop_le')]);
        }
  
        $size = !empty($file['size']) ? intval($file['size']) : 0;
        $error = !empty($file['error']) ? intval($file['error']) : 0;
        $name = !empty($file['name']) ? $file['name'] : null;
        $tmp_file = !empty($file['tmp_name']) ? $file['tmp_name'] : null;

        if(!empty($error) || empty($size) || empty($tmp_file)){
            return $this->System->getResponse([MESSAGE => __d('template', 'tep_dang_tai_khong_hop_le')]);
        }

        if($size > MAX_SIZE_FILE_UPLOAD){
            return $this->System->getResponse([MESSAGE => __d('template', 'tep_dang_tai_co_dung_luong_qua_lon')]);
        }
    
        // đổi tên ảnh trước khi đẩy lên CDN
        $tmp_name = pathinfo($tmp_file, PATHINFO_BASENAME);
        if(empty($options['origin_name'])){
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $name = strtolower($this->Utilities->generateRandomString(20)) . '.' . $extension;
        }        

        $file_upload = str_replace($tmp_name, $name, $tmp_file);
        rename($tmp_file, $file_upload);

        // lấy thông tin tài khoản khách hàng đang đăng nhập 
        $session = $this->controller->getRequest()->getSession();
        $member_info = $session->read(MEMBER);

        $auth_user = [
            'id' => !empty($member_info['id']) ? intval($member_info['id']) : rand(10000, 9999999999),
            'full_name' => !empty($member_info['full_name']) ? $member_info['full_name'] : __d('template', 'khach_vang_lai')
        ];

        $domain = $this->controller->getRequest()->host();
        $domain_cdn = parse_url(CDN_URL, PHP_URL_HOST);

        $token = TableRegistry::get('Utilities')->getSecureKeyCdn($domain, $domain_cdn, $auth_user);
        if(empty($token)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_ma_dang_tai_cdn')]);
        }

        return $this->_upToCdn($file_upload, $path_folder, $token, true);
    }

    public function uploadToCdnByUrl($url = null, $path_folder = null, $options = [])
    {
        if(empty($url)){
            return $this->System->getResponse([MESSAGE => __d('template', 'tep_dang_tai_khong_hop_le')]);
        }
        
        $headers = @get_headers($url);
        if(empty($headers) || !is_array($headers)){
            return $this->System->getResponse([MESSAGE => __d('template', 'tep_dang_tai_khong_hop_le')]);
        }

        foreach ($headers as $header) {
            if(strpos($header, 'Content-Type:') > -1){
                $list = explode('Content-Type:', $header);

                $type = !empty($list[1]) ? trim($list[1]) : null;
                if(strpos($type, 'text/html') > -1){
                    return $this->System->getResponse([MESSAGE => __d('template', 'tep_dang_tai_khong_hop_le')]);
                }
            }
        }

        $content = file_get_contents($url);
        $size = strlen($content);

        if($size > MAX_SIZE_FILE_UPLOAD){
            return $this->System->getResponse([MESSAGE => __d('template', 'tep_dang_tai_co_dung_luong_qua_lon')]);
        }

        $tmp_file = @tempnam(sys_get_temp_dir(), 'tmp_file');
        $handle = @fopen($tmp_file, 'w');
        @fwrite($handle, $content);
        @fclose($handle);

        if(empty($tmp_file)){
            return $this->System->getResponse([MESSAGE => __d('template', 'tep_dang_tai_khong_hop_le')]);
        }
        
        $extension = pathinfo($url, PATHINFO_EXTENSION);

        // rename file tmp before upload cdn
        $name = strtolower($this->Utilities->generateRandomString(20)) . '.' . $extension;
        $file_upload = str_replace(basename($tmp_file), $name, $tmp_file);
        @rename($tmp_file, $file_upload);

        $domain = $this->controller->getRequest()->host();
        $domain_cdn = parse_url(CDN_URL, PHP_URL_HOST);
        $auth_user = $this->Auth->user();

        $token = TableRegistry::get('Utilities')->getSecureKeyCdn($domain, $domain_cdn, $auth_user);
        if(empty($token)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_ma_dang_tai_cdn')]);
        }

        return $this->_upToCdn($file_upload, $path_folder, $token);
    }

    private function _upToCdn($file_upload = null, $path = null, $token = null, $ignore_log = false)
    {
        if(empty($file_upload) || empty($path) || empty($token)){
            return $this->System->getResponse([MESSAGE => __d('template', 'tep_dang_tai_khong_hop_le')]);
        }
      
        $request = $this->controller->getRequest();
        $http = new Client();

        $create_folder = true; // trước khi upload trên CDN sẽ kiểm tra tồn tại thư mục chưa, nếu chưa có thì sẽ tạo thư mục trước
        $response = $http->post(
            CDN_URL . '/myfilemanager/upload',
            [
                'path' => '/media/' . $path,
                'file' => fopen($file_upload, 'r'),
                'ignore_log' => $ignore_log,
                'create_folder' => $create_folder 
            ],
            [
                'headers' => [
                    'Referer' => $request->scheme() . '://' . $request->host(),
                    'cdn-token' => $token,
                    'cdn-language' => LANGUAGE,
                    'cdn-referer' => $request->host()
                ],
                'ssl_verify_peer' => FALSE,
                'ssl_verify_host ' => FALSE
            ]
        );

        if($response->getStatusCode() != 200 || empty($response->getStringBody())){
           return $this->System->getResponse([MESSAGE => __d('template', 'dang_tai_tep_khong_thanh_cong')]);
        }

        $result = json_decode($response->getStringBody(), true);
        if(empty($result[DATA]['file_info'])){
            return $this->System->getResponse([MESSAGE => __d('template', 'dang_tai_tep_khong_thanh_cong')]);
        }
        
        $file = $result[DATA]['file_info'];       
        $file['url'] = !empty($file['path']) ? str_replace(CDN_URL, '', $file['path']) : null;

        // delete file tmp
        unlink($file_upload);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'dang_tai_tep_thanh_cong'),
            DATA => $file
        ]);
    }

    private function _generateAccessKeyUpload($file_info = [])
    {
        $domain = $this->controller->getRequest()->host();

        $path_key = [ACCESS_KEY_UPLOAD];
        if(!empty($file_info['name'])){
            $path_key[] = $file_info['name'];
        }

        if(!empty($file_info['size'])){
            $path_key[] = $file_info['size'];
        }

        $access_key_upload = Security::hash(implode(SEPARATOR_KEY_UPLOAD, $path_key), 'md5', false);
        return base64_encode($domain . '|' . $access_key_upload);
    }

    public function _createTmpDataJson($folder_name = 'example', $data = null, $limit = 10)
    {
        if(empty($data)) return null;

        $dir_folder = TMP . $folder_name . DS;
        $create_dir_folder = new Folder($dir_folder, true, 0755);

        $folder_child_name = Text::uuid();
        $dir_folder_child = $dir_folder . $folder_child_name . DS;


        $create_folder_child = new Folder($dir_folder_child, true, 0755);
        $files = $create_folder_child->find('.*\.json', true);

        $data = array_chunk($data, $limit);
        foreach ($data as $k_data => $v_data) {
            $file = new File($dir_folder_child . str_pad($k_data, 5, '0', STR_PAD_LEFT). '.json', true, 0755);
            $file->write(json_encode($v_data), 'w');
            $file->close();
        }

        return $folder_child_name;
    }
  
}
