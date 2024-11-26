<?php
use Cake\Core\Configure;

// lưu ý cài đặt quyền:
// Chỉ liệt kê các controller và function liên quan đến xem, sửa, xóa, cập nhật, còn lại các function khác của controller sẽ luôn luôn có quyền truy cập
// Những controller được liệt kê nhưng không có function bên trong thì hoặc đc cấu hình all hoặc deny all

Configure::write('BASE_CONTROLLER',
    [
        'Category' => [
            'view' => [
                'listCategoryProduct',
                'listCategoryArticle',
                'listJsonCategoryProduct',
                'listJsonCategoryArticle',
                'detail'
            ],
            'add' => [
                'add'
            ],            
            'update' => [
                'update',
                'changeStatus',
                'duplicate',
                'changePosition'
            ],
            'delete' => [
                'delete'
            ]
        ],
        'Media' => [],
        'Contact' => [],
        'Customer' => [
            'view' => [
                'list',
                'listJson',
                'detail'
            ],
            'add' => [
                'add'
            ],
            'update' => [
                'update',
                'saveAddress',
                'saveNote',
                'changeStatus',
                'setDefault',
                'deleteAddress',
                'deleteNote'
            ],
            'delete' => [
                'delete'
            ]
        ],
        'Template' => [],
        'TemplateBlock' => [],
        'Comment' => [],
        'Seo' => [
            'view' => [
                'pageSeoInfo',
                'setting'
            ],
            'update' => [
                'savePageSeoInfo',
                'uploadFileRobots'
            ],
        ],
        'SeoRedirect' => [
            'view' => [
                'list',
                'listJson'
            ],
            'add' => [
                'add'
            ],
            'update' => [
                'update',
                'changeStatus'
            ],
            'delete' => [
                'delete'
            ]
        ],
        'Tag' => [
            'view' => [
                'list',
                'listJson'
            ],
            'add' => [
                'add'
            ],
            'update' => [
                'update'
            ],
            'delete' => [
                'delete'
            ]
        ],
        'Setting' => [
            'view' => [
                'dashboard',
                'websiteInfo',
                'link',
                'embedCode',
                'clearData',
                'changeMode',
                'recaptcha',
                'printForm',
                'product',
                'order',
                'social',
                'customer',
                'api'
            ],
            'update' => [
                'save',
                'clearCache',
                'processClearData',
            ]
        ],
        'Language' => [
            'view' => [
                'list',
                'listJson'
            ],
            'update' => [
                'isDefault',
                'changeStatus'
            ]
        ],
        'PaymentGateway' => [
            'view' => [
                'list'
            ],
            'update' => [
                'save'
            ]
        ],
        'Attribute' => [
            'view' => [
                'list',
                'listJson',
                'detail'
            ],
            'add' => [
                'add'
            ],
            'update' => [
                'update',
                'changeStatus',
                'duplicate',
                'changePosition'
            ],
            'delete' => [
                'delete'
            ]
        ],
        'Currency' => [
            'view' => [
                'list',
                'listJson'
            ],
            'add' => [
                'add'
            ],
            'update' => [
                'update',
                'changeStatus',
                'isDefault'
            ],
            'delete' => [
                'delete'
            ]
        ],
        'Role' => [
            'view' => [
                'list',
                'listJson',
                'detail'
            ],
            'add' => [
                'add'
            ],
            'update' => [
                'update',
                'changeStatus',
                'isDefault',
                'permissionSetup',
                'permissionSave'
            ],
            'delete' => [
                'delete'
            ]
        ],
        'User' => [
            'view' => [
                'list',
                'listJson',
                'detail'
            ],
            'add' => [
                'add'
            ],
            'update' => [
                'update',
                'changeStatus'
            ],
            'delete' => [
                'delete'
            ]
        ],
    ]
);

Configure::write('ARTICLE_TYPE_CONTROLLER',
    [
        'Article' => [
            'view' => [
                'list',
                'listJson',
                'detail'
            ],
            'add' => [
                'add'
            ],
            'status' => [
                'changeStatus'
            ],
            'update' => [
                'update',
                'duplicate',
                'changePosition',
                'quickUpload'
            ],
            'delete' => [
                'delete'
            ]
        ]        
    ]
);

Configure::write('PRODUCT_TYPE_CONTROLLER',
    [
        'Product' => [
            'view' => [
                'list',
                'listJson',
                'detail'
            ],
            'add' => [
                'add'
            ],
            'status' => [
                'changeStatus'
            ],
            'update' => [
                'update',
                'quickSave',
                'quickChange',
                'changePosition',
                'quickUpload'
            ],
            'delete' => [
                'delete'
            ]
        ],
        'Brand' => [],
        'Order' => [
            'view' => [
                'list',
                'listJson',
                'detail'
            ],
            'add' => [
                'add'
            ],
            'update' => [
                'update',
                'changeStatus',
                'paymentConfirm',
                'shippingConfirm',
                'shippingChangeStatus',
                'cancel',
                'changeNote',
                'updateContact'
            ]
        ],
        'Payment' => [
            'view' => [
                'list',
                'listJson',
                'detail'
            ],
            'update' => [
                'changeNote',
                'changeStatus'
            ]
        ],
        'Shipment' => [],
        'Report' => []

    ]
);

Configure::write('MOBILE_APP_TYPE_CONTROLLER',
    [
        'Mobile' => [],
        'MobileTemplate' => [],
        'MobileTemplateBlock' => []
    ]
);

Configure::write('PROMOTION_TYPE_CONTROLLER',
    [
        'Promotion' => [
            'view' => [
                'list',
                'listJson'
            ],
            'add' => [
                'add'
            ],
            'status' => [
                'changeStatus'
            ],
            'update' => [
                'update',
                'changePosition'
            ],
            'delete' => [
                'delete'
            ]
        ],
        'PromotionCoupon' => [
            'view' => [
                'list',
                'listJson'
            ],
            'add' => [
                'addCoupon'
            ],
            'status' => [
                'changeStatus'
            ],
            'delete' => [
                'delete'
            ]
        ]
    ]
);

Configure::write('POINT_TYPE_CONTROLLER',
    [
        'CustomerPoint' => [],
        'CustomerPointHistory' => [
            'view' => [
                'list',
                'listJson'
            ],
            'add' => [
                'add'
            ]
        ]
    ]
);

Configure::write('AFFILIATE_TYPE_CONTROLLER',
    [
        'CustomerAffiliate' => [
            'view' => [
                'statistical',
                'statisticsOrder',
                'chartOrder',
                'topPartner',
                'newPartner',
                'list',
                'listJson',
                'detail'
            ]
        ],
        'CustomerAffiliateOrder' => [],
        'CustomerAffiliateRequest' => [
            'view' => [
                'list',
                'listJson'
            ],
            'status' => [
                'changeStatus'
            ],
            'delete' => [
                'delete'
            ]
        ],
        'CustomersPointTomoney' => [
            'view' => [
                'list',
                'listJson'
            ],
            'add' => [
                'save'
            ],
            'status' => [
                'changeStatus'
            ],
            'update' => [
                'changeNote'
            ],
            'delete' => [
                'delete'
            ]
        ],
    ]
);

Configure::write('LOCALES_KEY_CONTROLLER',
    [
        'Article' => 'bai_viet',
        'Attribute' => 'thuoc_tinh_mo_rong',
        'AttributeOption' => 'tuy_chon_cua_thuoc_tinh',
        'Brand' => 'thuong_hieu',
        'Carriers' => 'hang_van_chuyen',
        'Category' => 'danh_muc',
        'City' => 'tinh_thanh',
        'Comment' => 'binh_luan',
        'Contact' => 'lien_he_cua_khach_hang',
        'ContactForm' => 'form_lien_he',
        'Currency' => 'tien_te',
        'Customer' => 'khach_hang',
        'Dashboard' => 'tong_quan',
        'District' => 'quan_huyen',
        'EmailTemplate' => 'mau_email',
        'Language' => 'ngon_ngu',
        'Link' => 'duong_dan',
        'Media' => 'media',
        'Object' => 'doi_tuong',
        'Order' => 'don_hang',
        'Payment' => 'giao_dich',
        'PaymentGateway' => 'cong_thanh_toan',
        'Product' => 'san_pham',
        'Report' => 'bao_cao',
        'Role' => 'nhom_quyen',
        'Seo' => 'seo',
        'SeoRedirect' => 'chuyen_huong_301',
        'SeoSiteMap' => 'site_map',
        'Setting' => 'cai_dat_chung',
        'Shipment' => 'van_don',
        'Tag' => 'the_bai_viet',
        'TemplateBlock' => 'block_giao_dien',
        'Template' => 'giao_dien',
        'TemplateModify' => 'chinh_sua_giao_dien',
        'User' => 'tai_khoan',
        'Ward' => 'phuong_xa',
        'Mobile' => 'mobile_app',
        'MobileTemplate' => 'giao_dien_mobile_app',
        'MobileTemplateBlock' => 'block_mobile_app',
        'Promotion' => 'chuong_trinh_khuyen_mai',
        'PromotionCoupon' => 'ma_coupon',
        'CustomerPoint' => 'diem_khach_hang',
        'CustomerPointHistory' => 'lich_su_su_dung_diem',
        'CustomerAffiliate' => 'affiliate',
        'CustomerAffiliateOrder' => 'don_hang_doi_tac',
        'CustomerAffiliateRequest' => 'yeu_cau_hop_tac',
        'CustomersPointTomoney' => 'yeu_cau_rut_tien',
    ]
);
