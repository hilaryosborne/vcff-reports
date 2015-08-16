<?php

class VCFF_Reports_Page_Entry extends VCFF_Page {
    
    protected $entry;
    
    public function __construct() {
        // Action to register the page
        add_action('admin_menu', array($this,'Register_Page'));
    }
    
    public function Register_Page() {
        // Add the page sub menu item
        add_submenu_page('', 'Reports', 'Reports', 'edit_posts', 'vcff_reports_entry', array($this,'Render'));
    }
    
    protected function _Get_Entry() {
        // Retrieve the entry uuid
        $_entry_uuid = $_GET['entry_uuid'];
        // Create a new entry helper
        $sql_entry_helper = new VCFF_Reports_Helper_SQL_Entries();
        // Retrieve the entry
        $_entry = $sql_entry_helper
            ->Get($_entry_uuid);
        // Create a new entry helper
        $flags_helper = new VCFF_Reports_Helper_Flags();
        // Retrieve any flags
        $_entry['store_flags'] = $flags_helper
            ->For_Entry($_entry_uuid);
        // Create a new entry helper
        $notes_helper = new VCFF_Reports_Helper_Notes();
        // Retrieve any flags
        $_entry['store_notes'] = $notes_helper
            ->For_Entry($_entry_uuid);
        // Populate the form entries list
        $form_entries[] = $_entry;
        // Populate the entry
        $this->entry = $_entry;
    }
    
    protected function _Submissions() {
        $this->_Update_Status();
        $this->_Update_Flags();
        $this->_Create_Note();
        $this->_Delete_Note();
        
        
    }

    public function Render() {
        
        $this->_Get_Entry();
        
        $this->_Submissions();
        
        // Create a new entry helper
        $reports_entry_helper = new VCFF_Reports_Helper_Entries();
        // Populate the status
        $statuses = $reports_entry_helper
            ->Get_Status();
            
        $entry = $this->entry;
        $entry_data = $entry['store_entry'];
        $entry_fields = $entry['store_fields'];
        $entry_meta = $entry['store_meta'];
        $entry_flags = $entry['store_flags'];
        $store_notes = $entry['store_notes'];
        // Start gathering content
        ob_start();
        // Retrieve the context director
        $dir = untrailingslashit( plugin_dir_path(__FILE__ ) );
        // Include the template file
        include(vcff_get_file_dir($dir.'/'.get_class($this).".tpl.php"));
        // Get contents
        $output = ob_get_contents();
        // Clean up
        ob_end_clean();
        // Return the contents
        echo $output;
    }
    
    /**
     * ENTRY STATUS
     * 
     */
    
    protected function _Update_Status() { 
        // If the action type is not right
        if ($_POST['_action'] != 'update_status') { return; }
        // Retrieve the entry data
        $entry = $this->entry;
        // Retrieve the new status
        $entry_status = $_POST['status'];
        // Create a new entry helper
        $reports_entry_helper = new VCFF_Reports_Helper_Entries();
        // Retrieve the valid status list
        $valid_status = $reports_entry_helper->Get_Status();
        // If this is note a valid status
        if (!isset($valid_status[$entry_status])) { return; }
        // Insert the new status
        $entry['store_entry']['status'] = $entry_status;
        // Update the entry
        $reports_entry_helper->Update($entry);
        // Add a display message
        $this->Add_Alert('<strong>Entry Updated!</strong> The entry status has been updated.','success');
        // Repopulate the entry
        $this->_Get_Entry();
    }
    
    /**
     * ENTRY NOTES
     * 
     */
    
    protected function _Create_Note() {
        // If the action type is not right
        if ($_POST['_action'] != 'new_comment') { return; }
        // If the action type is not right
        if (!isset($_POST['note_comment']) || !$_POST['note_comment']) { return; }
        // Retrieve the entry
        $entry = $this->entry;
        // Retrieve the entry data
        $entry_data = $entry['store_entry'];
        // Create a new notes helper
        $notes_helper = new VCFF_Reports_Helper_Notes();
        // Create the new note
        $notes_helper
            ->Update_Note($entry_data['uuid'],array(
                'note_data' => $_POST['note_comment']
            ));
            
        $this->Add_Alert('<strong>Entry Updated!</strong> A new note has been added.','success');
        // Repopulate the entry
        $this->_Get_Entry();
    }
    
    protected function _Delete_Note() {
        // If the action type is not right
        if (!isset($_GET['delete_note'])) { return; }
        // Retrieve the entry
        $entry = $this->entry;
        // Retrieve the entry data
        $entry_data = $entry['store_entry'];
        // Retrieve the entry data
        $entry_notes = $entry['store_notes'];
        // Retrieve the note uuid
        $note_uuid = $_GET['delete_note'];
        // If the action type is not right
        if (!isset($entry_notes[$note_uuid])) { return; }
        // Create a new notes helper
        $notes_helper = new VCFF_Reports_Helper_Notes();
        // Create the new note
        $notes_helper
            ->Remove_Note($entry_data['uuid'],$note_uuid);
            
        $this->Add_Alert('<strong>Entry Updated!</strong> The selected note was deleted.','success');
        // Repopulate the entry
        $this->_Get_Entry();
    }
    
    /**
     * ENTRY FLAG MANAGEMENT METHODS
     * Manage entry flag status
     */
    
    protected function _Update_Flags() {
        // If the action type is not right
        if ($_POST['_action'] != 'update_flags') { return; }
        // Retrieve the entry data
        $entry = $this->entry;
        // Retrieve the entry uuid
        $entry_uuid = $entry['store_entry']['uuid'];
        // If no entry or no entry uuid
        if (!$entry || !$entry_uuid) { return; }
        // Retrieve the selected action
        $selected_action = $_POST['flag_action'];
        // If there is no selected action, return out
        if (!$selected_action) { return; }
        // Get a list of the valid flags
        $flag_actions = $this->_Get_Flag_Actions();
        // If the flag does not have the action, move on
        if (!isset($flag_actions[$selected_action])) { return; }
        // Retrieve the selected flag action
        $flag_selected_action = $flag_actions[$selected_action];
        // Call the selected action
        call_user_func_array($flag_selected_action['_callback'],array($entry_uuid,$this));
        
        $this->Add_Alert('<strong>Entry Updated!</strong>','success');
        // Repopulate the entry
        $this->_Get_Entry();
    }
    
    protected function _Get_Flag_List() {
        // Create a new flags helper
        $flags_helper = new VCFF_Reports_Helper_Flags();
        // Retrieve the list of flags
        $flags = $flags_helper
            ->Get_Flags();
        // Return the allowed flags
        return $flags;
    }
    
    protected function _Get_Flagged() {
        // Retrieve the entry data
        $entry = $this->entry;
        // Retrieve the entry flags
        $entry_flags = $entry['store_flags'];
        // Retrieve the list of flags
        $flags = $this->_Get_Flag_List();
        // If there are no flags
        if (!$flags || !is_array($flags)) { return; }
        // The active flags list
        $active_flags = array();
        // Loop through each flag
        foreach ($flags as $flag_code => $flag_data) {
            // If there is no flag, move on
            if (!isset($entry_flags[$flag_code])) { continue; }
            // Populate with the active flags
            $active_flags[$flag_code] = $flag_data;
        }
        // Return the active flags
        return $active_flags;
    }
    
    protected function _Get_Flag_Actions() {
        // Retrieve the entry data
        $entry = $this->entry;
        // Retrieve the entry flags
        $entry_flags = $entry['store_flags'];
        // Create a new flags helper
        $flags_helper = new VCFF_Reports_Helper_Flags();
        // Retrieve the list of flags
        $flags = $this->_Get_Flag_List();
        // If there are no flags
        if (!$flags || !is_array($flags)) { return; }
        // The final flag list
        $flag_actions = array();
        // Loop through each flag
        foreach ($flags as $flag_code => $flag_data) { 
            // If the flag cannot be shown in actions
            if (!$flag_data['show_in_actions'] || !isset($flag_data['actions_single'])) { continue; } 
            // Retrieve the flag actions
            $_actions = $flag_data['actions_single'];
            // Loop through each flag action
            foreach ($_actions as $action_code => $action_data) {
                // If we need the entry to be flagged for this entry
                if (!isset($entry_flags[$flag_code]) && $action_data['_is_flagged']) { continue; }
                // If we need the entry to be flagged for this entry
                if (isset($entry_flags[$flag_code]) && !$action_data['_is_flagged']) { continue; }
                // Populate with the flag action
                $flag_actions[$action_code] = $action_data;
            }
        } 
        // Return the flag actions
        return $flag_actions;
    }
    
}

new VCFF_Reports_Page_Entry();