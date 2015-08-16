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

// Register the vcff admin css
vcff_admin_enqueue_script('vcff_events_simple_report', VCFF_REPORTS_URL . '/assets/admin/vcff_events_simple_report.js');
// Register the vcff admin css
vcff_admin_enqueue_style('vcff_events_simple_report', VCFF_REPORTS_URL . '/assets/admin/vcff_events_simple_report.css');

vcff_map_event('Report_Event_Simple');
