<?php

vcff_map_event(array(
    'type' => 'report_simple',
    'title' => 'I would like to store a simple submission report',
    'class' => 'Report_Event_Simple_Item'
));

// Register the vcff admin css
vcff_admin_enqueue_script('vcff_events_simple_report', VCFF_REPORTS_URL . '/assets/admin/vcff_events_simple_report.js');
// Register the vcff admin css
vcff_admin_enqueue_style('vcff_events_simple_report', VCFF_REPORTS_URL . '/assets/admin/vcff_events_simple_report.css');
