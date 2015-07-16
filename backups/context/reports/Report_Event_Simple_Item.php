<?php

class Report_Event_Simple_Item extends VCFF_Report_Item {
	
	public function Render() {
        // Retrieve the context director
        $action_dir = untrailingslashit( plugin_dir_path(__FILE__ ) );
        
        $form_instance = $this->form_instance;
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
    
    protected function _Get_Field_Items() {
        
        if (!isset($this->value['fields'])) { return; }
        
        return explode(',',$this->value['fields']);
    }
    
    protected function _Get_Field_List() {
        
        if (!isset($this->value['fields'])) { return; }
        
        return $this->value['fields'];
    }
    
    public function Get_Index_Fields() {
        // Retrieve the form instance
        $form_instance = $this->form_instance;
        // Return the form fields
        $form_fields = $form_instance->fields;
        // Retrieve the selected fields
        $selected_fields = $this->_Get_Field_Items();
        // If no selected fields were returned
        if (!$selected_fields || !is_array($selected_fields) || count($selected_fields) == 0) { return; }
        // The list to store fields
        $field_list = array();
        // Loop through each selected field
        foreach ($selected_fields as $k => $machine_code) {
            // If this field doesn't exist
            if (!isset($form_fields[$machine_code])) { continue; }
            // Populate the field list
            $field_list[$machine_code] = $form_fields[$machine_code];
        }
        // Return the field list
        return $field_list;
    }
    
    public function View_Render() {
    
        return 'Hey';
    }
    
    protected function _Get_Summary() {
    
        if (!isset($this->value['summary'])) { return; }
        
        return $this->value['summary'];
    }
    
    public function Trigger() { 
        // Retrieve the form instance
        $form_instance = $this->form_instance;
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // Create a new helper
        $reports_helper_entries = new VCFF_Reports_Helper_Entries();
        // Retrieve all of the reports
        $reports_helper_entries
            ->Set_Form_Instance($form_instance)
            ->Set_Action_Instance($action_instance)
            ->Create(array( 
                'additional' => array(
                    'summary' => $this->_Get_Summary()
                ), 
            ));
    }
}
