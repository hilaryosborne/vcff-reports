<?php

class VCFF_Reports_Helper_Flags {

    public function Get_Flags() {
        // The list of flags
        $flags = array(
            'all' => array(
                'code' => 'all',
                'name' => 'All',
                'show_in_menu' => false,
                'lookup' => array($this,'_Flag_All'),
                'show_in_menu' => true,
                'show_in_entry' => false,
                'show_in_icons' => false,
            ),
            'starred' => array(
                'code' => 'starred',
                'name' => 'Starred',
                'show_in_menu' => true,
                'show_in_entry' => false,
                'show_in_icons' => true,
                'show_in_actions' => true,
                'icons' => array(
                    'is_flagged' => '<a href="" class="dashicons icon-starred is-starred dashicons-star-filled"></a>',
                    'not_flagged' => '<a href="" class="dashicons icon-starred dashicons-star-empty"></a>',
                ),
                'actions_bulk' => array(
                    'starred_flag' => array(
                        'label' => 'Flag as starred',
                        '_callback' => array($this,'_Action_Trash_Move')
                    )
                ),
                'actions_single' => array(
                    'starred_flag' => array(
                        'label' => 'Flag as starred',
                        '_is_flagged' => false,
                        '_callback' => array($this,'_Action_Trash_Move')
                    ),
                    'starred_unflag' => array(
                        'label' => 'Remove star flag',
                        '_is_flagged' => true,
                        '_callback' => array($this,'_Action_Trash_Move')
                    )
                )
            ),
            'unread' => array(
                'code' => 'unread',
                'name' => 'Unread',
                'show_in_menu' => true,
                'show_in_entry' => false,
                'show_in_icons' => true,
                'show_in_actions' => true,
                'icons' => array(
                    'is_flagged' => '<a href="" class="dashicons icon-unread dashicons-format-status"></a>',
                    'not_flagged' => '',
                ),
                'actions_bulk' => array(
                    'unread_flag' => array(
                        'label' => 'Flag as unread',
                        '_callback' => array($this,'_Action_Trash_Move')
                    )
                )
            ),
            'trash' => array(
                'code' => 'trash',
                'name' => 'Trash',
                'show_in_menu' => true,
                'show_in_entry' => true,
                'show_in_icons' => false,
                'show_in_actions' => true,
                'actions_bulk' => array(
                    'trash_flag' => array(
                        'label' => 'Flag as Trash',
                        '_callback' => array($this,'_Action_Trash_Move')
                    ),
                    'trash_unflag' => array(
                        'label' => 'Restore Entries',
                        '_callback' => array($this,'_Action_Trash_Restore')
                    ),
                    'trash_delete' => array(
                        'label' => 'Permanently Delete',
                        '_callback' => array($this,'_Action_Trash_Delete')
                    )
                ),
                'actions_single' => array(
                    'trash_flag' => array(
                        'label' => 'Flag as Trash',
                        '_is_flagged' => false,
                        '_callback' => array($this,'_Action_Trash_Move')
                    ),
                    'trash_unflag' => array(
                        'label' => 'Restore Entries',
                        '_is_flagged' => true,
                        '_callback' => array($this,'_Action_Trash_Restore')
                    ),
                    'trash_delete' => array(
                        'label' => 'Permanently Delete',
                        '_is_flagged' => true,
                        '_callback' => array($this,'_Action_Trash_Delete')
                    )
                )
            ),
            'archive' => array(
                'code' => 'archive',
                'name' => 'Archive',
                'show_in_menu' => true,
                'show_in_entry' => true,
                'show_in_icons' => false,
                'show_in_actions' => true,
                'actions_bulk' => array(
                    'archive_flag' => array(
                        'label' => 'Move to Archive',
                        '_callback' => array($this,'_Action_Archive_Move')
                    ),
                    'archive_unflag' => array(
                        'label' => 'Unarchive Entries',
                        '_callback' => array($this,'_Action_Archive_Restore')
                    ),
                ),
                'actions_single' => array(
                    'archive_flag' => array(
                        'label' => 'Move to Archive',
                        '_is_flagged' => false,
                        '_callback' => array($this,'_Action_Archive_Move')
                    ),
                    'archive_unflag' => array(
                        'label' => 'Unarchive Entries',
                        '_is_flagged' => true,
                        '_callback' => array($this,'_Action_Archive_Restore')
                    ),
                )
            )
        );
        // Apply any filter for adding to the flag list
        $flags = apply_filters('vcff_reports_flags',$flags,$this);
        // Return the flag list
        return $flags;
    }
    
    public function With_Flag($report_id,$flag_code,$ordering,$limit,$count=false) {
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
        return $flags_query_helper->Find($count);
    }
    
    public function For_Entry($entry_uuid) {
        // Create a new sql helper
        $reports_helper_sql_flags = new VCFF_Reports_Helper_SQL_Flags();
        // Retrieve the flags table
        $flags_table = $reports_helper_sql_flags->Get_Flags_Table();
        // Create the database table
		global $wpdb;
        // Retrieve the results
        $flag_list = $wpdb->get_results($wpdb->prepare("SELECT * FROM $flags_table AS Flags WHERE Flags.entry_uuid = %s ",array($entry_uuid)),ARRAY_A);
        // If no flags were returned
        if (!$flag_list || !is_array($flag_list)) { return; }
        // The flags array
        $flags = array();
        // Loop through and build the flag list
        foreach ($flag_list as $k => $_flag) {
            // Add to the flag list
            $flags[$_flag['flag_code']] = $_flag;
        }
        // Return the flags list
        return $flags;
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
        return $flag_data['id'] ? true : false;
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
        $result = $wpdb->delete($entry_flags_table, array('flag_code' => $flag_code, 'entry_uuid' => $entry_data['uuid']));
        // Return true or false based on result
        return $result ? true : false;
    }
    
    /**
     * COMPLEX FLAG LOOKUP METHODS
     * These are for flags which contain complex rules or multiple flag requirements
     */
    
    public function _Flag_All($report_id,$flag_code,$ordering,$limit,$count=false) { 
        // Create a new flag query helper
        $flags_query_helper = new VCFF_Reports_Helper_Query_Flags();
        // Set the report ID
        $flags_query_helper
            ->Report($report_id)
            ->Without_Flags(array('archive','trash'),'all');
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
        return $flags_query_helper->Find($count);
    }
    
    /**
     * ACTION METHODS
     * These are for bundled flags to handle action submissions
     */
    
    public function _Action_Trash_Move($entry_uuid, $page_instance) {
        // Add the trash flag to the entry
        $this->Update_Flag('trash',$entry_uuid,array());
    }
    
    public function _Action_Trash_Restore($entry_uuid, $page_instance) {
        // Remove the trash flag from the entry
        $this->Remove_Flag('trash',$entry_uuid);
    }
    
    public function _Action_Trash_Delete($entry_uuid, $page_instance) {
        // Create a new entry sql helper
        $entry_sql_helper = new VCFF_Reports_Helper_SQL_Entries();
        // Attempt to retrieve the entry
        $entry = $entry_sql_helper->Get($entry_uuid);
        // If no entry was found
        if (!$entry || !is_array($entry)) { return false; }
        // REMOVE ENTRY
        // Delete the entry
        $entry_sql_helper
            ->Delete($entry_uuid);
        // REMOVE FLAGS
        // Create the database table
		global $wpdb;
        // Create a new sql helper
        $reports_helper_sql_flags = new VCFF_Reports_Helper_SQL_Flags();
        // Retrieve the flags table
        $flags_table = $reports_helper_sql_flags->Get_Flags_Table();
        // Remove all entries
        $wpdb->delete($flags_table, array('entry_uuid' => $entry_uuid));
        // REMOVE NOTES
        // Create a new sql helper
        $reports_helper_sql_notes = new VCFF_Reports_Helper_SQL_Notes();
        // Retrieve the flags table
        $notes_table = $reports_helper_sql_notes->Get_Notes_Table();
        // Remove all entries
        $wpdb->delete($notes_table, array('entry_uuid' => $entry_uuid));
    }

    public function _Action_Archive_Move($entry_uuid, $page_instance) {
        // Add the archive flag to the entry
        $this->Update_Flag('archive',$entry_uuid,array());
    }
    
    public function _Action_Archive_Restore($entry_uuid, $page_instance) {
        // Remove the archive flag to the entry
        $this->Remove_Flag('archive',$entry_uuid);
    }
}