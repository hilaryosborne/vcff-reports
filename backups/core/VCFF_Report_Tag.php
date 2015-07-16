<?php

class VCFF_Report_Tag {

    public $form_instance;
    
    public $action_instance;
    
    public $event_instance;
    
    public $weight = 100;
    
    public $range = array(0,30);
    
    public $filters = array();
    
    public $orderby = array('time_created','DESC');

}