<?php

class VCFF_Reports_Helper_Forms {
    
    public $form_post;
    
    public $form_instance;
    
    public function Set_Form_Post($post) {
        // Store the form post object
        $this->form_post = $post;
        // Retrieve the form uuid
        $form_uuid = vcff_get_uuid_by_form($post->ID);
        // Retrieve a new form instance helper
        $form_instance_helper = new VCFF_Forms_Helper_Instance();
        // Generate a new form instance
        $form_instance = $form_instance_helper
            ->Set_Form_UUID($form_uuid)
            ->Generate(); 
        // If the form instance could not be created
        if (!$form_instance) { return $this; }
        // Complete setting up the form instance
        $form_instance_helper
            ->Add_Fields()
            ->Add_Containers()
            ->Add_Meta()
            ->Add_Events();
        // Cache the form instance
        $this->form_instance = $form_instance; 
        // Return for chaining
        return $this;
    }
    
    public function Get_Reports() {
        // Retrieve the form instance
        $form_instance = $this->form_instance;
        // Retrieve the form's action items
        $events = $form_instance->events;
        // If no events were found, return out
        if (!$events || !is_array($events)) { return; }
        // Where we will store reports
        $report_list = array();
        // Loop through each of the form's events
        foreach ($events as $action_instance) {
            // Retrieve the event instance
            $event_instance = $action_instance->Get_Selected_Event_Instance();
            // If the event is not an object or is not a report
            if (!is_object($event_instance) || !isset($event_instance->is_report)) { continue; }
            // Save the event instance
            $report_list[] = $action_instance;
        }
        // Return the report list
        return $report_list;
    }
    
    public function Get_Report($report_id) {
        // Retrieve the form instance
        $form_instance = $this->form_instance;
        // Retrieve the form's action items
        $events = $form_instance->events;
        // If no events were found, return out
        if (!$events || !is_array($events)) { return; }
        // Loop through each of the form's events
        foreach ($events as $action_instance) {
            // Retrieve the event instance
            $event_instance = $action_instance->Get_Selected_Event_Instance();
            // If the event is not an object or is not a report
            if (!is_object($event_instance) || !isset($event_instance->is_report) || $action_instance->Get_ID() != $report_id) { continue; }
            // Save the event instance
            return $action_instance;
        }
    }
}