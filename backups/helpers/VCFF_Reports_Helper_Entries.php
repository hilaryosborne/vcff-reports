<?php

class VCFF_Reports_Helper_Entries {

    public $form_instance;
    
    public $action_instance;
    
    public function Set_Form_Instance($form_instance) {
        
        $this->form_instance = $form_instance;
        
        return $this;
    }
    
    public function Set_Action_Instance($action_instance) {
        
        $this->action_instance = $action_instance;
        
        return $this;
    }

    public function Get_Last_Entry() {}
    
    public function Get_Entry_Count() {}
    
    protected function _Get_Triggered_Tags() {
        
        // Retrieve the reports global
        $vcff_reports = vcff_get_library('vcff_reports');

        $tags = $vcff_reports->tags;
        
        if (!$tags || !is_array($tags)) { return; }
        
        $instances = array();
        
        foreach ($tags as $code => $class_name) {
        
            $tag_instance = new $class_name();
            
            $tag_instance->form_instance = $this->form_instance;
            
            $tag_instance->action_instance = $this->action_instance;
            
            $tag_instance->event_instance = $this->event_instance;
            // If the tag instance is not used on create
            if (!$tag_instance->On_Create()) { continue; }
            
            $instances[$code] = $tag_instance;
        }
        
        return $instances;
    }
    
    public function Create($params) {
        // Retrieve the form instance
        $form_instance = $this->form_instance;
        // Retrieve the list of form fields
        $form_fields = $form_instance->fields;
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // If there are no form fields
		if (!$form_fields || !is_array($form_fields)) { return; }
        // To populate with the field values
        $field_values = array();
		// Loop through each containers
		foreach ($form_fields as $machine_code => $field_instance) {
			// If this field has a condition result and the field is hidden
			if ($field_instance->Is_Hidden()) { continue; }
			// If this field has a custom validation method
			$field_values[$machine_code] = $field_instance->Get_Value();
        }
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        // Insert a new entry
        $entry_instance = $reports_helper_sql->Create_Entry(array( 
            'form_uuid' => $form_instance->Get_UUID(), 
            'form_key' => $form_instance->Get_Security_Key(),
            'event_id' => $action_instance->Get_ID(), 
            'event_code' => $action_instance->Get_Code(), 
            'data_fields' => base64_encode(serialize($field_values)), 
            'data_additional' => base64_encode(serialize($params['additional'])), 
            'time_created' => time(), 
            'time_modified' => time(), 
        ));
        
        return true;
        // Retrieve the triggered tag instances
        $tag_instances = $this->_Get_Triggered_Tags();
        // If no tag instances to trigger on entry creation
        if (!$tag_instances && !is_array($tag_instances) && count($tag_instances) == 0) { return; }
        // Loop through each tag instance
        foreach ($tag_instances as $k => $tag_instance) {
            // If no on create method exists
            if (!method_exists('Tag_Entry',$tag_instance)) { continue; }
            // Tag the entry with the tag instance
            $tag_instance->Tag_Entry($entry_instance);
        }
    }
    
}   
