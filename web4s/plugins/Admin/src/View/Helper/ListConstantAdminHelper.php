<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;

class ListConstantAdminHelper extends Helper
{   
    public function listStatus()
    {
        return [
            ENABLE => __d('admin', 'hoat_dong'),
            DISABLE => __d('admin', 'ngung_hoat_dong'),
        ];
    }

    public function listStatusShipping()
    {
        return [
            WAIT_DELIVER => __d('admin', 'cho_lay_hang'),
            DELIVERY => __d('admin', 'dang_giao_hang'),
            DELIVERED => __d('admin', 'da_giao_hang'),
            CANCEL_PACKAGE => __d('admin', 'huy_dong_goi'),
            CANCEL_WAIT_DELIVER => __d('admin', 'huy_giao_va_cho_nhan'),
            CANCEL_DELIVERED => __d('admin', 'huy_giao_va_da_nhan'),
        ];
    }

    public function listActionTypePointHistory()
    {
        return [
            ORDER => __d('admin', 'mua_hang_tich_diem'),
            PROMOTION => __d('admin', 'chuong_trinh_khuyen_mai'),
            ATTENDANCE => __d('admin', 'diem_danh_tich_diem'),
            OTHER => __d('admin', 'dieu_chinh_diem'),
            GIVE_POINT => __d('admin', 'tang_diem'),
            BUY_POINT => __d('admin', 'nap_diem'),
            AFFILIATE => __d('admin', 'tiep_thi_lien_ket')
        ];
    }

    public function listStatusArticle()
    {
        return [
            AWAITING_APPROVAL => __d('admin', 'cho_duyet'),
            ENABLE => __d('admin', 'hoat_dong'),
            DISABLE => __d('admin', 'ngung_hoat_dong'),
        ];
    }

    public function listStatusGenealogy()
    {
        return [
            ENABLE => 'Còn sống',
            DISABLE => 'Đã mất',
        ];
    }

    public function listGenealogicalGenealogy()
    {
        return [
            ENABLE => 'Có',
            DISABLE => 'Không',
        ];
    }

    public function listStatusProduct()
    {
        return [
            AWAITING_APPROVAL => __d('admin', 'cho_duyet'),
            ENABLE => __d('admin', 'hoat_dong'),
            DISABLE => __d('admin', 'ngung_hoat_dong'),
            STOP_BUSSINEUS => __d('admin', 'ngung_kinh_doanh'),
        ];
    }

    public function listStatusAffiliate()
    {
        return [
            AFFILIATE_APPROVAL => __d('admin', 'cho_duyet'),
            ENABLE => __d('admin', 'kich_hoat'),
            DISABLE => __d('admin', 'ngung_kich_hoat'),
        ];
    }

    public function listStatusPointHistory()
    {
        return [
            PENDING => __d('admin', 'cho_duyet'),
            ENABLE => __d('admin', 'thanh_cong'),
            DISABLE => __d('admin', 'huy'),
        ];
    }

    public function listTypeVideo()
    {
        return [
            VIDEO_YOUTUBE => __d('admin', 'youtube'),
            VIDEO_SYSTEM => __d('admin', 'he_thong'),
        ];
    }

    public function listStatusTransaction()
    {
        return [
            SANDBOX => __d('admin', 'thu_nghiem'),
            LIVE => __d('admin', 'thuc_te')
        ];
    }

    public function listMode()
    {
        return [
            SANDBOX => __d('admin', 'thu_nghiem'),
            LIVE => __d('admin', 'thuc_te')
        ];
    }

    public function listStatusOrder()
    {
        return [
            DRAFT => __d('admin', 'chua_xac_nhan'),
            NEW_ORDER => __d('admin', 'don_moi'),
            CONFIRM => __d('admin', 'xac_nhan'),
            PACKAGE => __d('admin', 'dong_goi'),
            EXPORT => __d('admin', 'xuat_kho'),
            DONE => __d('admin', 'thanh_cong'),
            CANCEL => __d('admin', 'don_huy')
        ];
    }

    public function listStatusPromotionCoupon()
    {
        return [
            USED => __d('admin', 'da_su_dung'),
            ENABLE => __d('admin', 'hoat_dong'),
            DISABLE => __d('admin', 'ngung_hoat_dong'),
        ];
    }

    public function listBankCodeZaloPay()
    {
        return [
            'ATM' => 'Thẻ ATM',
            'CC' => 'Credit Card',
            'zalopayapp' => 'Mã QR',
        ];
    }
}
