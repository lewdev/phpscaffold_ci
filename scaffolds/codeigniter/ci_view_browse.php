<?php
function ci_view_browse($table) {
    $str = "<h2><?=\$title?></h2>

<? if( isset(\${$table['name']}_arr) && count(\${$table['name']}_arr)):?>
    <table cellspacing=\"0\" border=\"1\" class=\"browse_table\">
    <tr>
"; // get column header names
    foreach($table AS $key => $value) {
        if($key=='id' || $key=='is_active')
            ;
        elseif( is_array($value)) {
            $str .= "        <th><?=\${$table['name']}_form['fields']['$key']?></th>\n";
        }
    }
        $str .= "        <th>View/Edit/Delete/Trash</th>
    </tr>

    <? foreach (\${$table['name']}_arr as \${$table['name']}) :?>
        <tr>
"; // get values for each column
        foreach($table AS $key => $value) {
            $column = $key;
            if (is_array($value))
                if(strpos($table[$column]['attrib'], 'TIMESTAMP'))
                    $str .= "        <td><?=date(\"Y-m-d\",strtotime(\${$table['name']}->$column))?></td>\n";
                elseif($column=='id' || $column=='is_active')
                    ;
                else
                    $str .= "        <td><?=\${$table['name']}->$column?></td>\n";
        }
            $str .= "
            <td>
              <?=anchor(\"{$table['name']}/view/\". \${$table['name']}->id, 'View')?>
            / <?=anchor(\"{$table['name']}/edit/\". \${$table['name']}->id, 'Edit')?>
            / <?=anchor(\"{$table['name']}/delete/\". \${$table['name']}->id, 'Delete')?>
        <? if( \${$table['name']}->is_active):?>
            / <?=anchor(\"{$table['name']}/trash/\". \${$table['name']}->id, 'Trash')?>
        <? else:?>
            / <?=anchor(\"{$table['name']}/untrash/\". \${$table['name']}->id, 'Untrash')?>
        <? endif;?>
            </td>
        </tr>
    <? endforeach;?>
    </table>
    <div class=\"pager\">
        <?php print \$pager->create_links()?>
    </div>
    <p align=\"right\"><strong>Total:</strong> <?=\${$table['name']}_total?> {$table['tablePlural']}
    <p><?=anchor(\"{$table['name']}/add\", \"Add a {$table['table_clean']}\")?></p>
<? else:?>
    <? if( \$page_type == 'trashcan'):?>
        <p><em>The trash can is empty!</em></p>
    <? else:?>
        <p><em>No records found. Please <?=anchor(\"{$table['name']}/add\", \"add a {$table['table_clean']}\")?>.</em></p>
    <? endif;?>
<? endif;?>
";
    return $str;
}