<div class="list-attendance">
    <div class="header-attendance">
        <div class="d-flex mb-20 text-left">
            <button type="button" class="btn btn-sm btn-secondary border-radius-0 change-day-attendance {if !empty($attendance.number_day) && $attendance.number_day == 7}btn-brand{/if}" data-day="7">
                1 tuần
            </button>
            <button type="button" class="btn btn-sm btn-secondary border-radius-0 change-day-attendance {if !empty($attendance.number_day) && $attendance.number_day == 14}btn-brand{/if}" data-day="14">
                2 tuần
            </button>
            <button type="button" class="btn btn-sm btn-secondary border-radius-0 change-day-attendance {if !empty($attendance.number_day) && $attendance.number_day == 21}btn-brand{/if}" data-day="21">
                3 tuần
            </button>
            <button type="button" class="btn btn-sm btn-secondary border-radius-0 change-day-attendance {if !empty($attendance.number_day) && $attendance.number_day == 30}btn-brand{/if}" data-day="30">
                1 tháng
            </button>
            <button type="button" class="btn btn-sm btn-secondary border-radius-0 change-day-attendance {if !empty($attendance.number_day) && ($attendance.number_day != 7 && $attendance.number_day != 14 && $attendance.number_day != 21 && $attendance.number_day != 30)}btn-brand{/if}" data-day="1">
                Tùy chọn
            </button>
            <input type="text" name="number_day" class="kt-hidden number_day" value="{if !empty($attendance.number_day)}{$attendance.number_day}{/if}">

        </div>

        <div class="option-day mb-20">
            <input name="option_day" placeholder="{__d('admin', 'so_ngay')}" class="form-control form-control-sm {if !empty($attendance.number_day) && ($attendance.number_day == 7 || $attendance.number_day == 14 || $attendance.number_day == 21 || $attendance.number_day == 30)}kt-hidden{/if} mt-10 w-20" type="text" value="{if !empty($attendance.number_day)}{$attendance.number_day}{/if}">
        </div>
    </div>
    <div class="list-grid-day">
        <ul class="day-grid">
            {if !empty($attendance.point_config)}
                {foreach from = $attendance.point_config key = key item = point}
                    <li class="item-day item-grid" data-id="{$key + 1}">
                        <span class="day-number text-primary fs-12">Ngày {$key + 1}</span>
                        <a class="change-point-day">
                            <div data-toggle="popover" data-id="{$key + 1}" data-change-value="{if !empty($point)}{$point}{else}1{/if}" data-label="Điểm" class="cursor-p label-value nh-quick-change"> 
                                <span class="point-title">+ {if !empty($point)}{$point|number_format:0:".":","}{else}1{/if} điểm</span>
                            </div>
                            <input type="number" class="kt-hidden" name="point_config[]" value="{if !empty($point)}{$point}{else}1{/if}">
                        </a>
                    </li>
                {/foreach}
            {else}
                <li class="item-day item-grid" data-id="1">
                    <span class="day-number text-primary fs-12">Ngày 1</span>
                    <a class="change-point-day">
                        <div data-toggle="popover" data-id="1" data-change-value="1" data-label="Điểm" class="cursor-p label-value nh-quick-change"> 
                            <span class="point-title">+ 1 điểm</span>
                        </div>
                        <input type="number" class="kt-hidden" name="point_config[]" value="1">
                    </a>
                </li>

                <li class="item-day item-grid" data-id="2">
                    <span class="day-number text-primary fs-12">Ngày 2</span>
                    <a class="change-point-day">
                        <div data-toggle="popover" data-id="2" data-change-value="1" data-label="Điểm" class="cursor-p label-value nh-quick-change"> 
                            <span class="point-title">+ 1 điểm</span>
                        </div>
                        <input type="number" class="kt-hidden" name="point_config[]" value="1">
                    </a>
                </li>

                <li class="item-day item-grid" data-id="3">
                    <span class="day-number text-primary fs-12">Ngày 3</span>
                    <a class="change-point-day">
                        <div data-toggle="popover" data-id="3" data-change-value="1" data-label="Điểm" class="cursor-p label-value nh-quick-change"> 
                            <span class="point-title">+ 1 điểm</span>
                        </div>
                        <input type="number" class="kt-hidden" name="point_config[]" value="1">
                    </a>
                </li>

                <li class="item-day item-grid" data-id="4">
                    <span class="day-number text-primary fs-12">Ngày 4</span>
                    <a class="change-point-day">
                        <div data-toggle="popover" data-id="4" data-change-value="1" data-label="Điểm" class="cursor-p label-value nh-quick-change"> 
                            <span class="point-title">+ 1 điểm</span>
                        </div>
                        <input type="number" class="kt-hidden" name="point_config[]" value="1">
                    </a>
                </li>

                <li class="item-day item-grid" data-id="5">
                    <span class="day-number text-primary fs-12">Ngày 5</span>
                    <a class="change-point-day">
                        <div data-toggle="popover" data-id="5" data-change-value="1" data-label="Điểm" class="cursor-p label-value nh-quick-change"> 
                            <span class="point-title">+ 1 điểm</span>
                        </div>
                        <input type="number" class="kt-hidden" name="point_config[]" value="1">
                    </a>
                </li>

                <li class="item-day item-grid" data-id="6">
                    <span class="day-number text-primary fs-12">Ngày 6</span>
                    <a class="change-point-day">
                        <div data-toggle="popover" data-id="6" data-change-value="1" data-label="Điểm" class="cursor-p label-value nh-quick-change"> 
                            <span class="point-title">+ 1 điểm</span>
                        </div>
                        <input type="number" class="kt-hidden" name="point_config[]" value="1">
                    </a>
                </li>

                <li class="item-day item-grid" data-id="7">
                    <span class="day-number text-primary fs-12" data-id="1">Ngày 7</span>
                    <a class="change-point-day">
                        <div data-toggle="popover" data-id="7" data-change-value="1" data-label="Điểm" class="cursor-p label-value nh-quick-change"> 
                            <span class="point-title">+ 1 điểm</span>
                        </div>
                        <input type="number" class="kt-hidden" name="point_config[]" value="1">
                    </a>
                </li>
            {/if}
        </ul>
    </div>
</div>