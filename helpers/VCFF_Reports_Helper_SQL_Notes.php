<?php

class VCFF_Reports_Helper_SQL_Notes {
    
    public $store_note;
    
    public function Get_Notes_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        return $wpdb->prefix.VCFF_REPORTS_SQL_NOTES_TBL; 
    }
    
    /**
     * UPKEEP FUNCTIONS
     * Functions used for maintaining the reports database
     */
    
    public function Upkeep() {
        
        $this->_Upkeep_Entry_Notes_Table();
    }
    
    protected function _Upkeep_Entry_Notes_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entry_notes_table = $this->Get_Notes_Table(); 
        // Charset string
		$charset_collate = $wpdb->get_charset_collate();
        // SQL for the entries table
        $entries_notes_table_sql = "CREATE TABLE $entry_notes_table (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			uuid varchar(255) NOT NULL,
            entry_uuid varchar(255) NOT NULL,
            form_uuid varchar(255) NOT NULL,
            note_data varchar(1) NOT NULL,
            time_created bigint(20) UNSIGNED NOT NULL,
            time_modified bigint(20) UNSIGNED NULL,
            date_created date NOT NULL,
            date_modifed date NULL,
			PRIMARY KEY (id)
		) ".$charset_collate.";"; 
        // Use the dbdelta to compare and upgrade
        dbDelta($entries_notes_table_sql);
    }
    
    /**
     * CREATE ENTRIES
     * Functions used in creating new entries
     */

    public function Add_Note($data) {
        // Populate the entry data
        $this->store_note = array(
            'id' => isset($data['id']) ? $data['id'] : null,
            'uuid' => isset($data['uuid']) ? $data['uuid'] : uniqid(),
            'entry_uuid' => $data['entry_uuid'],
            'form_uuid' => $data['form_uuid'],
            'note_data' => base64_encode(json_encode($data['note_data'])),
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
        $this->_Store_Note();
    }
    
    protected function _Store_Note() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entry_notes_table = $this->Get_Notes_Table(); 
        // Retrieve the storage data
        $store_note = $this->store_note;
        // If there are no store fields, return out
        if (!$store_note || !is_array($store_note)) { return array(); }
        // Check for an existing record
        $existing = $wpdb->get_row($wpdb->prepare("SELECT uuid FROM $entry_notes_table WHERE form_uuid = %s AND entry_uuid = %s AND uuid = %s", $store_note['form_uuid'], $store_note['uuid'], $store_note['uuid']));
        // If a record was returned
        if ($existing) {
            // Attempt to store the entry data
            $result = $wpdb->update($entry_notes_table, $store_note, array('uuid' => $existing->uuid));
        } // Otherwise attempt to insert a new record 
        else { $result = $wpdb->insert($entry_notes_table,$store_note); }
        // If the insert failed
        if ($result === false) { die('Note value failed to insert'); }
    }
    
    /**
     * RETRIEVE ENTRIES
     * Retrieve previously submitted entries
     */
    
    public function Get($uuid=false) {
        // If no uuid provided then use the stored one
        if (!$uuid) { $uuid = $this->store_note['uuid']; }
        // If still no uuid
        if (!$uuid) { return $this; }
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entry_notes_table = $this->Get_Notes_Table(); 
        // Check for an existing record
        $note = $wpdb->get_row($wpdb->prepare("SELECT * FROM $entry_notes_table WHERE uuid = %s",$uuid));
        // If no entry was found
        if (!$entry) { return; }
        // Populate the entry data
        $this->store_note = array(
            'id' => $note->id,
            'uuid' => $note->uuid,
            'entry_uuid' => $note->entry_uuid,
            'form_uuid' => $note->form_uuid,
            'note_data' => $note->note_data,
            'time_created' => $note->time_created,
            'time_modified' => $note->time_modified,
            'date_created' => $note->date_created,
            'date_modifed' => $note->date_modifed,
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
        if (!$uuid) { $uuid = $this->store_note['uuid']; }
        // If still no uuid
        if (!$uuid) { return $this; }
        // Create the database table
        global $wpdb;
        // Submission table name (including wp prefix)
        $entry_notes_table = $this->Get_Notes_Table(); 
        // Remove all entries
        $wpdb->delete($entry_notes_table, array('uuid' => $uuid));
        // Return for chaining
        return $this;
    }
}

$notes_helper = new VCFF_Reports_Helper_SQL_Notes();

$notes_helper->Upkeep();