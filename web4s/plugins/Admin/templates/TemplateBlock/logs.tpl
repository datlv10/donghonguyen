{if !empty($logs)}
    <div  class="kt-list-timeline__items">                            
        {foreach from = $logs item = log}
            {assign var = badge value = 'kt-list-timeline__badge--primary'}

            {if !empty($log.action) && $log.action == 'add'}
                {$badge = 'kt-list-timeline__badge--success'}
            {/if}

            {if !empty($log.action) && $log.action == 'delete'}
                {$badge = 'kt-list-timeline__badge--danger'}
            {/if}

            {if !empty($log.action) && $log.action == 'update_status'}
                {$badge = 'kt-list-timeline__badge--warning'}
            {/if}

            <div class="kt-list-timeline__item">
                <span class="kt-list-timeline__badge {$badge}"></span>
                <span class="kt-list-timeline__text">
                    {if !empty($log.user.full_name)}
                        {$log.user.full_name} - 
                    {/if}

                    {if !empty($log.description)}
                        {$log.description}
                    {/if}            
                </span>

                <span class="kt-list-timeline__time w-150px">
                    {if !empty($log.created_label)}
                        {$log.created_label}
                    {/if}
                </span>

                <span class="kt-list-timeline__time w-150px pl-10">                
                    <span nh-log="rollback" data-id="{if !empty($log.id)}{$log.id}{/if}" class="kt-badge kt-badge--success kt-badge--inline cursor-p">
                        {__d('admin', 'phuc_hoi_cau_hinh')}
                    </span>
                </span>
            </div>
        {/foreach}
    </div>

    {if !empty($pagination.pages) && $pagination.pages > 1}
        <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed mt-10 mb-10"></div>

        <div class="row">
            <div class="col-12">
                {$this->element('Admin.page/pagination')}
            </div>        
        </div>
        {/if}
{else}
    <i>
        {__d('admin', 'khong_tim_thay_thong_tin')}
    </i>
{/if}

