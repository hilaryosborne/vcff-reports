<?php

class VCFF_Report_Tag_Archive extends VCFF_Report_Tag {

    public $code = 'archive';
    
    public $name = 'Archive';
    
    public $show_menu_refine = false;
    
    public $show_menu_view = true;
    
    public $show_menu_view_weight = 100;
    
    public function __construct() { 
        
    }

    public function On_Create() {
        
        return false;
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

vcff_map_tag('archive','VCFF_Report_Tag_Archive');