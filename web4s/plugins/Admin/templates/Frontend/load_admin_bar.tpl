<div id="nh-admin-bar" class="nh-admin-bar">
    <ul>
        <li>
            <a href="https://web4s.vn" target="_blank">
                <img alt="Admin Logo" src="{ADMIN_PATH}/assets/media/logos/logo4s-02.svg" style="height: 25px;">
                <i class="text-muted" style="font-size: 12px;">
                    Version {ADMIN_VERSION_UPDATE}
                </i>
            </a>
            
        </li>

        <li>
            <a nh-admin-bar-action="clear-cache-website" href="javascript:;">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M6,9 L6,15 L10,15 L10,9 L6,9 Z M6.25,7 L19.75,7 C20.9926407,7 22,7.81402773 22,8.81818182 L22,15.1818182 C22,16.1859723 20.9926407,17 19.75,17 L6.25,17 C5.00735931,17 4,16.1859723 4,15.1818182 L4,8.81818182 C4,7.81402773 5.00735931,7 6.25,7 Z" fill="#000000" fill-rule="nonzero" transform="translate(13.000000, 12.000000) rotate(-45.000000) translate(-13.000000, -12.000000) "/>
                    </g>
                </svg>
                {__d('admin', 'xoa_cache_website')}
            </a>
        </li>

        <li>
            <a nh-admin-bar-action="clear-cache-browser" class="admin-clear-cache" href="javascript:;">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M15.9497475,3.80761184 L13.0246125,6.73274681 C12.2435639,7.51379539 12.2435639,8.78012535 13.0246125,9.56117394 L14.4388261,10.9753875 C15.2198746,11.7564361 16.4862046,11.7564361 17.2672532,10.9753875 L20.1923882,8.05025253 C20.7341101,10.0447871 20.2295941,12.2556873 18.674559,13.8107223 C16.8453326,15.6399488 14.1085592,16.0155296 11.8839934,14.9444337 L6.75735931,20.0710678 C5.97631073,20.8521164 4.70998077,20.8521164 3.92893219,20.0710678 C3.1478836,19.2900192 3.1478836,18.0236893 3.92893219,17.2426407 L9.05556629,12.1160066 C7.98447038,9.89144078 8.36005124,7.15466739 10.1892777,5.32544095 C11.7443127,3.77040588 13.9552129,3.26588995 15.9497475,3.80761184 Z" fill="#000000"/>
                        <path d="M16.6568542,5.92893219 L18.0710678,7.34314575 C18.4615921,7.73367004 18.4615921,8.36683502 18.0710678,8.75735931 L16.6913928,10.1370344 C16.3008685,10.5275587 15.6677035,10.5275587 15.2771792,10.1370344 L13.8629656,8.7228208 C13.4724413,8.33229651 13.4724413,7.69913153 13.8629656,7.30860724 L15.2426407,5.92893219 C15.633165,5.5384079 16.26633,5.5384079 16.6568542,5.92893219 Z" fill="#000000" opacity="0.3"/>
                    </g>
                </svg>
                {__d('admin', 'xoa_cache_trinh_duyet')}
            </a>
        </li>

        <li>
            <a nh-admin-bar-action="view-config-block" href="javascript:;">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect opacity="0.200000003" x="0" y="0" width="24" height="24"/>
                        <path d="M4.5,7 L9.5,7 C10.3284271,7 11,7.67157288 11,8.5 C11,9.32842712 10.3284271,10 9.5,10 L4.5,10 C3.67157288,10 3,9.32842712 3,8.5 C3,7.67157288 3.67157288,7 4.5,7 Z M13.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L13.5,18 C12.6715729,18 12,17.3284271 12,16.5 C12,15.6715729 12.6715729,15 13.5,15 Z" fill="#000000" opacity="0.3"/>
                        <path d="M17,11 C15.3431458,11 14,9.65685425 14,8 C14,6.34314575 15.3431458,5 17,5 C18.6568542,5 20,6.34314575 20,8 C20,9.65685425 18.6568542,11 17,11 Z M6,19 C4.34314575,19 3,17.6568542 3,16 C3,14.3431458 4.34314575,13 6,13 C7.65685425,13 9,14.3431458 9,16 C9,17.6568542 7.65685425,19 6,19 Z" fill="#000000"/>
                    </g>
                </svg>
                {__d('admin', 'xem_cau_hinh_block')}
            </a>
        </li>

        {if !empty($admin_name)}
            <li class="admin-account">
                <a href="{ADMIN_PATH}">
                    {__d('admin', 'chao')}: {$admin_name}
                </a>
            </li>
        {/if}
    </ul>
    <div class="toggle-admin" nh-admin-bar-action="minimize-admin-bar">
        <div class="up-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"/>
                    <path d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero"/>
                </g>
            </svg>
        </div>
        <div class="down-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"/>
                    <path d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000003, 11.999999) rotate(-180.000000) translate(-12.000003, -11.999999) "/>
                </g>
            </svg>
        </div>
    </div>
    <style type="text/css">
        .nh-admin-bar{
            background-color: #000;
            direction: ltr;
            color: #c3c4c7;
            font-weight: 400;
            line-height: 32px;
            height: 32px;
            width: 100%;
            min-width: 600px;
            z-index: 99999;
            font-size: 13px;
            top: 0;
            left: 0;
            position: fixed;
        }

        .nh-admin-bar ul{
            text-align: left;
            list-style: none;
            position: relative;
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .nh-admin-bar ul li{
            background: 0 0;
            clear: none;
            list-style: none;
            margin: 0;
            position: relative;
            text-indent: 0;
            display: inline-block;
        }
        .nh-admin-bar ul li a{
            padding: 0 15px;
        }

        .nh-admin-bar ul li:first-child{
            padding-left: 5px;
        }
        .nh-admin-bar ul li.close-admin-bar {
            padding-right: 5px;
        }

        .nh-admin-bar ul li.admin-account{
            float: right;
        }

        .nh-admin-bar ul li.minimize-admin-bar a,
        .nh-admin-bar ul li.close-admin-bar a{
            padding:  0 8px;
        }

        .nh-admin-bar ul li a{
            color: #fff;
            font-size: 13px;
            display: block;
        }

        .nh-admin-bar ul li a:hover{
            color: #c1c1c1;
        }

        .nh-admin-bar svg{
            height: 17px;
            width: 17px;
        }

        .nh-admin-bar svg g [fill]{
            fill: #fff;
        }

        .admin-close .nh-admin-bar{
            display: none;
        }
        .toggle-admin {
            position: absolute;
            top: 32px;
            right: 0;
            height: auto;
            line-height: initial;
            background: #000;
            border-radius: 0 0 4px 4px;
            width: 40px;
            text-align: center;
            cursor: pointer;
            padding-block: 3px;
            padding-top: 0;
            border-top: 1px solid #2b2b2b;
        }
        .admin-minimize .nh-admin-bar{
            -webkit-transition: top 0.8s ease-in-out;
            transition: top 0.8s ease-in-out;
            -webkit-transform: translate3d(0, -32px, 0);
            -moz-transform: translate3d(0, -32px, 0);
            -ms-transform: translate3d(0, -32px, 0);
            -o-transform: translate3d(0, -32px, 0);
            transform: translate3d(0, -32px, 0);
            -webkit-transition: transform 0.3s cubic-bezier(0.65, 0.05, 0.51, 0.82);
            transition: transform 0.3s cubic-bezier(0.65, 0.05, 0.51, 0.82);
        }
        .toggle-admin .down-toggle{
            display: none;
        }
        .admin-minimize .down-toggle{
            display: block;
        }
        .admin-minimize .up-toggle{
            display: none;
        }
    </style>
</div>

