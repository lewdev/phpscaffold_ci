<?php
include("lib/Scaffold.php");
include("lib/stringParse.php");

$show_form = 0;
$message = '';
$new_layout_css = ''; // yes or ''

if( isset( $_POST['scaffold_info'])) {
    $data = trim($_POST['sql']);
    $data_lines = explode("\n", $data);
    #echo $data."<br/>";

    // strip all comments and get the first line
    $first = true;
    $firstline = '';
    foreach ($data_lines  AS $key => $value) {
        $value = trim($value);
        if ($value[0] == '-' && $value[1] == '-') unset($data_lines[$key]);
        elseif (stripos($value, 'insert into')) unset($data_lines[$key]);
        
        // find the first occurrence of "TABLE"
        if($first && strpos($value, "TABLE")) {
            $firstline = $value;
            $first = false;
        }
    }
    #echo "\$firstline = ".$firstline;

    $table = array();

    // store into cookie
    foreach($_POST AS $key => $value) {
        $date = time() + 999999;
        if ($key == 'sql') $date = time() + 600;
        setcookie($key, $value, $date, '/');
    }

    $table['include'] = stripslashes($_POST['include']);
    #$table['id_key'] = trim($_POST['id_key']);
    $table['scaffold_info'] = trim($_POST['scaffold_info']);
    $table['website_name'] = trim($_POST['website_name']);
    $table['table_prefix'] = trim($_POST['table_prefix']);
    $table['author'] = trim($_POST['author']);
    $table['company_name'] = trim($_POST['company_name']);
    $table['company_url'] = trim($_POST['company_url']);
    $new_layout_css = trim($_POST['new_layout_css']);
    #echo '<h1>\$new_layout_css = '.$new_layout_css.'</h1>';
    $_COOKIE = $_POST;

    // get first table name
    if ( preg_match('/CREATE\ +TABLE\ +(IF NOT EXISTS)?\ *`.+`\.`([a-zA-Z0-9_]+)`\ \(/isU', $firstline, $matches) ) {
        $table['name'] = str_replace($table['table_prefix'],'', $matches[2]);
        $max = count($data_lines);
        for ($i = 1; $i < $max; $i++ ) {
            if ( strpos( trim($data_lines[$i]), '`') === 0) { // this line has a column
                $col = find_text(trim($data_lines[$i]));
                $blob = ( stripos($data_lines[$i], 'TEXT') || stripos($data_lines[$i], 'BLOB') ) ? 1 : 0;
                $datetime = ( stripos($data_lines[$i], 'DATETIME') ) ? 1 : 0;
                $table[$col] = 
                    array(
                      'blob' => $blob
                    , 'datetime' => $datetime
                    , 'attrib' => $data_lines[$i]
                    );
            }
        }
        $show_form = 1;
        //print_r($table);
    } else
        $message .= "Cannot find 'CREATE TABLE IF NOT EXISTS (`.+`\.)?`([a-zA-Z0-9-_]+)` \('";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <script type="text/javascript" src="js/prototype.js"></script>
    <script type="text/javascript" src="js/scriptaculous.js"></script>
    <script type="text/javascript" src="js/s.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>PHP MySQL CRUD CI Scaffold</title>
    <meta name="Keywords" content="php, mysql, crud, scaffold" />
    <meta name="Description" content="Fast PHP CRUD Scaffold Maker" />
    <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1 href="javascript:void()" onclick="" style="cursor:hand;cursor:pointer;">php CI<span class="color">Scaffold</span></h1>
<div class="submenu">
<?php if ($show_form) { ?>
<a href="javascript:showNew();">Enter New Table</a> | <a href="javascript:showAll()">Show All</a> | <a href="javascript:hideAll()">Hide All</a>
<?php } else {?>
<?php } ?>
</div>

<div class="container">
<?php if ($message != '') echo "<div class=message>$message</div>"; ?>

<div <?php if ($show_form) echo 'style=display:none'; ?> id="new_table">
<form action="" method="post">
Welcome to <span class="style1">phpscaffold.com</span>, where you can quickly generate your CRUD scaffold pages for PHP and MySQL.
<br />
<br />

Enter your phpMyAdmin Table Export SQL  Below to generate your pages. <a href="javascript:showHint('sql_hint');">[Hint]</a>
  
<br />
<br />

<div id="sql_hint" style="display:none; ">
  <div style="background: #FFFFDD;padding: 5px; margin: 10px 0;">
  Paste your phpMyAdmin export SQL queries for the table your which to generate list, edit, new, and delete 
  pages in the box below. A sample text maybe:
  <pre style="color: #888; ">
-- 
-- Table structure for table `book`
-- 

CREATE  TABLE IF NOT EXISTS `book` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `title_index` VARCHAR(127) NOT NULL COMMENT 'SEF title, to appear on URL (i.e. \'osashizu\', \'ofudesaki\', \'mikagura-uta\', \'kyosoden\')' ,
  `title_jpn` VARCHAR(255) NOT NULL ,
  `title_jpn_ro` VARCHAR(255) NOT NULL ,
  `section_type` SET('chapter','volume','song','section','entry') NOT NULL ,
  `status` VARCHAR(45) NULL COMMENT 'draft, published' ,
  `description` TEXT NULL COMMENT 'should have descriptions in every language and describe the translated title.' ,
  `created` DATETIME NOT NULL ,
  `revised` DATETIME NOT NULL ,
  `is_active` TINYINT(1)  NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `title_index_UNIQUE` (`title_index` ASC) )
ENGINE = InnoDB; 
</pre>

</div>

</div>

<textarea name="sql" id="sql" cols="80" rows="10"
          ><?=((isset($_REQUEST['sql']))?stripslashes($_REQUEST['sql']):'')?></textarea>

<p>
    Include File Name. You create this file. <a href="javascript:showHint('include_hint');">[Example]</a><br /> 
    <input name="include" type="text" id="include" value="<?=((isset($_REQUEST['include']))?stripslashes($_REQUEST['include']):'config.php') ?>" />
</p>

<div id="include_hint" style="display:none; ">
<pre style="background:#FFFFDD;padding: 5px; margin: 10px 0; ">
// connect to db
$link = mysql_connect('localhost', 'mysql_user', 'mysql_password');
if (!$link) {
    die('Not connected : ' . mysql_error());
}

if (! mysql_select_db('foo') ) {
    die ('Can\'t use foo : ' . mysql_error());
}
</pre>
</div>    

<a href="javascript:showHint('customization');">[Customization]</a>
<div id="customization" style="display:none; ">
    <p>Website Name<br /> 
    <input name="website_name" type="text" id="website_name"
       value="<?=((isset($_REQUEST['website_name']))?stripslashes($_REQUEST['website_name']):'Website Name');?>"/>
    </p>
    <p>Table Prefix<br /> 
    <input name="table_prefix" type="text" id="table_prefix" 
       value="<?=((isset($_REQUEST['table_prefix']))?stripslashes($_REQUEST['table_prefix']):"trmd_");?>"/>
    </p>
    <p>Author<br /> 
    <input name="author" type="text" id="author" 
       value="<?=((isset($_REQUEST['author']))?stripslashes($_REQUEST['author']):"Author's Name");?>"/>
    </p>
    <p>Company Name<br /> 
    <input name="company_name" type="text" id="company_name" 
       value="<?=((isset($_REQUEST['company_name']))?stripslashes($_REQUEST['company_name']):'');?>" />
    </p>
    <p>Company URL<br /> 
    <input name="company_url" type="text" id="company_url" 
       value="<?=((isset($_REQUEST['company_url']))?stripslashes($_REQUEST['company_url']):'');?>" />
    </p>
    <p>
   <input name="new_layout_css" type="checkbox" id="new_layout" 
       value="yes" <?=((isset($_REQUEST['new_layout_css']) && $new_layout_css=='yes')?'checked':'')?> />
       Generate New Layout &amp; CSS files <br/>
       (views/layout_header.php, views/layout_footer.php &amp; css/style.css)<br /> 
    </p>
    <input name="scaffold_info" type="hidden" value="1" />
</div>
<input type="submit" value="Make My Pages" />
</form>
</div>

<?php
function printSection($section='', $text) {
    $secTitle = strtoupper(substr($section,0,1)).substr($section,1);
    $str = '
        <div class="options"><a href="javascript:toggle(\''.$section.'\');">Show/Hide</a>
        | <a href="javascript:selectAll(\''.$section.'\');">Select All';
    if (stripos($_SERVER["HTTP_USER_AGENT"], "msie") !== false)
        $str .= " &amp; Copy";
    $str .= '</a> | <a href="download.php">Download All Files</a></div>
        <h2>'.$secTitle.'</h2>
        <textarea rows="30" cols="80" wrap="off" class="textarea" id="'.$section.'">'.
            htmlspecialchars($text)
        .'</textarea>';
    return $str;
}

if ($show_form) {
    $s = new Scaffold($table);

    echo printSection('controller', $s->ci_controller());
    echo printSection('model', $s->ci_model());
    echo printSection('browse', $s->ci_view_browse());
    echo printSection('view', $s->ci_view());
    echo printSection('form', $s->ci_view_form());
    echo printSection('actionsnav', $s->ci_view_actions());
    if($new_layout_css=='yes') {
        echo printSection('header', $s->ci_header($_REQUEST['title']));
        echo printSection('footer', $s->ci_footer());
        echo printSection('css', $s->ci_css());
    }
}
?>

<br>
<br>
If you have any questions, contact me uprz23 &lt; the at sign &gt; gmail.com. Thanks for visiting.
</div>
</body>
</html>