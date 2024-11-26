<div class="kt-portlet__body kt-portlet__body--fit">
    <div class="kt-widget17">
        <div class="kt-widget17__stats m-0 w-100 text-center">
            <div class="kt-widget17__items">
                <div class="kt-widget17__item cursor-default">
                    <span class="kt-widget17__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--brand">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                <rect fill="#000000" x="6" y="11" width="9" height="2" rx="1"></rect>
                                <rect fill="#000000" x="6" y="15" width="5" height="2" rx="1"></rect>
                            </g>
                        </svg>
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'tong_bai_viet')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if !empty($number_article)}
                                {$number_article|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'bai_viet')}
                    </span>
                </div>

                <div class="kt-widget17__item cursor-default">
                    <span class="kt-widget17__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--warning">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"/>
                                <rect fill="#000000" opacity="0.3" x="2" y="4" width="20" height="16" rx="2"/>
                                <polygon fill="#000000" opacity="0.3" points="4 20 10.5 11 17 20"/>
                                <polygon fill="#000000" points="11 20 15.5 14 20 20"/>
                                <circle fill="#000000" opacity="0.3" cx="18.5" cy="8.5" r="1.5"/>
                            </g>
                        </svg>
                    </span>
                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'anh')}
                    </span>
                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if !empty($number_image)}
                                {$number_image|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'bai_viet')}
                    </span>
                </div>
            </div>

            <div class="kt-widget17__items">
                <div class="kt-widget17__item cursor-default">
                    <span class="kt-widget17__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M12,21 C7.02943725,21 3,16.9705627 3,12 C3,7.02943725 7.02943725,3 12,3 C16.9705627,3 21,7.02943725 21,12 C21,16.9705627 16.9705627,21 12,21 Z M11.7752551,13.2928932 C12.3275399,13.2928932 12.7752551,12.845178 12.7752551,12.2928932 C12.7752551,11.7406085 12.3275399,11.2928932 11.7752551,11.2928932 C11.2229704,11.2928932 10.7752551,11.7406085 10.7752551,12.2928932 C10.7752551,12.845178 11.2229704,13.2928932 11.7752551,13.2928932 Z M11.2235429,9.10222252 C12.2904751,8.8163389 12.9236401,7.71966498 12.6377564,6.65273278 C12.3518728,5.58580057 11.2551989,4.95263559 10.1882667,5.23851922 C9.12133448,5.52440284 8.4881695,6.62107675 8.77405312,7.68800896 C9.05993675,8.75494117 10.1566107,9.38810614 11.2235429,9.10222252 Z M13.8117333,18.7614808 C14.8786655,18.4755972 15.5118305,17.3789232 15.2259469,16.311991 C14.9400633,15.2450588 13.8433893,14.6118939 12.7764571,14.8977775 C11.7095249,15.1836611 11.0763599,16.280335 11.3622436,17.3472672 C11.6481272,18.4141994 12.7448011,19.0473644 13.8117333,18.7614808 Z M7.68800896,15.2259469 C8.75494117,14.9400633 9.38810614,13.8433893 9.10222252,12.7764571 C8.8163389,11.7095249 7.71966498,11.0763599 6.65273278,11.3622436 C5.58580057,11.6481272 4.95263559,12.7448011 5.23851922,13.8117333 C5.52440284,14.8786655 6.62107675,15.5118305 7.68800896,15.2259469 Z M17.3472672,12.6377564 C18.4141994,12.3518728 19.0473644,11.2551989 18.7614808,10.1882667 C18.4755972,9.12133448 17.3789232,8.4881695 16.311991,8.77405312 C15.2450588,9.05993675 14.6118939,10.1566107 14.8977775,11.2235429 C15.1836611,12.2904751 16.280335,12.9236401 17.3472672,12.6377564 Z" fill="#000000" opacity="0.3"/>
                                <path d="M17.6573343,19 L21,19 C21.5522847,19 22,19.4477153 22,20 C22,20.5522847 21.5522847,21 21,21 L12,21 C14.1432966,21 16.1116082,20.2507999 17.6573343,19 Z" fill="#000000"/>
                            </g>
                        </svg>
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'video')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if !empty($number_video)}
                                {$number_video|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'bai_viet')}
                    </span>
                </div>
                
                <div class="kt-widget17__item cursor-default">
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
                        {__d('admin', 'tai_lieu')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if !empty($number_file)}
                                {$number_file|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'bai_viet')}
                    </span>
                </div>
            </div>

            <div class="kt-widget17__items">                
                <div class="kt-widget17__item cursor-default">
                    <span class="kt-widget17__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--danger">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z" fill="#000000"/>
                                <path d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z" fill="#000000" opacity="0.3"/>
                            </g>
                        </svg>
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'danh_muc_bai_viet')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if !empty($number_category)}
                                {$number_category|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'danh_muc')}
                    </span>
                </div>                

                <div class="kt-widget17__item cursor-default">
                    <span class="kt-widget17__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--success">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/>
                                <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero"/>
                            </g>
                        </svg>
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'bai_viet_chuan_seo')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if !empty($number_article_seo)}
                                {$number_article_seo|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'bai_viet')}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>