<div class="kt-portlet__head kt-portlet__head--noborder kt-portlet__space-x">
    <div class="kt-portlet__head-label">
        <a href="{ADMIN_PATH}/order" class="kt-portlet__head-title">
            {__d('admin', 'don_hang')}
        </a>
    </div>

    <div class="kt-portlet__head-toolbar">
        <a href="javascript:;" class="btn btn-label-light btn-sm btn-bold dropdown-toggle" data-toggle="dropdown">
            {__d('admin', 'thang_label')}
        </a>

        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right">
            <ul class="kt-nav">

                <li load-statistics-order="month" class="kt-nav__item">
                    <a class="kt-nav__link" href="javascript:;">                        
                        <span class="kt-nav__link-text">                           
                            {if empty($type) || (!empty($type) && $type == 'month')}
                                {__d('admin', 'thang_label')}
                            {/if}

                            {if !empty($type) && $type == 'year'}
                                {__d('admin', 'nam_label')}
                            {/if}

                            {if !empty($type) && $type == 'all'}
                                {__d('admin', 'tat_ca')}
                            {/if}
                        </span>                        
                    </a>
                </li>

                <li load-statistics-order="year" class="kt-nav__item">
                    <a class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'nam_label')}
                        </span>
                    </a>
                </li>

                <li load-statistics-order="all" class="kt-nav__item">
                    <a class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'tat_ca')}
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="kt-portlet__body kt-portlet__body--fit">
    <div class="kt-widget17">
        <div class="kt-widget17__visual kt-widget17__visual--chart kt-portlet-fit--top kt-portlet-fit--sides" style="background-image: url(assets/media/bg/bg-7.jpg)">
            <div class="kt-widget17__chart" style="height:175px;"></div>
        </div>

        <div class="kt-widget17__stats mb-30">
            <div class="kt-widget17__items">
                <div class="kt-widget17__item">
                    <span class="kt-widget17__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"/>
                                <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                            </g>
                        </svg>
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'tong_don_hang')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if !empty($number_order)}
                                {$number_order|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'don_hang')}
                    </span>
                </div>

                <div class="kt-widget17__item">
                    <span class="kt-widget17__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--danger">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                                <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                                <rect fill="#000000" opacity="0.3" x="10" y="9" width="7" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" x="7" y="9" width="2" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" x="7" y="13" width="2" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" x="10" y="13" width="7" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" x="7" y="17" width="2" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" x="10" y="17" width="7" height="2" rx="1"/>
                            </g>
                        </svg>
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'don_hang_moi')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if !empty($number_order_new)}
                                {$number_order_new|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'don_hang')}
                    </span>
                </div>
            </div>

            <div class="kt-widget17__items">
                <div class="kt-widget17__item">
                    <span class="kt-widget17__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--success">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                                <path d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000"/>
                                <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                            </g>
                        </svg>
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'thanh_cong')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if !empty($number_order_done)}
                                {$number_order_done|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'don_hang')}
                    </span>
                </div>

                <div class="kt-widget17__item">
                    <span class="kt-widget17__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--dark">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M4.5,3 L19.5,3 C20.3284271,3 21,3.67157288 21,4.5 L21,19.5 C21,20.3284271 20.3284271,21 19.5,21 L4.5,21 C3.67157288,21 3,20.3284271 3,19.5 L3,4.5 C3,3.67157288 3.67157288,3 4.5,3 Z M8,5 C7.44771525,5 7,5.44771525 7,6 C7,6.55228475 7.44771525,7 8,7 L16,7 C16.5522847,7 17,6.55228475 17,6 C17,5.44771525 16.5522847,5 16,5 L8,5 Z M10.5857864,14 L9.17157288,15.4142136 C8.78104858,15.8047379 8.78104858,16.4379028 9.17157288,16.8284271 C9.56209717,17.2189514 10.1952621,17.2189514 10.5857864,16.8284271 L12,15.4142136 L13.4142136,16.8284271 C13.8047379,17.2189514 14.4379028,17.2189514 14.8284271,16.8284271 C15.2189514,16.4379028 15.2189514,15.8047379 14.8284271,15.4142136 L13.4142136,14 L14.8284271,12.5857864 C15.2189514,12.1952621 15.2189514,11.5620972 14.8284271,11.1715729 C14.4379028,10.7810486 13.8047379,10.7810486 13.4142136,11.1715729 L12,12.5857864 L10.5857864,11.1715729 C10.1952621,10.7810486 9.56209717,10.7810486 9.17157288,11.1715729 C8.78104858,11.5620972 8.78104858,12.1952621 9.17157288,12.5857864 L10.5857864,14 Z" fill="#000000"/>
                            </g>
                        </svg>
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'don_huy')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if !empty($number_order_cancel)}
                                {$number_order_cancel|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'don_hang')}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>