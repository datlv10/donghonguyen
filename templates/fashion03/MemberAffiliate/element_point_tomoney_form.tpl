{assign var = config_point value = $this->Setting->getSettingWebsite('point')}

{assign var = point_to_money value = 1}
{if !empty($config_point.point_to_money)}
    {assign var = point_to_money value = $config_point.point_to_money}
{/if}

{assign var = point_max value = 1}
{if !empty($member.point)}
    {assign var = point_max value = $member.point}
{/if}

<form nh-form="point-tomoney" action="/member/affiliate/create-request-point-to-money" method="post" autocomplete="off">
    <div class="form-group">
        <label for="bank_id">
            {__d('template', 'ngan_hang')}
            <span class="required">*</span>
        </label>
        {$this->Form->select('bank_id', $this->Member->getListBankDropdown("{if !empty($member.id)}{$member.id}{/if}"), ['id' => 'bank_id', 'empty' => "-- {__d('template', 'ngan_hang')} --", 'class' => 'form-control form-control-sm selectpicker', 'data-size' => 10])}

    </div>

    <div class="form-group">
        <label for="point">
            {__d('template', 'diem')}: 
            <span class="required">*</span>
            <small class="text-muted font-weight-normal">
                ({__d('template', 'so_diem_co_the_rut')}: 
                {if !empty($member.point)}
                    {$member.point|number_format:0:".":","}
                {else}
                    0
                {/if}
                {__d('template', 'diem')})
            </small>
        </label>
        
        <div class="input-group">
            <input id="point" nh-point-money="{$point_to_money}" nh-point-max="{$point_max}" name="point" value="" type="text" class="form-control number-input" autocomplete="off">
            <div class="input-group-append">
                <span class="input-group-text input-group-main">
                    <span class="number-input point-to-money">0 </span>
                    <small>{CURRENCY_UNIT_DEFAULT}</small>
                </span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="note">
            {__d('template', 'ghi_chu')}: 
        </label>
        <input name="note" value="" type="text" class="form-control" autocomplete="off">
    </div>

    <div class="form-group btn-submit-form">
        <span nh-btn-action="submit" class="btn btn-submit">
            {__d('template', 'cap_nhat')}
        </span>
    </div>
</form>