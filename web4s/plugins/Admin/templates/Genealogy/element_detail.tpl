<div class="modal-header">
    <h5 class="modal-title">
        Thông tin 
        {if !empty($genealogy.sex) && $genealogy.sex == 'male'}
            ông
        {else if !empty($genealogy.sex) && $genealogy.sex == 'female'}
            bà
        {/if} 
        {if !empty($genealogy.full_name)}
            {$genealogy.full_name}
        {/if}
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    </button>
</div>

<div class="modal-body">
    {if !empty($genealogy)}
        <div class="header-info">
            <div class="row">
                <div class="col-sm-4 col-12">
                    <div class="avatar">
                        {assign var = avatar value = '/admin/assets/media/users/default.jpg'}
                        {if !empty($genealogy.image_avatar)}
                            {assign var = avatar value = "{CDN_URL}{$genealogy.image_avatar}"}
                        {/if}

                        <img src="{$avatar}" alt="{if !empty($genealogy.full_name)}{$genealogy.full_name}{/if}">   
                    </div>
                </div>

                <div class="col-sm-8 col-12">
                    <div class="base">
                        <table class="table table-borderless table-striped">
                            <tbody>
                                <tr>
                                    <td scope="row" width="40%" class="kt-font-bold">Họ và tên</td>
                                    <td>
                                        {if !empty($genealogy.full_name)}
                                            {$genealogy.full_name}
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="row" width="40%" class="kt-font-bold">
                                        {if !empty($genealogy.relationship) && $genealogy.relationship == 2}
                                            Tên hiệu
                                        {else}
                                            Tên tự
                                        {/if}
                                    </td>
                                    <td>
                                        {if !empty($genealogy.self_name)}
                                            {$genealogy.self_name}
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="row" width="40%" class="kt-font-bold">Giới tính</td>
                                    <td>
                                        {if !empty($genealogy.sex_name)}
                                            {$genealogy.sex_name}
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="row" width="40%" class="kt-font-bold">Trình độ học vấn</td>
                                    <td>
                                        {if !empty($genealogy.education_level_name)}
                                            {$genealogy.education_level_name}
                                        {/if}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="inner mt-5">
            <table class="table table-borderless table-striped">
                <tbody>
                    <tr>
                        <td scope="row" width="20%" class="kt-font-bold">Tình trạng</td>
                        <td>
                            {if !empty($genealogy.status_name)}
                                {$genealogy.status_name}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td scope="row" width="20%" class="kt-font-bold">Nguồn gốc</td>
                        <td>
                            {if !empty($genealogy.description)}
                                {$genealogy.description}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td scope="row" width="20%" class="kt-font-bold">Năm sinh</td>
                        <td>
                            {if !empty($genealogy.year_of_birth)}
                                {$genealogy.year_of_birth}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td scope="row" width="20%" class="kt-font-bold">Năm mất</td>
                        <td>
                            {if !empty($genealogy.year_of_death)}
                                {$genealogy.year_of_death}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td scope="row" width="20%" class="kt-font-bold">An táng</td>
                        <td>
                            {if !empty($genealogy.burial)}
                                {$genealogy.burial}
                            {/if}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {if !empty($list_husband)}
            <div class="inner-wife">
                <div class="title">
                    <h4>Danh sách chồng</h4>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Thứ</th>
                            <th scope="col">Họ và tên chồng</th>
                            <th scope="col">Năm sinh</th>
                            <th scope="col" class="text-center">Tình trạng</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        {foreach from=$list_husband key=key item=husband}
                            <tr>
                                <td scope="row" class="text-center">
                                    {if !empty($husband.relationship_position)}
                                        {$husband.relationship_position}
                                    {/if}
                                </td>

                                <td>
                                    {if !empty($husband.full_name)}
                                        {$husband.full_name}
                                    {/if}
                                </td>

                                <td>
                                    {if !empty($husband.year_of_birth)}
                                        {$husband.year_of_birth}
                                    {else}
                                        Không rõ
                                    {/if}
                                </td>
                                <td class="text-center">
                                    {if !empty($husband.status_name)}
                                        {$husband.status_name}
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        {/if}

        {if !empty($list_wife)}
            <div class="inner-wife">
                <div class="title">
                    <h4>Danh sách vợ</h4>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Thứ</th>
                            <th scope="col">Họ và tên vợ</th>
                            <th scope="col">Năm sinh</th>
                            <th scope="col" class="text-center">Tình trạng</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        {foreach from=$list_wife key=key item=wife}
                            <tr>
                                <td scope="row" class="text-center">
                                    {if !empty($wife.relationship_position)}
                                        {$wife.relationship_position}
                                    {/if}
                                </td>

                                <td>
                                    {if !empty($wife.full_name)}
                                        {$wife.full_name}
                                    {/if}
                                </td>

                                <td>
                                    {if !empty($wife.year_of_birth)}
                                        {$wife.year_of_birth}
                                    {else}
                                        Không rõ
                                    {/if}
                                </td>
                                <td class="text-center">
                                    {if !empty($wife.status_name)}
                                        {$wife.status_name}
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        {/if}

        {if !empty($list_child)}
            <div class="inner-wife">
                <div class="title">
                    <h4>Danh sách con</h4>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Thứ</th>
                            <th scope="col">Họ và tên</th>
                            <th scope="col">Năm sinh</th>
                            <th scope="col">Giới tính</th>
                            <th scope="col" class="text-center">Tình trạng</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$list_child key=key item=child}
                            <tr>
                                <td scope="row" class="text-center">
                                    {if !empty($child.relationship_position)}
                                        {$child.relationship_position}
                                    {/if}
                                </td>

                                <td>
                                    {if !empty($child.full_name)}
                                        {$child.full_name}
                                    {/if}
                                </td>

                                <td>
                                    {if !empty($child.year_of_birth)}
                                        {$child.year_of_birth}
                                    {else}
                                        Không rõ
                                    {/if}
                                </td>

                                <td>
                                    {if !empty($child.sex_name)}
                                        {$child.sex_name}
                                    {/if}
                                </td>

                                <td class="text-center">
                                    {if !empty($child.status_name)}
                                        {$child.status_name}
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        {/if}

        {if !empty($genealogy.content)}
            <div class="inner-wife">
                <div class="title">
                    <h4>Tiểu sử, Sự nghiệp, Ghi chú</h4>
                </div>

                <div class="content">
                    {$genealogy.content}
                </div>
            </div>
        {/if}
    {else}
        Không lấy được thông tin bản ghi
    {/if}
</div>