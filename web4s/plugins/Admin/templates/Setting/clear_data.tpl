<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>

            <span class="btn btn-sm btn-brand btn-save">
                {__d('admin', 'xoa_du_lieu')}
            </span>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/clear-data/process" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__body pb-0">
                <div class="kt-section">
                    <div class="kt-section__content">
                        <h4>
                            Bài viết
                        </h4>
                        <table class="table table-bordered table-hover nh-table fw-400 mb-40">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 60px;">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox-inline ml-10">
                                            <input type="checkbox" class="check-all">
                                            <span></span>
                                        </label>
                                    </th>

                                    <th class="w-30">
                                        {__d('admin', 'muc_luc')}
                                    </th>

                                    <th>
                                        {__d('admin', 'mo_ta')}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="category_article" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'danh_muc_bai_viet')}
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            {__d('admin', 'xoa_du_lieu_danh_muc_bai_viet')}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="article" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'bai_viet')} 
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            {__d('admin', 'xoa_du_lieu_bai_viet')}
                                        </span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        <h4>
                            Bán hàng
                        </h4>
                        <table class="table table-bordered table-hover nh-table fw-400 mb-40">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 60px;">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox-inline ml-10">
                                            <input type="checkbox" class="check-all">
                                            <span></span>
                                        </label>
                                    </th>

                                    <th class="w-30">
                                        {__d('admin', 'muc_luc')}
                                    </th>

                                    <th>
                                        {__d('admin', 'mo_ta')}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="category_product" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'danh_muc_san_pham')}
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">{__d('admin', 'xoa_du_lieu_danh_muc_san_pham')}</span>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="brand" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'thuong_hieu')}
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            {__d('admin', 'xoa_du_lieu_thuong_hieu')}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="product" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'san_pham')}
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            {__d('admin', 'xoa_du_lieu_san_pham')}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="order" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'don_hang')}
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            {__d('admin', 'xoa_du_lieu_don_hang_bao_gom_don_hang_payment_va_van_chuyen')}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="promotion" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'khuyen_mai')}
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            Xóa dữ liệu chương trình khuyến mãi và mã Coupon
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="point" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        Điểm khách hàng
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            Xóa dữ liệu về điểm của khách hàng
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="affiliate" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        Affiliate
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            Xóa dữ liệu về Affiiate
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <h4>
                            Website
                        </h4>
                        <table class="table table-bordered table-hover nh-table fw-400 mb-40">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 60px;">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox-inline ml-10">
                                            <input type="checkbox" class="check-all">
                                            <span></span>
                                        </label>
                                    </th>

                                    <th class="w-30">
                                        {__d('admin', 'muc_luc')}
                                    </th>

                                    <th>
                                        {__d('admin', 'mo_ta')}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                
                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="customer" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'khach_hang')}
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            {__d('admin', 'xoa_du_lieu_khach_hang')}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="counter" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        Bộ đếm truy cập
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            Xoá dữ liệu bộ đếm truy cập website
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="comment" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'binh_luan')}
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            Xoá dữ liệu bình luận
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="tag" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>
                                    <td>
                                        Thẻ Tag
                                    </td>
                                    <td>
                                        <span class="form-text text-muted">
                                            Xóa dữ liệu thẻ Tag của bài viết và sản phẩm
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="attribute" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'thuoc_tinh_mo_rong')}
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            {__d('admin', 'xoa_du_lieu_thuoc_tinh_mo_rong')}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="contact" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'lien_he')}
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            {__d('admin', 'xoa_du_lieu_lien_he_tu_khach_hang')}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="notification" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        {__d('admin', 'thong_bao')}
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            Xóa dữ liệu thông báo của Website
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10">
                                            <input name="nhnotification" class="check-single" type="checkbox">
                                            <span></span>
                                        </label>
                                    </th>

                                    <td>
                                        Thông báo hệ thống
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            Xóa dữ liệu thông báo của hệ thống
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>