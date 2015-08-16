<div class="bootstrap vcff-reports-entries vcff-admin-panel">
    
    <?php do_action('vcff_reports_entries_pre_header',$this); ?>
    <div class="row" style="margin-left:0px;margin-right:0px;">
        <div class="col-md-12">
            <h2>Report Entry/Submission</h2>
        </div>
    </div>
    <?php do_action('vcff_reports_entries_post_header',$this); ?>
    
    <div class="row row-contents" style="margin-left:0px;margin-right:0px;">
        
        <div class="col-md-9">
            
            <?php echo $this->Get_Alerts_HTML(); ?>
            
            <div class="submission-fields">
                <h3>Entry Fields</h3>
                <div class="submission-data">
                    <?php foreach ($entry_fields as $machine_code => $field_data): ?>
                    <div class="field-data">
                        <h4><?php echo $field_data['field_label']; ?></h4>
                        <div class="field-value">
                            <?php echo $field_data['field_value_html']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="tabs">
            
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tbs_entry_notes" aria-controls="tbs_entry_notes" role="tab" data-toggle="tab">Notes</a></li>
                    <li role="presentation"><a href="#tbs_entry_advanced" aria-controls="tbs_entry_advanced" role="tab" data-toggle="tab">Advanced</a></li>
                </ul>
                
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tbs_entry_notes">
                        <div class="row">
                            <div class="col-md-8">
                                <h3>Entry Notes</h3>
                                <?php foreach($store_notes as $note_uuid => $note_data):  ?>
                                <div class="well">
                                    <p><?php echo $note_data['note_data']; ?></p>
                                    <p><?php echo date('Y/m/d H:i',$note_data['time_created']); ?>, <a href="<?php print admin_url('index.php?page=vcff_reports_entry&entry_uuid='.$_GET['entry_uuid'].'&delete_note='.$note_data['uuid']); ?>">Delete Comment</a></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-4">
                                <form method="POST" action="<?php print admin_url('index.php?page=vcff_reports_entry&entry_uuid='.$_GET['entry_uuid']); ?>" enctype="multipart/form-data" class="form">
                                    <h3>Create Entry Note</h3>
                                    <p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum ullamcorper ante. Etiam aliquet massa et lorem.</p>
                                    <textarea name="note_comment" rows="5" class="form-control"></textarea>
                                    <input name="_action" type="hidden" value="new_comment">
                                    <button class="btn btn-default">Create Note</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tbs_entry_advanced">
                        <div class="row">
                            <div class="col-sm-8">
                                <?php foreach($entry_meta as $meta_code => $meta_data): ?>
                                <div class="meta-item">
                                    <h4><?php echo $meta_data['meta_label']; ?></h4>
                                    <pre><?php echo is_array($meta_data['meta_value']) ? json_encode($meta_data['meta_value']) : $meta_data['meta_value']; ?></pre>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-sm-4">
                                <h4>Entry Meta</h4>
                                <p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

        </div>

        <div class="col-md-3">
            
            <div class="entry-details">
                <h3>Entry Details</h3>
                <table class="table">
                    <tbody>
                        <tr>
                            <td width="30%"><strong>UUID:</strong></td>
                            <td><?php echo $entry_data['uuid']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Date Created:</strong></td>
                            <td><?php echo $entry_data['date_created']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Date Modified:</strong></td>
                            <td><?php echo $entry_data['date_modifed']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>User Agent:</strong></td>
                            <td><?php echo $entry_data['user_agent']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>IP Address:</strong></td>
                            <td><?php echo $entry_data['submitted_ip']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <?php if ($entry_data['status'] && isset($statuses[$entry_data['status']])): ?>
                                <?php $status_data = $statuses[$entry_data['status']]; ?>
                                <td><?php echo $status_data['name']; ?></td>
                            <?php endif; ?>
                        </tr>
                    </tbody>
                </table>
                
                <div class="entry-status">
                    <h3>Update Status</h3>
                    <form method="POST" action="<?php print admin_url('index.php?page=vcff_reports_entry&entry_uuid='.$_GET['entry_uuid']); ?>" enctype="multipart/form-data" class="form form-inline">
                        <select name="status" class="form-control">
                            <option value="">Select a new entry status</option>
                            <?php foreach ($statuses as $status_code => $status_data): ?>
                            <option value="<?php echo $status_code; ?>"><?php echo $status_data['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input name="_action" type="hidden" value="update_status">
                        <button class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
            
            <div class="entry-flag">
                <h3>Entry Flags</h3>
                <div class="flag-entry-flags">
                    <?php $active_flags = $this->_Get_Flagged(); ?>
                    <?php if ($active_flags && is_array($active_flags)): ?>
                    <?php foreach ($active_flags as $flag_code => $flag_data): ?><span><?php echo $flag_data['name']; ?></span> <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="flag-entry-form">
                    <form method="POST" action="<?php print admin_url('index.php?page=vcff_reports_entry&entry_uuid='.$_GET['entry_uuid']); ?>" enctype="multipart/form-data" class="form form-inline">
                        <select name="flag_action" class="form-control">
                            <option value="">Select an action</option>
                            <?php $flag_actions = $this->_Get_Flag_Actions(); ?>
                            <?php if ($flag_actions && is_array($flag_actions)): ?>
                            <?php foreach ($flag_actions as $action_code => $action_data): ?>
                            <option value="<?php echo $action_code; ?>"><?php echo $action_data['label']; ?></option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <input name="_action" type="hidden" value="update_flags">
                        <button class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
        
    </div>
    
</div>