<?php
function ci_controller($table) {
    $returnStr = '';
//echo "<pre>";
//print_r($table);
//echo "</pre>";
    $returnStr = "<?php
/**
* {$table['website_name']}
* @author: {$table['author']}
* @company: {$table['company_name']}
* @created: ".date("Y-m-d",time())."
* @revised: ".date("Y-m-d",time())."
* {$table['table_clean']} Controller
*/
class {$table['className']} extends Controller {
function {$table['className']}() {
    parent::Controller();
    \$load->model('{$table['name']}_model', '{$table['name']}');
    \$load->library('validation');
    \$load->helper('form');
}

function index() {
    \$browse();
}

function view(\$id) {
    \$data = array();
    if (\$data['{$table['name']}'] = \${$table['name']}->get(\$id)) {
        \$data['title'] = \"View {$table['table_clean']}\"; // can be '\$data['{$table['name']}']['title']'
        \$data['{$table['name']}_form'] = \${$table['name']}->get_form();
        \$load->view('layout_header', \$data);
        \$load->view('{$table['name']}/{$table['name']}_actions', \$data);
        \$load->view('{$table['name']}/{$table['name']}_view', \$data);
        \$load->view('layout_footer');
    } else {
        redirect('{$table['name']}/browse');
    }
} // END view(\$id)

function browse(\$page = 0) {
    \$data = array();

    // Get {$table['tablePlural']}
    \$page_by = 10;
    \$data['title'] = \"Browse {$table['tablePlural']}\";
    \$data['page_type'] = \"browse\";
    \$data['{$table['name']}_form'] = \${$table['name']}->get_form();
    \$data['{$table['name']}_arr'] = \${$table['name']}->get_many(\$page_by, \$page);
    \$data['{$table['name']}_total'] = \${$table['name']}->count_all();
    if (\$page > 0 && empty(\$data['{$table['name']}_arr']))
        show_404(); // The user has gone too far with paging.

    // Pagination prep
    \$load->library('pagination');
    \$config['base_url'] = site_url('{$table['name']}/browse') .'/';
    \$config['total_rows'] = \${$table['name']}->count_all();
    \$config['per_page'] = \$page_by;
    \$pagination->initialize(\$config);
    \$data['pager'] =& \$pagination; // Give the view a reference

    \$load->view('layout_header', \$data);
    \$load->view('{$table['name']}/{$table['name']}_actions', \$data);
    \$load->view('{$table['name']}/{$table['name']}_browse', \$data);
    \$load->view('layout_footer');
} // END browse(\$page = 0)

function trashcan(\$page = 0) {
    \$data = array();

    // Get {$table['name']} trash
    \$page_by = 10;
    \$data['title'] = \"Browse {$table['table_clean']} Trash Can\";
    \$data['page_type'] = \"trashcan\";
    \$data['{$table['name']}_form'] = \${$table['name']}->get_form();
    \$data['{$table['name']}_arr'] = \${$table['name']}->get_trash(\$page_by, \$page);
    \$data['{$table['name']}_total'] = \${$table['name']}->count_trash();
    if (\$page > 0 && empty(\$data['{$table['name']}_arr']))
        show_404(); // The user has gone too far with paging.

    // Pagination prep
    \$load->library('pagination');
    \$config['base_url'] = site_url('{$table['name']}/trashcan') .'/';
    \$config['total_rows'] = \${$table['name']}->count_trash();
    \$config['per_page'] = \$page_by;
    \$pagination->initialize(\$config);
    \$data['pager'] =& \$pagination; // Give the view a reference

    \$load->view('layout_header', \$data);
    \$load->view('{$table['name']}/{$table['name']}_actions', \$data);
    \$load->view('{$table['name']}/{$table['name']}_browse', \$data);
    \$load->view('layout_footer');
} // END trashcan()

function delete(\$id) {
    \$data = array();
    if (\$obj = \${$table['name']}->get(\$id)) {
        \${$table['name']}->delete(\$obj);
        redirect('{$table['name']}/browse');
    } else {
        echo \"{$table['name']} ID \$id not found.\";
    }
} // END delete(\$id)

function trash(\$id) {
    \$data = array();
    if (\$obj = \${$table['name']}->get(\$id)) {
        \${$table['name']}->trash(\$obj);
        redirect('{$table['name']}/browse');
    } else {
        echo \"{$table['name']} ID \$id not found.\";
    }
} // END trash(\$id)

function untrash(\$id) {
    \$data = array();
    if (\$obj = \${$table['name']}->get(\$id)) {
        \${$table['name']}->untrash(\$obj);
        redirect('{$table['name']}/browse');
    } else {
        echo \"{$table['name']} ID \$id not found.\";
    }
} // END untrash(\$id)

function add() {
    \$data = \${$table['name']}->get_form();
    \$validation->set_fields(\$data['fields']);
    unset(\$data['rules']['id']);
    \$validation->set_rules(\$data['rules']);

    \$data['title'] = 'Add a {$table['table_clean']}';
    \$data['action'] = site_url('{$table['name']}/add');
    \$data['button_text'] = 'Add {$table['table_clean']}';

    if ( ! \$validation->run()) {
        \$data['error'] = \$validation->error_string;
        if (\$validation->error_string) {
            \$data['values'] = \$_POST;
        }

        \$load->view('layout_header', \$data);
        \$load->view('{$table['name']}/{$table['name']}_actions', \$data);
        \$load->view('{$table['name']}/{$table['name']}_form', \$data);
        \$load->view('layout_footer');
    } else {
        if (\${$table['name']}->save(\$_POST)) {
            redirect('{$table['name']}/browse');
        } else {
            \$data['error'] = \"Don't know why, but it failed\";
            \$data['values'] = \$_POST;

            \$load->view('layout_header', \$data);
            \$load->view('{$table['name']}/{$table['name']}_actions', \$data);
            \$load->view('{$table['name']}/{$table['name']}_form', \$data);
            \$load->view('layout_footer');
        }
    }
} // END add()

function edit(\$id = NULL) {
    if ( ! \${$table['name']} = \${$table['name']}->get(\$id)) {
        show_404();
        return;
    }

    \$data = \${$table['name']}->get_form();
    \$validation->set_fields(\$data['fields']);
    \$validation->set_rules(\$data['rules']);

    \$data['title'] = 'Edit {$table['table_clean']}';
    \$data['action'] = site_url('{$table['name']}/edit/'. \$id);
    \$data['button_text'] = 'Save {$table['table_clean']}';

    \$data['values'] = (array) \${$table['name']};

    if ( ! \$validation->run()) {
        \$data['error'] = \$validation->error_string;
        if (\$validation->error_string) {
            \$data['values'] = \$_POST;
        }
        \$load->view('layout_header', \$data);
        \$load->view('{$table['name']}/{$table['name']}_actions', \$data);
        \$load->view('{$table['name']}/{$table['name']}_form', \$data);
        \$load->view('layout_footer');
    }
    else
    {
        if (\${$table['name']}->save(\$_POST)) {
            redirect('{$table['name']}/view/'. \$id);
        } else {
            \$data['error'] = \"Don't know why, but it failed\";
            \$data['error'] .= '<pre>'. print_r(\$_POST, TRUE) .'</pre>';
            \$data['values'] = \$_POST;
            \$load->view('layout_header', \$data );
            \$load->view('{$table['name']}/{$table['name']}_form', \$data);
            \$load->view('layout_footer');
        }
    }
} // END edit(\$id = NULL)
} //END class {$table['name']}";
    return $returnStr;
} // end ci_controller
