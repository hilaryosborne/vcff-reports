<?php

class VCFF_Reports_Page_Index extends VCFF_Page {
    
    public function __construct() {
        // Action to register the page
        add_action('admin_menu', array($this,'Register_Page'));
    }
    
    public function Register_Page() {
        // Add the page sub menu item
        add_submenu_page('edit.php?post_type=vcff_form', 'Reports/Entries', 'Reports/Entries', 'edit_posts', 'vcff_reports_form_index', array($this,'Render'));
    }
    
    protected function _Get_Reports() {
        // Retrieve the global wordpress database layer
        global $wpdb;
        // Retrieve a list of all the published vv forms
        $results = $wpdb->get_results("SELECT ID 
                FROM $wpdb->posts
                WHERE post_type = 'vcff_form'");
        // If no results were returned
        if (!$results || !is_array($results)) { return false; }
        // Loop through each of the results
        foreach ($results as $k => $row) {
            // Retrieve the form uuid
            $form_uuid = vcff_get_uuid_by_form($row->ID);
            // PREPARE PHASE
            $form_prepare_helper = new VCFF_Forms_Helper_Prepare();
            // Get the form instance
            $form_instance = $form_prepare_helper
                ->Get_Form(array(
                    'uuid' => $form_uuid,
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
            // If no events were found, return out
            if (!$form_events || !is_array($form_events) || count($form_events) < 1) { continue; }
            // The report data array 
            $_report = array(
                'form_uuid' => $form_instance->Get_UUID(),
                'form_type' => $form_instance->Get_Type(),
                'form_name' => $form_instance->Get_Name(),
                'form_instance' => $form_instance,
                'report_list' => array()
            );
            // Where we will store reports
            $_report_items = array(); $has_report_events = false;
            // Loop through each of the form's events
            foreach ($form_events as $action_instance) {
                // Retrieve the event instance
                $event_instance = $action_instance->Get_Selected_Event_Instance();
                // If the event is not an object or is not a report
                if (!is_object($event_instance) || !isset($event_instance->is_report)) { continue; }
                // Save the event instance
                $_report_items[] = array(
                    'report_name' => $action_instance->Get_Name(),
                    'report_description' => $action_instance->Get_Description(),
                    'report_id' => $action_instance->Get_ID(),
                    'report_code' => $action_instance->Get_Code(),
                    'entries_total' => $event_instance->Get_Entries(true),
                    'entries_unread' => $event_instance->Get_Unread_Entries(true),
                    'entries_last' => $event_instance->Get_Last_Entry(),
                    'action_instance' => $action_instance,
                    'event_instance' => $event_instance,
                );
                // Set the has report events flag to true
                $has_report_events = true;
            }
            // If no report events, continue on
            if (!$has_report_events) { continue; }
            // Add to the report data array
            $_report['report_list'] = $_report_items;
            // Add to the reports array
            $reports[] = $_report;
        }
        
        return $reports;
    }
    
    public function Render() {
        // Retrieve the report list
        $reports = $this->_Get_Reports();
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
    wp_enqueue_script('vcff_reports_form_index', VCFF_REPORTS_URL.'/assets/admin/vcff_reports_form_index.js');
    // Register the vcff admin css
    wp_enqueue_style('vcff_reports_form_index', VCFF_REPORTS_URL.'/assets/admin/vcff_reports_form_index.css');
});

new VCFF_Reports_Page_Index();