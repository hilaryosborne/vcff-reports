<?php

class VCFF_Reports_Page_Report_View_Entry extends VCFF_Report_Page {
    
    public $form_instance;
    
    public $action_instance;
    
    public $entry_instance;
    
    public function __construct() {
        // Action to register the page
        add_action('admin_menu', array($this,'Register_Page'));
        // Register the vcff admin css
        vcff_admin_enqueue_script('report_view_entry', VCFF_REPORTS_URL.'/assets/admin/report_view_entry.js');
        // Register the vcff admin css
        vcff_admin_enqueue_style('report_view_entry', VCFF_REPORTS_URL.'/assets/admin/report_view_entry.css');
    }
    
    public function Register_Page() {
        // Add the page sub menu item
        add_submenu_page('', 'Reports', 'Reports', 'edit_posts', 'vcff_report_view_entry', array($this,'Render'));
    }
    
    public function Render() {
        // Create a entry helper
        $reports_helper_entry = new VCFF_Reports_Helper_Entry();
        // Retrieve the entry instance
        $entry_instance = $reports_helper_entry
            ->Set_Entry_ID($_GET['entry_id'])
            ->Retrieve();
        // Populate the form instance
        $this->form_instance = $entry_instance->form_instance;
        // Populate the action instance
        $this->action_instance = $entry_instance->action_instance;
        // Populate the event instance
        $this->event_instance = $entry_instance;
        // Retrieve the context director
        $tmp_dir = untrailingslashit(plugin_dir_path(__FILE__ ) );
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

new VCFF_Reports_Page_Report_View_Entry();