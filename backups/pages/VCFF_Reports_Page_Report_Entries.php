<?php

class VCFF_Reports_Page_Report_Entries extends VCFF_Report_Page {

    public $form_instance;
    
    public $action_instance;
    
    public $event_instance;

    public $tag_selected;

    public $tag_instances = array();

    public function __construct() {
        // Action to register the page
        add_action('admin_menu', array($this,'Register_Page'));
        // Register the vcff admin css
        vcff_admin_enqueue_script('page_report_entries', VCFF_REPORTS_URL.'/assets/admin/page_report_entries.js');
        // Register the vcff admin css
        vcff_admin_enqueue_style('page_report_entries', VCFF_REPORTS_URL.'/assets/admin/page_report_entries.css');
    }
    
    public function Register_Page() {
        // Add the page sub menu item
        add_submenu_page('', 'Reports', 'Reports', 'edit_posts', 'vcff_reports_report_entries', array($this,'Render'));
    }
    
    protected function _Get_Tag_Instances() {

        // Retrieve the reports global
        $vcff_reports = vcff_get_library('vcff_reports');

        $tags = $vcff_reports->tags;
        
        if (!$tags || !is_array($tags)) { return; }
        
        foreach ($tags as $code => $class_name) {
        
            $tag_instance = new $class_name();
            
            $tag_instance->form_instance = $this->form_instance;
            
            $tag_instance->action_instance = $this->action_instance;
            
            $tag_instance->event_instance = $this->event_instance;
            
            $this->tag_instances[$code] = $tag_instance;
        }
        // Populate the selected instance
        $this->tag_selected = isset($_GET['tag']) && isset($this->tag_instances[$_GET['tag']]) ? $this->tag_instances[$_GET['tag']] : $this->tag_instances['all'];
    }
    
    protected function _Get_Weighted_Tag_List() {
        
        $tag_instances = $this->tag_instances;
        
        usort($tag_instances, function($a, $b){
            if ($a == $b) { return 0; }
            $a_weight = isset($a->show_menu_view_weight) ? $a->show_menu_view_weight : 9999 ;
            $b_weight = isset($b->show_menu_view_weight) ? $b->show_menu_view_weight : 9999 ;
            return ($a_weight < $b_weight) ? -1 : 1;
        });
        
        return $tag_instances;
    }
    
    protected function _Handle_Tag_Actions() {
        
        if (!isset($_POST['action'])) { return; }
        
        $tag_actions = $this->_Get_Tag_Actions();
        
        if (!isset($tag_actions[$_POST['action']])) { return; }
        
        if (!isset($_POST['entries']) || !is_array($_POST['entries'])) { return; }
        
        $action_code = $_POST['action'];
        
        $tag_action = $tag_actions[$action_code];
        
        $entries = $_POST['entries'];
        
        $result = call_user_func_array($tag_action[1],array($entries,$this));
        
        if ($result && is_bool($result) && isset($tag_action[2])) {
            $this->Add_Alert('success',$tag_action[2]);
        } elseif ($result && is_string($result)) {
            $this->Add_Alert('danger',$result);
        }
    }
    
    protected function _Get_Tag_Actions() {
        
        $tag_instances = $this->tag_instances;
        
        if (!$tag_instances || !is_array($tag_instances)) { return; }
        
        $action_list = array();
        
        foreach ($tag_instances as $code => $instance) {
            // If there is no update action
            if (!isset($instance->update_actions) || !is_array($instance->update_actions)) { continue; }
            
            foreach ($instance->update_actions as $action_code => $action_data) {
            
                $action_list[$action_code] = $action_data;
            }
        }
        
        return $action_list;
    }
    
    protected function _Get_Index_Fields() {
        // Retrieve the event instance
        $event_instance = $this->event_instance;
        // Return the index fields
        return $event_instance->Get_Index_Fields();
    }
    
    protected function _Get_Page_Numbers() {
    
        $tag_instance = $this->tag_selected;
        
        $tag_count = $tag_instance->Get_Count();
    
        $current_page = isset($_GET['pager']) && $_GET['pager'] > 0 ? (int)$_GET['pager'] : 1;
        
        $per_page = 30;
        
        $total_pages = floor($tag_count/30);
        
        return array(
            'page_previous' => $current_page > 1 ? ($current_page-1) : false,
            'page_current' => $current_page,
            'page_next' => $current_page < $total_pages ? ($current_page+1) : false,
            'showing_start' => (($current_page-1)*$per_page)+1,
            'showing_end' => ($current_page*$per_page) <= $tag_count ? ($current_page*$per_page) : $tag_count,
            'showing_total' => $tag_count
        );
    }
    
    public function Render() {
        // Retrieve the post object
        $post = get_post($_GET['form']);
        // If no post exists
        if (!$post || !is_object($post)) { die('No Post'); }
        
        $form_uuid = vcff_get_uuid_by_form($post->ID);
        // Create a new reports helper
        $reports_helper_Forms = new VCFF_Reports_Helper_Forms();
        // Retrieve the action instance
        $action_instance = $reports_helper_Forms
            ->Set_Form_Post($post)
            ->Get_Report($_GET['report']);
        // If there is no action instance
        if (!$action_instance) { die('No report'); }
        // Populate the form instance
        $this->form_instance = $reports_helper_Forms->form_instance;
        // Populate the action instance
        $this->action_instance = $action_instance;
        // Populate the event instance
        $this->event_instance = $action_instance->Get_Selected_Event_Instance();
        // Retrieve the summary fields
        $index_fields = $this->_Get_Index_Fields();
        
        $this->_Get_Tag_Instances();
        
        $this->_Handle_Tag_Actions();
        
        $action_list = $this->_Get_Tag_Actions();
        // Retrieve the list of instances
        $tag_list = $this->tag_instances;
        // Retrieve the selected tag
        $tag_selected = $this->tag_selected;
        // Retrieve the tag entries
        $tag_entries = $tag_selected->Get_Entries();
        // Retrieve the context director
        $tmp_dir = untrailingslashit( plugin_dir_path(__FILE__ ) );
        // Start gathering content
        ob_start();
        // Include the template file
        include(vcff_get_file_dir($tmp_dir.'/'.get_class($this).".tpl.php"));
        // Get contents
        $output = ob_get_contents();
        // Clean up
        ob_end_clean();
        // Return the contents
        echo $output;
    }
    
}
 
new VCFF_Reports_Page_Report_Entries();