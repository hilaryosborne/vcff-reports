<?php

class Report_Event_Simple_Item extends VCFF_Event_Item {
	
    public $is_report = true;
    
	public function Render() {
        // Retrieve the form instance
        $form_instance = $this->form_instance;
        // Retrieve the context director
        $action_dir = untrailingslashit( plugin_dir_path(__FILE__ ) );
        // Start gathering content
        ob_start();
        // Include the template file
        include($action_dir.'/'.get_class($this).'.tpl.php');
        // Get contents
        $output = ob_get_contents();
        // Clean up
        ob_end_clean();
        // Return the contents
        return $output;
    }
    
    public function _Get_Field_Items() {
        
        if (!isset($this->value['fields'])) { return; }
        
        return explode(',',$this->value['fields']);
    }
    
    public function _Get_Field_List() {
        
        if (!isset($this->value['fields'])) { return; }
        
        return $this->value['fields'];
    }
    
    public function _Get_Summary() {
        
        if (!isset($this->value['summary'])) { return; }
        
        return $this->value['summary'];
    }
    
    public function _Get_Field_Instances() {
        // Retrieve the form instance
        $form_instance = $this->form_instance;
        // Return the form fields
        $fields = $form_instance->fields;
        // Retrieve the selected fields
        $selected_fields = $this->_Get_Field_Items();
        // If no selected fields were returned
        if (!$selected_fields || !is_array($selected_fields) || count($selected_fields) == 0) { return; }
        // The list to store fields
        $field_instances = array();
        // Loop through each selected field
        foreach ($selected_fields as $k => $machine_code) {
            // If this field doesn't exist
            if (!isset($fields[$machine_code])) { continue; }
            // Populate the field list
            $field_instances[$machine_code] = $fields[$machine_code];
        }
        // Return the field list
        return $field_instances;
    }
    
    public function Get_Last_Entry() {
        // Retrieve the action instance
        $form_instance = $this->form_instance;
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // Create a new entries helper
        $entries_helper = new VCFF_Reports_Helper_Entries();
        // Return the last entry
        return $entries_helper
            ->Set_Form_Instance($form_instance)
            ->Set_Action_Instance($action_instance)
            ->Last();
    }
    
    public function Get_Entries($count=false) {
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // Retrieve the action code
        $report_code = $action_instance->Get_Code();
        // Create a new flag query helper
        $flag_query_helper = new VCFF_Reports_Helper_Query_Flags();
        // Use the query helper to find tagged entries
        return (int)$flag_query_helper
            ->Report($report_code)
            ->Without_Flags(array('trash','archieve'),'all')
            ->Find($count);
    }   
    
    public function Get_Unread_Entries($count=false) {
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // Retrieve the action code
        $report_code = $action_instance->Get_Code();
        // Create a new flag query helper
        $flag_query_helper = new VCFF_Reports_Helper_Query_Flags();
        // Use the query helper to find tagged entries
        return (int)$flag_query_helper
            ->Report($report_code)
            ->With_Flags(array('unread'),'all')
            ->Without_Flags(array('trash','archieve'),'all')
            ->Find($count);
    }
    
    public function Trigger() {
        // Retrieve the form instance
        $form_instance = $this->form_instance;
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // Create a new entries helper
        $reports_entry_helper = new VCFF_Reports_Helper_Entries();
        // Create a new database entry
        $reports_entry_helper
            ->Set_Form_Instance($form_instance)
            ->Set_Action_Instance($action_instance)
            ->Create(array(
                'form_data' => array(
                    'meta_code' => 'form_data',
                    'meta_value' => $form_instance->form_data,
                    'meta_label' => 'Raw Form Data'
                ),
                'form_content' => array(
                    'meta_code' => 'form_content',
                    'meta_value' => $form_instance->form_content,
                    'meta_label' => 'Raw Form Contents'
                )
            ));
    }
    
}
