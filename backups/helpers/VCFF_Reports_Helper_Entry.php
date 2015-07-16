<?php

class VCFF_Reports_Helper_Entry {
    
    public $entry_id;
    
    public $entry_data;
    
    public $form_instance;
    
    public $action_instance;
    
    public $event_instance;

    public $entry_instance;

    public function Set_Entry_ID($entry_id) {
    
        $this->entry_id = $entry_id;
        
        return $this;
    }

    protected function _Get_Form_Instance() {
        // Retrieve the entry data
        $entry_data = $this->entry_data;
        // Retrieve a new form instance helper
        $form_instance_helper = new VCFF_Forms_Helper_Instance();
        // Generate a new form instance
        $form_instance = $form_instance_helper
            ->Set_Form_UUID($entry_data->form_uuid)
            ->Set_Form_Data($this->_Get_Form_Data())
            ->Generate();  
        // If the form instance could not be created
        if (!$form_instance) { return $this; }
        // Complete setting up the form instance
        $form_instance_helper
            ->Add_Fields()
            ->Add_Containers()
            ->Add_Meta()
            ->Add_Events();
        // Cache the form instance
        $this->form_instance = $form_instance; 
    }
    
    protected function _Get_Form_Data() {
        // Retrieve the entry data
        $entry_data = $this->entry_data;
        // Retrieve the submission decoded fields
        $data =  unserialize(base64_decode($entry_data->data_fields));
        // If no usable data was returned
        if (!$data || !is_array($data)) { return ; }
        // Create a list for the html values
        $raw_values = array();
        // Loop through each of the returned data
        foreach ($data as $machine_code => $field_value) {
            // Populate with the value
            $raw_values[$machine_code] = $field_value;
        }
        // Return the html value
        return $raw_values;
    }
    
    protected function _Get_Action_Instance() {
        // Retrieve the form instance
        $form_instance = $this->form_instance; 
        // If no form instance, return out
        if (!$form_instance) { return; }
        // Retrieve the form's action items
        $events = $form_instance->events;
        // If no events were found, return out
        if (!$events || !is_array($events)) { return; }
        // Retrieve the entry data
        $entry_data = $this->entry_data;
        // Loop through each of the form's events
        foreach ($events as $action_instance) {
            // Retrieve the event instance
            $event_instance = $action_instance->Get_Selected_Event_Instance();
            // If the event is not an object or is not a report
            if (!is_object($event_instance) || !isset($event_instance->is_report) || $action_instance->Get_ID() != $entry_data->event_id) { continue; }
            // Save the event instance
            $this->action_instance = $action_instance;
        }
    }
    
    protected function _Get_Event_Instance() {
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // If no action instance, return out
        if (!$action_instance) { return; }
        // Populate the event instance
        $this->event_instance = $action_instance->Get_Selected_Event_Instance();
    }
    
    protected function _Get_Entry_Instance() {
        // Retrieve the event instance
        $event_instance = $this->event_instance;
        // If no event instance, return out
        if (!$event_instance) { return; }
        // Retrieve the entry data
        $entry_data = $this->entry_data;
        // Set a new entry object
        $entry_instance = new VCFF_Report_Entry();
        // Populate the entry instance
        $entry_instance
            ->Set_Form_Instance($this->form_instance)
            ->Set_Action_Instance($this->action_instance)
            ->Set_Entry_Data($entry_data);
        // Save the event instance
        $this->entry_instance = $entry_instance;
    }
    
    protected function _Get_Entry_Tag_Instances() {

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
            
            if (!$tag_instance->Has_Entry($this->entry_instance)) { continue; }
            
            $this->entry_instance->tag_instances[] = $tag_instance;
        }
    }
    
    public function Retrieve() {
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        // Retrieve the entry data
        $entry_data = $reports_helper_sql
            ->Select_Entry($this->entry_id);
        // If no entry data was retrieved
        if (!$entry_data || !is_object($entry_data)) { return ; }
        // Populate the entry data
        $this->entry_data = $entry_data;
        
        $this->_Get_Form_Instance();
        
        $this->_Get_Action_Instance();
        
        $this->_Get_Event_Instance();
        
        $this->_Get_Entry_Instance();
        
        $this->_Get_Entry_Tag_Instances();
        
        return $this->entry_instance;
    }
}