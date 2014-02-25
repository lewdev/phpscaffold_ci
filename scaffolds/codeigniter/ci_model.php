<?php
function ci_model($table) {
    $column_array = array();
    $returnStr = '';

    $returnStr = "<?php
/**
* {$table['website_name']}
* @author: {$table['author']}
* @company: {$table['company_name']}
* @created: ".date("Y-m-d",time())."
* @revised: ".date("Y-m-d",time())."
* {$table['table_clean']} Model
*/
class {$table['className']}_model extends Model {

function {$table['className']}_model() {
    parent::Model();
    \$load->database();
}

function get(\$id) {
    \$obj = FALSE;
    if (is_numeric(\$id)) {
        \$db->where('id', \$id);
        \$query = \$db->get('{$table['name']}');
        if (\$query->num_rows() > 0)
            \$obj = \$query->row();
    }
    return \$obj;
}

function save(\$obj) {
    \$obj = (object) \$obj;

    // Prepare the object (removes non-numbers for integers)\n";
foreach($table AS $key => $value) {
    if (is_array($value)) {
        $column = $key;
        $column_array[] = $key;
        if( strpos($table[$column]['attrib'], 'INT ') ||
            strpos($table[$column]['attrib'], 'DECIMAL ')):
            $returnStr .= "        \$obj->{$column} = preg_replace('/[^0-9\.]+/', '', \$obj->{$column});\n";
        endif;
    }
}
$returnStr .= "
    return (isset(\$obj->id) && \$obj->id > 0) ? \$_update(\$obj) : \$result = \$_create(\$obj);
}

function delete(\$obj) {
    \$db->where('id', \$obj->id);
    return \$db->delete('{$table['name']}');
}
function trash(\$obj) {
    \$obj->is_active = 0;
    \$obj->revised = date(\"Y-m-d H:i:s\", time());
    return \$save(\$obj);
}

function untrash(\$obj) {
    \$obj->is_active = 1;
    return \$save(\$obj);
}

function getLastID() {
    \$db->select('MAX(id) AS last_id');
    \$result = \$db->get('{$table['name']}')->result();
    return \$result[0]->last_id;
}

function _create(\$obj) {
    \$obj = (object) \$obj;
    \$obj->is_active = 1;
    \$obj->created = date(\"Y-m-d H:i:s\", time());
    \$obj->revised = \$obj->created;
    return \$db->insert('{$table['name']}', \$obj);
}

function _update(\$obj) {
    \$obj = (object) \$obj;
    \$obj->revised = date(\"Y-m-d H:i:s\", time());
    \$db->where('id', \$obj->id);
    return \$db->update('{$table['name']}', \$obj);
}

function _delete(\$obj) {
    \$obj = (object) \$obj;
    \$db->where('id', \$obj->id);
    return \$db->delete('{$table['name']}', \$obj);
}

function get_many(\$limit = 10, \$offset = 0) {
    \$objs = array();
    \$db->limit(\$limit);
    \$db->offset(\$offset);
    \$db->where('is_active', 1);
    \$db->order_by('created', 'desc');
    \$query = \$db->get('{$table['name']}');
    if (\$query->num_rows() > 0) {
        \$objs = \$query->result();
    }
    return \$objs;
}

function get_trash(\$limit = 10, \$offset = 0) {
    \$objs = array();
    \$db->limit(\$limit);
    \$db->offset(\$offset);
    \$db->where('is_active', 0);
    \$db->order_by('created', 'desc');
    \$query = \$db->get('{$table['name']}');
    if (\$query->num_rows() > 0) {
        \$objs = \$query->result();
    }
    return \$objs;
}

function count_all() {
    \$db->select('COUNT(id) AS count_all');
    \$db->where('is_active', 1);
    \$result = \$db->get('{$table['name']}')->result();
    return \$result[0]->count_all;
}

function count_trash() {
    \$db->select('COUNT(id) AS count_all');
    \$db->where('is_active', 0);
    \$result = \$db->get('{$table['name']}')->result();
    return \$result[0]->count_all;
}

function get_form() {
    \$form = array(
        'fields' => array(\n";
foreach($table AS $column => $value) {
    if (is_array($value) && $column != 'id') {
        $attrib = $table[$column]['attrib'];
        //get comments
        $returnStr .= "                '".$column."' => '".cleanText($column)."',";
        if( preg_match("/COMMENT\ '(.+)'\ /isU", $attrib, $match)) {
            $comment = str_replace('\\','',$match[1]);
            $returnStr .= " // ".$comment;
        }
        $returnStr .= "\n";
    }
}
$returnStr .= "            ),
        // rules: required|trim|exact_length[4]|max_length[n]|min_length[n]|matches[form_item]
        //    valid_email|valid_emails|numeric|integer|options[]|alpha|alpha_numeric|alpha_dash|
        'rules' => array( \n";
foreach($table AS $column => $value) {
    $delimiter = false;
    if (is_array($value) && $column != 'id') {
        $attrib = $table[$column]['attrib'];
        //required|numeric|trim|exact_length[4]|valid_email|alpha|options[]
        $returnStr .= "                '".$column."' => '";

        // if NOT NULL, then it must be required
        if( strpos($attrib, 'NOT NULL')):
            if($delimiter) $returnStr .= '|';
            $returnStr .= 'required';
            $delimiter = true;
        endif;

        // add rules based on datatype
        if( preg_match('/VARCHAR\(([0-9]+)\)/', $attrib, $match)):
            if($delimiter) $returnStr .= '|';
            $returnStr .= 'trim|htmlspecialchars|max_length['.$match[1].']';
            $delimiter = true;
        elseif( strpos($attrib, 'TEXT')):
            if($delimiter) $returnStr .= '|';
            $returnStr .= 'xss_clean';
            $delimiter = true;
        elseif( strpos($attrib, 'INT ')):
            if($delimiter) $returnStr .= '|';
            $returnStr .= 'integer';
            $delimiter = true;
        endif;

        // add rules based on common column names
        if( strpos($column, 'username') ):
            if($delimiter) $returnStr .= '|';
            $returnStr .= 'alpha_dash|min_length[3]';
            $delimiter = true;
        elseif( strpos($column, 'password') ):
            if($delimiter) $returnStr .= '|';
            $returnStr .= 'min_length[3]';
            $delimiter = true;
        elseif( strpos($column, 'email')):
            if($delimiter) $returnStr .= '|';
            $returnStr .= 'valid_email';
            $delimiter = true;
        endif;

        $returnStr .= "',\n";
    }
}
$returnStr .= "            ),
        'values' => array( // default values\n";
foreach($table AS $column => $value):
    $attrib = $table[$column]['attrib'];
    $val = '';
    if( preg_match("/DEFAULT\ '+(.+)'+ (COMMENT)+/isU", $attrib, $match))
        $val = $match[1];
    if (is_array($value))
        $returnStr .= "                '$column' => '$val',\n";
endforeach;
$returnStr .= "            ),
    );
    return \$form;
} //END get_form()
}";
    return $returnStr;
} // end ci_model