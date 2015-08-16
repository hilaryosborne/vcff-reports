<?php

class VCFF_Reports_Helper_Entries {
    
    public $form_instance;
    
    public function Set_Form_Instance($form_instance) {
    
        $this->form_instance = $form_instance;
        
        return $this;
    }
    
    public function Set_Action_Instance($action_instance) {
    
        $this->action_instance = $action_instance;
        
        return $this;
    }
    
    public function Get_Status() {
        // The list of flags
        $flags = array(
            'complete' => array(
                'code' => 'complete',
                'name' => 'Complete',
                //'_update' => array($this,'_Status_Make_Complete'),
            ),
            'cool' => array(
                'code' => 'cool',
                'name' => 'Cool',
                //'_update' => array($this,'_Status_Make_Complete'),
            )
        );
        // Apply any filter for adding to the flag list
        $status = apply_filters('vcff_reports_status',$status,$this);
        // Return the flag list
        return $flags;
    }
    
    public function Last() {
        // Create the database table
		global $wpdb; 
        // Retrieve the form instance
        $form_instance = $this->form_instance;
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // Retrieve the action code
        $form_uuid = $form_instance->Get_UUID();
        // Retrieve the action code
        $action_code = $action_instance->Get_Code();
        // Create a new entry SQL helper
        $entries_sql_helper = new VCFF_Reports_Helper_SQL_Entries();
        // Submission table name (including wp prefix)
        $entries_table = $entries_sql_helper->Get_Entry_Table();
        // Check for an existing record
        $entry = $wpdb->get_row($wpdb->prepare("SELECT uuid FROM $entries_table WHERE form_uuid = %s AND event_code = %s ORDER BY time_created DESC LIMIT 1",$form_uuid,$action_code));
        // If no entry was found
        if (!$entry) { return false; }
        // Otherwise return the full entry data
        $last_entry = $entries_sql_helper->Get($entry->uuid);
        
        return array(
            'store_entry' => $last_entry->store_entry,
            'store_fields' => $last_entry->store_fields,
            'store_meta' => $last_entry->store_meta
        );
    }
    
    public function Update($entry) {
        // Create a new entries sql helper
        $sql_entries_helper = new VCFF_Reports_Helper_SQL_Entries();
        // Add the entry
        $sql_entries_helper
            ->Add_Entry($entry['store_entry']);
        // If there is meta to store
        if (isset($entry['store_fields']) && is_array($entry['store_fields'])) {
            // Loop through each field
            foreach ($entry['store_fields'] as $machine_code => $field_data) { 
                // Add the entry
                $sql_entries_helper->Add_Field_Item($field_data);
            }
        }
        // If there is meta to store
        if (isset($entry['store_meta']) && is_array($entry['store_meta'])) {
            // Loop through each field
            foreach ($entry['store_meta'] as $meta_code => $meta_data) {
                // Add the entry
                $sql_entries_helper->Add_Meta_Item($meta_data);
            }
        }
        // Store the changes
        $sql_entries_helper->Store();
    }
    
    public function Create($extra_meta=array()) {
        // Retrieve the form instance
        $form_instance = $this->form_instance;
        // Retrieve the form instance
        $action_instance = $this->action_instance;
        // Create a new entry SQL helper
        $entries_sql_helper = new VCFF_Reports_Helper_SQL_Entries();
        // Add the entry
        $entries_sql_helper
            ->Add_Entry(array(
                'form_uuid' => $form_instance->Get_UUID(),
                'form_type' => $form_instance->Get_Type(),
                'event_id' => $action_instance->Get_ID(),
                'event_code' => $action_instance->Get_Code(),
                'source_url' => 'http://something',
            ));
        // If extra meta information was supplied
        if ($extra_meta && is_array($extra_meta)) {
            // Loop through each submission meta item
            foreach ($extra_meta as $meta_key => $meta_data) {
                // Add the submission meta item
                $entries_sql_helper
                    ->Add_Meta_Item(array(
                        'meta_code' => $meta_data['meta_code'],
                        'meta_value' => $meta_data['meta_value'],
                        'meta_label' => $meta_data['meta_label'],
                    ));
            }
        }
        // Return the form fields
        $form_fields = $form_instance->fields;
        // Loop through each form field
        foreach ($form_fields as $machine_code => $field_instance) { 
            // Add the entry
            $entries_sql_helper
                ->Add_Field_Item(array(
                    'machine_code' => $machine_code,
                    'field_label' => $field_instance->Get_Label(),
                    'field_value' => $field_instance->Get_Value(),
                    'field_value_html' => $field_instance->Get_HTML_Value(false),
                    'field_value_text' => $field_instance->Get_TEXT_Value(false),
                ));
        }
        // Store and retrieve the stored entry
        $entry = $entries_sql_helper->Store();
        
        $flags_sql_helper = new VCFF_Reports_Helper_SQL_Flags();
        
        $flags_sql_helper
            ->Add_Flag(array(
                'entry_uuid' => $entry['uuid'],
                'form_uuid' => $form_instance->Get_UUID(),
                'flag_code' => 'unread',
                'flag_data' => array('hey'),
            ))
            ->Store();
        
        return $this;
    }
    
}