{if !empty($use_multiple_language)}
    {assign var = default_language value = $this->LanguageAdmin->getDefaultLanguage()}

    <div class="kt-langs__topbar kt-header__topbar kt-padding-r-0">
        <div class="kt-header__topbar-item kt-header__topbar-item--langs">

            {if !empty($list_languages)}
                <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
                    <span data-toggle="kt-tooltip" title="{if !empty($list_languages[$lang])}{$list_languages[$lang]}{/if}" data-placement="bottom" class="kt-header__topbar-icon {if $default_language != $lang}kt-pulse kt-pulse--danger{/if}">
                        <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{if !empty($list_languages[$lang])}{$list_languages[$lang]}{/if}" />
                        <span class="kt-pulse__ring"></span>
                    </span>
                </div>
                
                {if $list_languages|@count gte 2}
                    <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround">
                        <ul class="kt-nav kt-margin-t-10 kt-margin-b-10">
                            {foreach from = $list_languages key = key item = item}
                                {assign var = url_lang value = $this->SystemAdmin->getUrlVars('lang', {$key})}
                                <li class="kt-nav__item {if $lang eq $key}kt-nav__item--active{/if}">
                                    <a href="{$url_lang}" class="kt-nav__link nh-is-default">
                                        <span class="kt-nav__link-icon">
                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$key}.svg" alt="{$item}" />
                                        </span>
                                        <span class="kt-nav__link-text">{$item}</span>
                                    </a>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}
            {/if}
        </div> 
    </div>
{/if}