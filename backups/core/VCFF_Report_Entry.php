<?php

class VCFF_Report_Entry {

    public $form_instance;
    
    public $action_instance;
    
    public $entry_data;
    
    public function Set_Form_Instance($form_instance) {
        
        $this->form_instance = $form_instance;
        
        return $this;
    }
    
    public function Set_Action_Instance($action_instance) {
        
        $this->action_instance = $action_instance;
        
        return $this;
    }

    public function Set_Entry_Data($entry_data) {
        
        $this->entry_data = $entry_data;
        
        return $this;
    }
    
    protected function _Get_Decoded_Fields() {
        
        return unserialize(base64_decode($this->entry_data->data_fields));
    }
    
    public function Get_Status() {
        
        $status = $this->entry_data->status;
        
        switch ($status) {
            case 'unread' : return 'Unread'; break;
            case 'trash' : return 'Trash'; break;
            case 'archived' : return 'Archived'; break;
            case 'read' : return 'Read'; break;
        }
    }
    
    public function Get_ID() {
        
        return $this->entry_data->id;
    }
    
    public function Get_Action_ID() {
        
        return $this->entry_data->event_id;
    }
    
    public function Get_Form_ID() {
        
        return $this->entry_data->form_uuid;
    }
    
    public function Is_Flagged() {
        
        return $this->entry_data->flagged;
    }
    
    public function Get_Created_Date() {
        
        return date('jS F, Y', $this->entry_data->time_created);
    }
    
    public function Get_Updated_Date() {
        
        return date('jS F, Y', $this->entry_data->time_modified);
    }
    
    public function Get_Tag_Codes() {
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        
        $tag_records = $reports_helper_sql
            ->Select_Entry_Tags($this->Get_ID());
            
        $tag_list = array();
            
        foreach ($tag_records as $k => $record) {
            $tag_list[] = $record->tag_code;
        }
        
        return $tag_list;
    }
    
    public function Get_Field_Text_Value($machine_code) {
        
        $form_instance = $this->form_instance;
        
        $field_instance = $form_instance->fields[$machine_code];
        // Return the html value
        return $field_instance->Get_TEXT_Value();
    }
    
    public function Get_Field_HTML_Values() {
        // Retrieve the submission decoded fields
        $data = $this->_Get_Decoded_Fields();
        // If no usable data was returned
        if (!$data || !is_array($data)) { return ; }
        // Create a list for the html values
        $html_values = array();
        // Loop through each of the returned data
        foreach ($data as $machine_code => $field_values) {
            // If there is no HTML value, continue on
            if (!isset($field_values['html'])) { continue; }
            // Populate with the value
            $html_values[$machine_code] = $field_values['html'];
        }
        // Return the html value
        return $html_values;
    }

    public function Get_Field_Text_Values() {
        // Retrieve the submission decoded fields
        $data = $this->_Get_Decoded_Fields();
        // If no usable data was returned
        if (!$data || !is_array($data)) { return ; }
        // Create a list for the html values
        $text_values = array();
        // Loop through each of the returned data
        foreach ($data as $machine_code => $field_values) {
            // If there is no HTML value, continue on
            if (!isset($field_values['text'])) { continue; }
            // Populate with the value
            $text_values[$machine_code] = $field_values['text'];
        }
        // Return the html value
        return $text_values;
    }
    
    public function Get_Field_Raw_Values() {
        // Retrieve the submission decoded fields
        $data = $this->_Get_Decoded_Fields();
        // If no usable data was returned
        if (!$data || !is_array($data)) { return ; }
        // Create a list for the html values
        $raw_values = array();
        // Loop through each of the returned data
        foreach ($data as $machine_code => $field_values) {
            // If there is no HTML value, continue on
            if (!isset($field_values['raw'])) { continue; }
            // Populate with the value
            $raw_values[$machine_code] = $field_values['raw'];
        }
        // Return the html value
        return $raw_values;
    }

}