<?php
use Cake\Core\Configure;


// token
define('CDN_KEY', '3j2onldksd99903jw31322320ulevansi901dsodj888u02j32o3d');

// image thumbs size
define('LIST_THUMBS_SIZE', [50, 150, 250, 350, 500, 720]);

// max file size upload
if (!defined('MAX_FILE_SIZE')) define('MAX_FILE_SIZE', 209715200); // 200MB

// max image size upload
if (!defined('MAX_IMAGE_SIZE')) define('MAX_IMAGE_SIZE', 10485760); // 10MB

// extentions
define('EXTENSIONS_ALLOW', [
	'png' => ['image/png'],
	'jpg' => ['image/jpeg'],
	'jpeg' => ['image/jpeg'],
	'ico' => [
		'image/x-icon',
		'image/vnd.microsoft.icon'
	],
	'svg' => ['image/svg+xml'],
	'webp' => ['image/webp'],
	'gif' => ['image/gif'],

	'doc' => ['application/msword'],
	'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
	'xls' => ['application/vnd.ms-excel'],
	'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
	'csv' => ['text/csv'],
	'ppt' => ['application/vnd.ms-powerpoint'],
	'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
	'pdf' => ['application/pdf'],
	'txt' => ['text/plain'],

	'mp4' => ['video/mp4'],
	'avi' => ['video/x-msvideo'],
	'mpeg' => ['video/mpeg'],

	'mp3' => ['audio/mpeg'],
	'wav' => ['audio/wav'],
	'aac' => ['audio/aac'],

	'zip' => ['application/zip'],
	'rar' => [
		'application/vnd.rar',
		'application/octet-stream'
	],
	'7z' => ['application/x-7z-compressed']
]);

// list mime type
if (!defined('LIST_EXTENSIONS')) define('LIST_EXTENSIONS', array_keys(EXTENSIONS_ALLOW));

// list extentions
if (!defined('LIST_MIME_TYPES')) {
	$list_mine_type = [];
	foreach(EXTENSIONS_ALLOW as $mine_type){
		foreach($mine_type as $item){
			if(!in_array($item, $list_mine_type)) $list_mine_type[] = $item;
		}
	}
	define('LIST_MIME_TYPES', $list_mine_type);
}
