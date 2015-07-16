<?php

class VCFF_Reports_Helper_SQL_Flags {
    
    public $store_flag;
    
    public function Get_Flags_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        return $wpdb->prefix.VCFF_REPORTS_SQL_FLAGS_TBL; 
    }
    
    /**
     * UPKEEP FUNCTIONS
     * Functions used for maintaining the reports database
     */
    
    public function Upkeep() {
        
        $this->_Upkeep_Entry_Flags_Table();
    }
    
    protected function _Upkeep_Entry_Flags_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entry_flags_table = $this->Get_Flags_Table(); 
        // Charset string
		$charset_collate = $wpdb->get_charset_collate();
        // SQL for the entries table
        $entries_flags_table_sql = "CREATE TABLE $entry_flags_table (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            uuid varchar(255) NOT NULL,
			entry_uuid varchar(255) NOT NULL,
            form_uuid varchar(255) NOT NULL,
            flag_code varchar(255) NOT NULL,
            flag_data longtext NULL,
            time_created bigint(20) UNSIGNED NOT NULL,
            time_modified bigint(20) UNSIGNED NULL,
            date_created date NOT NULL,
            date_modifed date NULL,
			PRIMARY KEY (id)
		) ".$charset_collate.";"; 
        // Use the dbdelta to compare and upgrade
        dbDelta($entries_flags_table_sql);
    }
    
    /**
     * CREATE ENTRIES
     * Functions used in creating new entries
     */

    public function Add_Flag($data) {
        // Populate the entry data
        $this->store_flag = array(
            'id' => isset($data['id']) ? $data['id'] : null,
            'uuid' => isset($data['uuid']) ? $data['uuid'] : uniqid(),
            'entry_uuid' => $data['entry_uuid'],
            'form_uuid' => $data['form_uuid'],
            'flag_code' => $data['flag_code'],
            'flag_data' => base64_encode(json_encode($data['flag_data'])),
            'time_created' => isset($data['time_created']) ? $data['time_created'] : time(),
            'time_modified' => isset($data['time_modified']) ? $data['time_modified'] : time(),
            'date_created' => isset($data['date_created']) ? $data['date_created'] : date("Y-m-d H:i:s"),
            'date_modifed' => isset($data['date_modifed']) ? $data['date_modifed'] : date("Y-m-d H:i:s"),
        );
        // Return for chaining
        return $this;
    }
    
    public function Store() {
        // Store the entry
        $this->_Store_Flag();
        // Return the stored flag
        return $this->store_flag;
    }
    
    protected function _Store_Flag() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entry_flags_table = $this->Get_Flags_Table(); 
        // Retrieve the storage data
        $store_flag = $this->store_flag;
        // If there are no store fields, return out
        if (!$store_flag || !is_array($store_flag)) { return array(); }
        // Check for an existing record
        $existing = $wpdb->get_row($wpdb->prepare("SELECT uuid FROM $entry_flags_table WHERE form_uuid = %s AND entry_uuid = %s AND flag_code = %s", $store_flag['form_uuid'], $store_flag['uuid'], $store_flag['flag_code']));
        // If a record was returned
        if ($existing) {
            // Attempt to store the entry data
            $result = $wpdb->update($entry_flags_table, $store_flag, array('uuid' => $existing->uuid));
        } // Otherwise attempt to insert a new record 
        else { $result = $wpdb->insert($entry_flags_table,$store_flag); }
        // If the insert failed
        if ($result === false) { die('Flag value failed to insert'); }
    }
    
    /**
     * RETRIEVE ENTRIES
     * Retrieve previously submitted entries
     */
    
    public function Get($uuid=false) {
        // If no uuid provided then use the stored one
        if (!$uuid) { $uuid = $this->store_flag['uuid']; }
        // If still no uuid
        if (!$uuid) { return $this; }
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entry_flags_table = $this->Get_Flags_Table(); 
        // Check for an existing record
        $flag = $wpdb->get_row($wpdb->prepare("SELECT * FROM $entry_flags_table WHERE uuid = %s",$uuid));
        // If no entry was found
        if (!$entry) { return; }
        // Populate the entry data
        $this->store_flag = array(
            'id' => $flag->id,
            'entry_uuid' => $flag->entry_uuid,
            'form_uuid' => $flag->form_uuid,
            'uuid' => $flag->uuid,
            'flag_code' => $flag->flag_code,
            'flag_data' => $flag->flag_data,
            'time_created' => $flag->time_created,
            'time_modified' => $flag->time_modified,
            'date_created' => $flag->date_created,
            'date_modifed' => $flag->date_modifed,
        );
        // Return for chaining
        return $this;
    }
    
    /**
     * DELETE ENTRIES
     * Deletes previously stored entries
     */
     
    public function Delete($uuid=false) {
        // If no uuid provided then use the stored one
        if (!$uuid) { $uuid = $this->store_flag['uuid']; }
        // If still no uuid
        if (!$uuid) { return $this; }
        // Create the database table
        global $wpdb;
        // Submission table name (including wp prefix)
        $entry_flags_table = $this->Get_Flags_Table(); 
        // Remove all entries
        $wpdb->delete($entry_flags_table, array('uuid' => $uuid));
        // Return for chaining
        return $this;
    }
}

$flags_helper = new VCFF_Reports_Helper_SQL_Flags();

$flags_helper->Upkeep();