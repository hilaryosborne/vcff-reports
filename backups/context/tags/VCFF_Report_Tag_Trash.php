<?php

class VCFF_Report_Tag_Trash extends VCFF_Report_Tag {

    public $code = 'trash';
    
    public $name = 'Trash';
    
    public $show_menu_refine = false;
    
    public $show_menu_view = true;
    
    public $show_menu_view_weight = 200;

    public function __construct() { 
        // Add the update actions
        $this->update_actions['make_trash'] = array('Send to trash',array($this,'Action_Make_Trashed'),'Entries have been marked as trash');
        // If we are viewing the trash page
        if ($_GET['tag'] == 'trash') {
            // Add the update actions
            $this->update_actions['remove_trash'] = array('Remove from trash',array($this,'Action_Remove_Trashed'),'Entries have been marked as trash');
            // Add the update actions
            $this->update_actions['delete'] = array('Delete Permanently',array($this,'Action_Delete'),'Entries have been marked as trash');
        }
    }

    public function Action_Make_Trashed($ids) {
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
    
    public function Action_Remove_Trashed($ids) {
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
    
    public function Action_Delete($ids) {
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

    public function On_Create() {
        
        return false;
    }

    public function Is($entry_instance) {
        
        return $entry_instance->Is_Flagged() ? true : false;
    }

    public function Has_Entry($entry_instance) {
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        // Insert a new entry
        $tag = $reports_helper_sql->Select_Entry_Tag($entry_instance->Get_ID(),$this->code);
        
        return is_object($tag) ? $tag : false;
    }

    public function Get_Entries() {
    
        $action_instance = $this->action_instance;
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        // Retrieve the entries
        $entries = $reports_helper_sql
            ->Select_Entries_By_Report(array(
                'filter_event_id' => $action_instance->Get_ID(),
                'tags_required' => array($this->code),
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
    
        $action_instance = $this->action_instance;
        // Create a new sql helper
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        // Retrieve the entries
        return $reports_helper_sql
            ->Count_Entries_By_Report(array(
                'filter_event_id' => $action_instance->Get_ID(),
                'tags_required' => array($this->code)
            ));
    }
    
}

vcff_map_tag('trash','VCFF_Report_Tag_Trash');