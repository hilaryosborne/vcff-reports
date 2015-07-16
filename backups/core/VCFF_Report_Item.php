<?php

class VCFF_Report_Item extends VCFF_Event_Item {

    public $is_report = true;
    
    public function Get_Last_Entry() {
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // Retrieve the action instance id
        $action_instance_id = $action_instance->Get_ID();
        // Create a new reports helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL(); 
        // Return the current count
        $submission = $reports_helper_sql
            ->Get_Last_Entry($action_instance_id);
        // If no submission result was found
        if (!$submission || !is_object($submission)) { return 'No Submission'; }
        
        return date('d-m-Y',$submission->time_created);
    }
    
    public function Get_ALL_Entry_Count() {
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // Retrieve the action instance id
        $action_instance_id = $action_instance->Get_ID();
        // Create a new reports helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL(); 
        // Return the current count
        return (int)$reports_helper_sql
            ->Get_Entry_Count($action_instance_id);
    }
    
    public function Get_Unread_Entry_Count() {
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // Retrieve the action instance id
        $action_instance_id = $action_instance->Get_ID();
        // Create a new reports helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL(); 
        // Return the current count
        return (int)$reports_helper_sql
            ->Get_Entry_Count_Of_Status($action_instance_id,'unread');
    }
}