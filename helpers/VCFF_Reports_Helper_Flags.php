<?php

class VCFF_Reports_Helper_Flags {

    public function Get_Flags() {
        // The list of flags
        $flags = array(
            'all' => array(
                'code' => 'all',
                'name' => 'All',
                'show_in_menu' => false,
            ),
            'starred' => array(
                'code' => 'starred',
                'name' => 'Starred',
                'show_in_menu' => true,
            ),
            'unread' => array(
                'code' => 'unread',
                'name' => 'Unread',
                'show_in_menu' => true,
            ),
            'trash' => array(
                'code' => 'trash',
                'name' => 'Trash',
                'show_in_menu' => true,
            ),
            'archive' => array(
                'code' => 'archive',
                'name' => 'Archive',
                'show_in_menu' => true,
            )
        );
        // Apply any filter for adding to the flag list
        $flags = apply_filters('vcff_reports_flags_list',$flags,$this);
        // Return the flag list
        return $flags;
    }
    
    public function With_Flag($report_id,$flag_code,$ordering,$limit) {
        // Create a new flag query helper
        $flags_query_helper = new VCFF_Reports_Helper_Query_Flags();
        // Set the report ID
        $flags_query_helper
            ->Report($report_id)
            ->With_Flags(array($flag_code),'all');
        // Set the ordering rules
        if (is_array($ordering) && count($ordering) > 0) {
            // Set the order parameters
            $flags_query_helper->Order($ordering);
        }
        // Set the limit rules
        if (is_array($limit) && count($limit) > 0) {
            // Add the limit parameters
            $flags_query_helper->Limit($limit[0],$limit[1]);
        }
        // Return the results
        return $flags_query_helper->Find();
    }
    
    public function Update_Flag($flag_code,$flag_entry,$flag_data) {
        // Create a new entry sql helper
        $entries_sql_helper = new VCFF_Reports_Helper_SQL_Entries();
        // Attempt to retrieve the entry
        $entry = $entries_sql_helper->Get($flag_entry);
        // If no entry was found
        if (!$entry || !is_array($entry)) { return false; }
        // Retreieve the entry data
        $entry_data = $entry['store_entry'];
        // Create a new flags sql helper
        $flags_sql_helper = new VCFF_Reports_Helper_SQL_Flags();
        // Add the flag
        $flag_data = $flags_sql_helper->Add_Flag(array(
            'entry_uuid' => $entry_data['uuid'],
            'form_uuid' => $entry_data['form_uuid'],
            'flag_code' => $flag_code,
            'flag_data' => $flag_data,
        ))->Store();
        // Return the flag
        return $flag_data['id'];
    }
    
    public function Remove_Flag($flag_code,$flag_entry) {
        // Create a new entry sql helper
        $entries_sql_helper = new VCFF_Reports_Helper_SQL_Entries();
        // Attempt to retrieve the entry
        $entry = $entries_sql_helper->Get($flag_entry);
        // If no entry was found
        if (!$entry || !is_array($entry)) { return false; }
        // Retreieve the entry data
        $entry_data = $entry['store_entry'];
        // Create a new flags sql helper
        $flags_sql_helper = new VCFF_Reports_Helper_SQL_Flags();
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entry_flags_table = $flags_sql_helper->Get_Flags_Table(); 
        // Remove all entries
        return $wpdb->delete($entry_flags_table, array('flag_code' => $flag_code));
    }
}