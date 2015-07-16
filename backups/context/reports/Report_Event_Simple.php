<?php

class Report_Event_Simple  {
    
    static $type = 'report_simple';
    
    static $title = 'I would like to store a simple submission report';
    
	static $class_item = 'Report_Event_Simple_Item';
	
    static function Params() {
        // Return any field params
        return array();
    } 
}

add_action('admin_enqueue_scripts',function(){
    // Register the vcff admin css
    wp_enqueue_script('vcff_events_simple_report', VCFF_REPORTS_URL . '/assets/admin/vcff_events_simple_report.js');
    // Register the vcff admin css
    wp_enqueue_style('vcff_events_simple_report', VCFF_REPORTS_URL . '/assets/admin/vcff_events_simple_report.css');
});

vcff_map_report('Report_Event_Simple');
