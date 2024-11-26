{strip}
    {if !empty($structure)}
        {foreach from = $structure item = rows key = type}
            {if $type == {HEADER} || $type == {FOOTER}}
                <{$type}>
            {/if}
                {if !empty($rows)}
                    {foreach from = $rows item = row}
                        {if !empty($row.columns) && !empty($row.code)}
                            {$this->element('layout/row', ['row' => $row], [])}
                        {/if}
                    {/foreach}
                {/if}
            {if $type == {HEADER} || $type == {FOOTER}}
                </{$type}>
            {/if}
        {/foreach}
    {/if}
{/strip}