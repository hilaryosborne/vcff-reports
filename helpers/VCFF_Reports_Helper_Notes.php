<?php


class VCFF_Reports_Helper_Notes {

    public function For_Entry($entry_uuid) {
        // Create a new sql helper
        $reports_helper_sql_notes = new VCFF_Reports_Helper_SQL_Notes();
        // Retrieve the flags table
        $notes_table = $reports_helper_sql_notes->Get_Notes_Table();
        // Create the database table
		global $wpdb;
        // Retrieve the results
        $notes_list = $wpdb->get_results($wpdb->prepare("SELECT * FROM $notes_table AS Notes WHERE Notes.entry_uuid = %s ",array($entry_uuid)),ARRAY_A);
        // If no flags were returned
        if (!$notes_list || !is_array($notes_list)) { return; }
        // The flags array
        $notes = array();
        // Loop through and build the flag list
        foreach ($notes_list as $k => $_note) {
            // Add to the flag list
            $notes[$_note['uuid']] = $_note;
        }
        // Return the flags list
        return $notes;
    }
    
    public function Update_Note($note_entry,$note_data) {
        // Create a new entry sql helper
        $entries_sql_helper = new VCFF_Reports_Helper_SQL_Entries();
        // Attempt to retrieve the entry
        $entry = $entries_sql_helper->Get($note_entry);
        // If no entry was found
        if (!$entry || !is_array($entry)) { return false; }
        // Retreieve the entry data
        $entry_data = $entry['store_entry']; 
        // Create a new sql helper
        $reports_helper_sql_notes = new VCFF_Reports_Helper_SQL_Notes();
        // Insert the entry uuid
        $note_data['entry_uuid'] = $entry_data['uuid'];
        // Insert the form uuid
        $note_data['form_uuid'] = $entry_data['form_uuid'];
        // Add the flag
        $note_data = $reports_helper_sql_notes->Add_Note($note_data)->Store();
        // Return the flag
        return $note_data['id'] ? true : false;
    }
    
    public function Remove_Note($note_entry,$note_uuid) {
        // Create a new entry sql helper
        $entries_sql_helper = new VCFF_Reports_Helper_SQL_Entries();
        // Attempt to retrieve the entry
        $entry = $entries_sql_helper->Get($note_entry);
        // If no entry was found
        if (!$entry || !is_array($entry)) { return false; }
        // Retreieve the entry data
        $entry_data = $entry['store_entry'];
        // Create a new sql helper
        $reports_helper_sql_notes = new VCFF_Reports_Helper_SQL_Notes();
        // Create the database table
		global $wpdb; 
        // Retrieve the flags table
        $notes_table = $reports_helper_sql_notes->Get_Notes_Table(); 
        // Remove all entries
        $result = $wpdb->delete($notes_table, array('uuid' => $note_uuid, 'entry_uuid' => $entry_data['uuid']));
        // Return true or false based on result
        return $result ? true : false;
    }

}