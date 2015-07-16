<?php

class VCFF_Reports_Page_Form_Index extends VCFF_Report_Page {
    
    public function __construct() {
        // Action to register the page
        add_action('admin_menu', array($this,'Register_Page'));
    }
    
    public function Register_Page() {
        // Add the page sub menu item
        add_submenu_page('edit.php?post_type=vcff_form', 'Reports', 'Reports', 'edit_posts', 'vcff_reports_form_index', array($this,'Render'));
    }
    
    public function Render() {
        // Retrieve the global wordpress database layer
        global $wpdb;
        // Retrieve a list of all the published vv forms
        $forms = $wpdb->get_results("SELECT ID, post_title 
                FROM $wpdb->posts
                WHERE post_status = 'publish'
                AND post_type = 'vcff_form'");
        // Retrieve the context director
        $tmp_dir = untrailingslashit( plugin_dir_path(__FILE__ ) );
        // Start gathering content
        ob_start();
        // Include the template file
        include(vcff_get_file_dir($tmp_dir.'/'.get_class($this).".tpl.php"));
        // Get contents
        $output = ob_get_contents();
        // Clean up
        ob_end_clean();
        // Return the contents
        echo $output;
    }
}



new VCFF_Reports_Page_Form_Index();