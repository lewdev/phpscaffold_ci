<?php
class Scaffold {

    public $table = array();
    public $table_clean = ''; // clean & readable table name
    public $className = ''; // used just for class declaration and construtor
    public $tablePlural = ''; // for ascthetics
    public $download = false;

    public $website_name = '';
    public $author = '';
    public $company_name = '';

    function Scaffold($table){
        $this->table = $table; // used for file names
        $this->table_clean = $this->cleanText($table['name']);
        $this->className = str_replace(' ','_',$this->cleanText($table['name']));

        // to get the table name's plural form
        $lastchar = substr($this->table_clean,strlen($this->table_clean)-1,1);
        if( $lastchar!='s') {
            if( $lastchar=='x')
                $this->tablePlural = substr($this->table_clean,0,strlen($this->table_clean)-1)."es";
            elseif( $lastchar=='y')
                $this->tablePlural = substr($this->table_clean,0,strlen($this->table_clean)-1)."ies";
            else
                $this->tablePlural = $this->cleanText($table['name']).'s';
        }
    }

    function ci_controller() {
        $returnStr = '';
//echo "<pre>";
//print_r($this->table);
//echo "</pre>";
        $returnStr = "<?php
/**
 * {$this->table['website_name']}
 * @author: {$this->table['author']}
 * @company: {$this->table['company_name']}
 * @created: ".date("Y-m-d",time())."
 * @revised: ".date("Y-m-d",time())."
 * {$this->table_clean} Controller
 */
class {$this->className} extends Controller {
    function {$this->className}() {
        parent::Controller();
        \$this->load->model('{$this->table['name']}_model', '{$this->table['name']}');
        \$this->load->library('validation');
        \$this->load->helper('form');
    }

    function index() {
        \$this->browse();
    }

    function view(\$id) {
        \$data = array();
        if (\$data['{$this->table['name']}'] = \$this->{$this->table['name']}->get(\$id)) {
            \$data['title'] = \"View {$this->table_clean}\"; // can be '\$data['{$this->table['name']}']['title']'
            \$data['{$this->table['name']}_form'] = \$this->{$this->table['name']}->get_form();
            \$this->load->view('layout_header', \$data);
            \$this->load->view('{$this->table['name']}/{$this->table['name']}_actions', \$data);
            \$this->load->view('{$this->table['name']}/{$this->table['name']}_view', \$data);
            \$this->load->view('layout_footer');
        } else {
            redirect('{$this->table['name']}/browse');
        }
    } // END view(\$id)

    function browse(\$page = 0) {
        \$data = array();

        // Get {$this->tablePlural}
        \$page_by = 10;
        \$data['title'] = \"Browse {$this->tablePlural}\";
        \$data['page_type'] = \"browse\";
        \$data['{$this->table['name']}_form'] = \$this->{$this->table['name']}->get_form();
        \$data['{$this->table['name']}_arr'] = \$this->{$this->table['name']}->get_many(\$page_by, \$page);
        \$data['{$this->table['name']}_total'] = \$this->{$this->table['name']}->count_all();
        if (\$page > 0 && empty(\$data['{$this->table['name']}_arr']))
            show_404(); // The user has gone too far with paging.

        // Pagination prep
        \$this->load->library('pagination');
        \$config['base_url'] = site_url('{$this->table['name']}/browse') .'/';
        \$config['total_rows'] = \$this->{$this->table['name']}->count_all();
        \$config['per_page'] = \$page_by;
        \$this->pagination->initialize(\$config);
        \$data['pager'] =& \$this->pagination; // Give the view a reference

        \$this->load->view('layout_header', \$data);
        \$this->load->view('{$this->table['name']}/{$this->table['name']}_actions', \$data);
        \$this->load->view('{$this->table['name']}/{$this->table['name']}_browse', \$data);
        \$this->load->view('layout_footer');
    } // END browse(\$page = 0)

    function trashcan(\$page = 0) {
        \$data = array();

        // Get {$this->table['name']} trash
        \$page_by = 10;
        \$data['title'] = \"Browse {$this->table_clean} Trash Can\";
        \$data['page_type'] = \"trashcan\";
        \$data['{$this->table['name']}_form'] = \$this->{$this->table['name']}->get_form();
        \$data['{$this->table['name']}_arr'] = \$this->{$this->table['name']}->get_trash(\$page_by, \$page);
        \$data['{$this->table['name']}_total'] = \$this->{$this->table['name']}->count_trash();
        if (\$page > 0 && empty(\$data['{$this->table['name']}_arr']))
            show_404(); // The user has gone too far with paging.

        // Pagination prep
        \$this->load->library('pagination');
        \$config['base_url'] = site_url('{$this->table['name']}/trashcan') .'/';
        \$config['total_rows'] = \$this->{$this->table['name']}->count_trash();
        \$config['per_page'] = \$page_by;
        \$this->pagination->initialize(\$config);
        \$data['pager'] =& \$this->pagination; // Give the view a reference

        \$this->load->view('layout_header', \$data);
        \$this->load->view('{$this->table['name']}/{$this->table['name']}_actions', \$data);
        \$this->load->view('{$this->table['name']}/{$this->table['name']}_browse', \$data);
        \$this->load->view('layout_footer');
    } // END trashcan()

    function delete(\$id) {
        \$data = array();
        if (\$obj = \$this->{$this->table['name']}->get(\$id)) {
            \$this->{$this->table['name']}->delete(\$obj);
            redirect('{$this->table['name']}/browse');
        } else {
            echo \"{$this->table['name']} ID \$id not found.\";
        }
    } // END delete(\$id)

    function trash(\$id) {
        \$data = array();
        if (\$obj = \$this->{$this->table['name']}->get(\$id)) {
            \$this->{$this->table['name']}->trash(\$obj);
            redirect('{$this->table['name']}/browse');
        } else {
            echo \"{$this->table['name']} ID \$id not found.\";
        }
    } // END trash(\$id)

    function untrash(\$id) {
        \$data = array();
        if (\$obj = \$this->{$this->table['name']}->get(\$id)) {
            \$this->{$this->table['name']}->untrash(\$obj);
            redirect('{$this->table['name']}/browse');
        } else {
            echo \"{$this->table['name']} ID \$id not found.\";
        }
    } // END untrash(\$id)

    function add() {
        \$data = \$this->{$this->table['name']}->get_form();
        \$this->validation->set_fields(\$data['fields']);
        unset(\$data['rules']['id']);
        \$this->validation->set_rules(\$data['rules']);

        \$data['title'] = 'Add a {$this->table_clean}';
        \$data['action'] = site_url('{$this->table['name']}/add');
        \$data['button_text'] = 'Add {$this->table_clean}';

        if ( ! \$this->validation->run()) {
            \$data['error'] = \$this->validation->error_string;
            if (\$this->validation->error_string) {
                \$data['values'] = \$_POST;
            }

            \$this->load->view('layout_header', \$data);
            \$this->load->view('{$this->table['name']}/{$this->table['name']}_actions', \$data);
            \$this->load->view('{$this->table['name']}/{$this->table['name']}_form', \$data);
            \$this->load->view('layout_footer');
        } else {
            if (\$this->{$this->table['name']}->save(\$_POST)) {
                redirect('{$this->table['name']}/browse');
            } else {
                \$data['error'] = \"Don't know why, but it failed\";
                \$data['values'] = \$_POST;
                
                \$this->load->view('layout_header', \$data);
                \$this->load->view('{$this->table['name']}/{$this->table['name']}_actions', \$data);
                \$this->load->view('{$this->table['name']}/{$this->table['name']}_form', \$data);
                \$this->load->view('layout_footer');
            }
        }
    } // END add()

    function edit(\$id = NULL) {
        if ( ! \${$this->table['name']} = \$this->{$this->table['name']}->get(\$id)) {
            show_404();
            return;
        }

        \$data = \$this->{$this->table['name']}->get_form();
        \$this->validation->set_fields(\$data['fields']);
        \$this->validation->set_rules(\$data['rules']);

        \$data['title'] = 'Edit {$this->table_clean}';
        \$data['action'] = site_url('{$this->table['name']}/edit/'. \$id);
        \$data['button_text'] = 'Save {$this->table_clean}';

        \$data['values'] = (array) \${$this->table['name']};

        if ( ! \$this->validation->run()) {
            \$data['error'] = \$this->validation->error_string;
            if (\$this->validation->error_string) {
                \$data['values'] = \$_POST;
            }
            \$this->load->view('layout_header', \$data);
            \$this->load->view('{$this->table['name']}/{$this->table['name']}_actions', \$data);
            \$this->load->view('{$this->table['name']}/{$this->table['name']}_form', \$data);
            \$this->load->view('layout_footer');
        }
        else
        {
            if (\$this->{$this->table['name']}->save(\$_POST)) {
                redirect('{$this->table['name']}/view/'. \$id);
            } else {
                \$data['error'] = \"Don't know why, but it failed\";
                \$data['error'] .= '<pre>'. print_r(\$_POST, TRUE) .'</pre>';
                \$data['values'] = \$_POST;
                \$this->load->view('layout_header', \$data );
                \$this->load->view('{$this->table['name']}/{$this->table['name']}_form', \$data);
                \$this->load->view('layout_footer');
            }
        }
    } // END edit(\$id = NULL)
} //END class {$this->table['name']}";
        return $returnStr;
    } // end ci_controller

    function ci_model() {
        $column_array = array();
        $returnStr = '';

        $returnStr = "<?php
/**
 * {$this->table['website_name']}
 * @author: {$this->table['author']}
 * @company: {$this->table['company_name']}
 * @created: ".date("Y-m-d",time())."
 * @revised: ".date("Y-m-d",time())."
 * {$this->table_clean} Model
 */
class {$this->className}_model extends Model {

    function {$this->className}_model() {
        parent::Model();
        \$this->load->database();
    }

    function get(\$id) {
        \$obj = FALSE;
        if (is_numeric(\$id)) {
            \$this->db->where('id', \$id);
            \$query = \$this->db->get('{$this->table['name']}');
            if (\$query->num_rows() > 0)
                \$obj = \$query->row();
        }
        return \$obj;
    }

    function save(\$obj) {
        \$obj = (object) \$obj;

        // Prepare the object (removes non-numbers for integers)\n";
    foreach($this->table AS $key => $value) {
        if (is_array($value)) {
            $column = $key;
            $column_array[] = $key;
            if( strpos($this->table[$column]['attrib'], 'INT ') ||
                strpos($this->table[$column]['attrib'], 'DECIMAL ')):
                $returnStr .= "        \$obj->{$column} = preg_replace('/[^0-9\.]+/', '', \$obj->{$column});\n";
            endif;
        }
    }
    $returnStr .= "
        return (isset(\$obj->id) && \$obj->id > 0) ? \$this->_update(\$obj) : \$result = \$this->_create(\$obj);
    }

    function delete(\$obj) {
        \$this->db->where('id', \$obj->id);
        return \$this->db->delete('{$this->table['name']}');
    }
    function trash(\$obj) {
        \$obj->is_active = 0;
        \$obj->revised = date(\"Y-m-d H:i:s\", time());
        return \$this->save(\$obj);
    }

    function untrash(\$obj) {
        \$obj->is_active = 1;
        return \$this->save(\$obj);
    }

    function getLastID() {
        \$this->db->select('MAX(id) AS last_id');
        \$result = \$this->db->get('{$this->table['name']}')->result();
        return \$result[0]->last_id;
    }

    function _create(\$obj) {
        \$obj = (object) \$obj;
        \$obj->is_active = 1;
        \$obj->created = date(\"Y-m-d H:i:s\", time());
        \$obj->revised = \$obj->created;
        return \$this->db->insert('{$this->table['name']}', \$obj);
    }

    function _update(\$obj) {
        \$obj = (object) \$obj;
        \$obj->revised = date(\"Y-m-d H:i:s\", time());
        \$this->db->where('id', \$obj->id);
        return \$this->db->update('{$this->table['name']}', \$obj);
    }

    function _delete(\$obj) {
        \$obj = (object) \$obj;
        \$this->db->where('id', \$obj->id);
        return \$this->db->delete('{$this->table['name']}', \$obj);
    }

    function get_many(\$limit = 10, \$offset = 0) {
        \$objs = array();
        \$this->db->limit(\$limit);
        \$this->db->offset(\$offset);
        \$this->db->where('is_active', 1);
        \$this->db->order_by('created', 'desc');
        \$query = \$this->db->get('{$this->table['name']}');
        if (\$query->num_rows() > 0) {
            \$objs = \$query->result();
        }
        return \$objs;
    }

    function get_trash(\$limit = 10, \$offset = 0) {
        \$objs = array();
        \$this->db->limit(\$limit);
        \$this->db->offset(\$offset);
        \$this->db->where('is_active', 0);
        \$this->db->order_by('created', 'desc');
        \$query = \$this->db->get('{$this->table['name']}');
        if (\$query->num_rows() > 0) {
            \$objs = \$query->result();
        }
        return \$objs;
    }

    function count_all() {
        \$this->db->select('COUNT(id) AS count_all');
        \$this->db->where('is_active', 1);
        \$result = \$this->db->get('{$this->table['name']}')->result();
        return \$result[0]->count_all;
    }

    function count_trash() {
        \$this->db->select('COUNT(id) AS count_all');
        \$this->db->where('is_active', 0);
        \$result = \$this->db->get('{$this->table['name']}')->result();
        return \$result[0]->count_all;
    }

    function get_form() {
        \$form = array(
            'fields' => array(\n";
    foreach($this->table AS $column => $value) {
        if (is_array($value) && $column != 'id') {
            $attrib = $this->table[$column]['attrib'];
            //get comments
            $returnStr .= "                '".$column."' => '".$this->cleanText($column)."',";
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
    foreach($this->table AS $column => $value) {
        $delimiter = false;
        if (is_array($value) && $column != 'id') {
            $attrib = $this->table[$column]['attrib'];
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
    foreach($this->table AS $column => $value):
        $attrib = $this->table[$column]['attrib'];
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

    function ci_header(){
        $str = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\" />
    <meta name=\"viewport\" content=\"width=device-width, minimum-scale=1.0, maximum-scale=1.0\" />
    <meta name=\"author\" content=\"{$this->table['author']}\">

    <title><?=(isset(\$title)?\$title.' | {$this->table['website_name']}':'{$this->table['website_name']}')?></title>

    <!-- The framework (optional) -->
    <link rel=\"stylesheet\" href=\"<?=\$this->config->item('base_url')?>css/inuit.css\" />
    <!-- Your extension -->
    <link rel=\"stylesheet\" href=\"<?=\$this->config->item('base_url')?>css/style.css\" />
    <!-- Favicons and the like (avoid using transparent .png) -->
    <link rel=\"shortcut icon\" href=\"<?=\$this->config->item('base_url')?>icon.png\" />
    <link rel=\"apple-touch-icon-precomposed\" href=\"<?=\$this->config->item('base_url')?>icon.png\" />
</head>
<body>
    <header>
    <h1 align=\"center\">{$this->table['website_name']}</h1>
    <nav>
        <ul id=\"nav\" class=\"centered dropdown cf\">
            <li><?=anchor('/', 'Home')?> </li>
            <li><?=anchor('{$this->table['name']}', '{$this->table_clean} App')?> </li>
        </ul>
    </nav>
    </header>
    <article>
";
        return $str;
    }

    function ci_view_browse() {
        $str = "<h2><?=\$title?></h2>

<? if( isset(\${$this->table['name']}_arr) && count(\${$this->table['name']}_arr)):?>
    <table cellspacing=\"0\" border=\"1\" class=\"browse_table\">
    <tr>
"; // get column header names
    foreach($this->table AS $key => $value) {
        if($key=='id' || $key=='is_active')
            ;
        elseif( is_array($value)) {
            $str .= "        <th><?=\${$this->table['name']}_form['fields']['$key']?></th>\n";
        }
    }
        $str .= "        <th>View/Edit/Delete/Trash</th>
    </tr>

    <? foreach (\${$this->table['name']}_arr as \${$this->table['name']}) :?>
        <tr>
"; // get values for each column
        foreach($this->table AS $key => $value) {
            $column = $key;
            if (is_array($value))
                if(strpos($this->table[$column]['attrib'], 'TIMESTAMP'))
                    $str .= "        <td><?=date(\"Y-m-d\",strtotime(\${$this->table['name']}->$column))?></td>\n";
                elseif($column=='id' || $column=='is_active')
                    ;
                else
                    $str .= "        <td><?=\${$this->table['name']}->$column?></td>\n";
        }
            $str .= "
            <td>
              <?=anchor(\"{$this->table['name']}/view/\". \${$this->table['name']}->id, 'View')?>
            / <?=anchor(\"{$this->table['name']}/edit/\". \${$this->table['name']}->id, 'Edit')?>
            / <?=anchor(\"{$this->table['name']}/delete/\". \${$this->table['name']}->id, 'Delete')?>
        <? if( \${$this->table['name']}->is_active):?>
            / <?=anchor(\"{$this->table['name']}/trash/\". \${$this->table['name']}->id, 'Trash')?>
        <? else:?>
            / <?=anchor(\"{$this->table['name']}/untrash/\". \${$this->table['name']}->id, 'Untrash')?>
        <? endif;?>
            </td>
        </tr>
    <? endforeach;?>
    </table>
    <div class=\"pager\">
        <?php print \$pager->create_links()?>
    </div>
    <p align=\"right\"><strong>Total:</strong> <?=\${$this->table['name']}_total?> {$this->tablePlural}
    <p><?=anchor(\"{$this->table['name']}/add\", \"Add a {$this->table_clean}\")?></p>
<? else:?>
    <? if( \$page_type == 'trashcan'):?>
        <p><em>The trash can is empty!</em></p>
    <? else:?>
        <p><em>No records found. Please <?=anchor(\"{$this->table['name']}/add\", \"add a {$this->table_clean}\")?>.</em></p>
    <? endif;?>
<? endif;?>
";
        return $str;
    }

    function ci_view(){
        $str = '';
        $str .= "
<h2><?=\$title?></h2>
<p align=\"right\">

    <?=anchor('{$this->table['name']}/edit/'.\${$this->table['name']}->id, 'Edit')?>
    <?=anchor('{$this->table['name']}/delete/'.\${$this->table['name']}->id, 'Delete')?>
    <? if( \${$this->table['name']}->is_active):?>   
        <?=anchor('{$this->table['name']}/trash/'.\${$this->table['name']}->id, 'Trash')?>
    <? else:?>
        <?=anchor('{$this->table['name']}/untrash/'.\${$this->table['name']}->id, 'Untrash')?>
    <? endif;?>
</p>
<table cellspacing=\"0\" border=\"0\" class=\"view_table\">";
    foreach($this->table AS $key => $value)
        if (is_array($value)) {
            $column = $key ;
            if( $column=='id' || $column=='is_active') {
                ;//do nothing
            } elseif( $column=='revised' || $column=='created') {
                $str .= "
<tr>
    <td><label for=\"$column'\"><?=\${$this->table['name']}_form['fields']['$column']?></label></td>
    <td><?=date(\"F d, Y H:i A\", strtotime(\${$this->table['name']}->$column))?></td>
</tr>";
            } else {
                $str .= "
<tr>
    <td><label for=\"$column'\"><?=\${$this->table['name']}_form['fields']['$column']?></label></td>
    <td><?=\${$this->table['name']}->$column?></td>
</tr>";
            }
        }
        $str .= "
</table>
";
        return $str;
    } // END ci_view()

    function ci_view_form(){
        $str = '';
        $str .= "<?php \$tabindex = 1;?>
<h2><?=\$title?></h2>

<div class=\"errors\">
    <?=\$error?>
</div>

<form method=\"post\" action=\"<?=\$action?>\"><fieldset>
<?=form_hidden('id', \$values['id'])?>
";
    foreach( $this->table AS $column => $value):
        if( is_array($value)) {
            $is_required = false;
            $attrib = $this->table[$column]['attrib'];
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
                //echo $this->table[$column]['attrib'];
                $options = array();
                $set = '';
                if(preg_match('/SET\((.)+\)/isU', $attrib, $matches)) {
                    $set = str_replace("SET(","",$matches[0]);
                    $set = str_replace("'","",$set);
                    $set = str_replace(")","",$set);
                    $options = explode(',',$set);
                    $strarr = "array(";
                    foreach($options as $option)
                        $strarr .= "'{$option}'=>'{$this->cleanText($option)}',";
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

<p><?=anchor('{$this->table['name']}', 'Cancel')?></p>
";
        return $str;
    }

    function ci_footer(){
        $str = "
    </article>
    <footer>
        <nav>
        <ul id=\"nav\" class=\"centered\">
            <li><?=anchor('/', 'Home')?> </li>
            <li><?=anchor('{$this->table['name']}', '{$this->tablePlural} App')?> </li>
        </ul>
        </nav>
        <p align=\"center\">";
        if( isset($this->table['company_url']) && $this->table['company_url'] != ''):
            $str .= "<a href=\"".$this->table['company_url']."\">".$this->table['company_name']."</a>";
        else:
            $str .= $this->table['company_name'];
        endif;
        $str .= ". Copyright <?=date(\"Y\",time())?> All Rights Reserved.</p>
    </footer>
</body>
</html>
";
        return $str;
    }

    function ci_css(){
        // css/style.css
        return "
/*------------------------------------*\
	MAIN
\*------------------------------------*/
html{
    font-family:\"Helvetica Neue\", Arial, sans-serif;
    color:#e4eef6;
    background:-moz-linear-gradient(-90deg,#5998c7,#4a8ec2) fixed;
    background:-webkit-gradient(linear,left top,left bottom,from(#5998c7),to(#4a8ec2)) fixed;
    background:black; /*#4a8ec2;*/
}
body{
    background:none;
    padding-top:50px;
    text-shadow:0 -1px 0 rgba(0,0,0,0.5), 0 1px 0 rgba(255,255,255,0.25);
    background-color:#000055;
    width:960px;
    margin-left: auto;
    margin-right: auto;
    padding:10px;
}
#page{
    margin:0 20px;
    float:none;
}
#nav { background-color:black; margin-left:auto; margin-right:auto; }
#nav li a {    padding:0 10px; }
#nav li li {
    padding:5px 10px;
    background-color:black;
    white-space:nowrap;
}
#nav li a:hover {
    text-decoration:none;
    background-color:gray;
}
article { }
article a { color:gray; 
    text-shadow:none; }
article a:hover { }
.browse td {
    padding:5px; margin:0px;
    font-size: 11pt;
}
.actionsnav {
	background-color:black;
	border:1px dotted white;
	float:right;
	font-size:10pt;
	padding:5px;
	margin:5px;
}
.actionsnav ul {
    margin-left:20px;
    margin-right:auto;
    margin-bottom:auto;
}
/*------------------------------------*\
	CENTRED NAV
\*------------------------------------*/
/*
http://csswizardry.com/2011/01/create-a-centred-horizontal-navigation/
Add a class of centred/centered to create a centred nav.
*/
#nav.centred,#nav.centered {
    text-align:center; }
#nav.centred li,#nav.centered li {
    display:inline;
    float:none;
}
#nav.centred a,#nav.centered a{
    display:inline-block;
}
/*------------------------------------*\
	TYPE
\*------------------------------------*/
h1{
    font-weight:bold;
    line-height:1;
}
a{ color:inherit; }

/*------------------------------------*\
	IMAGES
\*------------------------------------*/
#logo{ margin-bottom:1.5em; }

/*------------------------------------*\
	NARROW
\*------------------------------------*/
/* CSS for tablets and narrower devices */
@media (min-width: 721px) and (max-width: 960px){
}
/*--- END NARROW ---*/

/*------------------------------------*\
	MOBILE
\*------------------------------------*/
/* CSS for mobile devices. Linearise it! */
@media (max-width: 720px) {
    body{ font-size:0.75em; }
}
/*--- END MOBILE ---*/
";
    }

    # Lists the administrative actions available
    # /views/{table_name}_actions.php
    function ci_view_actions(){
        return "
<div class=\"actionsnav\">
<strong>{$this->table_clean} Admin</strong>
<ul>
    <li><?=anchor('{$this->table['name']}/browse', 'Browse {$this->tablePlural}')?> </li>
    <li><?=anchor('{$this->table['name']}/add', 'Add a {$this->table_clean}')?></li>
    <li><?=anchor('{$this->table['name']}/trashcan', 'Trash Can')?></li>
</ul>
</div>
";
    }

    function cleanText($name) {
        return ucwords(str_replace("_", " ", trim($name)));
    }
    function html_chars($var) {
        return ($this->download) ? $var : htmlspecialchars($var);
    }
}