<?php
include("lib/stringParse.php");
include("lib/Scaffold.php");
include("lib/CreateZip.php");

if (isset($_COOKIE['scaffold_info'])) {
    $data = trim($_COOKIE['sql']);
    $data_lines = explode("\n", $data);

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
    $table['include'] = stripslashes($_COOKIE['include']);
    #$table['id_key'] = trim($_POST['id_key']);
    $table['scaffold_info'] = trim($_COOKIE['scaffold_info']);
    $table['website_name'] = trim($_COOKIE['website_name']);
    $table['table_prefix'] = trim($_COOKIE['table_prefix']);
    $table['author'] = trim($_COOKIE['author']);
    $table['company_name'] = trim($_COOKIE['company_name']);
    $new_layout_css = trim($_COOKIE['new_layout_css']);

    // get first table name
	$tablename_regex = "CREATE\ +TABLE\ +(IF NOT EXISTS)?\ *(`.+`\.)?`([a-zA-Z0-9_]+)`\ \(";
     if ( preg_match('/'.$tablename_regex.'/isU', $firstline, $matches) ) {
        $table['name'] = str_replace($table['table_prefix'],'', $matches[2]);
		echo $table['name'];
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
        $message .= "Cannot find '".$tablename_regex."'";
}

if ($show_form) {
    #$base = md5(rand(0,99999) + time());
    $base = date('Ymd-his');
    $s = new Scaffold($table);
    $s->download = true;

    /*file_put_contents( "temp/$base/{$table['list_page']}", $s->listtable() );
    file_put_contents( "temp/$base/{$table['new_page']}", $s->newrow() );
    file_put_contents( "temp/$base/{$table['edit_page']}", $s->editrow() );
    file_put_contents( "temp/$base/{$table['delete_page']}", $s->deleterow() );
    */
    $createZip = new CreateZip;
    $createZip->addFile($s->ci_controller(),  'controllers/'.$table['name'].'.php' ); 
    $createZip->addFile($s->ci_model(),       'models/'.$table['name'].'_model.php' ); 
    $createZip->addFile($s->ci_view(),        'views/'.$table['name'].'/'.$table['name'].'_view.php' ); 
    $createZip->addFile($s->ci_view_browse(), 'views/'.$table['name'].'/'.$table['name'].'_browse.php' ); 
    $createZip->addFile($s->ci_view_form(),   'views/'.$table['name'].'/'.$table['name'].'_form.php' ); 
    $createZip->addFile($s->ci_view_actions(),'views/'.$table['name'].'/'.$table['name'].'_actions.php' ); 
    if( $new_layout_css == "yes") {
        $createZip->addFile($s->ci_header(),      'views/layout_header.php' );
        $createZip->addFile($s->ci_footer(),      'views/layout_footer.php' );
        $createZip -> addFile($s->ci_css(),         'css/style.css' );
    }

    $fileName = "temp/".$table['name']."_".$base.".zip"; 

    $fd = fopen ($fileName, "wb"); 
    $out = fwrite ($fd, $createZip->getZippedfile() ); 
    fclose ($fd);
    $createZip->forceDownload($fileName); 

	//@unlink($fileName); 
} else {
    echo "something went wrong";
}