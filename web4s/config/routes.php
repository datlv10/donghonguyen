<?php

use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Core\Configure;

$routes->setRouteClass(DashedRoute::class);



// webhooks 
$routes->connect('/payment/webhooks/:code', ['controller' => 'Payment', 'action' => 'webhooks'], ['pass' => ['code']]);
$routes->connect('/shipping/webhooks/:code', ['controller' => 'Shipping', 'action' => 'webhooks'], ['pass' => ['code']]);
$routes->connect('/kiot-viet/webhooks/stock-update', ['controller' => 'Website', 'action' => 'webhooksKiotviet']);
$routes->connect('/kiot-viet/webhooks/product-update', ['controller' => 'Website', 'action' => 'webhooksKiotviet']);


$routes->scope('/', function (RouteBuilder $builder) {
    $builder->registerMiddleware('csrf', new CsrfProtectionMiddleware([
        'httponly' => true,
    ]));
    $builder->applyMiddleware('csrf');

    // system
    $builder->connect('/404', ['controller' => 'Page', 'action' => 'notFound']);
    $builder->connect('/error', ['controller' => 'Page', 'action' => 'error']);
    $builder->connect('/sitemap.xml', ['controller' => 'Sitemap', 'action' => 'index']);
    $builder->connect('/sitemap-:group', ['controller' => 'Sitemap', 'action' => 'index'], ['pass' => ['group']]);    

    $builder->connect('/language/active', ['controller' => 'Language', 'action' => 'activeLanguage']);
    $builder->connect('/currency/active', ['controller' => 'Currency', 'action' => 'activeCurrency']);

    // block
    $builder->connect('/block/ajax-load-content/:code', ['controller' => 'Block', 'action' => 'ajaxLoadContent'], ['pass' => ['code']]);
    
    // product
    $builder->connect('/product/quick-view/:id', ['controller' => 'Product', 'action' => 'quickView'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/product/compare', ['controller' => 'Product', 'action' => 'compare']);

    // cart
    $builder->connect('/cart/add-product', ['controller' => 'Cart', 'action' => 'addProduct']);
    $builder->connect('/cart/remove-product', ['controller' => 'Cart', 'action' => 'removeProduct']);
    $builder->connect('/cart/update', ['controller' => 'Cart', 'action' => 'updateCart']);
    $builder->connect('/cart/reload-sidebar-cart', ['controller' => 'Cart', 'action' => 'reloadSidebarCart']);

    // order
    $builder->connect('/order/cart-info', ['controller' => 'Order', 'action' => 'cartInfo']);
    $builder->connect('/order/info', ['controller' => 'Order', 'action' => 'orderInfo']);
    $builder->connect('/order/choose-address', ['controller' => 'Order', 'action' => 'chooseAddress']);
    $builder->connect('/order/create', ['controller' => 'Order', 'action' => 'create']);
    $builder->connect('/order/checkout', ['controller' => 'Order', 'action' => 'checkout']);
    $builder->connect('/order/checkout/process', ['controller' => 'Order', 'action' => 'processCheckout']);
    $builder->connect('/order/shipping-method/select', ['controller' => 'Shipping', 'action' => 'selectMethod']);

    $builder->connect('/order/success', ['controller' => 'Order', 'action' => 'success']);
    $builder->connect('/order/error', ['controller' => 'Order', 'action' => 'error']);

    
    //payment
    $builder->connect('/payment/return/:gateway', ['controller' => 'Payment', 'action' => 'returnPayment'], ['pass' => ['gateway']]);
    $builder->connect('/payment/return/:gateway/:code', ['controller' => 'Payment', 'action' => 'returnPayment'], ['pass' => ['gateway', 'code']]);
    $builder->connect('/order/payment-process/vnpt-pay', ['controller' => 'Payment', 'action' => 'vnptPayProcess']);

    //promotion
    $builder->connect('/promotion/check-coupon', ['controller' => 'PromotionCoupon', 'action' => 'check']);
    $builder->connect('/promotion/delete-coupon', ['controller' => 'PromotionCoupon', 'action' => 'delete']); 

    //affiliate
    $builder->connect('/affiliate/apply', ['controller' => 'Affiliate', 'action' => 'apply']);
    $builder->connect('/affiliate/delete', ['controller' => 'Affiliate', 'action' => 'delete']); 

    // member
    $builder->connect('/member/login', ['controller' => 'Member', 'action' => 'login']);
    $builder->connect('/member/ajax-login', ['controller' => 'Member', 'action' => 'ajaxLogin']);
    $builder->connect('/member/social-login', ['controller' => 'Member', 'action' => 'socialLogin']);
    $builder->connect('/member/register', ['controller' => 'Member', 'action' => 'register']);
    $builder->connect('/member/ajax-register', ['controller' => 'Member', 'action' => 'ajaxRegister']);
    $builder->connect('/member/dashboard', ['controller' => 'Member', 'action' => 'dashboard']);
    $builder->connect('/member/address', ['controller' => 'Member', 'action' => 'address']);
    $builder->connect('/member/address/save', ['controller' => 'Member', 'action' => 'saveAddress']);
    $builder->connect('/member/address/is-default', ['controller' => 'Member', 'action' => 'isDefault']);
    $builder->connect('/member/address/delete', ['controller' => 'Member', 'action' => 'deleteAddress']);
    $builder->connect('/member/profile', ['controller' => 'Member', 'action' => 'profile']);
    $builder->connect('/member/save-profile', ['controller' => 'Member', 'action' => 'saveProfile']);
    $builder->connect('/member/change-password', ['controller' => 'Member', 'action' => 'changePassword']);
    $builder->connect('/member/ajax-change-password', ['controller' => 'Member', 'action' => 'ajaxChangePassword']);
    $builder->connect('/member/logout', ['controller' => 'Member', 'action' => 'logout']);
    $builder->connect('/member/order', ['controller' => 'Member', 'action' => 'order']);
    $builder->connect('/member/order/detail/:code', ['controller' => 'Member', 'action' => 'orderDetail'], ['pass' => ['code']]);
    $builder->connect('/member/order/cancel', ['controller' => 'Member', 'action' => 'cancelOrder']);
    $builder->connect('/member/forgot-password', ['controller' => 'Member', 'action' => 'forgotPassword']);
    $builder->connect('/member/ajax-forgot-password', ['controller' => 'Member', 'action' => 'ajaxForgotPassword']);
    $builder->connect('/member/verify-forgot-password', ['controller' => 'Member', 'action' => 'verifyForgotPassword']);
    $builder->connect('/member/ajax-verify-forgot-password', ['controller' => 'Member', 'action' => 'ajaxVerifyForgotPassword']);
    $builder->connect('/member/verify-email', ['controller' => 'Member', 'action' => 'verifyEmail']);
    $builder->connect('/member/ajax-verify-email', ['controller' => 'Member', 'action' => 'ajaxVerifyEmail']);
    $builder->connect('/member/reload-mini-member', ['controller' => 'Member', 'action' => 'reloadMiniMember']);
    $builder->connect('/member/upload-avatar', ['controller' => 'Member', 'action' => 'uploadAvatar']);
    $builder->connect('/member/delete-avatar', ['controller' => 'Member', 'action' => 'deleteAvatar']);
    $builder->connect('/member/success', ['controller' => 'Member', 'action' => 'success']);
    $builder->connect('/member/resend-verify-code', ['controller' => 'Member', 'action' => 'resendVerifyCode']);
    $builder->connect('/member/promotion', ['controller' => 'Member', 'action' => 'promotion']);
    $builder->connect('/member/change-phone', ['controller' => 'Member', 'action' => 'changePhone']);
    $builder->connect('/member/ajax-change-phone', ['controller' => 'Member', 'action' => 'ajaxChangePhone']);
    $builder->connect('/member/change-email', ['controller' => 'Member', 'action' => 'changeEmail']);
    $builder->connect('/member/ajax-change-email', ['controller' => 'Member', 'action' => 'ajaxChangeEmail']);
    $builder->connect('/member/get-verify-code', ['controller' => 'Member', 'action' => 'getVerifyCode']);

    $builder->connect('/member/attendance', ['controller' => 'Member', 'action' => 'attendance']);
    $builder->connect('/member/attendance-tick', ['controller' => 'Member', 'action' => 'attendanceTick']);

    $builder->connect('/member/bank', ['controller' => 'Member', 'action' => 'listBank']);
    $builder->connect('/member/bank/delete', ['controller' => 'Member', 'action' => 'deleteBank']);
    $builder->connect('/member/bank/save', ['controller' => 'Member', 'action' => 'saveBank']);

    $builder->connect('/member/oauth/google', ['controller' => 'Member', 'action' => 'oauthGoogle']);
    $builder->connect('/member/oauth/facebook', ['controller' => 'Member', 'action' => 'oauthFacebook']);

    $builder->connect('/member/affiliate/dashboard', ['controller' => 'MemberAffiliate', 'action' => 'affiliateDashboard']);
    $builder->connect('/member/affiliate/load-chart-profit', ['controller' => 'MemberAffiliate', 'action' => 'loadChartProfit']);
    $builder->connect('/member/affiliate/load-statistic-month', ['controller' => 'MemberAffiliate', 'action' => 'loadStatisticMonth']);
    $builder->connect('/member/affiliate/policy', ['controller' => 'MemberAffiliate', 'action' => 'affiliatePolicy']);
    $builder->connect('/member/affiliate/active', ['controller' => 'MemberAffiliate', 'action' => 'affiliateActive']);
    $builder->connect('/member/affiliate/process-active', ['controller' => 'MemberAffiliate', 'action' => 'processAffiliateActive']);
    $builder->connect('/member/affiliate/order', ['controller' => 'MemberAffiliate', 'action' => 'affiliateOrder']);
    $builder->connect('/member/affiliate/order-info/:code', ['controller' => 'MemberAffiliate', 'action' => 'affiliateOrderInfomation'], ['pass' => ['code']]);
    $builder->connect('/member/affiliate/list-point-tomoney', ['controller' => 'MemberAffiliate', 'action' => 'listPointToMoney']);
    $builder->connect('/member/affiliate/create-request-point-to-money', ['controller' => 'MemberAffiliate', 'action' => 'createRequestPointToMoney']);
    
    $builder->connect('/member/wallet', ['controller' => 'MemberWallet', 'action' => 'wallet']);
    $builder->connect('/member/ajax-history-point', ['controller' => 'MemberWallet', 'action' => 'ajaxHistoryPoint']);
    $builder->connect('/member/wallet/give-point', ['controller' => 'MemberWallet', 'action' => 'givePoint']);
    $builder->connect('/member/wallet/ajax-give-point', ['controller' => 'MemberWallet', 'action' => 'ajaxGivePoint']);

    $builder->connect('/member/wallet/buy-point', ['controller' => 'MemberWallet', 'action' => 'buyPoint']);
    $builder->connect('/member/wallet/ajax-buy-point', ['controller' => 'MemberWallet', 'action' => 'ajaxBuyPoint']);
    $builder->connect('/member/wallet/buy-point-success', ['controller' => 'MemberWallet', 'action' => 'buyPointSuccess']);

    // customer point
    $builder->connect('/customer/point/apply-order', ['controller' => 'CustomerPoint', 'action' => 'applyPointToOrder']);
    $builder->connect('/customer/point/clear-in-order', ['controller' => 'CustomerPoint', 'action' => 'clearPointInOrder']);
    
    // location
    $builder->connect('/location/city/json/:id', ['controller' => 'Location', 'action' => 'listJsonCity'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/location/district/json/:id', ['controller' => 'Location', 'action' => 'listJsonDistrict'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/location/ward/json/:id', ['controller' => 'Location', 'action' => 'listJsonWard'], ['pass' => ['id'], 'id' => '[0-9]+']);

    // comment 
    $builder->connect('/comment/add', ['controller' => 'Comment', 'action' => 'add']);
    $builder->connect('/comment/load', ['controller' => 'Comment', 'action' => 'load']);
    $builder->connect('/comment/like', ['controller' => 'Comment', 'action' => 'like']);
    $builder->connect('/comment/upload-image', ['controller' => 'Comment', 'action' => 'uploadImage']);

    $builder->connect('/comment/short-rating', ['controller' => 'Comment', 'action' => 'shortRating']);

    //wishlist
    $builder->connect('/wishlist/add-product', ['controller' => 'Wishlist', 'action' => 'addProduct']);
    $builder->connect('/wishlist/remove-product', ['controller' => 'Wishlist', 'action' => 'removeProduct']);

    //counter
    $builder->connect('/counter/close-brower', ['controller' => 'Counter', 'action' => 'checkCloseBrower']);

    // notification
    $builder->connect('/notification/subscribe', ['controller' => 'Notification', 'action' => 'subscribe']);
    $builder->connect('/notification/unsubscribe', ['controller' => 'Notification', 'action' => 'unsubscribe']);
    $builder->connect('/notification/list', ['controller' => 'Notification', 'action' => 'listNotification']);

    // contact
    $builder->connect('/contact/send-info', ['controller' => 'Contact', 'action' => 'sendInfo']);

    // embed
    $builder->connect('/embed/load-content', ['controller' => 'System', 'action' => 'loadEmbed']);
    $builder->connect('/embed/load-embed-attribute', ['controller' => 'System', 'action' => 'loadEmbedAttribute']);

    // load sdk social
    $builder->connect('/social/load-sdk/:type', ['controller' => 'System', 'action' => 'loadSdkSocial'], ['pass' => ['type']]);

    // search auto suggest
    $builder->connect('/search/suggest', ['controller' => 'Search', 'action' => 'suggest']);

    // website
    $builder->connect('/website/load-setting-block', ['controller' => 'Website', 'action' => 'loadSettingBlock']);

    // gia phả
    $builder->connect('/giapha/:id', ['controller' => 'Genealogy', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);

    // page
    $builder->connect('/*', ['controller' => 'Page', 'action' => 'index']);

    $builder->fallbacks();
});

// router job
$routes->scope('/job', function (RouteBuilder $builder) {
    $builder->connect('/send-email', ['controller' => 'Job', 'action' => 'sendEmail']);
});

// router api
Router::prefix('api', function ($routes) {
    $routes->connect('/generate-bearer-token', ['controller' => 'App', 'action' => 'generateBearerToken']);
    $routes->connect('/get-data-from-token', ['controller' => 'App', 'action' => 'getDataFromToken']);

    $routes->connect('/product', ['controller' => 'Product', 'action' => 'list']);
    $routes->connect('/product/list', ['controller' => 'Product', 'action' => 'list']);
    $routes->connect('/product/detail', ['controller' => 'Product', 'action' => 'detail']);

    $routes->connect('/category/list', ['controller' => 'Category', 'action' => 'list']);
    $routes->connect('/category/detail', ['controller' => 'Category', 'action' => 'detail']);

    $routes->connect('/article/list', ['controller' => 'Article', 'action' => 'list']);
    $routes->connect('/article/detail', ['controller' => 'Article', 'action' => 'detail']);

    $routes->connect('/member/login', ['controller' => 'Member', 'action' => 'login']);
    $routes->connect('/member/social-login', ['controller' => 'Member', 'action' => 'socialLogin']);
    $routes->connect('/member/logout', ['controller' => 'Member', 'action' => 'logout']);
    $routes->connect('/member/social-login', ['controller' => 'Member', 'action' => 'socialLogin']);
    $routes->connect('/member/register', ['controller' => 'Member', 'action' => 'register']);
    $routes->connect('/member/update-profile', ['controller' => 'Member', 'action' => 'updateProfile']);
    $routes->connect('/member/address/list', ['controller' => 'Member', 'action' => 'listAddress']);
    $routes->connect('/member/address/save', ['controller' => 'Member', 'action' => 'saveAddress']);
    $routes->connect('/member/address/set-default', ['controller' => 'Member', 'action' => 'setDefaultAddress']);
    $routes->connect('/member/address/delete', ['controller' => 'Member', 'action' => 'deleteAddress']);
    $routes->connect('/member/infomation', ['controller' => 'Member', 'action' => 'infomation']);
    $routes->connect('/member/change-password', ['controller' => 'Member', 'action' => 'changePassword']);
    $routes->connect('/member/forgot-password', ['controller' => 'Member', 'action' => 'forgotPassword']);
    $routes->connect('/member/verify-forgot-password', ['controller' => 'Member', 'action' => 'verifyForgotPassword']);
    $routes->connect('/member/resend-verify-code', ['controller' => 'Member', 'action' => 'resendVerifyCode']);
    $routes->connect('/member/verify-account', ['controller' => 'Member', 'action' => 'verifyAccount']);
    $routes->connect('/member/update-avatar', ['controller' => 'Member', 'action' => 'updateAvatar']);
    $routes->connect('/member/list-orders', ['controller' => 'Member', 'action' => 'listOrders']);
    $routes->connect('/member/order-infomation', ['controller' => 'Member', 'action' => 'orderInfomation']);
    $routes->connect('/member/order-cancel', ['controller' => 'Member', 'action' => 'cancelOrder']);
    $routes->connect('/member/register-by-number-phone', ['controller' => 'Member', 'action' => 'registerByNumberPhone']);
    $routes->connect('/member/get-verify-code', ['controller' => 'Member', 'action' => 'getVerifyCode']);
    $routes->connect('/member/change-important-info', ['controller' => 'Member', 'action' => 'changeImportantInfo']);
    $routes->connect('/member/customer-login', ['controller' => 'Member', 'action' => 'customerLogin']);
    $routes->connect('/member/delete-account', ['controller' => 'Member', 'action' => 'deleteAccount']);

    $routes->connect('/member/affiliate/register', ['controller' => 'Affiliate', 'action' => 'registerAffiliate']);

    $routes->connect('/member/affiliate/statistical', ['controller' => 'Affiliate', 'action' => 'allStatistical']);
    $routes->connect('/member/affiliate/all-statistical', ['controller' => 'Affiliate', 'action' => 'allStatistical']);
    $routes->connect('/member/affiliate/order-statistical', ['controller' => 'Affiliate', 'action' => 'orderStatistical']);
    $routes->connect('/member/affiliate/month-statistical', ['controller' => 'Affiliate', 'action' => 'monthStatistical']);

    $routes->connect('/member/affiliate/chart-profit', ['controller' => 'Affiliate', 'action' => 'chartProfit']);
    $routes->connect('/member/affiliate/order', ['controller' => 'Affiliate', 'action' => 'affiliateOrder']);
    $routes->connect('/member/affiliate/order-infomation', ['controller' => 'Affiliate', 'action' => 'affiliateOrderInfomation']);
    $routes->connect('/member/affiliate/list-point-tomoney', ['controller' => 'Affiliate', 'action' => 'listPointToMoney']);
    $routes->connect('/member/affiliate/liquidity-information', ['controller' => 'Affiliate', 'action' => 'liquidityInformation']);
    $routes->connect('/member/affiliate/create-request-point-to-money', ['controller' => 'Affiliate', 'action' => 'createRequestPointToMoney']);
    $routes->connect('/member/affiliate/list-bank-partner', ['controller' => 'Affiliate', 'action' => 'listBankOfPartner']);
    $routes->connect('/member/affiliate/save-bank', ['controller' => 'Affiliate', 'action' => 'saveBank']);
    $routes->connect('/member/affiliate/delete-bank', ['controller' => 'Affiliate', 'action' => 'deleteBank']);
    $routes->connect('/member/affiliate/list-bank', ['controller' => 'Affiliate', 'action' => 'listBank']);
    $routes->connect('/member/affiliate/list-survey', ['controller' => 'Affiliate', 'action' => 'listSurvey']);
    $routes->connect('/member/affiliate/list-rank', ['controller' => 'Affiliate', 'action' => 'listRank']);
    

    $routes->connect('/member/affiliate/apply', ['controller' => 'Affiliate', 'action' => 'applyAffiliate']);
    $routes->connect('/member/affiliate/delete', ['controller' => 'Affiliate', 'action' => 'deleteAffiliate']); 

    $routes->connect('/otp/number-phone', ['controller' => 'Otp', 'action' => 'otpNumberPhone']);
    $routes->connect('/otp/verify-number-phone', ['controller' => 'Otp', 'action' => 'verifyNumberPhone']);
    
    $routes->connect('/customer/point/attendance', ['controller' => 'CustomerPoint', 'action' => 'attendance']);
    $routes->connect('/customer/point/attendance-tick', ['controller' => 'CustomerPoint', 'action' => 'attendanceTick']);

    $routes->connect('/customer/point/apply-order', ['controller' => 'CustomerPoint', 'action' => 'applyPointToOrder']);
    $routes->connect('/customer/point/clear-in-order', ['controller' => 'CustomerPoint', 'action' => 'clearPointInOrder']);
    $routes->connect('/customer/point/infomation', ['controller' => 'CustomerPoint', 'action' => 'infoCustomerPoint']);
    $routes->connect('/customer/point/history-using', ['controller' => 'CustomerPoint', 'action' => 'historyUsingPoint']);

    $routes->connect('/customer/point/give', ['controller' => 'CustomerPoint', 'action' => 'givePoint']);
    $routes->connect('/customer/point/buy', ['controller' => 'CustomerPoint', 'action' => 'buyPoint']);

    $routes->connect('/cart/add-product', ['controller' => 'Cart', 'action' => 'addProduct']);
    $routes->connect('/cart/infomation', ['controller' => 'Cart', 'action' => 'infomation']);
    $routes->connect('/cart/update-quantity-product', ['controller' => 'Cart', 'action' => 'updateQuantityProduct']);
    $routes->connect('/cart/remove-product', ['controller' => 'Cart', 'action' => 'removeProduct']);


    $routes->connect('/order/confirm-infomation', ['controller' => 'Order', 'action' => 'confirmInfomation']);
    $routes->connect('/order/customer-infomation', ['controller' => 'Order', 'action' => 'customerInfomation']);
    $routes->connect('/order/create', ['controller' => 'Order', 'action' => 'create']);
    $routes->connect('/order/checkout', ['controller' => 'Order', 'action' => 'checkout']);
    $routes->connect('/order/choose-address', ['controller' => 'Order', 'action' => 'chooseAddress']);

    //shippping
    $routes->connect('/shipping/list', ['controller' => 'Shipping', 'action' => 'getListShippingMethod']);
    $routes->connect('/shipping/select', ['controller' => 'Shipping', 'action' => 'selectMethod']);

    $routes->connect('/brand/list', ['controller' => 'Brand', 'action' => 'list']);
    $routes->connect('/brand/detail', ['controller' => 'Brand', 'action' => 'detail']);

    $routes->connect('/city', ['controller' => 'Location', 'action' => 'cityList']);
    $routes->connect('/district', ['controller' => 'Location', 'action' => 'districtList']);
    $routes->connect('/ward', ['controller' => 'Location', 'action' => 'wardList']);

    $routes->connect('/wishlist/add-product', ['controller' => 'Wishlist', 'action' => 'addProduct']);
    $routes->connect('/wishlist/remove-product', ['controller' => 'Wishlist', 'action' => 'removeProduct']);

    $routes->connect('/mobile-template/setting', ['controller' => 'MobileTemplate', 'action' => 'settingInfo']);
    $routes->connect('/mobile-template/page', ['controller' => 'MobileTemplate', 'action' => 'pageInfo']);
    $routes->connect('/mobile-template/advanced-search', ['controller' => 'MobileTemplate', 'action' => 'advancedSearch']);

    $routes->connect('/comment/add-comment', ['controller' => 'Comment', 'action' => 'addComment']);
    $routes->connect('/comment/add-rating', ['controller' => 'Comment', 'action' => 'addRating']);
    $routes->connect('/comment/get-reply', ['controller' => 'Comment', 'action' => 'getReply']);
    $routes->connect('/comment/like', ['controller' => 'Comment', 'action' => 'like']);
    $routes->connect('/comment/upload-image', ['controller' => 'Comment', 'action' => 'uploadImage']);

    $routes->connect('/promotion/check-coupon', ['controller' => 'Promotion', 'action' => 'check']);
    $routes->connect('/promotion/delete-coupon', ['controller' => 'Promotion', 'action' => 'delete']);
    $routes->connect('/promotion/list-coupon', ['controller' => 'Promotion', 'action' => 'listCoupon']);

    $routes->connect('/payment/return/:code', ['controller' => 'Payment', 'action' => 'returnPayment'], ['pass' => ['code']]);
    
    $routes->connect('/notification/subscribe', ['controller' => 'Notification', 'action' => 'subscribe']);
    $routes->connect('/notification/unsubscribe', ['controller' => 'Notification', 'action' => 'unsubscribe']);
    $routes->connect('/notification/list', ['controller' => 'Notification', 'action' => 'listNotification']);


    // các api được gọi từ CRM
    $routes->connect('/website/migrate', ['controller' => 'Website', 'action' => 'migrate']);
    $routes->connect('/website/update-size', ['controller' => 'Website', 'action' => 'updateSize']);
    $routes->connect('/website/update-duration', ['controller' => 'Website', 'action' => 'updateDuration']);
    $routes->connect('/website/update-search-unicode/:type', ['controller' => 'Website', 'action' => 'reUpdateSearchUnicode'], ['pass' => ['type']]);
    $routes->connect('/website/new-notification', ['controller' => 'Website', 'action' => 'newNotification']);
    
    // $routes->connect('/website/initialization-template', ['controller' => 'Website', 'action' => 'initializationTemplate']);
    // $routes->connect('/website/import-structure-database', ['controller' => 'Website', 'action' => 'importStructureDatabase']);
    // $routes->connect('/website/import-initialization-database', ['controller' => 'Website', 'action' => 'importInitializationDatabase']);
    // $routes->connect('/website/initialization-config', ['controller' => 'Website', 'action' => 'initializationConfig']);
    // $routes->connect('/website/transfer-core', ['controller' => 'Website', 'action' => 'transferCore']);
    // $routes->connect('/website/install-core', ['controller' => 'Website', 'action' => 'installCore']);
    // $routes->connect('/website/import-database', ['controller' => 'Website', 'action' => 'importDatabase']);
    // $routes->connect('/website/update-file-config-database', ['controller' => 'Website', 'action' => 'updateFileConfigDatabase']);

    $routes->fallbacks(DashedRoute::class);
});


// router admin
if(!defined('ADMIN_PATH')){
    define('ADMIN_PATH', '/admin');    
}

$routes->scope(ADMIN_PATH, function (RouteBuilder $builder) {
    $builder->registerMiddleware('csrf', new CsrfProtectionMiddleware([
        'httponly' => true,
    ]));    
    $builder->applyMiddleware('csrf');

    $builder->connect('/', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'login']); 
    $builder->connect('/login', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'login']);
    $builder->connect('/ajax-login', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'ajaxLogin']);    
    $builder->connect('/logout', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'logout']);
    $builder->connect('/404', ['plugin' => 'Admin', 'controller' => 'Error', 'action' => 'notFound']);
    $builder->connect('/denied', ['plugin' => 'Admin', 'controller' => 'Error', 'action' => 'denied']);
    $builder->connect('/error', ['plugin' => 'Admin', 'controller' => 'Error', 'action' => 'error']);

    // dashboard
    $builder->connect('/main', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'dashboard']);
    $builder->connect('/dashboard', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'dashboard']);
    $builder->connect('/dashboard/statistics/order', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'statisticsOrder']);
    $builder->connect('/dashboard/chart/order', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'chartOrder']);
    $builder->connect('/dashboard/statistics/product', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'statisticsProduct']);
    $builder->connect('/dashboard/statistics/article', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'statisticsArticle']);
    $builder->connect('/dashboard/info-website', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'infoWebsite']);
    $builder->connect('/dashboard/seo-website', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'seoWebsite']);
    $builder->connect('/dashboard/setting-website', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'settingWebsite']);
    $builder->connect('/dashboard/expiry-website', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'expiryWebsite']);
    $builder->connect('/dashboard/duration-website', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'durationWebsite']);
    $builder->connect('/dashboard/statistics/customer', ['plugin' => 'Admin', 'controller' => 'Dashboard', 'action' => 'statisticsCustomer']);

    // users
    $builder->connect('/user', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'list']);
    $builder->connect('/user/list', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'list']);
    $builder->connect('/user/list/json', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'listJson']);
    $builder->connect('/user/add', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'add']);
    $builder->connect('/user/update/:id', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/user/change-password/:id', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'changePassword'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/user/detail/:id', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/user/save', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'save']);
    $builder->connect('/user/save/:id', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/user/delete', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'delete']);
    $builder->connect('/user/change-status', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'changeStatus']);
    $builder->connect('/user/auto-suggest', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'autoSuggest']);
    $builder->connect('/user/profile', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'profile']);
    $builder->connect('/user/profile-save', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'profileSave']);
    $builder->connect('/user/profile-change-password', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'profileChangePassword']);
    $builder->connect('/user/profile-change-pass-Save', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'changePasswordProfile']);
    $builder->connect('/user/language-admin', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'languageAdmin']);
    $builder->connect('/user/language-admin-save', ['plugin' => 'Admin', 'controller' => 'User', 'action' => 'saveLanguageAdmin']);

    //roles
    $builder->connect('/role', ['plugin' => 'Admin', 'controller' => 'Role', 'action' => 'list']);
    $builder->connect('/role/list', ['plugin' => 'Admin', 'controller' => 'Role', 'action' => 'list']);
    $builder->connect('/role/list/json', ['plugin' => 'Admin', 'controller' => 'Role', 'action' => 'listJson']);
    $builder->connect('/role/add', ['plugin' => 'Admin', 'controller' => 'Role', 'action' => 'add']);
    $builder->connect('/role/update/:id', ['plugin' => 'Admin', 'controller' => 'Role', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/role/detail/:id', ['plugin' => 'Admin', 'controller' => 'Role', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/role/save', ['plugin' => 'Admin', 'controller' => 'Role', 'action' => 'save']);
    $builder->connect('/role/save/:id', ['plugin' => 'Admin', 'controller' => 'Role', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/role/delete', ['plugin' => 'Admin', 'controller' => 'Role', 'action' => 'delete']);

    $builder->connect('/role/permission', ['plugin' => 'Admin', 'controller' => 'Role', 'action' => 'permissionSetup']);
    $builder->connect('/role/permission/save', ['plugin' => 'Admin', 'controller' => 'Role', 'action' => 'permissionSave']);    

    // articles
    $builder->connect('/article', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'list']);
    $builder->connect('/article/list', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'list']);
    $builder->connect('/article/list/json', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'listJson']);
    $builder->connect('/article/add', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'add']);
    $builder->connect('/article/update/:id', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/article/detail/:id', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);

    $builder->connect('/article/save', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'save']);
    $builder->connect('/article/save/:id', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/article/delete', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'delete']);
    $builder->connect('/article/change-status', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'changeStatus']);
    $builder->connect('/article/change-position', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'changePosition']);
    $builder->connect('/article/duplicate', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'duplicate']);
    $builder->connect('/article/auto-suggest', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'autoSuggest']);
    $builder->connect('/article/quick-upload', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'quickUpload']);
    $builder->connect('/article/upload-modal/:id', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'uploadModal'], ['pass' => ['id'], 'id' => '[0-9]+']);

    $builder->connect('/article/load-attribute-by-category', ['plugin' => 'Admin', 'controller' => 'Article', 'action' => 'loadAttributeByCategory']);



    // phả đồ
    $builder->connect('/genealogy', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'list']);
    $builder->connect('/genealogy/list', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'list']);
    $builder->connect('/genealogy/list/json', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'listJson']);
    $builder->connect('/genealogy/add', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'add']);
    $builder->connect('/genealogy/update/:id', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/genealogy/detail/:id', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);

    $builder->connect('/genealogy/save', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'save']);
    $builder->connect('/genealogy/save/:id', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/genealogy/delete', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'delete']);
    $builder->connect('/genealogy/change-status', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'changeStatus']);

    $builder->connect('/genealogy/load-list-genealogy', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'loadListGenealogy']);
    $builder->connect('/genealogy/load-relationship', ['plugin' => 'Admin', 'controller' => 'Genealogy', 'action' => 'loadRelationship']);

    

    // tags
    $builder->connect('/tag', ['plugin' => 'Admin', 'controller' => 'Tag', 'action' => 'list']);
    $builder->connect('/tag/list', ['plugin' => 'Admin', 'controller' => 'Tag', 'action' => 'list']);
    $builder->connect('/tag/list/json', ['plugin' => 'Admin', 'controller' => 'Tag', 'action' => 'listJson']);

    $builder->connect('/tag/add', ['plugin' => 'Admin', 'controller' => 'Tag', 'action' => 'add']);
    $builder->connect('/tag/update/:id', ['plugin' => 'Admin', 'controller' => 'Tag', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);

    $builder->connect('/tag/save', ['plugin' => 'Admin', 'controller' => 'Tag', 'action' => 'save']);
    $builder->connect('/tag/save/:id', ['plugin' => 'Admin', 'controller' => 'Tag', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/tag/delete', ['plugin' => 'Admin', 'controller' => 'Tag', 'action' => 'delete']);
    $builder->connect('/tag/auto-suggest', ['plugin' => 'Admin', 'controller' => 'Tag', 'action' => 'autoSuggest']);

    // seo 
    $builder->connect('/site-map', ['plugin' => 'Admin', 'controller' => 'SeoSiteMap', 'action' => 'index']);
    $builder->connect('/page-seo-info', ['plugin' => 'Admin', 'controller' => 'Seo', 'action' => 'pageSeoInfo']);
    $builder->connect('/page-seo-info/save', ['plugin' => 'Admin', 'controller' => 'Seo', 'action' => 'savePageSeoInfo']);
    
    $builder->connect('/seo-setting', ['plugin' => 'Admin', 'controller' => 'Seo', 'action' => 'setting']);    
    $builder->connect('/seo-setting/upload-file-robots', ['plugin' => 'Admin', 'controller' => 'Seo', 'action' => 'uploadFileRobots'], ['pass' => ['type']]);

    //redirect
    $builder->connect('/redirect/setting', ['plugin' => 'Admin', 'controller' => 'Redirect', 'action' => 'setting']);

    //redirect 301
    $builder->connect('/redirect-301', ['plugin' => 'Admin', 'controller' => 'SeoRedirect', 'action' => 'list']);    
    $builder->connect('/redirect-301/json', ['plugin' => 'Admin', 'controller' => 'SeoRedirect', 'action' => 'listJson']);
    $builder->connect('/redirect-301/add', ['plugin' => 'Admin', 'controller' => 'SeoRedirect', 'action' => 'add']);
    $builder->connect('/redirect-301/update/:id', ['plugin' => 'Admin', 'controller' => 'SeoRedirect', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);

    $builder->connect('/redirect-301/save', ['plugin' => 'Admin', 'controller' => 'SeoRedirect', 'action' => 'save']);
    $builder->connect('/redirect-301/save/:id', ['plugin' => 'Admin', 'controller' => 'SeoRedirect', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/redirect-301/delete', ['plugin' => 'Admin', 'controller' => 'SeoRedirect', 'action' => 'delete']);
    $builder->connect('/redirect-301/change-status', ['plugin' => 'Admin', 'controller' => 'SeoRedirect', 'action' => 'changeStatus']);

    // categories
    $builder->connect('/category/product', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'listCategoryProduct']);
    $builder->connect('/category/product/list', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'listCategoryProduct']);
    $builder->connect('/category/product/list/json', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'listJsonCategoryProduct']);
    $builder->connect('/category/product/auto-suggest', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'autoSuggest']);

    $builder->connect('/category/article', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'listCategoryArticle']);
    $builder->connect('/category/article/list', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'listCategoryArticle']);
    $builder->connect('/category/article/list/json', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'listJsonCategoryArticle']);


    $builder->connect('/category/:type/add', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'add'], ['pass' => ['type']]);
    $builder->connect('/category/:type/update/:id', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'update'], ['pass' => ['type', 'id'], 'id' => '[0-9]+']);
    $builder->connect('/category/:type/detail/:id', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'detail'], ['pass' => ['type', 'id'], 'id' => '[0-9]+']);
    $builder->connect('/category/:type/save', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'save'], ['pass' => ['type']]);
    $builder->connect('/category/:type/save/:id', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'save'], ['pass' => ['type', 'id'], 'id' => '[0-9]+']);
    $builder->connect('/category/:type/delete', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'delete'], ['pass' => ['type']]);
    $builder->connect('/category/change-status', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'changeStatus']);
    $builder->connect('/category/:type/duplicate', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'duplicate'], ['pass' => ['type']]);
    $builder->connect('/category/change-position', ['plugin' => 'Admin', 'controller' => 'Category', 'action' => 'changePosition']);
    

    // products
    $builder->connect('/product', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'list']);
    $builder->connect('/product/list', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'list']);
    $builder->connect('/product/list/json', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'listJson']);
    $builder->connect('/product/add', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'add']);
    $builder->connect('/product/update/:id', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/product/detail/:id', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);

    $builder->connect('/product/save', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'save']);
    $builder->connect('/product/save/:id', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/product/delete', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'delete']);
    $builder->connect('/product/change-status', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'changeStatus']);
    $builder->connect('/product/change-position', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'changePosition']);
    $builder->connect('/product/duplicate', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'duplicate']);
    $builder->connect('/product/auto-suggest', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'autoSuggest']);
    $builder->connect('/product/auto-suggest-normal', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'autoSuggestNormalProduct']);
    $builder->connect('/product/quick-save', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'quickSave']);
    $builder->connect('/product/quick-change', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'quickChange']);
    $builder->connect('/product/quick-upload', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'quickUpload']);
    $builder->connect('/product/upload-modal/:id', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'uploadModal'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/product/view-list-item/:id', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'viewListItems'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/product/duplicate', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'duplicate']);
    $builder->connect('/product/discount-product', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'discountProduct']);
    
    $builder->connect('/product/load-attribute-by-category', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'loadAttributeByCategory']);
    $builder->connect('/product/load-attribute-item-by-category', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'loadAttributeItemByCategory']);
    $builder->connect('/product/load-special-attribute-item-by-category', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'loadSpecialAttributeItemByCategory']);
    $builder->connect('/product/load-brand-by-category', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'loadBrandByCategory']);

    // products item
    $builder->connect('/product/item/select-attribute-special', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'ajaxSeletAttributeSpecial']);

    // export | import product
    $builder->connect('/product/download-file-import', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'downloadFileImportProduct']);
    $builder->connect('/product/import-excel', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'importDataByExcel']);
    $builder->connect('/product/process-import-excel', ['plugin' => 'Admin', 'controller' => 'Product', 'action' => 'processImportExcel']);

    $builder->connect('/product/kiotviet-code', ['plugin' => 'Admin', 'controller' => 'StoreKiotviet', 'action' => 'syncProductCode']);
    $builder->connect('/product/kiotviet-sync-all-product', ['plugin' => 'Admin', 'controller' => 'StoreKiotviet', 'action' => 'syncAllProduct']);

    // links
    $builder->connect('/link/check-exist', ['plugin' => 'Admin', 'controller' => 'Link', 'action' => 'checkExist']);

    // orders
    $builder->connect('/order', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'list']);
    $builder->connect('/order/list', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'list']);
    $builder->connect('/order/list/json', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'listJson']);
    $builder->connect('/order/add', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'add']);
    $builder->connect('/order/update/:id', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/order/detail/:id', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);

    $builder->connect('/order/save', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'save']);
    $builder->connect('/order/save/:id', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/order/change-status/:id', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'changeStatus'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/order/change-note', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'changeNote']);
    $builder->connect('/order/payment-confirm/:id', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'paymentConfirm'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/order/addresses/list/:id', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'listAdresses'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/order/shipping-confirm/:id', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'shippingConfirm'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/order/update-contact/:id', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'updateContact'], ['pass' => ['id'], 'id' => '[0-9]+']);

    $builder->connect('/order/shipping-change-status/:id', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'shippingChangeStatus'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/order/cancel/:id', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'cancel'], ['pass' => ['id'], 'id' => '[0-9]+']);

    $builder->connect('/order/load-carries-for-order', ['plugin' => 'Admin', 'controller' => 'Order', 'action' => 'loadCarriesForOrder']);

    // customers
    $builder->connect('/customer', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'list']);
    $builder->connect('/customer/list', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'list']);
    $builder->connect('/customer/list/json', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'listJson']);
    $builder->connect('/customer/add', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'add']);
    $builder->connect('/customer/update/:id', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/customer/detail/:id', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/customer/save-address/:id', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'saveAddress'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/customer/save-address', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'saveAddress']);
    $builder->connect('/customer/save-note/:id', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'saveNote'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/customer/delete', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'delete']);
    $builder->connect('/customer/change-status', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'changeStatus']);
    $builder->connect('/customer/auto-suggest', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'autoSuggest']);
    $builder->connect('/customer/get-customer', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'getCustomer']);
    $builder->connect('/customer/get-address', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'getAddress']);
    $builder->connect('/customer/save', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'save']);
    $builder->connect('/customer/save/:id', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/customer/set-default', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'setDefault']);
    $builder->connect('/customer/delete-address', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'deleteAddress']);
    $builder->connect('/customer/delete-note', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'deleteNote']);
    $builder->connect('/customer/check-exist/:type', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'checkExist'], ['pass' => ['type']]);
    $builder->connect('/customer/change-password/:id', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'changePassword'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/customer/add-account/:id', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'addAccount'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/customer/account-status/:id', ['plugin' => 'Admin', 'controller' => 'Customer', 'action' => 'accountStatus'], ['pass' => ['id'], 'id' => '[0-9]+']);

    // customers point
    $builder->connect('/customer/point', ['plugin' => 'Admin', 'controller' => 'CustomerPoint', 'action' => 'list']);
    $builder->connect('/customer/point/list/json', ['plugin' => 'Admin', 'controller' => 'CustomerPoint', 'action' => 'listJson']);
    $builder->connect('/customer/point/detail/:id', ['plugin' => 'Admin', 'controller' => 'CustomerPoint', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/customer/point/detail/history-json/:id', ['plugin' => 'Admin', 'controller' => 'CustomerPoint', 'action' => 'detailListPointHistory'], ['pass' => ['id'], 'id' => '[0-9]+']);
    
    $builder->connect('/customer/point-history', ['plugin' => 'Admin', 'controller' => 'CustomerPointHistory', 'action' => 'list']);
    $builder->connect('/customer/point-history/json', ['plugin' => 'Admin', 'controller' => 'CustomerPointHistory', 'action' => 'listJson']);
    $builder->connect('/customer/point-history/add', ['plugin' => 'Admin', 'controller' => 'CustomerPointHistory', 'action' => 'add']);
    $builder->connect('/customer/point-history/save', ['plugin' => 'Admin', 'controller' => 'CustomerPointHistory', 'action' => 'save']);   

    // affiliate
    $builder->connect('/customer/affiliate/statistical', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliate', 'action' => 'statistical']);
    $builder->connect('/customer/affiliate/statistics/order', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliate', 'action' => 'statisticsOrder']);
    $builder->connect('/customer/affiliate/chart/order', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliate', 'action' => 'chartOrder']);
    $builder->connect('/customer/affiliate/statistics/top-partner', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliate', 'action' => 'topPartner']);
    $builder->connect('/customer/affiliate/statistics/new-partner', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliate', 'action' => 'newPartner']);

    $builder->connect('/customer/affiliate', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliate', 'action' => 'list']);
    $builder->connect('/customer/affiliate/list', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliate', 'action' => 'list']);
    $builder->connect('/customer/affiliate/list/json', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliate', 'action' => 'listJson']);
    $builder->connect('/customer/affiliate/detail/:id', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliate', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/customer/affiliate/list-order/json/:id', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliate', 'action' => 'listOrderJson'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/customer/affiliate/load-statistic-dashboard', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliate', 'action' => 'loadStatisticDashboard']);

    $builder->connect('/customer/point-tomoney', ['plugin' => 'Admin', 'controller' => 'CustomersPointTomoney', 'action' => 'list']);
    $builder->connect('/customer/point-tomoney/list', ['plugin' => 'Admin', 'controller' => 'CustomersPointTomoney', 'action' => 'list']);
    $builder->connect('/customer/point-tomoney/list/json', ['plugin' => 'Admin', 'controller' => 'CustomersPointTomoney', 'action' => 'listJson']);
    $builder->connect('/customer/point-tomoney/save', ['plugin' => 'Admin', 'controller' => 'CustomersPointTomoney', 'action' => 'save']);
    $builder->connect('/customer/point-tomoney/change-status', ['plugin' => 'Admin', 'controller' => 'CustomersPointTomoney', 'action' => 'changeStatus']);
    $builder->connect('/customer/point-tomoney/delete', ['plugin' => 'Admin', 'controller' => 'CustomersPointTomoney', 'action' => 'delete']);
    $builder->connect('/customer/point-tomoney/change-note', ['plugin' => 'Admin', 'controller' => 'CustomersPointTomoney', 'action' => 'changeNote']);
    $builder->connect('/customer/affiliate/point-tomoney/save', ['plugin' => 'Admin', 'controller' => 'CustomersPointTomoney', 'action' => 'save']);
    $builder->connect('/customer/affiliate/point-tomoney/list-bank/json/:id', ['plugin' => 'Admin', 'controller' => 'CustomersPointTomoney', 'action' => 'listBankJson'], ['pass' => ['id'], 'id' => '[0-9]+']);

    $builder->connect('/customer/affiliate/request', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliateRequest', 'action' => 'list']);
    $builder->connect('/customer/affiliate/request/list', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliateRequest', 'action' => 'list']);
    $builder->connect('/customer/affiliate/request/list/json', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliateRequest', 'action' => 'listJson']);
    $builder->connect('/customer/affiliate/request/delete', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliateRequest', 'action' => 'delete']);
    $builder->connect('/customer/affiliate/request/change-status', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliateRequest', 'action' => 'changeStatus']);

    $builder->connect('/customer/affiliate/order', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliateOrder', 'action' => 'list']);
    $builder->connect('/customer/affiliate/order/list', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliateOrder', 'action' => 'list']);
    $builder->connect('/customer/affiliate/order/list/json', ['plugin' => 'Admin', 'controller' => 'CustomerAffiliateOrder', 'action' => 'listJson']);

    //Language
    $builder->connect('/language', ['plugin' => 'Admin', 'controller' => 'Language', 'action' => 'list']);
    $builder->connect('/language/list', ['plugin' => 'Admin', 'controller' => 'Language', 'action' => 'list']);
    $builder->connect('/language/list/json', ['plugin' => 'Admin', 'controller' => 'Language', 'action' => 'listJson']);
    $builder->connect('/language/change-status', ['plugin' => 'Admin', 'controller' => 'Language', 'action' => 'changeStatus']);
    $builder->connect('/language/is-default', ['plugin' => 'Admin', 'controller' => 'Language', 'action' => 'isDefault']);

    // country
    $builder->connect('/country', ['plugin' => 'Admin', 'controller' => 'Country', 'action' => 'list']);
    $builder->connect('/country/list', ['plugin' => 'Admin', 'controller' => 'Country', 'action' => 'list']);
    $builder->connect('/country/list/json', ['plugin' => 'Admin', 'controller' => 'Country', 'action' => 'listJson']);
    $builder->connect('/country/change-position', ['plugin' => 'Admin', 'controller' => 'Country', 'action' => 'changePosition']);
    $builder->connect('/country/auto-suggest', ['plugin' => 'Admin', 'controller' => 'Country', 'action' => 'autoSuggest']);

    $builder->connect('/country/add', ['plugin' => 'Admin', 'controller' => 'Country', 'action' => 'add']);
    $builder->connect('/country/update/:id', ['plugin' => 'Admin', 'controller' => 'Country', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/country/save', ['plugin' => 'Admin', 'controller' => 'Country', 'action' => 'save']);
    $builder->connect('/country/save/:id', ['plugin' => 'Admin', 'controller' => 'Country', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/country/delete', ['plugin' => 'Admin', 'controller' => 'Country', 'action' => 'delete']);
    $builder->connect('/country/change-status', ['plugin' => 'Admin', 'controller' => 'Country', 'action' => 'changeStatus']);


    //City
    $builder->connect('/city/:id', ['plugin' => 'Admin', 'controller' => 'City', 'action' => 'list'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/city/list/:id', ['plugin' => 'Admin', 'controller' => 'City', 'action' => 'list'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/city/list/json/:id', ['plugin' => 'Admin', 'controller' => 'City', 'action' => 'listJson'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/city/change-position', ['plugin' => 'Admin', 'controller' => 'City', 'action' => 'changePosition']);
    $builder->connect('/city/auto-suggest', ['plugin' => 'Admin', 'controller' => 'City', 'action' => 'autoSuggest']);

    $builder->connect('/city/add/:countryid', ['plugin' => 'Admin', 'controller' => 'City', 'action' => 'add'], ['pass' => ['countryid'], 'countryid' => '[0-9]+']);
    $builder->connect('/city/update/:countryid/:id', ['plugin' => 'Admin', 'controller' => 'City', 'action' => 'update'], ['pass' => ['countryid', 'id'], 'id' => '[0-9]+', 'id' => '[0-9]+']);
    $builder->connect('/city/save', ['plugin' => 'Admin', 'controller' => 'City', 'action' => 'save']);
    $builder->connect('/city/save/:id', ['plugin' => 'Admin', 'controller' => 'City', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/city/delete', ['plugin' => 'Admin', 'controller' => 'City', 'action' => 'delete']);
    $builder->connect('/city/change-status', ['plugin' => 'Admin', 'controller' => 'City', 'action' => 'changeStatus']);

    //District
    $builder->connect('/district/:id', ['plugin' => 'Admin', 'controller' => 'District', 'action' => 'list'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/district/list/:id', ['plugin' => 'Admin', 'controller' => 'District', 'action' => 'list'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/district/list/json/:id', ['plugin' => 'Admin', 'controller' => 'District', 'action' => 'listJson'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/district/change-position', ['plugin' => 'Admin', 'controller' => 'District', 'action' => 'changePosition']);
    $builder->connect('/district/auto-suggest', ['plugin' => 'Admin', 'controller' => 'District', 'action' => 'autoSuggest']);

    $builder->connect('/district/add/:cityid', ['plugin' => 'Admin', 'controller' => 'District', 'action' => 'add'], ['pass' => ['cityid'], 'cityid' => '[0-9]+']);
    $builder->connect('/district/update/:cityid/:id', ['plugin' => 'Admin', 'controller' => 'District', 'action' => 'update'], ['pass' => ['cityid', 'id'], 'id' => '[0-9]+', 'id' => '[0-9]+']);

    $builder->connect('/district/save', ['plugin' => 'Admin', 'controller' => 'District', 'action' => 'save']);
    $builder->connect('/district/save/:id', ['plugin' => 'Admin', 'controller' => 'District', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/district/delete', ['plugin' => 'Admin', 'controller' => 'District', 'action' => 'delete']);
    $builder->connect('/district/change-status', ['plugin' => 'Admin', 'controller' => 'District', 'action' => 'changeStatus']);

    //Ward
    $builder->connect('/ward/:id', ['plugin' => 'Admin', 'controller' => 'Ward', 'action' => 'list'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/ward/list/:id', ['plugin' => 'Admin', 'controller' => 'Ward', 'action' => 'list'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/ward/list/json/:id', ['plugin' => 'Admin', 'controller' => 'Ward', 'action' => 'listJson'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/ward/change-position', ['plugin' => 'Admin', 'controller' => 'Ward', 'action' => 'changePosition']);
    $builder->connect('/ward/auto-suggest', ['plugin' => 'Admin', 'controller' => 'Ward', 'action' => 'autoSuggest']);

    $builder->connect('/ward/add/:districtid', ['plugin' => 'Admin', 'controller' => 'Ward', 'action' => 'add'], ['pass' => ['districtid'], 'districtid' => '[0-9]+']);
    $builder->connect('/ward/update/:districtid/:id', ['plugin' => 'Admin', 'controller' => 'Ward', 'action' => 'update'], ['pass' => ['districtid', 'id'], 'id' => '[0-9]+', 'id' => '[0-9]+']);

    $builder->connect('/ward/save', ['plugin' => 'Admin', 'controller' => 'Ward', 'action' => 'save']);
    $builder->connect('/ward/save/:id', ['plugin' => 'Admin', 'controller' => 'Ward', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/ward/delete', ['plugin' => 'Admin', 'controller' => 'Ward', 'action' => 'delete']);
    $builder->connect('/ward/change-status', ['plugin' => 'Admin', 'controller' => 'Ward', 'action' => 'changeStatus']);


    // Brand
    $builder->connect('/brand', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'list']);
    $builder->connect('/brand/list', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'list']);
    $builder->connect('/brand/list/json', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'listJson']);
    $builder->connect('/brand/add', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'add']);
    $builder->connect('/brand/update/:id', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/brand/detail/:id', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/brand/save', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'save']);
    $builder->connect('/brand/save/:id', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/brand/delete', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'delete']);
    $builder->connect('/brand/change-status', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'changeStatus']);
    $builder->connect('/brand/change-position', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'changePosition']);
    $builder->connect('/brand/auto-suggest', ['plugin' => 'Admin', 'controller' => 'Brand', 'action' => 'autoSuggest']);
    
    // Setting
    $builder->connect('/setting', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'dashboard']);
    $builder->connect('/setting/dashboard', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'dashboard']);
    $builder->connect('/setting/save/:type', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'save'], ['pass' => ['type']]);
    $builder->connect('/setting/website-info', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'websiteInfo']);    
    
    $builder->connect('/setting/link', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'link']);    
    $builder->connect('/setting/change-mode', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'changeMode']);
    $builder->connect('/setting/recaptcha', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'recaptcha']);
    $builder->connect('/setting/product', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'product']);
    $builder->connect('/setting/affiliate', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'affiliate']);
    $builder->connect('/setting/commission-rate', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'commissionRate']);
    $builder->connect('/setting/order', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'order']);
    $builder->connect('/setting/social', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'social']);
    $builder->connect('/setting/customer', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'customer']);
    $builder->connect('/setting/api', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'api']);
    $builder->connect('/setting/send-messages', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'sendMessages']);
    $builder->connect('/setting/approved', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'approved']);
    $builder->connect('/setting/save-approved', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'saveApproved']);
    $builder->connect('/setting/plugin', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'plugin']);
    $builder->connect('/setting/plugin/json', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'pluginJson']);
    $builder->connect('/setting/plugin/change-status', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'changeStatusPlugin']);
    $builder->connect('/setting/replace-search-unicode', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'replaceSearchUnicode']);


    // qr code
    $builder->connect('/setting/qr-code', ['plugin' => 'Admin', 'controller' => 'QrCode', 'action' => 'index']);
    $builder->connect('/setting/qr-bank-transaction', ['plugin' => 'Admin', 'controller' => 'QrCode', 'action' => 'settingQrBankTransaction']);    
    $builder->connect('/setting/preview-setting-qr-bank', ['plugin' => 'Admin', 'controller' => 'QrCode', 'action' => 'previewSettingQrBank']);
    $builder->connect('/setting/qr-normal', ['plugin' => 'Admin', 'controller' => 'QrCode', 'action' => 'settingQrNormal']);
    $builder->connect('/setting/generate-qr', ['plugin' => 'Admin', 'controller' => 'QrCode', 'action' => 'generateQr']);
    $builder->connect('/setting/ajax-generate-qr', ['plugin' => 'Admin', 'controller' => 'QrCode', 'action' => 'ajaxGenerateQr']);

    // Print Form
    $builder->connect('/setting/print-form', ['plugin' => 'Admin', 'controller' => 'PrintTemplate', 'action' => 'printConfig']);
    $builder->connect('/setting/print-template/load-info', ['plugin' => 'Admin', 'controller' => 'PrintTemplate', 'action' => 'loadInfoTemplate']);
    $builder->connect('/setting/print-template/view-content', ['plugin' => 'Admin', 'controller' => 'PrintTemplate', 'action' => 'loadViewContentFileTemplate']);
    $builder->connect('/setting/print-template/save', ['plugin' => 'Admin', 'controller' => 'PrintTemplate', 'action' => 'savePrintTemplate']);
    $builder->connect('/setting/print-template/edit-view', ['plugin' => 'Admin', 'controller' => 'PrintTemplate', 'action' => 'saveViewContentFileTemplate']);

    $builder->connect('/print', ['plugin' => 'Admin', 'controller' => 'Print', 'action' => 'print']);
    $builder->connect('/print/get-content', ['plugin' => 'Admin', 'controller' => 'Print', 'action' => 'getContent']);

    //Email Template
    $builder->connect('/setting/email', ['plugin' => 'Admin', 'controller' => 'EmailTemplate', 'action' => 'email']);
    $builder->connect('/setting/email-template/load-info', ['plugin' => 'Admin', 'controller' => 'EmailTemplate', 'action' => 'loadInfoTemplate']);
    $builder->connect('/setting/email-template/view-content', ['plugin' => 'Admin', 'controller' => 'EmailTemplate', 'action' => 'loadViewContentFileTemplate']);
    $builder->connect('/setting/email-template/save', ['plugin' => 'Admin', 'controller' => 'EmailTemplate', 'action' => 'saveEmailTemplate']);
    $builder->connect('/setting/email-template/edit-view', ['plugin' => 'Admin', 'controller' => 'EmailTemplate', 'action' => 'saveViewContentFileTemplate']);
    $builder->connect('/setting/email-send-try', ['plugin' => 'Admin', 'controller' => 'EmailTemplate', 'action' => 'emailSendTry']);

    //Store partner
    $builder->connect('/setting/setting-store-partner', ['plugin' => 'Admin', 'controller' => 'StorePartner', 'action' => 'settingStorePartner']);
    $builder->connect('/setting/sync-stores-kiotviet', ['plugin' => 'Admin', 'controller' => 'StoreKiotviet', 'action' => 'syncStore']);
    $builder->connect('/setting/store-default-kiotviet', ['plugin' => 'Admin', 'controller' => 'StoreKiotviet', 'action' => 'setStoreDefault']);
    $builder->connect('/setting/register-kiotviet', ['plugin' => 'Admin', 'controller' => 'StoreKiotviet', 'action' => 'registerWebhook']);
    $builder->connect('/setting/list-kiotviet', ['plugin' => 'Admin', 'controller' => 'StoreKiotviet', 'action' => 'listWebhooks']);
    $builder->connect('/setting/delete-kiotviet', ['plugin' => 'Admin', 'controller' => 'StoreKiotviet', 'action' => 'deleteWebhooks']);
    $builder->connect('/setting/sync-attribute-kiotviet', ['plugin' => 'Admin', 'controller' => 'StoreKiotviet', 'action' => 'syncAttributeKiotviet']);

    //embed code
    $builder->connect('/setting/embed-code', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'embedCode']);

    // clear cache
    $builder->connect('/setting/clear-cache', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'clearCache']);

    // clear data
    $builder->connect('/setting/clear-data', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'clearData']);
    $builder->connect('/setting/clear-data/process', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'processClearData']);

    //payment gateway
    $builder->connect('/setting/payment-gateway', ['plugin' => 'Admin', 'controller' => 'PaymentGateway', 'action' => 'list']);
    $builder->connect('/setting/payment-gateway/:code', ['plugin' => 'Admin', 'controller' => 'PaymentGateway', 'action' => 'save'], ['pass' => ['code']]);

    //sms brandname
    $builder->connect('/setting/sms-brandname', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'smsBrandname']);
    $builder->connect('/setting/sms-brandname/save-fpt-telecom', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'saveFptTelecom']);
    $builder->connect('/setting/sms-brandname/save-esms', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'saveEsms']);

    //carriers
    $builder->connect('/setting/carriers', ['plugin' => 'Admin', 'controller' => 'Carriers', 'action' => 'list']);    
    $builder->connect('/setting/carriers/ghn-sync-cities', ['plugin' => 'Admin', 'controller' => 'Carriers', 'action' => 'giaohangnhanhSyncCities']);
    $builder->connect('/setting/carriers/ghn-sync-districts', ['plugin' => 'Admin', 'controller' => 'Carriers', 'action' => 'giaohangnhanhSyncDistricts']);
    $builder->connect('/setting/carriers/ghn-sync-wards', ['plugin' => 'Admin', 'controller' => 'Carriers', 'action' => 'giaohangnhanhSyncWards']);
    $builder->connect('/setting/carriers/ghn-sync-stores', ['plugin' => 'Admin', 'controller' => 'Carriers', 'action' => 'giaohangnhanhSyncStores']);
    $builder->connect('/setting/carriers/ghtk-sync-stores', ['plugin' => 'Admin', 'controller' => 'Carriers', 'action' => 'giaohangtietkiemSyncStores']);

    $builder->connect('/setting/carriers/ghn-initialization-cities', ['plugin' => 'Admin', 'controller' => 'Carriers', 'action' => 'giaohangnhanhInitializationCities']);
    $builder->connect('/setting/carriers/ghn-initialization-districts', ['plugin' => 'Admin', 'controller' => 'Carriers', 'action' => 'giaohangnhanhInitializationDistricts']);
    $builder->connect('/setting/carriers/ghn-initialization-wards', ['plugin' => 'Admin', 'controller' => 'Carriers', 'action' => 'giaohangnhanhInitializationWards']);

    $builder->connect('/setting/carriers/:code', ['plugin' => 'Admin', 'controller' => 'Carriers', 'action' => 'save'], ['pass' => ['code']]);

    // attributes
    $builder->connect('/setting/dashboard-attribute', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'attribute']);
    $builder->connect('/setting/embed-attribute/:type', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'embedAttribute'], ['pass' => ['type']]);

    $builder->connect('/setting/attribute', ['plugin' => 'Admin', 'controller' => 'Attribute', 'action' => 'list']);
    $builder->connect('/setting/attribute/list/json', ['plugin' => 'Admin', 'controller' => 'Attribute', 'action' => 'listJson']);
    $builder->connect('/setting/attribute/add', ['plugin' => 'Admin', 'controller' => 'Attribute', 'action' => 'add']);
    $builder->connect('/setting/attribute/update/:id', ['plugin' => 'Admin', 'controller' => 'Attribute', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/setting/attribute/save', ['plugin' => 'Admin', 'controller' => 'Attribute', 'action' => 'save']);
    $builder->connect('/setting/attribute/save/:id', ['plugin' => 'Admin', 'controller' => 'Attribute', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/setting/attribute/delete', ['plugin' => 'Admin', 'controller' => 'Attribute', 'action' => 'delete']);
    $builder->connect('/setting/attribute/change-position', ['plugin' => 'Admin', 'controller' => 'Attribute', 'action' => 'changePosition']);
    $builder->connect('/setting/attribute/get-list-input', ['plugin' => 'Admin', 'controller' => 'Attribute', 'action' => 'getListInput']);

    // options of attribute
    $builder->connect('/setting/attribute/option/:id', ['plugin' => 'Admin', 'controller' => 'AttributeOption', 'action' => 'list'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/setting/attribute/option/list/json/:id', ['plugin' => 'Admin', 'controller' => 'AttributeOption', 'action' => 'listJson'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/setting/attribute/option/add/:id', ['plugin' => 'Admin', 'controller' => 'AttributeOption', 'action' => 'add'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/setting/attribute/option/update/:id/:subid', ['plugin' => 'Admin', 'controller' => 'AttributeOption', 'action' => 'update'], [
        'pass' => ['id', 'subid'],
        'id' => '[0-9]+',
        'subid' => '[0-9]+',
    ]);
    $builder->connect('/setting/attribute/option/save/:id', ['plugin' => 'Admin', 'controller' => 'AttributeOption', 'action' => 'save'], [
        'pass' => ['id'],
        'id' => '[0-9]+'
    ]);
    $builder->connect('/setting/attribute/option/save/:id/:subid', ['plugin' => 'Admin', 'controller' => 'AttributeOption', 'action' => 'save'], [
        'pass' => ['id', 'subid'],
        'id' => '[0-9]+',
        'subid' => '[0-9]+',
    ]);
    $builder->connect('/setting/attribute/option/delete', ['plugin' => 'Admin', 'controller' => 'AttributeOption', 'action' => 'delete']);
    $builder->connect('/setting/attribute/option/change-position', ['plugin' => 'Admin', 'controller' => 'AttributeOption', 'action' => 'changePosition']);


    $builder->connect('/setting/attribute/config-by-category', ['plugin' => 'Admin', 'controller' => 'AttributeSetting', 'action' => 'configByCategory']);
    $builder->connect('/setting/attribute/load-attributes-by-category', ['plugin' => 'Admin', 'controller' => 'AttributeSetting', 'action' => 'loadAttributesByCategory']);


    $builder->connect('/setting/point', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'point']);
    $builder->connect('/setting/shipping', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'shipping']);
    $builder->connect('/setting/shipping/save', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'saveSettingShipping']);

    $builder->connect('/setting/notification', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'notification']);
    $builder->connect('/setting/notification/save', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'saveNotification']);

    $builder->connect('/setting/cdn-path', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'cdnPath']);
    $builder->connect('/setting/language', ['plugin' => 'Admin', 'controller' => 'Setting', 'action' => 'language']);

    //export data
    $builder->connect('/transform-data/export', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'export']);
    $builder->connect('/transform-data/export/initialization', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'initialization']);
    $builder->connect('/transform-data/export/read-database', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'readDatabasesetting']);
    $builder->connect('/transform-data/export/load-config-advanced', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'loadConfigAdvanced']);
    $builder->connect('/transform-data/export/save-config-advanced', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'saveConfigAdvanced']);
    $builder->connect('/transform-data/export/config-data', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'configData']);
    $builder->connect('/transform-data/export/config-id', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'configId']);
    $builder->connect('/transform-data/export/config-cdn', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'configCdn']);
    $builder->connect('/transform-data/export/categories/:type', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'migrateCategories'], ['pass' => ['type']]);
    $builder->connect('/transform-data/export/articles', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'migrateArticles']);
    $builder->connect('/transform-data/export/brands', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'migrateBrands']);
    $builder->connect('/transform-data/export/products', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'migrateProducts']);
    $builder->connect('/transform-data/export/attributes', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'migrateAttributes']);
    $builder->connect('/transform-data/export/tags', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'migrateTags']);
    $builder->connect('/transform-data/export/export-data', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'exportData']);
    $builder->connect('/transform-data/export/migrate-data', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'migrateData']);
    $builder->connect('/transform-data/export/success', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'success']);

    $builder->connect('/transform-data/export/download-file-data', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'downloadFile']);
    $builder->connect('/transform-data/export/download-file-media', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'downloadMedia']);
    $builder->connect('/transform-data/export/download-file-thumb', ['plugin' => 'Admin', 'controller' => 'TransformData', 'action' => 'downloadThumb']);
    

    //currency
    $builder->connect('/currency', ['plugin' => 'Admin', 'controller' => 'Currency', 'action' => 'list']);
    $builder->connect('/currency/list', ['plugin' => 'Admin', 'controller' => 'Currency', 'action' => 'list']);
    $builder->connect('/currency/list/json', ['plugin' => 'Admin', 'controller' => 'Currency', 'action' => 'listJson']);
    $builder->connect('/currency/save', ['plugin' => 'Admin', 'controller' => 'Currency', 'action' => 'save']);
    $builder->connect('/currency/save/:id', ['plugin' => 'Admin', 'controller' => 'Currency', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/currency/is-default', ['plugin' => 'Admin', 'controller' => 'Currency', 'action' => 'isDefault']);
    $builder->connect('/currency/add', ['plugin' => 'Admin', 'controller' => 'Currency', 'action' => 'add']);
    $builder->connect('/currency/update/:id', ['plugin' => 'Admin', 'controller' => 'Currency', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/currency/change-status', ['plugin' => 'Admin', 'controller' => 'Currency', 'action' => 'changeStatus']);
    $builder->connect('/currency/delete', ['plugin' => 'Admin', 'controller' => 'Currency', 'action' => 'delete']);
    
    //Object
    $builder->connect('/source', ['plugin' => 'Admin', 'controller' => 'Object', 'action' => 'list']);
    $builder->connect('/source/list', ['plugin' => 'Admin', 'controller' => 'Object', 'action' => 'list']);
    $builder->connect('/source/list/json', ['plugin' => 'Admin', 'controller' => 'Object', 'action' => 'listJson']);
    $builder->connect('/source/add', ['plugin' => 'Admin', 'controller' => 'Object', 'action' => 'add']);
    $builder->connect('/source/save', ['plugin' => 'Admin', 'controller' => 'Object', 'action' => 'save']);
    $builder->connect('/source/save/:id', ['plugin' => 'Admin', 'controller' => 'Object', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/source/is-default', ['plugin' => 'Admin', 'controller' => 'Object', 'action' => 'isDefault']);
    $builder->connect('/source/delete', ['plugin' => 'Admin', 'controller' => 'Object', 'action' => 'delete']);
    $builder->connect('/source/save', ['plugin' => 'Admin', 'controller' => 'Object', 'action' => 'save']);
    $builder->connect('/source/save/:id', ['plugin' => 'Admin', 'controller' => 'Object', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    
    //Shipping
    $builder->connect('/shipment', ['plugin' => 'Admin', 'controller' => 'Shipment', 'action' => 'list']);
    $builder->connect('/shipment/list', ['plugin' => 'Admin', 'controller' => 'Shipment', 'action' => 'list']);
    $builder->connect('/shipment/list/json', ['plugin' => 'Admin', 'controller' => 'Shipment', 'action' => 'listJson']);
    $builder->connect('/shipment/detail/:id', ['plugin' => 'Admin', 'controller' => 'Shipment', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);

    //modify template file
    $builder->connect('/template/modify/css-custom', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'cssCustom']);
    $builder->connect('/template/modify/js-custom', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'jsCustom']);
    $builder->connect('/template/modify/save/:type', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'save'], ['pass' => ['type']]);
    $builder->connect('/template/modify/view', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'modifyView']);
    $builder->connect('/template/modify/read-folder', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'readFolder']);
    $builder->connect('/template/modify/load-file', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'loadFile']);
    $builder->connect('/template/modify/save-file', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'saveFile']);
    $builder->connect('/template/modify/rename-file', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'renameFile']);
    $builder->connect('/template/modify/delete-file', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'deleteFile']);
    $builder->connect('/template/modify/download-file', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'downloadFile']);
    $builder->connect('/template/modify/upload-file', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'uploadFile']);

    $builder->connect('/template/modify/log', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'logFile']);
    $builder->connect('/template/modify/get-log-file', ['plugin' => 'Admin', 'controller' => 'TemplateModify', 'action' => 'getLogFile']);
    
    // template
    $builder->connect('/template/dashboard', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'dashboard']);
    $builder->connect('/template', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'list']);
    $builder->connect('/template/list', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'list']);
    $builder->connect('/template/customize', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'customize']);
    $builder->connect('/template/save', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'save']);
    $builder->connect('/template/save/:id', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/template/export-template', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'exportTemplate']);
    $builder->connect('/template/load-form-export-template', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'loadFormExportTemplate']);
    $builder->connect('/template/import-template', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'importTemplate']);
    $builder->connect('/template/check-exist', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'checkExistTemplate']);
    $builder->connect('/template/delete', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'delete']);

    $builder->connect('/template/set-default', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'setDefault']);
    $builder->connect('/template/page/save', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'savePage']);
    $builder->connect('/template/page/load-info', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'loadInfoPage']);
    $builder->connect('/template/page/load-info-layout', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'loadInfoLayout']);
    $builder->connect('/template/page/load-dropdown-category/:type', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'loadDropdownCategory'], ['pass' => ['type']]);
    $builder->connect('/template/page/load-dropdown-page', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'loadDropdownPage']);
    $builder->connect('/template/page/save-layout', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'saveLayoutPage']);
    $builder->connect('/template/page/get-type', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'getType']);
    $builder->connect('/template/page/delete', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'deletePage']);
    $builder->connect('/template/page/delete-config', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'deleteConfigPage']);
    $builder->connect('/template/page/duplicate', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'duplicatePage']);
    $builder->connect('/template/page/load-structure', ['plugin' => 'Admin', 'controller' => 'Template', 'action' => 'loadStructurePage']);

    $builder->connect('/template/block', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'list']);
    $builder->connect('/template/block/list', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'list']);
    $builder->connect('/template/block/list/json', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'listJson']);
    $builder->connect('/template/block/add', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'add']);
    $builder->connect('/template/block/update/:code', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'update'], ['pass' => ['code']]);
    $builder->connect('/template/block/create', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'create']);
    $builder->connect('/template/block/delete', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'delete']);
    $builder->connect('/template/block/change-status', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'changeStatus']);
    $builder->connect('/template/block/save/:code', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'save'], ['pass' => ['code']]);
    $builder->connect('/template/block/save/general-config/:code', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'saveGeneralConfig'], ['pass' => ['code']]);
    $builder->connect('/template/block/save/main-config/:code', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'saveMainConfig'], ['pass' => ['code']]);
    $builder->connect('/template/block/save/data-extend/:code', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'saveDataExtend'], ['pass' => ['code']]);
    $builder->connect('/template/block/save/file-view/:code', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'saveFileView'], ['pass' => ['code']]);

    $builder->connect('/template/block/load-view-select-data', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'loadViewData']);
    $builder->connect('/template/block/load-view-data-for-tab', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'loadViewDataForTab']);
    $builder->connect('/template/block/load-dropdown-categories', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'loadDropdownCategories']);
    $builder->connect('/template/block/load-checkbox-categories', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'loadCheckboxCategories']);
    $builder->connect('/template/block/load-editor-data-extend-sub-menu', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'loadEditorDataExtendSubMenu']);
    $builder->connect('/template/block/config-type-load/:type', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'loadConfigTypeLoadOfBlock'], ['pass' => ['type']]);
    $builder->connect('/template/block/load-content-file-view/:code', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'loadContentFileView'], ['pass' => ['code']]);
    $builder->connect('/template/block/add-view/:code', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'addFileView'], ['pass' => ['code']]);
    $builder->connect('/template/block/translate-label', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'translateLabel']);


    $builder->connect('/template/block/delete-view/:code', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'deleteFileView'], ['pass' => ['code']]);

    $builder->connect('/template/block/duplicate', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'duplicate']);


    $builder->connect('/template/block/logs/:code', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'logs'], ['pass' => ['code']]);
    $builder->connect('/template/block/rollback-log/:code', ['plugin' => 'Admin', 'controller' => 'TemplateBlock', 'action' => 'rollbackLog'], ['pass' => ['code']]);
    
    // log
    $builder->connect('/log', ['plugin' => 'Admin', 'controller' => 'Log', 'action' => 'list']);
    $builder->connect('/log/list', ['plugin' => 'Admin', 'controller' => 'Log', 'action' => 'list']);
    $builder->connect('/log/list/json', ['plugin' => 'Admin', 'controller' => 'Log', 'action' => 'listJson']);

    // template v2
    $builder->connect('/template-v2/customize', ['plugin' => 'Admin', 'controller' => 'TemplateV2', 'action' => 'customize']);
    $builder->connect('/template-v2/get-elements', ['plugin' => 'Admin', 'controller' => 'TemplateV2', 'action' => 'getElements']);





    // mobile app
    $builder->connect('/mobile-app', ['plugin' => 'Admin', 'controller' => 'Mobile', 'action' => 'dashboard']);
    $builder->connect('/mobile-app/dashboard', ['plugin' => 'Admin', 'controller' => 'Mobile', 'action' => 'dashboard']);
    $builder->connect('/mobile-app/setting', ['plugin' => 'Admin', 'controller' => 'Mobile', 'action' => 'setting']);
    
    $builder->connect('/mobile-app/setting/save-info-app', ['plugin' => 'Admin', 'controller' => 'Mobile', 'action' => 'saveInfoApp']);
    $builder->connect('/mobile-app/setting/save-info-vphone', ['plugin' => 'Admin', 'controller' => 'Mobile', 'action' => 'saveInfoVphone']);
    $builder->connect('/mobile-app/setting/save-info-comment', ['plugin' => 'Admin', 'controller' => 'Mobile', 'action' => 'saveInfoComment']);
    $builder->connect('/mobile-app/setting/save-info-social-login', ['plugin' => 'Admin', 'controller' => 'Mobile', 'action' => 'saveInfoSocialLogin']);
    $builder->connect('/mobile-app/setting/save-info-social', ['plugin' => 'Admin', 'controller' => 'Mobile', 'action' => 'saveInfoSocial']);
    $builder->connect('/mobile-app/setting/save-contact', ['plugin' => 'Admin', 'controller' => 'Mobile', 'action' => 'saveContact']);
    $builder->connect('/mobile-app/setting/save-config-momo', ['plugin' => 'Admin', 'controller' => 'Mobile', 'action' => 'saveConfigMomo']);

    $builder->connect('/mobile-app/template', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'list']);
    $builder->connect('/mobile-app/template/list', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'list']);

    $builder->connect('/mobile-app/template/setting-general', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'config']);
    $builder->connect('/mobile-app/template/setting-general/color-config', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'colorConfig']);
    $builder->connect('/mobile-app/template/setting-general/product-config', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'productConfig']);
    $builder->connect('/mobile-app/template/setting-general/advanced-search-config', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'advancedSearchConfig']);
    $builder->connect('/mobile-app/template/setting-general/link-policy-config', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'linkPolicyConfig']);

    $builder->connect('/mobile-app/template/customize', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'customize']);
    $builder->connect('/mobile-app/template/save-customize', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'saveCustomize']);    
    $builder->connect('/mobile-app/template/load-structure-page', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'loadStructurePage']);
    $builder->connect('/mobile-app/template/load-config-page', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'loadConfigPage']);
    $builder->connect('/mobile-app/template/load-info-page', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'loadInfoPage']);
    $builder->connect('/mobile-app/template/save-page', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'savePage']);
    $builder->connect('/mobile-app/template/load-dropdown-page', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'loadDropdownPage']);
    $builder->connect('/mobile-app/template/load-dropdown-category/:type', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'loadDropdownCategory'], ['pass' => ['type']]);
    $builder->connect('/mobile-app/template/delete', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'delete']);
    $builder->connect('/mobile-app/template/delete-page', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'deletePage']);
    $builder->connect('/mobile-app/template/delete-config-page', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'deleteConfigPage']);

    $builder->connect('/mobile-app/template/export', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'exportTemplate']);
    $builder->connect('/mobile-app/template/import', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'importTemplate']);
    $builder->connect('/mobile-app/template/load-form-export', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'loadFormExportTemplate']);
    $builder->connect('/mobile-app/template/check-exist', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'checkExistTemplate']);
    $builder->connect('/mobile-app/template/set-default', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'setDefault']);
    

    $builder->connect('/mobile-app/template/media', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'media']);
    $builder->connect('/mobile-app/template/save-media', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'saveMedia']);

    $builder->connect('/mobile-app/template/text', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'text']);
    $builder->connect('/mobile-app/template/save-text', ['plugin' => 'Admin', 'controller' => 'MobileTemplate', 'action' => 'saveText']);    

    $builder->connect('/mobile-app/block', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'list']);
    $builder->connect('/mobile-app/block/list', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'list']);
    $builder->connect('/mobile-app/block/list/json', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'listJson']);
    $builder->connect('/mobile-app/block/add', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'add']);
    $builder->connect('/mobile-app/block/create', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'create']);
    $builder->connect('/mobile-app/block/delete', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'delete']);
    $builder->connect('/mobile-app/block/update/:code', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'update'], ['pass' => ['code']]);
    $builder->connect('/mobile-app/block/save-main-config/:code', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'saveMainConfig'], ['pass' => ['code']]);
    $builder->connect('/mobile-app/block/save-data-config/:code', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'saveDataConfig'], ['pass' => ['code']]);
    $builder->connect('/mobile-app/block/save-layout-config/:code', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'saveLayoutConfig'], ['pass' => ['code']]);
    $builder->connect('/mobile-app/block/load-view-select-data', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'loadViewData']);
    $builder->connect('/mobile-app/block/config-type-load/:type', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'loadConfigTypeLoadOfBlock'], ['pass' => ['type']]);
    $builder->connect('/mobile-app/block/load-checkbox-categories', ['plugin' => 'Admin', 'controller' => 'MobileTemplateBlock', 'action' => 'loadCheckboxCategories']);

    //media
    $builder->connect('/media', ['plugin' => 'Admin', 'controller' => 'Media', 'action' => 'media']);

    //payment
    $builder->connect('/payment', ['plugin' => 'Admin', 'controller' => 'Payment', 'action' => 'list']);
    $builder->connect('/payment/list', ['plugin' => 'Admin', 'controller' => 'Payment', 'action' => 'list']);
    $builder->connect('/payment/list-gateway', ['plugin' => 'Admin', 'controller' => 'Payment', 'action' => 'listGateway']);
    $builder->connect('/payment/list/json', ['plugin' => 'Admin', 'controller' => 'Payment', 'action' => 'listJson']);
    $builder->connect('/payment/detail/:code', ['plugin' => 'Admin', 'controller' => 'Payment', 'action' => 'detail'], ['pass' => ['code']]);
    $builder->connect('/payment/change-note', ['plugin' => 'Admin', 'controller' => 'Payment', 'action' => 'changeNote']);
    $builder->connect('/payment/change-status/:id', ['plugin' => 'Admin', 'controller' => 'Payment', 'action' => 'changeStatus'], ['pass' => ['id'], 'id' => '[0-9]+']);

    //contact
    $builder->connect('/contact', ['plugin' => 'Admin', 'controller' => 'Contact', 'action' => 'list']);
    $builder->connect('/contact/list', ['plugin' => 'Admin', 'controller' => 'Contact', 'action' => 'list']);
    $builder->connect('/contact/list/json', ['plugin' => 'Admin', 'controller' => 'Contact', 'action' => 'listJson']);
    $builder->connect('/contact/detail/:id', ['plugin' => 'Admin', 'controller' => 'Contact', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);

    //contact form
    $builder->connect('/contact/form', ['plugin' => 'Admin', 'controller' => 'ContactForm', 'action' => 'list']);
    $builder->connect('/contact/form/list', ['plugin' => 'Admin', 'controller' => 'ContactForm', 'action' => 'list']);
    $builder->connect('/contact/form/list/json', ['plugin' => 'Admin', 'controller' => 'ContactForm', 'action' => 'listJson']);
    $builder->connect('/contact/form/add', ['plugin' => 'Admin', 'controller' => 'ContactForm', 'action' => 'add']);
    $builder->connect('/contact/form/update/:id', ['plugin' => 'Admin', 'controller' => 'ContactForm', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/contact/form/save', ['plugin' => 'Admin', 'controller' => 'ContactForm', 'action' => 'save']);
    $builder->connect('/contact/form/save/:id', ['plugin' => 'Admin', 'controller' => 'ContactForm', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/contact/form/delete', ['plugin' => 'Admin', 'controller' => 'ContactForm', 'action' => 'delete']);

    //comment
    $builder->connect('/comment', ['plugin' => 'Admin', 'controller' => 'Comment', 'action' => 'list']);
    $builder->connect('/comment/list', ['plugin' => 'Admin', 'controller' => 'Comment', 'action' => 'list']);
    $builder->connect('/comment/view-comment/:id', ['plugin' => 'Admin', 'controller' => 'Comment', 'action' => 'viewComment'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/comment/comment-modal', ['plugin' => 'Admin', 'controller' => 'Comment', 'action' => 'commentModal']);
    $builder->connect('/comment/upload-file', ['plugin' => 'Admin', 'controller' => 'Comment', 'action' => 'uploadFile']);
    $builder->connect('/comment/admin-reply', ['plugin' => 'Admin', 'controller' => 'Comment', 'action' => 'adminReply']);
    $builder->connect('/comment/change-status', ['plugin' => 'Admin', 'controller' => 'Comment', 'action' => 'changeStatus']);
    $builder->connect('/comment/delete', ['plugin' => 'Admin', 'controller' => 'Comment', 'action' => 'delete']);

    //report
    $builder->connect('/report', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'report']);
    $builder->connect('/report/load-dashboard-revenue', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'loadDashboardRevenue']);
    $builder->connect('/report/load-dashboard-city', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'loadDashboardCity']);
    $builder->connect('/report/load-dashboard-source', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'loadDashboardSource']);
    $builder->connect('/report/load-dashboard-product', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'loadDashboardProduct']);
    $builder->connect('/report/load-dashboard-staff', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'loadDashboardStaff']);
    $builder->connect('/report/revenue', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'reportRevenue']);
    $builder->connect('/report/load-revenue', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'loadReportRevenue']);
    $builder->connect('/report/staff', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'reportStaff']);
    $builder->connect('/report/load-staff', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'loadReportStaff']);
    $builder->connect('/report/city', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'reportCity']);
    $builder->connect('/report/load-city', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'loadReportCity']);
    $builder->connect('/report/product', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'reportProduct']);
    $builder->connect('/report/load-product', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'loadReportProduct']);
    $builder->connect('/report/source', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'reportSource']);
    $builder->connect('/report/load-source', ['plugin' => 'Admin', 'controller' => 'Report', 'action' => 'loadReportSource']);


    // promotion
    $builder->connect('/promotion', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'list']);
    $builder->connect('/promotion/list', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'list']);
    $builder->connect('/promotion/list/json', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'listJson']);
    $builder->connect('/promotion/add', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'add']);
    $builder->connect('/promotion/update/:id', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/promotion/auto-suggest', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'autoSuggest']);

    $builder->connect('/promotion/save', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'save']);
    $builder->connect('/promotion/save/:id', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/promotion/delete', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'delete']);
    $builder->connect('/promotion/change-status', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'changeStatus']);
    $builder->connect('/promotion/change-position', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'changePosition']);
    $builder->connect('/promotion/load-list-promotion-invalid', ['plugin' => 'Admin', 'controller' => 'Promotion', 'action' => 'loadListPromotionInvalid']);

    // coupon
    $builder->connect('/promotion/coupon', ['plugin' => 'Admin', 'controller' => 'PromotionCoupon', 'action' => 'list'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/promotion/coupon/list/:id', ['plugin' => 'Admin', 'controller' => 'PromotionCoupon', 'action' => 'list'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/promotion/coupon/list/json', ['plugin' => 'Admin', 'controller' => 'PromotionCoupon', 'action' => 'listJson']);
    $builder->connect('/promotion/coupon/list/json/:id', ['plugin' => 'Admin', 'controller' => 'PromotionCoupon', 'action' => 'listJson'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/promotion/coupon/add-coupon', ['plugin' => 'Admin', 'controller' => 'PromotionCoupon', 'action' => 'addCoupon']);
    $builder->connect('/promotion/coupon/change-status', ['plugin' => 'Admin', 'controller' => 'PromotionCoupon', 'action' => 'changeStatus']);
    $builder->connect('/promotion/coupon/delete', ['plugin' => 'Admin', 'controller' => 'PromotionCoupon', 'action' => 'delete']);
    
    // shipping method
    $builder->connect('/shipping-method', ['plugin' => 'Admin', 'controller' => 'ShippingMethod', 'action' => 'list']);
    $builder->connect('/shipping-method/list', ['plugin' => 'Admin', 'controller' => 'ShippingMethod', 'action' => 'list']);
    $builder->connect('/shipping-method/list/json', ['plugin' => 'Admin', 'controller' => 'ShippingMethod', 'action' => 'listJson']);
    $builder->connect('/shipping-method/add', ['plugin' => 'Admin', 'controller' => 'ShippingMethod', 'action' => 'add']);
    $builder->connect('/shipping-method/update/:id', ['plugin' => 'Admin', 'controller' => 'ShippingMethod', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);

    $builder->connect('/shipping-method/save', ['plugin' => 'Admin', 'controller' => 'ShippingMethod', 'action' => 'save']);
    $builder->connect('/shipping-method/save/:id', ['plugin' => 'Admin', 'controller' => 'ShippingMethod', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/shipping-method/delete', ['plugin' => 'Admin', 'controller' => 'ShippingMethod', 'action' => 'delete']);
    $builder->connect('/shipping-method/change-status', ['plugin' => 'Admin', 'controller' => 'ShippingMethod', 'action' => 'changeStatus']);
    $builder->connect('/shipping-method/change-position', ['plugin' => 'Admin', 'controller' => 'ShippingMethod', 'action' => 'changePosition']);

    // notifications
    $builder->connect('/notification', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'list']);
    $builder->connect('/notification/list', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'list']);
    $builder->connect('/notification/list/json', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'listJson']);
    $builder->connect('/notification/add', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'add']);
    $builder->connect('/notification/update/:id', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'update'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/notification/detail/:id', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'detail'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/notification/save', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'save']);
    $builder->connect('/notification/save/:id', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'save'], ['pass' => ['id'], 'id' => '[0-9]+']);
    $builder->connect('/notification/delete', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'delete']);
    $builder->connect('/notification/change-status', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'changeStatus']);
    $builder->connect('/notification/send', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'send']);

    $builder->connect('/notification/subscribe', ['plugin' => 'Admin', 'controller' => 'NotificationSubscribe', 'action' => 'list']);
    $builder->connect('/notification/subscribe/list', ['plugin' => 'Admin', 'controller' => 'NotificationSubscribe', 'action' => 'list']);
    $builder->connect('/notification/subscribe/list/json', ['plugin' => 'Admin', 'controller' => 'NotificationSubscribe', 'action' => 'listJson']);
    $builder->connect('/notification/subscribe/delete', ['plugin' => 'Admin', 'controller' => 'NotificationSubscribe', 'action' => 'delete']);

    $builder->connect('/notification/sent', ['plugin' => 'Admin', 'controller' => 'NotificationSent', 'action' => 'list']);
    $builder->connect('/notification/sent/list', ['plugin' => 'Admin', 'controller' => 'NotificationSent', 'action' => 'list']);
    $builder->connect('/notification/sent/list/json', ['plugin' => 'Admin', 'controller' => 'NotificationSent', 'action' => 'listJson']);


    $builder->connect('/notification/test', ['plugin' => 'Admin', 'controller' => 'Notification', 'action' => 'sendTest']);

    // frontend
    $builder->connect('/frontend/load-admin-bar', ['plugin' => 'Admin', 'controller' => 'Frontend', 'action' => 'loadAdminBar']);
    $builder->connect('/frontend/clear-cache', ['plugin' => 'Admin', 'controller' => 'Frontend', 'action' => 'clearCache']);

    $builder->connect('/feedback', ['plugin' => 'Admin', 'controller' => 'Feedback', 'action' => 'index']);
    $builder->connect('/feedback/send', ['plugin' => 'Admin', 'controller' => 'Feedback', 'action' => 'send']);
    $builder->connect('/feedback/success', ['plugin' => 'Admin', 'controller' => 'Feedback', 'action' => 'success']);
    $builder->connect('/feedback/upload-files', ['plugin' => 'Admin', 'controller' => 'Feedback', 'action' => 'uploadFiles']);

    $builder->connect('/system/get-tinymce-templates', ['plugin' => 'Admin', 'controller' => 'System', 'action' => 'getTinymceTemplates']);


    // nh-notification
    $builder->connect('/nh-notification/list', ['plugin' => 'Admin', 'controller' => 'NhNotification', 'action' => 'listNotifications']);

    // myfilemanager admin
    $builder->connect('/myfilemanager', ['plugin' => 'Admin', 'controller' => 'MyFilemanager', 'action' => 'index']);
    $builder->connect('/myfilemanager/files', ['plugin' => 'Admin', 'controller' => 'MyFilemanager', 'action' => 'files']);
    $builder->connect('/myfilemanager/create-folder', ['plugin' => 'Admin', 'controller' => 'MyFilemanager', 'action' => 'createFolder']);
    $builder->connect('/myfilemanager/rename', ['plugin' => 'Admin', 'controller' => 'MyFilemanager', 'action' => 'rename']);
    $builder->connect('/myfilemanager/delete', ['plugin' => 'Admin', 'controller' => 'MyFilemanager', 'action' => 'delete']);
    $builder->connect('/myfilemanager/paste', ['plugin' => 'Admin', 'controller' => 'MyFilemanager', 'action' => 'paste']);
    $builder->connect('/myfilemanager/upload', ['plugin' => 'Admin', 'controller' => 'MyFilemanager', 'action' => 'upload']);
    $builder->connect('/myfilemanager/navigation', ['plugin' => 'Admin', 'controller' => 'MyFilemanager', 'action' => 'navigation']);

    $builder->fallbacks();
});

// router build layout
Router::prefix('buildlayout', function ($routes) {
    $routes->connect('/get-elements', ['controller' => 'Layout', 'action' => 'getElements']);
    $routes->connect('/get-structure-initialize', ['controller' => 'Layout', 'action' => 'getStructureInitialize']);
    
    $routes->fallbacks(DashedRoute::class);
});