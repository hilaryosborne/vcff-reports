<?php

class VCFF_Reports_AJAX_Flags {

    public function __construct() {

        add_action('wp_ajax_reports_flag', array($this,'_Process'));
    }
    
    public function _Process() {
        // Retrieve the flag action
        $flag_action = $_GET['flag_action'];
        // Retrieve the flag action
        $flag_code = $_GET['flag_code'];
        // Retrieve the flag action
        $flag_entry = $_GET['flag_entry'];
        // If no flag action was provided
        if (!$flag_action || !$flag_code || !$flag_entry) {
            // Encode the meta fields and return
            echo json_encode(array(
                'result' => 'failed',
                'alert' => 'A flag action, flag code and flag entry needs to be provided'
            )); wp_die();
        }
        // Create a new flag helper class
        $flag_helper = new VCFF_Reports_Helper_Flags();
        // Get a list of the valid flags
        $valid_flags = $flag_helper->Get_Flags();
        // Check if the supplied flag is valid
        if (!isset($valid_flags[$flag_code])) {
            // Encode the meta fields and return
            echo json_encode(array(
                'result' => 'failed',
                'alert' => 'A valid flag code needs to be provided'
            )); wp_die();
        }
        // Determine which action to take
        switch ($flag_action) {
            case 'flag_entry' : $this->_AJAX_Flag_Entry($flag_code,$flag_entry); break;
            case 'unflag_entry' : $this->_AJAX_UnFlag_Entry($flag_code,$flag_entry); break;
        }
    }
    
    protected function _AJAX_Flag_Entry($flag_code,$flag_entry) {
        // Create a new flag helper class
        $flag_helper = new VCFF_Reports_Helper_Flags();
        // Get a list of the valid flags
        $flags = $flag_helper->Get_Flags();
        // Retrieve the flag data
        $flag_data = $flags[$flag_code];
        // If the flag has it's own flag callable
        if (isset($flag_data['entry_flag']) && is_array($flag_data['entry_flag'])) {
            // Call the user function
            $result = call_user_func_array($flag_data['entry_flag'],array($flag_entry));
        } // Otherwise just simply add a flag to the entry
        else { $flag_helper->Update_Flag($flag_code,$flag_entry,array()); }
        // If the result was successfull
        if (is_bool($result) && $result == true) {
            // Encode the meta fields and return
            echo json_encode(array(
                'result' => 'success'
            )); wp_die();
        } // Otherwise if the flagging failed 
        else {
            // Encode the meta fields and return
            echo json_encode(array(
                'result' => 'failed',
                'alert' => is_string($result) ? $result : 'The entry failed to flag'
            )); wp_die();
        }
    }
    
    protected function _AJAX_UnFlag_Entry($flag_code,$flag_entry) {
        // Create a new flag helper class
        $flag_helper = new VCFF_Reports_Helper_Flags();
        // Get a list of the valid flags
        $flags = $flag_helper->Get_Flags();
        // Retrieve the flag data
        $flag_data = $flags[$flag_code];
        // If the flag has it's own flag callable
        if (isset($flag_data['entry_unflag']) && is_array($flag_data['entry_unflag'])) {
            // Call the user function
            $result = call_user_func_array($flag_data['entry_unflag'],array($flag_entry));
        } // Otherwise just simply add a flag to the entry 
        else { $result = $flag_helper->Remove_Flag($flag_code,$flag_entry); }
        // If the result was successfull
        if (is_bool($result) && $result == true) {
            // Encode the meta fields and return
            echo json_encode(array(
                'result' => 'success'
            )); wp_die();
        } // Otherwise if the flagging failed 
        else {
            // Encode the meta fields and return
            echo json_encode(array(
                'result' => 'failed',
                'alert' => is_string($result) ? $result : 'The entry failed to unflag'
            )); wp_die();
        }
    }
    
}

new VCFF_Reports_AJAX_Flags();