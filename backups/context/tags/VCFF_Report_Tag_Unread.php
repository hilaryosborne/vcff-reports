<?php

add_action('vcff_report_entries_tbody_first_col',function($page_instance,$entry_instance){
    
    $tag_instances = $page_instance->tag_instances;

    $unread_instance = $tag_instances['unread'];
    
    if ($unread_instance->Has_Entry($entry_instance)) {
     echo '<span class="tag tag-unread">unread</span>';
    } 
},0,2);

class VCFF_Report_Tag_Unread extends VCFF_Report_Tag {

    public $code = 'unread';
    
    public $name = 'Unread';
    
    public $show_menu_refine = true;
    
    public $show_menu_view = true;
    
    public $show_menu_view_weight = 5;
    
    public $update_action;

    public $on_entry_create = true;
    
    public function __construct() { 
        // Add the update actions
        $this->update_actions['mark_unread'] = array('Mark as unread',array($this,'Mark_Unread'),'Entries have been marked as read');

        if ($_GET['tag'] == 'unread') {

            $this->update_actions['mark_read'] = array('Mark as read',array($this,'Mark_Read'),'Entries have been marked as unread');
        }
    }
    
    public function On_Create() {
        
        return true;
    }
    
    public function Tag_Entry($entry_instance) {
        // Retrieve the form instance
        $form_instance = $this->form_instance;
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        // Insert a new entry
        $reports_helper_sql->Create_Entry_Tag(array( 
            'entry_id' => $entry_instance->Get_ID(),
            'tag_code' => $this->code, 
            'tag_data' => base64_encode(serialize(array())), 
            'time_created' => time(), 
            'time_modified' => time(), 
        ));
    }

    public function Has_Entry($entry_instance) {
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        // Insert a new entry
        $tag = $reports_helper_sql
            ->Select_Entry_Tag($entry_instance->Get_ID(),$this->code);
        // Return the result
        return is_object($tag) ? $tag : false;
    }

    public function Get_Entries() {
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        // Retrieve the entries
        $entries = $reports_helper_sql
            ->Select_Entries_By_Report(array(
                'filter_event_id' => $action_instance->Get_ID(),
                'tags_required' => array($this->code),
                'tags_exclude' => array('trash','archive'),
                'orderby' => $this->orderby,
            )); 
        // If no entries were returned
        if (!$entries || !is_array($entries)) { return; }
        // The list for entry objects
        $entry_list = array();
        // Loop through each entries
        foreach ($entries as $k => $entry_data) {
            // Create a new entry helper
            $reports_helper_entry = new VCFF_Reports_Helper_Entry();
            // Set the entry id and retrieve
            $entry_instance = $reports_helper_entry
                ->Set_Entry_ID($entry_data->id)
                ->Retrieve();
            // Add to the entry list
            $entry_list[] = $entry_instance;
        }
        // Return the entry list
        return $entry_list;
    }
    
    public function Get_Count() {
        // Retrieve the action instance
        $action_instance = $this->action_instance;
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        // Retrieve the entries
        return $reports_helper_sql
            ->Count_Entries_By_Report(array(
                'filter_event_id' => $action_instance->Get_ID(),
                'tags_required' => array($this->code),
                'tags_exclude' => array('trash','archive'),
            ));
    }
    
    public function Mark_Unread($ids) {
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        // If a valid list of ids was returned
        if (!$ids || !is_array($ids)) { return; }
        // Loop through each provided entry
        foreach ($ids as $k => $entry_id) {
            // Insert a new entry
            $tag = $reports_helper_sql
                ->Select_Entry_Tag($entry_id,$this->code);
            // If the entry already has a tag
            if ($tag) { continue; }
                // Insert a new entry
            $reports_helper_sql->Create_Entry_Tag(array( 
                'entry_id' => $entry_id,
                'tag_code' => $this->code, 
                'tag_data' => base64_encode(serialize(array())), 
                'time_created' => time(), 
                'time_modified' => time(), 
            ));
        }
        
        return true;
    }
    
    public function Mark_Read($ids) {
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        // If a valid list of ids was returned
        if (!$ids || !is_array($ids)) { return; }
        // Loop through each provided entry
        foreach ($ids as $k => $entry_id) {
            // Insert a new entry
            $tag = $reports_helper_sql
                ->Select_Entry_Tag($entry_id,$this->code);
            // If the entry already has a tag
            if (!$tag) { continue; }
                // Insert a new entry
            $reports_helper_sql->Delete_Entry_Tag(array( 
                'entry_id' => $entry_id,
                'tag_code' => $this->code
            ));
        }
        
        return true;
    }
} 

vcff_map_tag('unread','VCFF_Report_Tag_Unread');