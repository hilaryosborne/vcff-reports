<?php

class VCFF_Reports_Helper_SQL_Entries {
    
    public $store_entry;
    
    public $store_fields;
    
    public $store_meta;
    
    public function Get_Entry_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        return $wpdb->prefix.VCFF_REPORTS_SQL_ENTRY_TBL; 
    }
    
    public function Get_Entry_Meta_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        return $wpdb->prefix.VCFF_REPORTS_SQL_ENTRY_META_TBL; 
    }
    
    public function Get_Entry_Field_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        return $wpdb->prefix.VCFF_REPORTS_SQL_ENTRY_FIELDS_TBL; 
    }

    public function Get_Entry() {
        // Return the current stored entry
        return $this->store_entry;
    }
    
    public function Get_Entry_Meta() {
        // Return the current stored meta
        return $this->store_meta;
    }
    
    public function Get_Entry_Fields() {
        // Return the current stored fields
        return $this->store_fields;
    }
    
    /**
     * UPKEEP FUNCTIONS
     * Functions used for maintaining the reports database
     */
    
    public function Upkeep() {
        
        $this->_Upkeep_Entry_Table();
        
        $this->_Upkeep_Entry_Meta_Table();
        
        $this->_Upkeep_Entry_Fields_Table();
    }
    
    protected function _Upkeep_Entry_Table() { 
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entry_Table(); 
        // Charset string
		$charset_collate = $wpdb->get_charset_collate();
        // SQL for the entries table
        $entries_table_sql = "CREATE TABLE $entries_table (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			uuid varchar(255) NOT NULL,
            form_uuid varchar(255) NOT NULL,
            form_type varchar(255) NOT NULL,
            event_id varchar(255) NOT NULL,
            event_code varchar(255) NOT NULL,
            source_url longtext NULL,
            user_agent longtext NULL,
            time_created bigint(20) UNSIGNED NOT NULL,
            time_modified bigint(20) UNSIGNED NULL,
            date_created date NOT NULL,
            date_modifed date NULL,
			submitted_ip varchar(32) NOT NULL,
            status varchar(32) NOT NULL,
			PRIMARY KEY (id)
		) ".$charset_collate.";"; 
        // Use the dbdelta to compare and upgrade
        dbDelta($entries_table_sql);
    }
    
    protected function _Upkeep_Entry_Meta_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entry_meta_table = $this->Get_Entry_Meta_Table(); 
        // Charset string
		$charset_collate = $wpdb->get_charset_collate();
        // SQL for the entries table
        $entries_meta_table_sql = "CREATE TABLE $entry_meta_table (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			entry_uuid varchar(255) NOT NULL,
            form_uuid varchar(255) NOT NULL,
            meta_code varchar(255) NOT NULL,
            is_encoded varchar(1) NOT NULL,
            meta_label longtext NULL,
            meta_value longtext NULL,
			PRIMARY KEY (id)
		) ".$charset_collate.";"; 
        // Use the dbdelta to compare and upgrade
        dbDelta($entries_meta_table_sql);
    }
    
    protected function _Upkeep_Entry_Fields_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entry_fields_table = $this->Get_Entry_Field_Table(); 
        // Charset string
		$charset_collate = $wpdb->get_charset_collate();
        // SQL for the entries table
        $entries_fields_table_sql = "CREATE TABLE $entry_fields_table (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			entry_uuid varchar(255) NOT NULL,
            form_uuid varchar(255) NOT NULL,
            field_machine_code varchar(255) NOT NULL,
            is_encoded varchar(1) NOT NULL,
            field_label longtext NULL,
            field_value longtext NULL,
            field_value_html longtext NULL,
            field_value_text longtext NULL,
			PRIMARY KEY (id)
		) ".$charset_collate.";"; 
        // Use the dbdelta to compare and upgrade
        dbDelta($entries_fields_table_sql);
    }
    
    /**
     * CREATE ENTRIES
     * Functions used in creating new entries
     */
    
    public function Add_Entry($data) {
        // Populate the entry data
        $this->store_entry = array(
            'id' => isset($data['id']) ? $data['id'] : null,
            'uuid' => isset($data['uuid']) ? $data['uuid'] : uniqid(),
            'form_uuid' => $data['form_uuid'],
            'form_type' => $data['form_type'],
            'event_id' => $data['event_id'],
            'event_code' => $data['event_code'],
            'source_url' => $data['source_url'],
            'user_agent' => isset($data['user_agent']) ? $data['user_agent'] : $this->_Get_User_Agent(),
            'time_created' => isset($data['time_created']) ? $data['time_created'] : time(),
            'time_modified' => isset($data['time_modified']) ? $data['time_modified'] : time(),
            'date_created' => isset($data['date_created']) ? $data['date_created'] : date("Y-m-d H:i:s"),
            'date_modifed' => isset($data['date_modifed']) ? $data['date_modifed'] : date("Y-m-d H:i:s"),
            'submitted_ip' => isset($data['submitted_ip']) ? $data['submitted_ip'] : $this->_Get_User_IP(),
            'status' => isset($data['status']) ? $data['status'] : 'complete',
        );
        // Return for chaining
        return $this;
    }

    public function Add_Meta_Item($data) {
        // Add to the store fields
        $this->store_meta[$data['meta_code']] = array(
            'meta_code' => $data['meta_code'],
            'meta_value' => $data['meta_value'],
            'meta_label' => $data['meta_label'],
        );
        // Return for chaining
        return $this;
    }
    
    public function Add_Field_Item($data) {
        // Add to the store fields
        $this->store_fields[$data['machine_code']] = array(
            'machine_code' => $data['machine_code'],
            'field_label' => $data['field_label'],
            'field_value' => $data['field_value'],
            'field_value_html' => $data['field_value_html'],
            'field_value_text' => $data['field_value_text'],
        ); 
        // Return for chaining
        return $this;
    }
    
    public function Store() {
        // Store the entry
        $this->_Store_Entry();
        // Store the meta
        $this->_Store_Meta();
        // Store the fields
        $this->_Store_Fields();
        // Return the stored entry
        return $this->store_entry;
    }
    
    protected function _Store_Entry() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entry_Table();
        // Submission table name (including wp prefix)
        $entry_fields_table = $this->Get_Entry_Field_Table(); 
        // Submission table name (including wp prefix)
        $entry_meta_table = $this->Get_Entry_Meta_Table(); 
        // Retrieve the storage data
        $store_data = $this->store_entry;
        // If there is an id
        if ($store_data['id']) {
            // Check for an existing record
            $existing = $wpdb->get_row($wpdb->prepare("SELECT id FROM $entries_table WHERE uuid = %d",$store_data['uuid']));
            // If a record was returned
            if ($existing) {
                // Attempt to store the entry data
                $result = $wpdb->update($entries_table,$store_data,array('uuid' => $store_data['uuid']));
                // If the insert failed
                if ($result === false) { die('Record failed to update entry'); }
                // Delete all of the associated fields
                $result = $wpdb->delete($entry_fields_table, array('entry_uuid' => $store_data['uuid']));
                // If the insert failed
                if ($result === false) { die('Record failed to purge entry values'); }
                // Delete all of the associated fields
                $result = $wpdb->delete($entry_meta_table, array('entry_uuid' => $store_data['uuid']));
                // If the insert failed
                if ($result === false) { die('Record failed to purge entry values'); }
            }
        } // Otherwise if we are inserting 
        else {
            // Attempt to store the entry data
            $result = $wpdb->insert($entries_table,$store_data);
            // If the insert failed
            if ($result === false) { die('Record failed to insert'); }
            // Store the result id
            $this->store_entry['id'] = $wpdb->insert_id;
        }
        // Return the inserted id
        return $store_data;
    }
    
    protected function _Store_Meta() {
        // Create the database table
		global $wpdb; 
        // Retrieve the entry data
        $entry_data = $this->store_entry;
        // Submission table name (including wp prefix)
        $entry_meta_table = $this->Get_Entry_Meta_Table(); 
        // Retrieve the storage data
        $store_meta = $this->store_meta;
        // If there are no store fields, return out
        if (!$store_meta || !is_array($store_meta)) { return array(); }
        // Loop through each field to store
        foreach ($store_meta as $meta_code => $meta_data) {
            // Determine if the value will be encoded
            $is_encoded = is_array($meta_data['meta_value']) || is_object($meta_data['meta_value']) ? true : false;
            // Calculate the store value
            $store_value = $is_encoded ? base64_encode(json_encode($meta_data['meta_value'])) : $meta_data['meta_value'] ;
            // If there is no value, continue on
            if ($store_value == '') { continue; }
            // Attempt to store the entry data
            $result = $wpdb->insert($entry_meta_table,array(
                'entry_uuid' => $entry_data['uuid'],
                'form_uuid' => $entry_data['form_uuid'],
                'is_encoded' => $is_encoded ? 'y' : '',
                'meta_code' => $meta_data['meta_code'],
                'meta_label' => $meta_data['meta_label'],
                'meta_value' => $store_value,
            ));
            // If the insert failed
            if ($result === false) { die('Field value failed to insert'); }
        }
    }
    
    protected function _Store_Fields() { 
        // Create the database table
		global $wpdb; 
        // Retrieve the entry data
        $entry_data = $this->store_entry;
        // Submission table name (including wp prefix)
        $entry_fields_table = $this->Get_Entry_Field_Table(); 
        // Retrieve the storage data
        $store_fields = $this->store_fields;
        // If there are no store fields, return out
        if (!$store_fields || !is_array($store_fields)) { return array(); }
        // Loop through each field to store
        foreach ($store_fields as $machine_code => $field_data) {
            // Determine if the value will be encoded
            $is_encoded = is_array($field_data['field_value']) || is_object($field_data['field_value']) ? true : false;
            // Calculate the store value
            $store_value = $is_encoded ? base64_encode(json_encode($field_data['field_value'])) : $field_data['field_value'] ;
            // If there is no value, continue on
            if ($store_value == '') { continue; }
            // Attempt to store the entry data
            $result = $wpdb->insert($entry_fields_table,array(
                'entry_uuid' => $entry_data['uuid'],
                'form_uuid' => $entry_data['form_uuid'],
                'is_encoded' => $is_encoded ? 'y' : '',
                'field_machine_code' => $machine_code,
                'field_label' => $field_data['field_label'],
                'field_value' => $store_value,
                'field_value_html' => $field_data['field_value_html'],
                'field_value_text' => $field_data['field_value_text'],
            ));
            // If the insert failed
            if ($result === false) { die('Field value failed to insert'); }
        }
    }
    
    protected function _Get_User_Agent() {
        // Return the user agent string
        return $_SERVER['HTTP_USER_AGENT'];
    }
    
    protected function _Get_User_IP () {
        // Check the following var
        if (getenv('HTTP_CLIENT_IP')) {
            // Return the var
            return getenv('HTTP_CLIENT_IP');
        } // Otherwise check the following var
        else if(getenv('HTTP_X_FORWARDED_FOR')) {
            // Return the var
            return getenv('HTTP_X_FORWARDED_FOR');
        } // Otherwise check the following var
        else if(getenv('HTTP_X_FORWARDED')) {
            // Return the var
            return getenv('HTTP_X_FORWARDED');
        } // Otherwise check the following var
        else if(getenv('HTTP_FORWARDED_FOR')) {
            // Return the var
            return getenv('HTTP_FORWARDED_FOR');
        } // Otherwise check the following var
        else if(getenv('HTTP_FORWARDED')) {
            // Return the var
            return getenv('HTTP_FORWARDED');
        } // Otherwise check the following var
        else if(getenv('REMOTE_ADDR')) {
            // Return the var
            return getenv('REMOTE_ADDR');
        } // Otherwise check the following var
        else { return 'UNKNOWN'; }
    }
    
    /**
     * RETRIEVE ENTRIES
     * Retrieve previously submitted entries
     */
    
    public function Get($uuid=false) {
        // If no uuid provided then use the stored one
        if (!$uuid) { $uuid = $this->store_entry['uuid']; }
        // If still no uuid
        if (!$uuid) { return false; }
        // Attempt to retrieve the entry data
        $this->_Get_Entry($uuid);
        // If no stored entry was found, return out
        if (!$this->store_entry || !isset($this->store_entry['id'])) { return false; }
        // Otherwise attempt to get the fields
        $this->_Get_Fields();
        // And get the meta
        $this->_Get_Meta();
        // Return for chaining
        return array(
            'store_entry' => $this->store_entry,
            'store_fields' => $this->store_fields,
            'store_meta' => $this->store_meta
        );
    }
    
    protected function _Get_Entry($uuid) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entry_Table();
        // Check for an existing record
        $entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM $entries_table WHERE uuid = %s",$uuid));
        // If no entry was found
        if (!$entry) { return; }
        // Populate the entry data
        $this->store_entry = array(
            'id' => $entry->id,
            'uuid' => $entry->uuid,
            'form_uuid' => $entry->form_uuid,
            'form_type' => $entry->form_type,
            'event_id' => $entry->event_id,
            'event_code' => $entry->event_code,
            'source_url' => $entry->source_url,
            'user_agent' => $entry->user_agent,
            'time_created' => $entry->time_created,
            'time_modified' => $entry->time_modified,
            'date_created' => $entry->date_created,
            'date_modifed' => $entry->date_modifed,
            'submitted_ip' => $entry->submitted_ip,
            'status' => $entry->status,
        );
    }
    
    protected function _Get_Fields() {
        // Create the database table
		global $wpdb; 
        // Retrieve the entry data
        $entry_data = $this->store_entry;
        // Submission table name (including wp prefix)
        $entries_fields_table = $this->Get_Entry_Field_Table();
        // Check for an existing record
        $fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM $entries_fields_table WHERE entry_uuid = %s",$entry_data['uuid']));
        // If no fields were returned
        if (!$fields || !is_array($fields)) { return; }
        // Loop through each field to store
        foreach ($fields as $k => $data) {
            // Retrieve the meta code
            $machine_code = $data->field_machine_code;
            // Retrieve the encoded flag
            $is_encoded = $data->is_encoded;
            // Retrieve the meta value
            $value = strtolower($is_encoded) == 'y' ? json_decode(base64_decode($data->field_value),true) : $data->field_value;
            // Populate the store fields
            $this->store_fields[$machine_code] = array(
                'id' => $data->id,
                'entry_uuid' => $data->entry_uuid,
                'form_uuid' => $data->form_uuid,
                'machine_code' => $machine_code,
                'field_label' => $data->field_label,
                'field_value' => $value,
                'field_value_html' => $data->field_value_html,
                'field_value_text' => $data->field_value_text
            );
        }
    }
    
    protected function _Get_Meta() {
        // Create the database table
		global $wpdb; 
        // Retrieve the entry data
        $entry_data = $this->store_entry;
        // Submission table name (including wp prefix)
        $entries_meta_table = $this->Get_Entry_Meta_Table();
        // Check for an existing record
        $metas = $wpdb->get_results($wpdb->prepare("SELECT * FROM $entries_meta_table WHERE entry_uuid = %s",$entry_data['uuid']));
        // If no fields were returned
        if (!$metas || !is_array($metas)) { return; }
        // Loop through each field to store
        foreach ($metas as $k => $data) {
            // Retrieve the meta code
            $meta_code = $data->meta_code;
            // Retrieve the encoded flag
            $is_encoded = $data->is_encoded;
            // Retrieve the meta value
            $meta_value = strtolower($is_encoded) == 'y' ? json_decode(base64_decode($data->meta_value),true) : $data->meta_value;
            // Populate the meta fields
            $this->store_meta[$meta_code] = array(
                'id' => $data->id,
                'entry_uuid' => $data->entry_uuid,
                'form_uuid' => $data->form_uuid,
                'meta_code' => $data->meta_code,
                'meta_value' => $meta_value,
                'meta_label' => $data->meta_label,
            );
        }
    }
    
    /**
     * DELETE ENTRIES
     * Deletes previously stored entries
     */
     
    public function Delete($uuid=false) {
        // If no uuid provided then use the stored one
        if (!$uuid) { $uuid = $this->store_entry['uuid']; }
        // If still no uuid
        if (!$uuid) { return $this; }
        // Create the database table
        global $wpdb;
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entry_Table();
        // Remove all entries
        $wpdb->delete($entries_table, array('uuid' => $uuid));
        // Submission table name (including wp prefix)
        $entries_fields_table = $this->Get_Entry_Field_Table();
        // Remove all entries
        $wpdb->delete($entries_fields_table, array('entry_uuid' => $uuid));
        // Submission table name (including wp prefix)
        $entries_meta_table = $this->Get_Entry_Meta_Table();
        // Remove all entries
        $wpdb->delete($entries_meta_table, array('entry_uuid' => $uuid));
        // Return for chaining
        return $this;
    }
}

$helper = new VCFF_Reports_Helper_SQL_Entries();

$helper->Upkeep();
