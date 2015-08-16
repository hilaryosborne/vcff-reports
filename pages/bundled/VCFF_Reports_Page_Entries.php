<?php

class VCFF_Reports_Page_Entries {
    
    protected $form_instance;
    
    protected $action_instance;
    
    protected $event_instance;
    
    protected $page_rows = 30;
    
    public function __construct() {
        // Action to register the page
        add_action('admin_menu', array($this,'Register_Page'));
    }
    
    public function Register_Page() {
        // Add the page sub menu item
        add_submenu_page('', 'Reports', 'Reports', 'edit_posts', 'vcff_reports_entries', array($this,'Render'));
    }

    protected function _Get_Instances() {
        // PREPARE PHASE
        $form_prepare_helper = new VCFF_Forms_Helper_Prepare();
        // Get the form instance
        $form_instance = $form_prepare_helper
            ->Get_Form(array(
                'uuid' => $_GET['form_uuid'],
            ));
        // If the form instance could not be created
        if (!$form_instance) { die('could not create form instance'); }
        // POPULATE PHASE
        $form_populate_helper = new VCFF_Forms_Helper_Populate();
        // Run the populate helper
        $form_populate_helper
            ->Set_Form_Instance($form_instance)
            ->Populate(array());
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
    
    protected function _Do_Bulk_Actions() {
        // If no post action, return out
        if (!$_POST['action'] || $_POST['action'] == '-1') { return; }
        // Retrieve the selected action
        $selected_action = $_POST['action'];
        // Retrieve the selected entries
        $selected_entries = $_POST['_entries'];
        // If no selected entries, return out
        if (!$selected_entries || !is_array($selected_entries)) { return; }
        // Create a new flag helper class
        $flag_helper = new VCFF_Reports_Helper_Flags();
        // Get a list of the valid flags
        $flags = $flag_helper->Get_Flags();
        // Loop through each flags
        foreach ($flags as $code => $_flag) {
            // If the flag is not allowed for actions
            if (!$_flag['show_in_actions'] || !isset($_flag['actions'])) { continue; }
            // Retrieve the actions
            $flag_actions = $_flag['actions'];
            // If the flag does not have the action, move on
            if (!isset($flag_actions[$selected_action])) { continue; }
            // Retrieve the selected flag action
            $flag_selected_action = $flag_actions[$selected_action];
            // Loop through each selected entry
            foreach ($selected_entries as $k => $uuid) {
                // Call the selected action
                call_user_func_array($flag_selected_action['callback'],array($uuid,$this)); 
            }
        }
    }
    
    protected function _Get_Pager() {
         // Retrieve the report id
        $report_id = $_GET['report_id'];
        // Which flag to show
        $dis_flag = $_GET['dis_flag'] ? $_GET['dis_flag'] : 'all';
        // What page number
        if ($_POST['dis_page']) {
            // Use the posted page number
            $page = (int) $_POST['dis_page'];
        } // If there is a get page number
        elseif ($_GET['dis_page']) {
            // Use the get page number
            $page = (int)$_GET['dis_page'];
        } // Otherwise set to the default page number
        else { $page = 1; }
        // Create a new flag helper class
        $flag_helper = new VCFF_Reports_Helper_Flags();
        // Get a list of the valid flags
        $valid_flags = $flag_helper->Get_Flags();
        // Check if the supplied flag is valid
        if (!isset($valid_flags[$dis_flag])) { die('not valid flag'); }
        // Retrieve the flag data
        $flag_data = $valid_flags[$dis_flag];
        // If the flag has it's own flag callable
        if (isset($flag_data['lookup']) && is_array($flag_data['lookup'])) {
            // Call the user function
            $entry_count = call_user_func_array($flag_data['lookup'],array($report_id,$dis_flag,false,false,true)); 
        } // Otherwise use the standard flag entry query 
        else {
            // Create a new report helper flag
            $reports_helper_flags = new VCFF_Reports_Helper_Flags();
            // Retrieve the relevant entries
            $entry_count = $reports_helper_flags->With_Flag($report_id,$dis_flag,false,false,true);    
        }
        // Calculate the total number of possible pages
        $total_pages = ceil($entry_count/$this->page_rows);
        // Return the pager array
        return array(
            'items' => $entry_count,
            'pages' => $total_pages,
            'pages_current' => $page,
            'pages_last' => $total_pages,
            'pages_first' => 1,
            'pages_next' => ($page+1) > $total_pages ? false : ($page+1),
            'pages_prev' => ($page-1) < 1 ? false : ($page-1)
        );
    }
    
    protected function _Get_Entries() {
        // Retrieve the report id
        $report_id = $_GET['report_id'];
        // Which flag to show
        $dis_flag = $_GET['dis_flag'] ? $_GET['dis_flag'] : 'all';
        // Any ordering rules
        $dis_ordering = $_GET['dis_ordering'] ? explode(',',$_GET['dis_ordering']) : array('time_created' => 'DESC');
        // What page number
        if ($_POST['dis_page']) {
            // Use the posted page number
            $page = (int) $_POST['dis_page'];
        } // If there is a get page number
        elseif ($_GET['dis_page']) {
            // Use the get page number
            $page = (int)$_GET['dis_page'];
        } // Otherwise set to the default page number
        else { $page = 1; }
        // Create the display page
        $dis_page = array((($page-1)*$this->page_rows),$this->page_rows);
        // Create a new flag helper class
        $flag_helper = new VCFF_Reports_Helper_Flags();
        // Get a list of the valid flags
        $valid_flags = $flag_helper->Get_Flags();
        // Check if the supplied flag is valid
        if (!isset($valid_flags[$dis_flag])) { die('not valid flag'); }
        // Retrieve the flag data
        $flag_data = $valid_flags[$dis_flag];
        // If the flag has it's own flag callable
        if (isset($flag_data['lookup']) && is_array($flag_data['lookup'])) {
            // Call the user function
            $entries = call_user_func_array($flag_data['lookup'],array($report_id,$dis_flag,$dis_ordering,$dis_page)); 
        } // Otherwise use the standard flag entry query 
        else {
            // Create a new report helper flag
            $reports_helper_flags = new VCFF_Reports_Helper_Flags();
            // Retrieve the relevant entries
            $entries = $reports_helper_flags->With_Flag($report_id,$dis_flag,$dis_ordering,$dis_page);    
        }  
        // The list of form entries
        $form_entries = array();
        // Loop through each entry
        foreach ($entries as $k => $sql_entry) {
            // Create a new entry helper
            $sql_entry_helper = new VCFF_Reports_Helper_SQL_Entries();
            // Retrieve the entry
            $_entry = $sql_entry_helper
                ->Get($sql_entry->uuid);
            // Create a new entry helper
            $flags_helper = new VCFF_Reports_Helper_Flags();
            // Retrieve any flags
            $_entry['store_flags'] = $flags_helper
                ->For_Entry($sql_entry->uuid);
            // Populate the form entries list
            $form_entries[] = $_entry;
        } 
        // Return the found fields
        return $form_entries;
    }
    
    protected function _Get_Flag_Count($flag) {
        // Retrieve the report id
        $report_id = $_GET['report_id'];
        // Create a new flag helper class
        $flag_helper = new VCFF_Reports_Helper_Flags();
        // Get a list of the valid flags
        $valid_flags = $flag_helper->Get_Flags();
        // Check if the supplied flag is valid
        if (!isset($valid_flags[$flag])) { die('not valid flag'); }
        // Retrieve the flag data
        $flag_data = $valid_flags[$flag];
        // If the flag has it's own flag callable
        if (isset($flag_data['lookup']) && is_array($flag_data['lookup'])) {
            // Call the user function
            return call_user_func_array($flag_data['lookup'],array($report_id,$flag,false,false,true)); 
        } // Otherwise use the standard flag entry query 
        else {
            // Create a new report helper flag
            $reports_helper_flags = new VCFF_Reports_Helper_Flags();
            // Retrieve the relevant entries
            return $reports_helper_flags->With_Flag($report_id,$flag,false,false,true);    
        }
    }
    
    public function Render() {
        // Get the various instances
        $this->_Get_Instances();
        // Do any bulk actions
        $this->_Do_Bulk_Actions();
        // Retrieve the table columns
        $fields = $this->event_instance->_Get_Field_Instances();
        // Retrieve the entries
        $entries = $this->_Get_Entries();
        
        $pagination = $this->_Get_Pager();
        // Create a new flags helper
        $flags_helper = new VCFF_Reports_Helper_Flags();
        // Retrieve the list of flags
        $flags = $flags_helper
            ->Get_Flags();
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