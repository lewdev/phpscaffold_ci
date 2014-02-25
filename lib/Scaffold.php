<?php
/*
 * tablename
 * tablename_cleaned
 * tablename_singular
 * tablename_plural
 * tablecolumns
 *  name
 *  type
 *  features
 *  defalult
 * 
 * softdelete_on
 * created_at_on
 * modified_at_on
 * 
 * website_name
 * author
 * company_name
 * company_url
 */


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
    }

#call_user_func($varFunction);

/*
 * call_user_func_array ( callable $callback , array $param_arr )

 function foobar($arg, $arg2) {
    echo __FUNCTION__, " got $arg and $arg2\n";
}

// Call the foobar() function with 2 arguments
call_user_func_array("foobar", array("one", "two"));

 */


/*
    function cleanText($name) {
        return ucwords(str_replace("_", " ", trim($name)));
    }
    function html_chars($var) {
        return ($this->download) ? $var : htmlspecialchars($var);
    }
*/
}