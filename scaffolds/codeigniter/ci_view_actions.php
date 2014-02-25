<?php
# Lists the administrative actions available
# /views/{table_name}_actions.php
function ci_view_actions($table){
    return "
<div class=\"actionsnav\">
<strong>{$table['table_clean']} Admin</strong>
<ul>
    <li><?=anchor('{$table['name']}/browse', 'Browse {$table['tablePlural']}')?> </li>
    <li><?=anchor('{$table['name']}/add', 'Add a {$table['table_clean']}')?></li>
    <li><?=anchor('{$table['name']}/trashcan', 'Trash Can')?></li>
</ul>
</div>
";
}