<?php

// config of system
define('CRM_DOMAIN', 'crm.web4s.com.vn');
// define('CRM_DOMAIN', 'crm-web4s');
define('CRM_URL', 'http://' . CRM_DOMAIN);

define('ACCESS_KEY_UPLOAD', '3j2onldksd99903jw31322320ulevansi901dsodj888u02j32o3d');
define('SEPARATOR_KEY_UPLOAD', '___web4sCDN___');
define('MAX_SIZE_FILE_UPLOAD', '5000000');

define('LANGUAGE_DEFAULT', 'vi');
define('LANGUAGE_DEFAULT_ADMIN', 'vi');
define('PAGINATION_LIMIT_ADMIN', 20);

define('WEIGHT_PRODUCT_DEFAULT', 500);
define('LENGTH_PRODUCT_DEFAULT', 20);
define('WIDTH_PRODUCT_DEFAULT', 20);
define('HEIGHT_PRODUCT_DEFAULT', 20);



// khi có thay đổi gì về js trong admin thì thay đổi giá trị này
define('ADMIN_VERSION_UPDATE', '5.3.0');

// define variable of system 
define('LANG', 'lang');
define('LANG_FRONTEND', 'lang_frontend');
define('KEY', 'key');
define('JSON', 'json');
define('CODE', 'code');
define('DATA', 'data');
define('DATA_EXTEND', 'data_extend');
define('EXTEND', 'extend');
define('META', 'meta');
define('SORT', 'sort');
define('PAGE', 'page');
define('FIELD', 'field');
define('STATUS', 'status');
define('STATUS_ITEM', 'status_item');
define('MESSAGE', 'message');
define('SUCCESS', 'success');
define('ERROR', 'error');
define('SESSION_END', 'session_end');
define('TYPE', 'type');
define('TYPE_COMMENT', 'type_comment');
define('PAGINATION', 'pagination');
define('DESC', 'desc');
define('ASC', 'asc');
define('PERPAGE', 'perpage');
define('QUERY', 'query');
define('DATA_FORM', 'data_form');
define('DATA_FILTER', 'data_filter');
define('FILTER', 'filter');
define('NOT_ID', 'not_id');
define('LAYOUT', 'layout');
define('BLOCK', 'block');
define('DATA_BLOCK', 'data_block');
define('DATA_BLOCK_DETAIL', 'data_block_detail');
define('KEYWORD', 'keyword');
define('FOLDER', 'folder');
define('FILE', 'file');
define('TEMPLATE', 'template');
define('TEMPLATE_PAGE', 'template_page');
define('SETTING', 'setting');
define('LIVE', 'live');
define('DEVELOP', 'develop');
define('CART', 'cart');
define('NUMBER_EMPTY', '0.00');
define('MEMBER', 'member');
define('COUPON', 'coupon');
define('SHIPPING', 'shipping');
define('WEBSITE', 'website');
define('DRAFT', 'draft');
define('SITEMAP', 'sitemap');
define('PAYMENT_GATEWAY', 'payment_gateway');
define('SHIPPING_METHOD', 'shipping_method');
define('PLUGIN', 'plugin');
define('TOKEN', 'token');

define('ALL', 'all');
define('LIST_TYPE', 'list');

define('TOTAL_RECORD', 'total_record');

define('AWAITING_APPROVAL', -1);
define('ENABLE', 1);
define('DISABLE', 0);
define('STOP_BUSSINEUS', 2);
define('USED', 2);
define('AFFILIATE_APPROVAL', 2);
define('PENDING', 2);


define('FULL_INFO', 'full');
define('SIMPLE_INFO', 'simple');
define('LIST_INFO', 'list');

define('PERCENT', 'percent');
define('MONEY', 'money');

define('DISCOUNT_ORDER', 'discount_order');
define('DISCOUNT_PRODUCT', 'discount_product');
define('FREE_SHIP', 'free_ship');
define('GIVE_PRODUCT', 'give_product');
define('GIVE_POINT_PROMOTION', 'give_point_promotion');

define('POINT_BY_PERCENT_ORDER', 'point_by_percent_order');
define('POINT_BY_VALUE', 'point_by_value');

define('PRODUCT', 'product');
define('AFFILIATE', 'affiliate');
define('GENEALOGY', 'genealogy');
define('ARTICLE', 'article');
define('MEDIA', 'media');
define('PRODUCT_ITEM', 'product_item');
define('PRODUCT_DETAIL', 'product_detail');
define('ARTICLE_DETAIL', 'article_detail');
define('BRAND_DETAIL', 'brand_detail');
define('CATEGORY', 'category');
define('CATEGORY_PRODUCT', 'category_product');
define('BRAND_PRODUCT', 'brand_product');
define('CATEGORY_ARTICLE', 'category_article');
define('ATTRIBUTE', 'attribute');
define('ATTRIBUTE_OPTION', 'attribute_option');
define('PROMOTION_DETAIL', 'promotion_detail');
define('PROMOTION', 'promotion');
define('WALLET', 'wallet');
define('TAB_PRODUCT', 'tab_product');
define('TAB_ARTICLE', 'tab_article');
define('ATTENDANCE', 'attendance');
define('GIVE_POINT', 'give_point');
define('BUY_POINT', 'buy_point');
define('WITHDRAW', 'withdraw');

define('CURRENCY_PARAM', 'currency');
define('BRAND', 'brand');
define('ROLE', 'role');
define('TAG', 'tag');
define('NOTIFICATION', 'notification');
define('NH_NOTIFICATION', 'nh_notification');

define('MOBILE_APP', 'mobile_app');
define('MOBILE_PAGE', 'mobile_page');
define('MOBILE_TEMPLATE', 'mobile_template');
define('MOBILE_BLOCK', 'mobile_block');

define('HOME', 'home');
define('NORMAL', 'normal');
define('SEARCH', 'search');

define('ADDRESS', 'address');
define('CONTACT', 'contact');


define('VIDEO_YOUTUBE', 'video_youtube');
define('VIDEO_SYSTEM', 'video_system');

define('NO_IMAGE_URL', '/assets/media/error/no-image.jpg');
define('FLAGS_URL', '/assets/media/flags/');


define('TEXT', 'text');
define('RICH_TEXT', 'rich_text');
define('NUMERIC', 'numeric');
define('SINGLE_SELECT', 'single_select');
define('MULTIPLE_SELECT', 'multiple_select');
define('DATE', 'date');
define('DATE_TIME', 'date_time');
define('SWITCH_INPUT', 'switch_input');
define('SPECICAL_SELECT_ITEM', 'special_select_item');

define('IMAGE', 'image');
define('IMAGES', 'images');
define('FILES', 'files');
define('VIDEO', 'video');

define('ALBUM_IMAGE', 'album_image');
define('ALBUM_VIDEO', 'album_video');
define('PRODUCT_SELECT', 'product_select');
define('ARTICLE_SELECT', 'article_select');
define('CITY', 'city');
define('CITY_DISTRICT', 'city_district');
define('CITY_DISTRICT_WARD', 'city_district_ward');

define('MULTIPLE_SELECT_ITEM', 'multiple_select_item');
define('SINGLE_SELECT_ITEM', 'single_select_item');

// payment method
define('CASH', 'cash');
define('BANK', 'bank');
define('CREDIT', 'credit');
define('GATEWAY', 'gateway');
define('VOUCHER', 'voucher');
define('COD', 'cod');

// shipping method
define('RECEIVED_AT_STORE', 'received_at_store');
define('NORMAL_SHIPPING', 'normal_shipping');
define('SHIPPING_CARRIER', 'carrier_shipping');

// type object payment
define('CUSTOMER', 'customer');
define('SUPPLIER', 'supplier');
define('EMPLOYEE', 'employee');
define('SHIPPER', 'shipper');
define('OTHER', 'other');

// type order
define('ORDER', 'order');
define('ORDER_RETURN', 'return');
define('IMPORT', 'import');
define('TRANSFER', 'transfer');
define('RETAIL', 'retail');
define('OTHER_BILL', 'other');

//status order
define('NEW_ORDER', 'new');
define('CONFIRM', 'confirm');
define('PACKAGE', 'package');
define('EXPORT', 'export');
define('DONE', 'done');
define('CANCEL', 'cancel');

//status group order
define('WAIT_PAYMENT', 'wait_payment');
define('PROCESSING', 'processing');
define('TRANSPORT', 'transport');

// status order return
define('WAITING_RECEIVING', 'waiting_receiving');
define('RECEIVED', 'received');

// shipping status
define('WAIT_DELIVER', 'wait_deliver'); //cho lay hang
define('DELIVERY', 'delivery'); //dang giao hang
define('DELIVERED', 'delivered'); //da giao hang
define('CANCEL_PACKAGE', 'cancel_package'); //huy dong goi
define('CANCEL_WAIT_DELIVER', 'cancel_wait_deliver'); //huy giao va cho nhan
define('CANCEL_DELIVERED', 'cancel_delivered'); //huy giao va da nhan

//type of objects table
define('ORDER_SOURCE', 'order_source');

//type of block
define('SYSTEM', 'system');
define('CUSTOM', 'custom');
define('FORM', 'FORM');
define('MENU', 'menu');
define('HTML', 'html');
define('SLIDER', 'slider');
define('COMMENT', 'comment');
define('RATING', 'rating');
define('ICON', 'icon');
define('API_RATING', 'api_rating');
define('API_COMMENT', 'api_comment');
define('API_VIEWED', 'api_viewed');
define('SEARCH_BOX', 'search_box');

define('WISHLIST', 'wishlist');
define('WISHLIST_PRODUCT', 'wishlist_product');
define('WISHLIST_ARTICLE', 'wishlist_article');

define('COMPARE', 'compare');

// field in config block
define('NUMBER_RECORD', 'number_record');
define('HAS_PAGINATION', 'has_pagination');
define('SORT_FIELD', 'sort_field');
define('SORT_TYPE', 'sort_type');
define('DATA_TYPE', 'data_type');

define('BY_URL', 'by_url');
define('BY_PAGE_ID', 'by_page_id');
define('PRODUCTS_VIEWED', 'products_viewed');
define('ARTICLES_VIEWED', 'articles_viewed');

// type row of template
define('HEADER', 'header');
define('CONTENT', 'content');
define('FOOTER', 'footer');

//status transaction payment gateway
define('SANDBOX', 'sandbox');

//code payment gateway
define('BANK_TRANFER', 'bank_tranfer');
define('PAYPAL', 'paypal');
define('BAOKIM', 'baokim');
define('ONEPAY', 'onepay');
define('ONEPAY_DOMESTIC', 'onepay_domestic');
define('ONEPAY_INTERNATIONAL', 'onepay_international');
define('ONEPAY_INSTALLMENT', 'onepay_installment');
define('ALEPAY', 'alepay');
define('VNPAY', 'vnpay');
define('MOMO', 'momo');
define('AZPAY', 'azpay');
define('VNPTPAY', 'vnptpay');
define('ZALOPAY', 'zalopay');

//code shipping carrier
define('GIAO_HANG_NHANH', 'ghn');
define('GIAO_HANG_TIET_KIEM', 'ghtk');

//code Sms Brandname
define('FPT_TELECOM', 'fpt_telecom');
define('ESMS', 'esms');

// type load block
define('TIMEOUT', 'timeout');
define('SCROLL', 'scroll');
define('ACTIVED', 'actived');


define('TOKEN_RECAPTCHA', 'token_recaptcha');

// type email template
define('ACTIVE_ACCOUNT', 'active_account');
define('FORGOT_PASSWORD', 'forgot_password');
define('LOGIN', 'login');
define('VERIFY_CHANGE_EMAIL', 'verify_change_email');
define('VERIFY_CHANGE_PHONE', 'verify_change_phone');
define('VERIFY_PHONE', 'verify_phone');
define('VERIFY_EMAIL', 'verify_email');

define('LEFT', 'left');
define('RIGHT', 'right');
define('CENTER', 'center');

define('FEATURED', 'featured');
define('DISCOUNT', 'discount');
define('STOCKING', 'stocking');

define('POINT', 'point');
define('FOLDER_DATABASE_INITIALIZATION', 'database_initialization');



define('FILTER_TYPE', 'filter_type');
define('FILTER_KEYWORD', 'filter_keyword');



define('AUDIO', 'audio');
define('DOCUMENT', 'document');
define('ARCHIVE', 'archive');
define('PATH', 'path');

define('TIME', 'time');
define('SIZE', 'size');
define('EXTENSION', 'extension');
define('NAME', 'name');
define('ASSESTS', 'assests');
define('LIMIT', 'limit');

define('THUMBS', 'thumbs');
define('MEDIA_THUMBS', 'media_thumbs');

define('PARTNER_STORE', 'partner_store');
define('KIOTVIET', 'kiotviet');
define('NHANH', 'nhanh');


// Loại QR
define('BANK_ACCOUNT', 'bank_account'); // tài khoản ngân hàng
define('BANK_TRANSACTION', 'bank_transaction'); // giao dịch ngân hàng

define('URL', 'url');
define('EMAIL', 'email');