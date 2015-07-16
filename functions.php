<?php

function vcff_map_report($class) {
    // Retrieve the reports global
    $vcff_reports = vcff_get_library('vcff_reports');
    // Retrieve the form code
    $report_type = $class::$type;
    $report_title = $class::$title;
    // Add the form to our list of available forms
    $vcff_reports->contexts[$report_type] = array(
        'type' => $report_type,
        'title' => $report_title
    );
    // Map the report as an event
    vcff_map_event('Report_Event_Simple');
}

function vcff_map_tag($status,$classname) {
    // Retrieve the reports global
    $vcff_reports = vcff_get_library('vcff_reports');
    
    $vcff_reports->tags[$status] = $classname;
}