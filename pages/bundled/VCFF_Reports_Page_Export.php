<?php

class VCFF_Reports_Page_Export extends VCFF_Helper {
    
    public function __construct() {
        // Action to register the page
        add_action('admin_menu', array($this,'Register_Page'));
        // Action to register the page
        add_action('admin_init', function(){
        
            if (!$_POST || !isset($_POST['vcff_export'])) { return; }
        
            $this->_Download_Export();
        });
        
        // Action to register the page
        add_action('admin_init', function(){
        
            if (!$_POST || !isset($_POST['vcff_import'])) { return; }
        
            $this->_Upload_Import();
        });
        
        // Add the view event css
        add_action('admin_enqueue_scripts',function(){
            // Register the vcff admin css
        	wp_enqueue_style('vcff_page_import_export', VCFF_FORMS_URL.'/assets/admin/page_import_export.css');
        });
        
    }
    
    protected function _Download_Export() {
        // If the user is not an admin
        if (!vcff_is_admin()) { return; }
        // If no forms could be found, return out
        if (!isset($_POST['forms']) || !is_array($_POST['forms']) || count($_POST['forms']) == 0) { return; }
        // Create a new export helper
        $forms_helper_export = new VCFF_Forms_Helper_Export();
        // Retrieve the exported data
        $export_data = $forms_helper_export
            ->Export($_POST['forms'],$_POST['settings'])
            ->Get_Data(); 
        // Create the backup filename
        $filename = 'vcff-'.date('Y').date('m').date('d').'-'.time().'.json';
        // Set the headers for the file download
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        // Return the export string
        echo json_encode($export_data);
        // Exit out of wordpress
        exit();
    }
    
    protected function _Upload_Import() {
        // If the user is not an admin
        if (!vcff_is_admin()) { return; }
        // If no forms could be found, return out
        if (!isset($_FILES['import_file']) || !is_array($_FILES['import_file'])) { return; }
        // Retrieve the import data
        $import_data = file_get_contents($_FILES['import_file']['tmp_name']);
        // If no import data
        if (!$import_data) { return; }
        // Create a new export helper
        $forms_helper_import = new VCFF_Forms_Helper_Import(); 
        // Retrieve the exported data
        $forms_helper_import
            ->Set_Data(json_decode($import_data,true))
            ->Import($_POST['settings']);
        // Transfer the alerts
        $this->alerts = $forms_helper_import->alerts;
    }
    
    public function Register_Page() {
        // Add the page sub menu item
        add_submenu_page('edit.php?post_type=vcff_form', 'Import/Export', 'Import/Export', 'edit_posts', 'vcff_forms_import_export', array($this,'Render'));
    }

    protected function _Get_Form_List() {
        // Retrieve the global wordpress database layer
        global $wpdb;
        // Check the vcff_form post type exists
        if (!post_type_exists('vcff_form')){ return; } 
        // Retrieve a list of all the published vv forms
        $form_posts = $wpdb->get_results("SELECT ID, post_title 
	        FROM $wpdb->posts
	        WHERE post_status = 'publish' AND post_type = 'vcff_form'");
        // If no published posts were returned
        return $form_posts; 
    }
    
    public function Render() {
        // If the user is not an admin
        if (!vcff_is_admin()) { return; }
        
        $form_list = $this->_Get_Form_List();
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

new VCFF_Reports_Page_Export();