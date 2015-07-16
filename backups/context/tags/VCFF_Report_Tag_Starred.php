<?php

add_action('vcff_report_entries_thead_left',function($page_instance){
    echo '<th class="col-starred"></th>';
});

add_action('vcff_report_entries_tbody_left',function($page_instance,$entry_instance){
    
    $tag_instances = $page_instance->tag_instances;

    $starred_instance = $tag_instances['starred'];

    if ($starred_instance->Has_Entry($entry_instance)) {
        echo '<td class="col-starred"><a data-starred="'.$entry_instance->Get_ID().'" href="" class="flag-toggle dashicons dashicons-star-filled"></a></td>';
    } else {
        echo '<td class="col-starred"><a data-starred="'.$entry_instance->Get_ID().'" href="" class="flag-toggle dashicons dashicons-star-empty"></a></td>';
    }

}, 10, 2);

add_action('vcff_report_entries_tfoot_left',function($page_instance){
    echo '<th class="col-starred"></th>';
});

add_action('wp_ajax_reports_tag_starred_update', array($this,'AJAX_Update'));

// Register the vcff admin css
vcff_admin_enqueue_script('report_tag_starred', VCFF_REPORTS_URL.'/assets/admin/report_tag_starred.js');
// Register the vcff admin css
vcff_admin_enqueue_style('report_tag_starred', VCFF_REPORTS_URL.'/assets/admin/report_tag_starred.css');

add_action('wp_ajax_reports_entry_flag', function(){
    // Retrieve the entry id
    $entry_id = $_POST['entry_id'];
    // Create a new sql helper
    $reports_helper_sql = new VCFF_Reports_Helper_SQL();
    // Insert a new entry
    $tag_entry = $reports_helper_sql
        ->Select_Entry_Tag($entry_id,'starred');
    // If the entry is currently unstarred
    if (!$tag_entry) {
        // Create a new star entry
        $reports_helper_sql->Create_Entry_Tag(array( 
            'entry_id' => $entry_id,
            'tag_code' => 'starred', 
            'tag_data' => base64_encode(serialize(array())), 
            'time_created' => time(), 
            'time_modified' => time(), 
        ));
        // Encode the meta fields and return
        echo json_encode(array(
            'data' => array(
                'is_flagged' => true
            ),
            'result' => 'success'
        )); wp_die();
    } // Otherwise if it is starred 
    else {
        // Insert a new entry
        $reports_helper_sql->Delete_Entry_Tag(array( 
            'entry_id' => $entry_id,
            'tag_code' => 'starred'
        ));
        // Encode the meta fields and return
        echo json_encode(array(
            'data' => array('is_flagged' => false),
            'result' => 'success'
        )); wp_die();
    }
}); 

class VCFF_Report_Tag_Starred extends VCFF_Report_Tag {

    public $code = 'starred';
    
    public $name = 'Starred';
    
    public $show_menu_refine = true;
    
    public $show_menu_view = true;
    
    public $show_menu_view_weight = 10;
    
    public function __construct() { 
        // Add the update actions
        $this->update_actions['star'] = array('Mark as starred',array($this,'Make_Starred'),'Entries have been marked as starred');
    }
    
    public function Make_Starred($ids) {
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
        // Return boolean true
        return true;
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
                'tags_exclude' => array('trash','archive'),
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
                'tags_required' => array($this->code),
                'tags_exclude' => array('trash','archive'),
            ));
    }
}

vcff_map_tag('starred','VCFF_Report_Tag_Starred');