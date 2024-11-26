{assign var = permissions value = $this->RoleAdmin->getPermissionAllRouter()}

<div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
    <div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
        <ul class="kt-menu__nav ">


            {* ---------------------------- menu tổng quan *}
            {assign var = link_dashboard value = "{ADMIN_PATH}/main"}
            {assign var = access_dashboard value = "{if !empty($permissions[$link_dashboard])}1{/if}"}

            {if $access_dashboard}
                <li class="kt-menu__item">
                    <a href="{$link_dashboard}" class="kt-menu__link " path-menu="dashboard">
                        <span class="kt-menu__link-icon">
                            <i class="fa fa-th-large"></i>
                        </span>
                        <span class="kt-menu__link-text">
                            {__d('admin', 'tong_quan')}
                        </span>
                    </a>
                </li>
            {/if}
            



            {* ---------------------------- menu bài viết *}
            {assign var = link_article_add value = "{ADMIN_PATH}/article/add"}
            {assign var = link_article value = "{ADMIN_PATH}/article"}
            {assign var = link_category_article value = "{ADMIN_PATH}/category/article"}

            {assign var = access_article_add value = "{if !empty($permissions[$link_article_add])}1{/if}"}
            {assign var = access_article value = "{if !empty($permissions[$link_article])}1{/if}"}
            {assign var = access_category_article value = "{if !empty($permissions[$link_category_article])}1{/if}"}
            
            {if $access_article_add || $access_article || $access_category_article}
                {* header menu bài viết *}
                <li class="kt-menu__section ">
                    <h4 class="kt-menu__section-text">
                        {__d('admin', 'bai_viet')}
                    </h4>
                    <i class="kt-menu__section-icon flaticon-more-v2"></i>
                </li>

                {* menu bài viết *}
                <li class="kt-menu__item  kt-menu__item--submenu">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <i class="fa fa-file-alt"></i>
                        </span>

                        <span class="kt-menu__link-text">
                            {__d('admin', 'bai_viet')}
                        </span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>

                    <div class="kt-menu__submenu">
                        <span class="kt-menu__arrow"></span>

                        <ul class="kt-menu__subnav">
                            {if $access_article_add}
                                <li class="kt-menu__item">
                                    <a href="{$link_article_add}" class="kt-menu__link" path-menu="article_add">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>

                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'them_bai_viet')}
                                        </span>
                                    </a>
                                </li>
                            {/if}

                            {if $access_article}
                                <li class="kt-menu__item">
                                    <a href="{$link_article}" class="kt-menu__link" path-menu="article">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'danh_sach')}
                                        </span>
                                    </a>
                                </li>
                            {/if}

                            {if $access_category_article}
                                <li class="kt-menu__item">
                                    <a href="{$link_category_article}" class="kt-menu__link" path-menu="category_article">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'danh_muc')}
                                        </span>
                                    </a>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </li>
            {/if}






            {* ---------------------------- menu media *}
            {assign var = link_media value = "{ADMIN_PATH}/media"}
            {assign var = access_media value = "{if !empty($permissions[$link_media])}1{/if}"}
            
            {if $access_media}
                <li class="kt-menu__item">
                    <a href="{$link_media}" class="kt-menu__link" path-menu="media">
                        <span class="kt-menu__link-icon">
                            <i class="fa fa-images"></i>
                        </span>

                        <span class="kt-menu__link-text">
                            {__d('admin', 'media')}
                        </span>
                    </a>
                </li>
            {/if}
            

            {* menu phả đồ *}
            {assign var = link_genealogy value = "{ADMIN_PATH}/genealogy"}
            {assign var = access_genealogy value = "{if !empty($permissions[$link_genealogy])}1{/if}"}

            {if $access_genealogy}
                <li class="kt-menu__item">
                    <a href="{$link_genealogy}" class="kt-menu__link" path-menu="genealogy">
                        <span class="kt-menu__link-icon">
                            <i class="fa-solid fa-sitemap"></i>
                        </span>

                        <span class="kt-menu__link-text">
                            Phả đồ
                        </span>
                    </a>
                </li>
            {/if}





            {* ---------------------------- menu bán hàng *}
            {if !empty($plugins[{PRODUCT}])}

                {assign var = link_order_add value = "{ADMIN_PATH}/order/add"}
                {assign var = link_order value = "{ADMIN_PATH}/order"}
                {assign var = link_payment value = "{ADMIN_PATH}/payment"}
                {assign var = link_shipment value = "{ADMIN_PATH}/shipment"}

                {assign var = link_product_add value = "{ADMIN_PATH}/product/add"}
                {assign var = link_product value = "{ADMIN_PATH}/product"}
                {assign var = link_category_product value = "{ADMIN_PATH}/category/product"}
                {assign var = link_brand value = "{ADMIN_PATH}/brand"}

                {assign var = link_promotion_add value = "{ADMIN_PATH}/promotion/add"}
                {assign var = link_promotion value = "{ADMIN_PATH}/promotion"}
                {assign var = link_coupon value = "{ADMIN_PATH}/promotion/coupon"}

                {assign var = link_report value = "{ADMIN_PATH}/report"}

                {assign var = access_order_add value = "{if !empty($permissions[$link_order_add])}1{/if}"}
                {assign var = access_order value = "{if !empty($permissions[$link_order])}1{/if}"}
                {assign var = access_payment value = "{if !empty($permissions[$link_payment])}1{/if}"}
                {assign var = access_shipment value = "{if !empty($permissions[$link_shipment])}1{/if}"}

                {assign var = access_product_add value = "{if !empty($permissions[$link_product_add])}1{/if}"}
                {assign var = access_product value = "{if !empty($permissions[$link_product])}1{/if}"}
                {assign var = access_category_product value = "{if !empty($permissions[$link_category_product])}1{/if}"}
                {assign var = access_brand value = "{if !empty($permissions[$link_brand])}1{/if}"}

                {assign var = access_promotion_add value = "{if !empty($permissions[$link_promotion_add])}1{/if}"}
                {assign var = access_promotion value = "{if !empty($permissions[$link_promotion])}1{/if}"}
                {assign var = access_coupon value = "{if !empty($permissions[$link_coupon])}1{/if}"}

                {assign var = access_report value = "{if !empty($permissions[$link_report])}1{/if}"}

                {* header bán hàng *}
                {if $access_order_add || $access_order || $access_payment || $access_shipment || 
                    $access_product_add || $access_product || $access_category_product || $access_brand || $access_promotion_add || $access_promotion || $link_coupon || $access_report
                }
                    <li class="kt-menu__section">
                        <h4 class="kt-menu__section-text">
                            {__d('admin', 'ban_hang')}
                        </h4>
                        <i class="kt-menu__section-icon flaticon-more-v2"></i>
                    </li>
                {/if}


                {* menu sản phẩm *}
                {if $access_product_add || $access_product || $access_category_product || $access_brand}
                    <li class="kt-menu__item kt-menu__item--submenu">
                        <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                            <span class="kt-menu__link-icon">
                                <i class="fa fa-box-open"></i>
                            </span>
                            <span class="kt-menu__link-text">
                                {__d('admin', 'san_pham')}
                            </span>
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>

                        <div class="kt-menu__submenu ">
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">     
                                {if $access_product_add}
                                    <li class="kt-menu__item">
                                        <a href="{$link_product_add}" class="kt-menu__link" path-menu="product_add">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'them_san_pham')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}

                                {if $access_product}
                                    <li class="kt-menu__item">
                                        <a href="{$link_product}" class="kt-menu__link" path-menu="product">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'danh_sach')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}

                                {if $access_category_product}
                                    <li class="kt-menu__item">
                                        <a href="{$link_category_product}" class="kt-menu__link" path-menu="category_product">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'danh_muc')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}

                                {if $access_brand}
                                    <li class="kt-menu__item">
                                        <a href="{$link_brand}" class="kt-menu__link" path-menu="brand">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'thuong_hieu')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}
                            </ul>
                        </div>
                    </li>
                {/if}

                {* menu đơn hàng *}
                {if $access_order_add || $access_order || $access_shipment}
                    <li class="kt-menu__item  kt-menu__item--submenu">
                        <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                            <span class="kt-menu__link-icon">
                                <i class="fa fa-shopping-basket"></i>
                            </span>
                            <span class="kt-menu__link-text">
                                {__d('admin', 'don_hang')}
                            </span>
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>

                        <div class="kt-menu__submenu">
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                {if $access_order_add}
                                    <li class="kt-menu__item">
                                        <a href="{$link_order_add}" class="kt-menu__link" path-menu="order_add">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'tao_don_moi')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}

                                {if $access_order}
                                    <li class="kt-menu__item">
                                        <a href="{$link_order}" class="kt-menu__link" path-menu="order">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'danh_sach')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}

                                {if $access_shipment}
                                    <li class="kt-menu__item">
                                        <a href="{$link_shipment}" class="kt-menu__link" path-menu="shipment">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'van_don')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}
                            </ul>
                        </div>
                    </li>
                {/if}

                {*menu giao dịch*}
                {if $access_payment}
                    <li class="kt-menu__item  kt-menu__item--submenu">
                        <a href="{$link_payment}" class="kt-menu__link" path-menu="payment">
                            <span class="kt-menu__link-icon">
                                <i class="fa fa-money-bill-alt"></i>
                            </span>
                            <span class="kt-menu__link-text">
                                {__d('admin', 'giao_dich')}
                            </span>
                        </a>
                    </li>
                {/if}

                {* menu khuyến mãi *}
                {if !empty($plugins[{PROMOTION}])}
                    {if $access_promotion_add || $access_promotion || $access_coupon}
                        <li class="kt-menu__item kt-menu__item--submenu">
                            <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                                <span class="kt-menu__link-icon">
                                    <i class="fa fa-gift"></i>
                                </span>
                                <span class="kt-menu__link-text">
                                    {__d('admin', 'khuyen_mai')}
                                </span>
                                <i class="kt-menu__ver-arrow la la-angle-right"></i>
                            </a>

                            <div class="kt-menu__submenu ">
                                <span class="kt-menu__arrow"></span>
                                <ul class="kt-menu__subnav">     
                                    {if $access_promotion_add}
                                        <li class="kt-menu__item">
                                            <a href="{$link_promotion_add}" class="kt-menu__link" path-menu="promotion_add">
                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                    <span></span>
                                                </i>
                                                <span class="kt-menu__link-text">
                                                    {__d('admin', 'them_khuyen_mai')}
                                                </span>
                                            </a>
                                        </li>
                                    {/if}

                                    {if $access_promotion}
                                        <li class="kt-menu__item">
                                            <a href="{$link_promotion}" class="kt-menu__link" path-menu="promotion">
                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                    <span></span>
                                                </i>
                                                <span class="kt-menu__link-text">
                                                    {__d('admin', 'danh_sach')}
                                                </span>
                                            </a>
                                        </li>
                                    {/if}

                                    {if $access_coupon}
                                        <li class="kt-menu__item">
                                            <a href="{$link_coupon}" class="kt-menu__link" path-menu="coupon">
                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                    <span></span>
                                                </i>
                                                <span class="kt-menu__link-text">
                                                    {__d('admin', 'ma_coupon')}
                                                </span>
                                            </a>
                                        </li>
                                    {/if}
                                </ul>
                            </div>
                        </li>
                    {/if}
                {/if}

                {* menu báo cáo *}
                {if $access_report}
                    <li class="kt-menu__item kt-menu__item--submenu">
                        <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                            <span class="kt-menu__link-icon">
                                <i class="fa fa-chart-pie"></i>
                            </span>
                            <span class="kt-menu__link-text">
                                {__d('admin', 'bao_cao')}
                            </span>
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>
                        <div class="kt-menu__submenu ">
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">     
                                <li class="kt-menu__item">
                                    <a href="{ADMIN_PATH}/report" class="kt-menu__link" path-menu="report">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'tong_quan')}
                                        </span>
                                    </a>
                                </li>
                                
                                <li class="kt-menu__item">
                                    <a href="{ADMIN_PATH}/report/revenue" class="kt-menu__link" path-menu="report_revenue">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'theo_thoi_gian')}
                                        </span>
                                    </a>
                                </li>
                                
                                <li class="kt-menu__item">
                                    <a href="{ADMIN_PATH}/report/product" class="kt-menu__link" path-menu="report_product">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'theo_san_pham')}
                                        </span>
                                    </a>
                                </li>

                                <li class="kt-menu__item">
                                    <a href="{ADMIN_PATH}/report/staff" class="kt-menu__link" path-menu="report_staff">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'theo_nhan_vien')}
                                        </span>
                                    </a>
                                </li>

                                <li class="kt-menu__item">
                                    <a href="{ADMIN_PATH}/report/city" class="kt-menu__link" path-menu="report_city">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'theo_tinh_thanh')}
                                        </span>
                                    </a>
                                </li>

                                <li class="kt-menu__item">
                                    <a href="{ADMIN_PATH}/report/source" class="kt-menu__link" path-menu="report_source">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'theo_nguon_don_hang')}
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                {/if}
            {/if}




            {* ---------------------------- menu khách hàng *}
            {assign var = link_customer value = "{ADMIN_PATH}/customer"}
            {assign var = link_comment value = "{ADMIN_PATH}/comment"}
            {assign var = link_contact value = "{ADMIN_PATH}/contact"}
            {assign var = link_point value = "{ADMIN_PATH}/customer/point"}
            {assign var = link_point_history value = "{ADMIN_PATH}/customer/point-history"}

            {assign var = link_affiliate value = "{ADMIN_PATH}/customer/affiliate"}
            {assign var = link_affiliate_statistical value = "{ADMIN_PATH}/customer/affiliate/statistical"}
            {assign var = link_affiliate_request value = "{ADMIN_PATH}/customer/affiliate/request"}
            {assign var = link_affiliate_point_tomoney value = "{ADMIN_PATH}/customer/point-tomoney"}
            {assign var = link_affiliate_order value = "{ADMIN_PATH}/customer/affiliate/order"}

            {assign var = link_notification value = "{ADMIN_PATH}/notification"}
            {assign var = link_notification_add value = "{ADMIN_PATH}/notification/add"}
            {assign var = link_notification_sent value = "{ADMIN_PATH}/notification/sent"}
            {assign var = link_notification_subscribe value = "{ADMIN_PATH}/notification/subscribe"}

            {assign var = access_customer value = "{if !empty($permissions[$link_customer])}1{/if}"}
            {assign var = access_comment value = "{if !empty($permissions[$link_comment])}1{/if}"}
            {assign var = access_contact value = "{if !empty($permissions[$link_contact])}1{/if}"}
            {assign var = access_point value = "{if !empty($permissions[$link_point])}1{/if}"}
            {assign var = access_point_history value = "{if !empty($permissions[$link_point_history])}1{/if}"}

            {assign var = access_affiliate value = "{if !empty($permissions[$link_affiliate])}1{/if}"}
            {assign var = access_affiliate_statistical value = "{if !empty($permissions[$link_affiliate_statistical])}1{/if}"}
            {assign var = access_affiliate_request value = "{if !empty($permissions[$link_affiliate_request])}1{/if}"}
            {assign var = access_affiliate_point_tomoney value = "{if !empty($permissions[$link_affiliate_point_tomoney])}1{/if}"}
            {assign var = access_affiliate_order value = "{if !empty($permissions[$link_affiliate_order])}1{/if}"}

            {assign var = access_notification value = "{if !empty($permissions[$link_notification])}1{/if}"}
            {assign var = access_notification_add value = "{if !empty($permissions[$link_notification_add])}1{/if}"}
            {assign var = access_notification_sent value = "{if !empty($permissions[$link_notification_sent])}1{/if}"}
            {assign var = access_notification_subscribe value = "{if !empty($permissions[$link_notification_subscribe])}1{/if}"}

            {if $access_customer || $access_comment || $access_contact || $access_point || $access_point_history || $access_affiliate || $access_affiliate_statistical || $access_affiliate_request || $access_notification || $access_notification_add || $access_notification_subscribe}
                {* header khách hàng *}
                <li class="kt-menu__section">
                    <h4 class="kt-menu__section-text">
                        {__d('admin', 'khach_hang')}
                    </h4>
                    <i class="kt-menu__section-icon flaticon-more-v2"></i>
                </li>
            {/if}

            {* menu khách hàng *}
            {if $access_customer}
                <li class="kt-menu__item">
                    <a href="{$link_customer}" class="kt-menu__link" path-menu="customer">
                        <span class="kt-menu__link-icon">
                            <i class="fa fa-users"></i>
                        </span>
                        <span class="kt-menu__link-text">
                            {__d('admin', 'khach_hang')}
                        </span>
                    </a>
                </li>
            {/if}

            {* menu quản lý điểm *}
            {if !empty($plugins[{POINT}])}
                {if $access_point || $access_point_history}
                    <li class="kt-menu__item kt-menu__item--submenu">
                        <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                            <span class="kt-menu__link-icon">
                                <i class="fa fa-wallet"></i>
                            </span>
                            <span class="kt-menu__link-text">
                                {__d('admin', 'diem_khach_hang')}
                            </span>
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>

                        <div class="kt-menu__submenu ">
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">     
                                {if $access_point}
                                    <li class="kt-menu__item">
                                        <a href="{$link_point}" class="kt-menu__link" path-menu="payment">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'danh_sach')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}

                                {if $access_point_history}
                                    <li class="kt-menu__item">
                                        <a href="{$link_point_history}" class="kt-menu__link" path-menu="customers_point_history">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'su_dung_diem')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}
                            </ul>
                        </div>
                    </li>
                {/if}
            {/if}

            {* menu quản lý affiliate *}
            {if !empty($plugins[{AFFILIATE}])}
                <li class="kt-menu__item kt-menu__item--submenu">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <i class="fas fa-project-diagram"></i>
                        </span>
                        <span class="kt-menu__link-text">
                            {__d('admin', 'affiliate')}
                        </span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>

                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">     
                            {if $access_affiliate_statistical}
                                <li class="kt-menu__item">
                                    <a href="{$link_affiliate_statistical}" class="kt-menu__link" path-menu="affiliate_statistical">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'thong_ke')}
                                        </span>
                                    </a>
                                </li>
                            {/if}

                            {if $access_affiliate}
                                <li class="kt-menu__item">
                                    <a href="{$link_affiliate}" class="kt-menu__link" path-menu="affiliate">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'danh_sach_doi_tac')}
                                        </span>
                                    </a>
                                </li>
                            {/if}

                            {if $access_affiliate_order}
                                <li class="kt-menu__item">
                                    <a href="{$link_affiliate_order}" class="kt-menu__link" path-menu="affiliate_order">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'don_hang_doi_tac')}
                                        </span>
                                    </a>
                                </li>
                            {/if}

                            {if $access_affiliate_request}
                                <li class="kt-menu__item">
                                    <a href="{$link_affiliate_request}" class="kt-menu__link" path-menu="affiliate_request">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'yeu_cau_hop_tac')}
                                        </span>
                                    </a>
                                </li>
                            {/if}

                            {if $access_affiliate_point_tomoney}
                                <li class="kt-menu__item">
                                    <a href="{$link_affiliate_point_tomoney}" class="kt-menu__link" path-menu="customer_tomoney">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'yeu_cau_rut_tien')}
                                        </span>
                                    </a>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </li>
            {/if}

            {* menu bình luận *}
            {if $access_comment}
                <li class="kt-menu__item">
                    <a href="{$link_comment}" class="kt-menu__link" path-menu="comment">
                        <span class="kt-menu__link-icon">
                            <i class="fa fa-comments"></i>
                        </span>
                        <span class="kt-menu__link-text">
                            {__d('admin', 'binh_luan_danh_gia')}
                        </span>
                    </a>
                </li>
            {/if}

            {* menu liên hệ *}
            {if $access_contact}
                <li class="kt-menu__item">
                    <a href="{$link_contact}" class="kt-menu__link" path-menu="contact">
                        <span class="kt-menu__link-icon">
                            <i class="fab fa-telegram-plane"></i>
                        </span>
                        <span class="kt-menu__link-text">
                            {__d('admin', 'lien_he')}
                        </span>
                    </a>
                </li>
            {/if}


            {* menu thong bao *}
            {if !empty($plugins.notification)}
                {if $access_notification || $access_notification_add || $access_notification_sent ||$access_notification_subscribe}
                    <li class="kt-menu__item kt-menu__item--submenu">
                        <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                            <span class="kt-menu__link-icon">
                                <i class="fa fa-bell"></i>
                            </span>
                            <span class="kt-menu__link-text">
                                {__d('admin', 'thong_bao')}
                            </span>
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>

                        <div class="kt-menu__submenu ">
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">

                                {if !empty($access_notification)}
                                    <li class="kt-menu__item">
                                        <a href="{$link_notification}" class="kt-menu__link" path-menu="notification">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'danh_sach')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}

                                {if !empty($access_notification_add)}
                                    <li class="kt-menu__item">
                                        <a href="{$link_notification_add}" class="kt-menu__link" path-menu="notification_add">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'tao_thong_bao_moi')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}

                                {if !empty($access_notification_subscribe)}
                                    <li class="kt-menu__item">
                                        <a href="{$link_notification_subscribe}" class="kt-menu__link" path-menu="notification_subscribe">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'danh_sach_thiet_bi')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}

                                {if !empty($access_notification_sent)}
                                    <li class="kt-menu__item">
                                        <a href="{$link_notification_sent}" class="kt-menu__link" path-menu="notification_sent">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="kt-menu__link-text">
                                                {__d('admin', 'lich_su_gui')}
                                            </span>
                                        </a>
                                    </li>
                                {/if}
                            </ul>
                        </div>
                    </li>
                {/if}
            {/if}



            {* ---------------------------- menu cài đặt *}
            {assign var = link_tag value = "{ADMIN_PATH}/tag"}
            {assign var = link_page_seo_info value = "{ADMIN_PATH}/page-seo-info"}
            {assign var = link_seo_redirect value = "{ADMIN_PATH}/redirect-301"}
            {assign var = link_seo_setting value = "{ADMIN_PATH}/seo-setting"}

            {assign var = link_setting value = "{ADMIN_PATH}/setting/dashboard"}
            {assign var = link_template value = "{ADMIN_PATH}/template/dashboard"}

            {assign var = access_tag value = "{if !empty($permissions[$link_tag])}1{/if}"}
            {assign var = access_page_seo_info value = "{if !empty($permissions[$link_page_seo_info])}1{/if}"}
            {assign var = access_seo_redirect value = "{if !empty($permissions[$link_seo_redirect])}1{/if}"}
            {assign var = access_seo_setting value = "{if !empty($permissions[$link_seo_setting])}1{/if}"}

            {assign var = access_setting value = "{if !empty($permissions[$link_setting])}1{/if}"}
            {assign var = access_template value = "{if !empty($permissions[$link_template])}1{/if}"}

            {* header cài đặt *}
            {if $access_tag || $access_page_seo_info || $access_seo_redirect || $access_seo_setting || $access_setting || $access_template}
                <li class="kt-menu__section">
                    <h4 class="kt-menu__section-text">
                        {__d('admin', 'cai_dat')}
                    </h4>
                    <i class="kt-menu__section-icon flaticon-more-v2"></i>
                </li>
            {/if}

            {* menu SEO *}
            {if $access_tag || $access_page_seo_info || $access_seo_redirect || $access_seo_setting}
                <li class="kt-menu__item  kt-menu__item--submenu">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <i class="fa fa-rss-square"></i>
                        </span>

                        <span class="kt-menu__link-text">
                            {__d('admin', 'seo')}
                        </span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>

                    <div class="kt-menu__submenu">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            {if $access_tag}
                                <li class="kt-menu__item">
                                    <a href="{$link_tag}" class="kt-menu__link" path-menu="tag">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'the_bai_viet')}
                                        </span>
                                    </a>
                                </li>
                            {/if}

                            {if $access_page_seo_info}
                                <li class="kt-menu__item">
                                    <a href="{$link_page_seo_info}" class="kt-menu__link" path-menu="page_seo_info">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'thong_tin_seo')}
                                        </span>
                                    </a>
                                </li>
                            {/if}

                            {if $access_seo_redirect}
                                <li class="kt-menu__item">
                                    <a href="{$link_seo_redirect}" class="kt-menu__link" path-menu="seo_redirect">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'chuyen_huong_301')}
                                        </span>
                                    </a>
                                </li>
                            {/if}

                            {if $access_seo_setting}
                                <li class="kt-menu__item">
                                    <a href="{$link_seo_setting}" class="kt-menu__link" path-menu="seo_setting">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">
                                            {__d('admin', 'cai_dat')}
                                        </span>
                                    </a>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </li>
            {/if}

            {* menu cài đặt chung *}
            {if $access_setting}
                <li class="kt-menu__item">
                    <a href="{$link_setting}" class="kt-menu__link" path-menu="setting">
                        <span class="kt-menu__link-icon">
                            <i class="fa fa-tools"></i>
                        </span>

                        <span class="kt-menu__link-text">
                            {__d('admin', 'cai_dat_chung')}
                        </span>
                    </a>
                </li>
            {/if}

            {* menu giao diện *}
            {if $access_template}
                <li class="kt-menu__item">
                    <a href="{$link_template}" class="kt-menu__link" path-menu="template">
                        <span class="kt-menu__link-icon">
                            <i class="fa fa-desktop"></i>
                        </span>

                        <span class="kt-menu__link-text">
                            {__d('admin', 'giao_dien')}
                        </span>
                    </a>
                </li>
            {/if}

            {* menu Mobile App *}
            {if !empty($plugins[{MOBILE_APP}])}
                {assign var = link_mobile_app value = "{ADMIN_PATH}/mobile-app"}
                {assign var = access_mobile_app value = "{if !empty($permissions[$link_mobile_app])}1{/if}"}
                {if $access_mobile_app}
                    <li class="kt-menu__item">
                        <a href="{ADMIN_PATH}/mobile-app" class="kt-menu__link" path-menu="mobile_app">
                            <span class="kt-menu__link-icon">
                                <i class="fa fa-mobile-alt"></i>
                            </span>

                            <span class="kt-menu__link-text">
                                {__d('admin', 'mobile_app')}
                            </span>
                        </a>
                    </li>
                {/if}
            {/if}

            {* header hỗ trợ đang ẩn *}
            <li class="kt-menu__section">
                <h4 class="kt-menu__section-text">
                    {__d('admin', 'ho_tro')}
                </h4>
                <i class="kt-menu__section-icon flaticon-more-v2"></i>
            </li>

            <li class="kt-menu__item">
                <a href="{ADMIN_PATH}/feedback" class="kt-menu__link" path-menu="feedback">
                    <span class="kt-menu__link-icon">
                        <i class="fab fa-telegram-plane"></i>
                    </span>

                    <span class="kt-menu__link-text">
                        {__d('admin', 'gui_yeu_cau')}
                    </span>
                </a>
            </li>

            <li class="kt-menu__section d-none">
                <h4 class="kt-menu__section-text">
                    {__d('admin', 'ho_tro')}
                </h4>
                <i class="kt-menu__section-icon flaticon-more-v2"></i>
            </li>
        </ul>
    </div>
</div>