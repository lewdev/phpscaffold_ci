<?php
function ci_view($table){
    $str = "
<h2><?=\$title?></h2>
<p align=\"right\">

    <?=anchor('{$table['name']}/edit/'.\${$table['name']}->id, 'Edit')?>
    <?=anchor('{$table['name']}/delete/'.\${$table['name']}->id, 'Delete')?>
    <? if( \${$table['name']}->is_active):?>   
        <?=anchor('{$table['name']}/trash/'.\${$table['name']}->id, 'Trash')?>
    <? else:?>
        <?=anchor('{$table['name']}/untrash/'.\${$table['name']}->id, 'Untrash')?>
    <? endif;?>
</p>
<table cellspacing=\"0\" border=\"0\" class=\"view_table\">";
    foreach($table AS $key => $value)
        if (is_array($value)) {
            $column = $key ;
            if( $column=='id' || $column=='is_active') {
                ;//do nothing
            } elseif( $column=='revised' || $column=='created') {
                $str .= "
<tr>
    <td><label for=\"$column'\"><?=\${$table['name']}_form['fields']['$column']?></label></td>
    <td><?=date(\"F d, Y H:i A\", strtotime(\${$table['name']}->$column))?></td>
</tr>";
            } else {
                $str .= "
<tr>
    <td><label for=\"$column'\"><?=\${$table['name']}_form['fields']['$column']?></label></td>
    <td><?=\${$table['name']}->$column?></td>
</tr>";
            }
        }
        $str .= "
</table>
";
    return $str;
} // END ci_view()