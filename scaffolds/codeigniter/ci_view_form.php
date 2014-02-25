<?php
function ci_view_form($table){
    $str = '';
    $str .= "<?php \$tabindex = 1;?>
<h2><?=\$title?></h2>

<div class=\"errors\">
    <?=\$error?>
</div>

<form method=\"post\" action=\"<?=\$action?>\"><fieldset>
<?=form_hidden('id', \$values['id'])?>
";
    foreach( $table AS $column => $value):
        if( is_array($value)) {
            $is_required = false;
            $attrib = $table[$column]['attrib'];
            if( strpos($attrib, 'NOT NULL'))
                $is_required = true;
            if( strpos($attrib, 'TEXT')) {
                $str .= "
    <div class=\"form-item\">
        <label for=\"{$column}\"><?=\$fields['{$column}']?></label>
        <div class=\"textarea\">
        <textarea name=\"{$column}\" rows=\"10\" cols=\"50\"
            tabindex=\"<?=\$tabindex++?>\"
            ><?=\$values['{$column}']?></textarea>
        ".($is_required?"<span class=\"required\">* required</span>":"")."
        </div>
    </div>";
        } elseif( $column == 'id') { 
            // do nothing
        } elseif( strpos($attrib, 'TIMESTAMP')
                &&strpos($attrib, 'revised')
                ||strpos($attrib, 'created')
                ) {
            //do nothing
        } elseif( strpos($attrib, 'is_active')) {
            //do nothing
        } elseif( strpos($attrib, 'DATE')) { 
            $str .= "
    <div class=\"form-item\">
        <label for=\"{$column}\"><?=\$fields['{$column}']?></label>
        <div class=\"inputdate\">
            <?=form_input('{$column}', \$values['{$column}'], 'tabindex='.\$tabindex++)?>
            ".($is_required?"<span class=\"required\">* required</span>":"")."
        </div>
    </div>";
        } elseif( strpos($attrib, 'DATETIME')) {
            $str .= "
    <div class=\"form-item\">
        <label for=\"{$column}\"><?=\$fields['{$column}']?></label>
        <div class=\"inputdatetime\">
            <?=form_input('{$column}', \$values['{$column}'], 'tabindex='.\$tabindex++)?>
            ".($is_required?"<span class=\"required\">* required</span>":"")."
        </div>
    </div>";
        } elseif( strpos($attrib, 'TINYINT(1)')) {
            $str .= "
    <div class=\"form-item\">
        <input type=\"checkbox\" id=\"{$column}\" tabindex=\"<?=\$tabindex++?>\"
            <?=((\$values['{$column}'])?'CHECKED':'')?> />
        <?#=form_checkbox('{$column}', \$values['{$column}'], ((\$values['{$column}'])?'true':'false'), 'tabindex='.\$tabindex++)?>
        <?=\$fields['{$column}']?>
        ".($is_required?"<span class=\"required\">* required</span>":"")."
    </div>";
        } elseif( strpos($attrib, 'SET(')) {
            //echo $table[$column]['attrib'];
            $options = array();
            $set = '';
            if(preg_match('/SET\((.)+\)/isU', $attrib, $matches)) {
                $set = str_replace("SET(","",$matches[0]);
                $set = str_replace("'","",$set);
                $set = str_replace(")","",$set);
                $set = str_replace("\\","",$set);
                $options = explode(',',$set);
                $strarr = "array(";
                foreach($options as $option)
                    $strarr .= "'{$option}'=>'{$cleanText($option)}',";
                $strarr .= ")";
                $str .= "
    <div class=\"form-item\">
        <label for=\"{$column}\"><?=\$fields['{$column}']?></label>
        <?=form_dropdown('{$column}', {$strarr}, \$values['{$column}'], 'tabindex='.\$tabindex++);?>
        ".($is_required?"<span class=\"required\">* required</span>":"")."
    </div>";
            }
        } else {
            $str .= "
    <div class=\"form-item\">
        <label for=\"{$column}\"><?=\$fields['{$column}']?></label>
        <div class=\"inputtext\">
            <?=form_input('{$column}', \$values['{$column}'], 'tabindex='.\$tabindex++)?>
            ".($is_required?"<span class=\"required\">* required</span>":"")."
        </div>
    </div>";
            }
        }
    endforeach;
        $str .= "
    <p><input type=\"submit\" value=\"<?=\$button_text?>\" class=\"button\" tabindex=\"<?=\$tabindex?>\" /></p>

</fieldset></form>

<p><?=anchor('{$table['name']}', 'Cancel')?></p>
";
    return $str;
}