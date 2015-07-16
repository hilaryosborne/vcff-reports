<?php

class VCFF_Reports_Page_Entries {
    
    protected $form_instance;
    
    protected $action_instance;
    
    protected $event_instance;
    
    public function __construct() {
        // Action to register the page
        add_action('admin_menu', array($this,'Register_Page'));
    }
    
    public function Register_Page() {
        // Add the page sub menu item
        add_submenu_page('', 'Reports', 'Reports', 'edit_posts', 'vcff_reports_entries', array($this,'Render'));
    }
    
    protected function _Get_Instances() {
        // Retrieve a new form instance helper
        $form_instance_helper = new VCFF_Forms_Helper_Instance();
        // Generate a new form instance
        $form_instance = $form_instance_helper
            ->Set_Form_UUID($_GET['form_uuid'])
            ->Generate(); 
        // If the form instance could not be created
        if (!$form_instance) { die('could not create form instance'); }
        // Complete setting up the form instance
        $form_instance_helper
            ->Add_Fields()
            ->Add_Containers()
            ->Add_Meta()
            ->Add_Supports()
            ->Add_Events();
        // Retrieve the form's action items
        $form_events = $form_instance->events;
        // The report found flag
        $report_found = false;
        // Loop through each of the form's events
        foreach ($form_events as $action_instance) {
            // If this is not the action instance we are looking for
            if ($action_instance->Get_ID() != $_GET['report_id']) { continue; }
            // Retrieve the event instance
            $event_instance = $action_instance->Get_Selected_Event_Instance();
            // If the event is not an object or is not a report
            if (!is_object($event_instance) || !isset($event_instance->is_report)) { continue; }
            // Populate the form instace
            $this->form_instance = $form_instance;
            // Populate the form instace
            $this->action_instance = $action_instance;
            // Populate the form instace
            $this->event_instance = $event_instance;
            // Flag that we found the report
            $report_found = true;
        }
        // If no report was found
        if (!$report_found) { die('Report not found'); }
    }
    
    protected function _Get_Entries() {
        // Retrieve the report id
        $report_id = $_GET['report_id'];
        // Which flag to show
        $dis_flag = $_GET['dis_flag'] ? $_GET['dis_flag'] : 'all';
        // Any ordering rules
        $dis_ordering = $_GET['dis_ordering'] ? explode(',',$_GET['dis_ordering']) : false;
        // What page number
        $page = $_GET['dis_page'] ? (int)$_GET['dis_page'] : 1 ;
        // Create the display page
        $dis_page = array(30,($page-1*30));
        // Create a new flag helper class
        $flag_helper = new VCFF_Reports_Helper_Flags();
        // Get a list of the valid flags
        $valid_flags = $flag_helper->Get_Flags();
        // Check if the supplied flag is valid
        if (!isset($valid_flags[$dis_flag])) { die('not valid flag'); }
        // Retrieve the flag data
        $flag_data = $valid_flags[$dis_flag];
        // If the flag has it's own flag callable
        if (isset($flag_data['entry_items']) && is_array($flag_data['entry_items'])) {
            // Call the user function
            $fields = call_user_func_array($flag_data['entry_items'],array($report_id,$dis_flag,$dis_ordering,$dis_page));
        } // Otherwise use the standard flag entry query 
        else {
            // Create a new report helper flag
            $reports_helper_flags = new VCFF_Reports_Helper_Flags();
            // Retrieve the relevant entries
            $entries = $reports_helper_flags->With_Flag($report_id,$dis_flag,$dis_ordering,$dis_page);
            // Retrieve the table columns
            $fields = $this->event_instance->_Get_Field_Instances();
        }
        // Return the found fields
        return $fields;
    }
    
    public function Render() {
        // Get the various instances
        $this->_Get_Instances();
        // Retrieve the table columns
        $fields = $this->event_instance->_Get_Field_Instances();
        // Retrieve the entries
        $entries = $this->_Get_Entries();
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

add_action('admin_enqueue_scripts',function(){
    // Register the vcff admin css
    wp_enqueue_script('vcff_reports_entries', VCFF_REPORTS_URL.'/assets/admin/vcff_reports_entries.js');
    // Register the vcff admin css
    wp_enqueue_style('vcff_reports_entries', VCFF_REPORTS_URL.'/assets/admin/vcff_reports_entries.css');
});

new VCFF_Reports_Page_Entries();