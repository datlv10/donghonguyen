<?php
use Cake\Core\Configure;

Configure::write('LIST_STATUS',
	[
		ENABLE,
		DISABLE
	]
);

Configure::write('LIST_STATUS_PRODUCT',
	[
		ENABLE,
		DISABLE,
		STOP_BUSSINEUS
	]
);

Configure::write('LIST_TYPE_CATEGORY',
	[
		PRODUCT,
		ARTICLE,
	]
);

Configure::write('LIST_TYPE_VIDEO',
	[
		VIDEO_YOUTUBE,
		VIDEO_SYSTEM,
	]
);

Configure::write('LENGTH_UNIT',
	[
        'cm' => 'cm',
        'mm' => 'mm',        
        'm' => 'm',
    ]
);

Configure::write('WEIGTH_UNIT',
	[
        'g' => 'gram',
        'kg' => 'kilograms'
    ]
);

Configure::write('ALL_ATTRIBUTE',
    [
        TEXT,
        RICH_TEXT,
        NUMERIC,
        SINGLE_SELECT,
        MULTIPLE_SELECT,
        DATE,
        DATE_TIME,
        SWITCH_INPUT,
        SPECICAL_SELECT_ITEM,

        IMAGE,
        IMAGES,
        VIDEO,
        FILES,

        ALBUM_IMAGE,
        ALBUM_VIDEO,

        PRODUCT_SELECT,
        ARTICLE_SELECT,
        
        CITY,
        CITY_DISTRICT,
        CITY_DISTRICT_WARD
    ]
);

Configure::write('LIST_ATTRIBUTE_NORMAL',
    [
        TEXT => 'TEXT',
        RICH_TEXT => 'RICH_TEXT',
        NUMERIC => 'NUMERIC',
        SINGLE_SELECT => 'SINGLE_SELECT',
        MULTIPLE_SELECT => 'MULTIPLE_SELECT',
        DATE => 'DATE',
        DATE_TIME => 'DATE_TIME',
        SWITCH_INPUT => 'SWITCH_INPUT',

        IMAGE => 'IMAGE',
        IMAGES => 'IMAGES',
        VIDEO => 'VIDEO',
        FILES => 'FILES',

        ALBUM_IMAGE => 'ALBUM_IMAGE',
        ALBUM_VIDEO => 'ALBUM_VIDEO',

        PRODUCT_SELECT => 'PRODUCT_SELECT',
        ARTICLE_SELECT => 'ARTICLE_SELECT',
        
        CITY => 'CITY',
        CITY_DISTRICT => 'CITY - DISTRICT',
        CITY_DISTRICT_WARD => 'CITY - DISTRICT - WARD'
    ]
);


Configure::write('ATTRIBUTE_PRODUCT_ITEM',
    [
        TEXT => 'TEXT',
        NUMERIC => 'NUMERIC',
        SINGLE_SELECT => 'SINGLE_SELECT',
        MULTIPLE_SELECT => 'MULTIPLE_SELECT',
        DATE => 'DATE',
        DATE_TIME => 'DATE_TIME',
        SWITCH_INPUT => 'SWITCH_INPUT',
        SPECICAL_SELECT_ITEM => 'SPECICAL_SELECT_ITEM',
    ]
);

Configure::write('ATTRIBUTE_HAS_LIST_OPTIONS',
	[    
        SINGLE_SELECT,
        MULTIPLE_SELECT,
        SPECICAL_SELECT_ITEM,
    ]
);

Configure::write('LIST_TYPE_ORDER',
    [    
        ORDER,
        ORDER_RETURN,
        IMPORT,
        TRANSFER,
        RETAIL,
        OTHER_BILL
    ]
);

Configure::write('LIST_STATUS_ORDER',
    [   
        DRAFT,
        NEW_ORDER,
        CONFIRM,
        PACKAGE,
        EXPORT,
        DONE,
        CANCEL,
        WAITING_RECEIVING,
        RECEIVED
    ]
);

Configure::write('LIST_STATUS_SHIPPING',
    [    
        WAIT_DELIVER,
        DELIVERY,
        DELIVERED,
        CANCEL_PACKAGE,
        CANCEL_WAIT_DELIVER,
        CANCEL_DELIVERED
    ]
);

Configure::write('LIST_PAYMENT_GATEWAY',
    [    
        COD,
        BANK,
        BAOKIM,
        PAYPAL,
        ONEPAY,
        ONEPAY_INSTALLMENT,
        ALEPAY,
        VNPAY,
        MOMO,
        ZALOPAY,
        AZPAY,
        VNPTPAY
    ]
);

Configure::write('LIST_SHIPPING_CARRIER',
    [    
        GIAO_HANG_NHANH,
        GIAO_HANG_TIET_KIEM
    ]
);

Configure::write('LIST_AZPAY_GATEWAY',
    [    
        AZPAY . '_' . 1, // onepay nội địa
        AZPAY . '_' . 2, // onepay quốc tế
        AZPAY . '_' . 3, // onepay trả góp
        AZPAY . '_' . 4, // MoMo
    ]
);

Configure::write('LIST_ACTION_TYPE_POINT',
    [
        ORDER, 
        PROMOTION, 
        ATTENDANCE, 
        OTHER, 
        GIVE_POINT,
        BUY_POINT,
        AFFILIATE,
        WITHDRAW
    ]
);

// Configure::write('LIST_PAGE_TYPE_TEMPLATE',
//     [    
//         DEFAULT_PAGE,
//         PRODUCT,
//         ARTICLE,
//         CANCEL_PACKAGE,
//         CANCEL_WAIT_DELIVER,
//         CANCEL_DELIVERED
//     ]
// );

Configure::write('WHITE_LIST_EXTENSION',
    [ 'ctp', 'tpl', 'po', 'txt', 'jpeg', 'jpg', 'png', 'gif', 'bmp', 'pdf', 'csv', 'doc', 'docx', 'xlsx', 'xls', 'html', 'css', 'js', 'ttf', 'eot', 'woff', 'svg', 'woff2', 'ppt', 'pptx' ]
);

Configure::write('TYPE_TOKEN',
    [
        ACTIVE_ACCOUNT,
        FORGOT_PASSWORD,
        LOGIN,
        VERIFY_CHANGE_EMAIL,
        VERIFY_CHANGE_PHONE,
        VERIFY_PHONE,
        VERIFY_EMAIL,
        GIVE_POINT
    ]
);

Configure::write('LIST_FLATFORM_NOTIFICATION',
    [
        'web',
        'ios',
        'android'
    ]
);

Configure::write('LIST_TYPE_NOTIFICATION',
    [
        ALL,
        WEBSITE,
        MOBILE_APP
    ]
);

Configure::write('FONTS_QRCODE',
    [
        'Roboto-Medium.ttf' => 'Roboto-Medium',
        'Roboto-MediumItalic.ttf' => 'Roboto-MediumItalic',
        'Roboto-Black.ttf' => 'Roboto-Black',
        'Roboto-BlackItalic.ttf' => 'Roboto-BlackItalic',
        'Roboto-Bold.ttf' => 'Roboto-Bold',
        'Roboto-BoldItalic.ttf' => 'Roboto-BoldItalic',
        'Roboto-Italic.ttf' => 'Roboto-Italic',
        'Roboto-Light.ttf' => 'Roboto-Light',
        'Roboto-LightItalic.ttf' => 'Roboto-LightItalic',        
        'Roboto-Regular.ttf' => 'Roboto-Regular',
        'Roboto-Thin.ttf' => 'Roboto-Thin',
        'Roboto-ThinItalic.ttf' => 'Roboto-ThinItalic'
    ]
);

Configure::write('FONTS_SIZE_QRCODE',
    [
        '10' => '10px',
        '11' => '11px',
        '12' => '12px',
        '13' => '13px',
        '14' => '14px',
        '16' => '16px',
        '18' => '18px',
        '20' => '20px',
    ]
);

Configure::write('LIST_BANK',
    [
        'VietinBank' => 'VietinBank - Công Thương Việt Nam',
        'VPBank' => 'VPBank - Việt Nam Thịnh Vượng',
        'BIDV' => 'BIDV - Đầu tư và Phát triển Việt Nam',
        'MB' => 'MB - Quân đội',
        'Vietcombank' => 'Vietcombank - Ngoại thương Việt Nam',
        'Techcombank' => 'Techcombank - Kỹ Thương Việt Nam',
        'ACB' => 'ACB - Ngân hàng Á Châu',
        'SHB' => 'SHB - Sài Gòn-Hà Nội',
        'HDBank' => 'HDBank - NH TMCP Phát triển Nhà Tp HCM',
        'Sacombank' => 'Sacombank - Sài Gòn Thương Tín',
        'VIB' => 'VIB - NH TMCP Quốc tế Việt Nam',
        'MSB' => 'MSB - Hàng Hải Việt Nam',
        'SCB' => 'SCB - Ngân hàng TMCP Sài Gòn',
        'OCB' => 'OCB - Phương Đông',
        'SeABank' => 'SeABank - Ngân hàng Đông Nam Á SeABank',
        'Eximbank' => 'Ngân hàng xuất nhập khẩu Việt Nam',
        'LienVietPostBank' => 'LienVietPostBank - Bưu điện Liên Việt',
        'TPBank' => 'TPBank - Ngân hàng Tiên Phong',
        'PVcombank' => 'PVcombank - Đại chúng Việt Nam',
        'BacABank' => 'Bac A Bank - Ngân hàng TMCP Bắc Á',
        'ĐongABank' => 'Đông Á Bank - Ngân hàng TMCP Đông Á',
        'ABBANK' => 'ABBANK - Ngân hàng An Bình',
        'BaoVietBank' => 'BaoViet Bank - Bảo Việt',
        'VietBank' => 'VietBank - Việt Nam Thương Tín',
        'NamABank' => 'Nam A Bank - Ngân hàng TMCP Nam Á',
        'VietABank' => 'Viet A Bank - Ngân hàng TMCP Việt Á',
        'NCB' => 'NCB - Quốc Dân',
        'BanVietBank' => 'BanVietBank - Ngân hàng Bản Việt',
        'Kienlongbank' => 'Kienlongbank - Kiên Long',
        'Saigonbank' => 'Saigonbank - Sài Gòn Công Thương',
        'PGBank' => 'PGBank - Xăng dầu Petrolimex'
    ]
);

Configure::write('education_level',
    [
        1 => 'Tiểu học',
        2 => 'Trung học cơ sở',
        3 => 'Trung học phổ thông',
        4 => 'Cao đẳng',
        5 => 'Đại học',
        6 => 'Thạc Sĩ',
        7 => 'Tiến Sĩ'
    ]
);