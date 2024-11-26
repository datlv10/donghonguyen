<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration35 extends AbstractMigration
{

    /**
     * 
     * - Lấy dữ liệu `brands` cũ 
     * - Xóa bảng `brands` cũ
     * - Khởi tạo bảng `bands` và `brands_content` mới
     * - Thêm dữ liệu vào bảng mới
     */

    public function up()
    {
        // lấy danh sách thương hiệu
        $rows = $this->fetchAll('SELECT * FROM `brands`');

        $brands = $content = $links = [];
        if(!empty($rows)){
            // lấy ngôn ngữ mặc định
            $language = $this->fetchRow('SELECT `code` FROM `languages` WHERE `is_default` = 1 ');
            $lang = !empty($language['code']) ? $language['code'] : null;

            foreach($rows as $row){
                $brand_id = !empty($row['id']) ? intval($row['id']) : null;
                $name = !empty($row['name']) ? $row['name'] : null;
                $created = !empty($row['created']) ? intval($row['created']) : null;
                $updated = !empty($row['updated']) ? intval($row['updated']) : null;
                if(empty($brand_id) || empty($name)) continue;

                $brands[] = [
                    'id' => $brand_id,
                    'image_avatar' => !empty($row['image_avatar']) ? $row['image_avatar'] : null,
                    'created_by' => !empty($row['created_by']) ? intval($row['created_by']) : null,
                    'created' => $created,
                    'updated' => $updated,
                    'status' => !empty($row['status']) ? intval($row['status']) : 1,
                    'deleted' => !empty($row['deleted']) ? 1 : 0
                ];

                $content[] = [
                    'brand_id' => $brand_id,
                    'name' => $name,
                    'seo_title' => $name,
                    'content' => !empty($row['content']) ? $row['content'] : null,
                    'search_unicode' => !empty($row['search_unicode']) ? $row['search_unicode'] : null,
                    'lang' => $lang
                ];

                $links[] = [
                    'foreign_id' => $brand_id,
                    'type' => BRAND_DETAIL,
                    'url' => $this->formatToUrl($name),
                    'lang' => $lang,
                    'created' => $created,
                    'updated' => $updated
                ];
            }
        }

        // xoá bảng `brands` cũ
        if ($this->hasTable('brands')) {
            $this->table('brands')->drop()->save();
        }

        if ($this->hasTable('brands_content')) {
            $this->table('brands_content')->drop()->save();
        }
        
        // khởi tạo bảng `brands` mới
        $query = "
            CREATE TABLE `brands` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `image_avatar` varchar(500) DEFAULT NULL,
                `images` text COMMENT 'Tên thương hiệu',
                `url_video` varchar(500) DEFAULT NULL,
                `type_video` varchar(20) DEFAULT '' COMMENT 'Nội dung',
                `files` text,
                `created_by` int(11) DEFAULT NULL,
                `created` int(11) DEFAULT NULL,
                `updated` int(11) DEFAULT NULL,
                `position` int(11) DEFAULT NULL,
                `status` int(1) DEFAULT '1',
                `deleted` int(1) DEFAULT '0',
                PRIMARY KEY (`id`) USING BTREE,
                KEY `deleted` (`deleted`) USING BTREE,
                KEY `status` (`status`) USING BTREE,
                KEY `status_2` (`status`,`deleted`) USING BTREE
            ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
        $this->execute($query);


        // khởi tạo bảng `brands_content` mới
        $query = "
            CREATE TABLE `brands_content` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `brand_id` int(11) DEFAULT NULL,
                `name` varchar(255) DEFAULT NULL,
                `content` text,
                `seo_title` varchar(255) DEFAULT NULL,
                `seo_description` varchar(255) DEFAULT NULL,
                `seo_keyword` varchar(500) DEFAULT NULL,
                `search_unicode` varchar(255) DEFAULT NULL,
                `lang` varchar(20) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `brand_id` (`brand_id`),
                KEY `lang` (`lang`),
                KEY `brand_id_2` (`brand_id`,`lang`)
            ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
        $this->execute($query);


        // thêm dữ liệu vào bảng mới
        if(!empty($brands)){
            $table = $this->table('brands');
            $table->insert($brands)->saveData();
        }

        if(!empty($content)){
            $table = $this->table('brands_content');
            $table->insert($content)->saveData();
        }

        if(!empty($links)){
            $table = $this->table('links');
            $table->insert($links)->saveData();
        }
    }

    public function down()
    {
    
    }

    public function formatToUrl($str = null)
    {
        if(empty($str)) return '';
        
        $str = $this->formatUnicode($str);        
        $str = preg_replace('`\[.*\]`U', '', $str);
        $str = preg_replace('`&(amp;)?#?[a-z0-9]+;`i', '-', $str);        
        $str = preg_replace('`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i', '\\1', $str);
        $options = [
            'delimiter' => '-',
            'limit' => 255,
            'lowercase' => true,
            'replacements' => [],
            'transliterate' => false,
        ];
        
        $char_map = [
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O', 
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 
            'ß' => 'ss', 
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 
            'ÿ' => 'y',
            // Latin symbols
            '©' => '(c)',
            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g', 
            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',
            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
            // Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U', 
            'Ž' => 'Z', 
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z', 
            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z', 
            'Ż' => 'Z', 
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',
            // Latvian
            'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 
            'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z'
        ];
        
        // make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

        // transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }
        
        // replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
        
        // remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
        
        // truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
        
        // remove delimiter from ends
        $str = trim($str, $options['delimiter']);
        
        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }


    private function formatUnicode($str = null)
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









}
