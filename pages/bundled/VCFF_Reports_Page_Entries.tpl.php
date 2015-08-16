<div class="bootstrap vcff-reports-entries vcff-admin-panel">
    
    <?php do_action('vcff_reports_entries_pre_header',$this); ?>
    <div class="row" style="margin-left:0px;margin-right:0px;">
        <div class="col-md-12">
            <h2>Report Entries</h2>
        </div>
    </div>
    <?php do_action('vcff_reports_entries_post_header',$this); ?>
    
    <div class="row row-contents" style="margin-left:0px;margin-right:0px;">
    <form method="POST" action="">
        
        <div class="row">

            <div class="col-md-2">
                <div class="list-group">
                    <?php foreach ($flags as $k => $_flag): ?>
                    <?php if (!$_flag['show_in_menu']) { continue; } ?>
                    <?php $flag_entries_count = $this->_Get_Flag_Count($_flag['code']); ?>
                    <a href="<?php print admin_url('index.php?page=vcff_reports_entries&form_uuid='.$_GET['form_uuid'].'&report_id='.$_GET['report_id'].'&dis_flag='.$_flag['code']); ?>" class="list-group-item"><?php if ($flag_entries_count > 0): ?><span class="badge"><?php echo $flag_entries_count; ?></span><?php endif; ?><?php echo $_flag['name']; ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-10">
            
                <div class="tablenav top">

                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                        <select name="action" id="bulk-action-selector-top">
                            <option value="-1" selected="selected">Bulk Actions</option>
                            <?php foreach ($flags as $k => $_flag): ?>
                                <?php if (!$_flag['show_in_actions']) { continue; } ?>
                                <?php $flag_actions = $_flag['actions']; ?>
                                <?php foreach ($flag_actions as $action_code => $action_data): ?>
                                <option value="<?php echo $action_code; ?>"><?php echo $action_data['field_label']; ?></option>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </select>
                        <input type="submit" name="" id="doaction" class="button action" value="Apply">
                    </div>

                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo $pagination['items']; ?> items</span>
                        <span class="pagination-links">
                            <?php if ($pagination['pages_first']): ?>
                            <a class="first-page" title="Go to the first page" href="<?php print admin_url('index.php?page=vcff_reports_entries&form_uuid='.$_GET['form_uuid'].'&report_id='.$_GET['report_id'].'&dis_page='.$pagination['pages_first']); ?>">«</a>
                            <?php else: ?>
                            <a class="first-page disabled" title="Go to the first page" href="<?php print admin_url('index.php?page=vcff_reports_entries&form_uuid='.$_GET['form_uuid'].'&report_id='.$_GET['report_id']); ?>">«</a>
                            <?php endif; ?>
                            <?php if ($pagination['pages_prev']): ?>
                            <a class="prev-page" title="Go to the previous page" href="<?php print admin_url('index.php?page=vcff_reports_entries&form_uuid='.$_GET['form_uuid'].'&report_id='.$_GET['report_id'].'&dis_page='.$pagination['pages_prev']); ?>">‹</a>
                            <?php else: ?>
                            <a class="prev-page disabled" title="Go to the previous page" href="<?php print admin_url('index.php?page=vcff_reports_entries&form_uuid='.$_GET['form_uuid'].'&report_id='.$_GET['report_id'].'&dis_page='.$pagination['pages_prev']); ?>">‹</a>
                            <?php endif; ?>
                            <span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Select Page</label><input class="current-page" id="current-page-selector" title="Current page" type="text" name="dis_page" value="<?php echo $pagination['pages_current']; ?>" size="1"> of <span class="total-pages"><?php echo $pagination['pages']; ?></span></span>
                            <?php if ($pagination['pages_next']): ?>
                            <a class="next-page" title="Go to the next page" href="<?php print admin_url('index.php?page=vcff_reports_entries&form_uuid='.$_GET['form_uuid'].'&report_id='.$_GET['report_id'].'&dis_page='.$pagination['pages_next']); ?>">›</a>
                            <?php else: ?>
                            <a class="next-page disabled" title="Go to the next page" href="<?php print admin_url('index.php?page=vcff_reports_entries&form_uuid='.$_GET['form_uuid'].'&report_id='.$_GET['report_id'].'&dis_page='.$pagination['pages_next']); ?>">›</a>
                            <?php endif; ?>
                            <?php if ($pagination['pages_last']): ?>
                            <a class="last-page" title="Go to the last page" href="<?php print admin_url('index.php?page=vcff_reports_entries&form_uuid='.$_GET['form_uuid'].'&report_id='.$_GET['report_id'].'&dis_page='.$pagination['pages_last']); ?>">»</a>
                            <?php else: ?>
                            <a class="last-page disabled" title="Go to the last page" href="<?php print admin_url('index.php?page=vcff_reports_entries&form_uuid='.$_GET['form_uuid'].'&report_id='.$_GET['report_id'].'&dis_page='.$pagination['pages_last']); ?>">»</a>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="col-select"><input id="n_1" type="checkbox"></th>
                            <th class="col-icons"></th>
                            <?php do_action('vcff_report_entries_thead_left',$this); ?>
                            <?php foreach($fields as $machine_code => $field_instance): ?>
                            <th class="col-field"><span><?php echo $field_instance->Get_Label(); ?></span></th>
                            <?php endforeach; ?>
                            <th class="col-field"><span>Date</span></th>
                            <?php do_action('vcff_report_entries_thead_right',$this); ?>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="col-select"><input id="n_2" type="checkbox"></th>
                            <th class="col-icons"></th>
                            <?php do_action('vcff_report_entries_tfoot_left',$this); ?>
                            <?php foreach($fields as $machine_code => $field_instance): ?>
                            <th><span><?php echo $field_instance->Get_Label(); ?></span></th>
                            <?php endforeach; ?>
                            <th class="col-field"><span>Date</span></th>
                            <?php do_action('vcff_report_entries_tfoot_right',$this); ?>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php if ($entries && is_array($entries)): ?>
                        <?php foreach($entries as $k => $entry): ?>
                        <tr class="entry-item" data-entry-uuid="<?php echo $entry['store_entry']['uuid']; ?>">
                            <th class="col-select"><input type="checkbox" name="_entries[]" value="<?php echo $entry['store_entry']['uuid']; ?>"></th>
                            <th class="col-icons">
                                <?php foreach ($flags as $k => $_flag): ?>
                                    <?php if (!$_flag['show_in_icons']) { continue; } ?>
                                    <?php if (isset($entry['store_flags'][$k]) && isset($_flag['icons']['is_flagged'])): ?>
                                        <?php echo $_flag['icons']['is_flagged']; ?>
                                    <?php elseif (!isset($entry['store_flags'][$k]) && isset($_flag['icons']['not_flagged'])): ?>
                                        <?php echo $_flag['icons']['not_flagged']; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </th>
                            <?php do_action('vcff_report_entries_tfoot_left',$this); ?>
                            <?php $entry_fields = $entry['store_fields']; ?>
                            <?php $i=0; foreach($fields as $machine_code => $field_instance): ?>
                            <?php if ($i==0): ?>
                            <th>
                            <span><a href="<?php print admin_url('index.php?page=vcff_reports_entry&entry_uuid='.$entry['store_entry']['uuid']); ?>"><?php echo $entry_fields[$machine_code]['field_value_text']; ?></a></span>
                                <div class="entry-tags">
                                    <?php foreach ($flags as $k => $_flag): ?>
                                    <?php if (!isset($entry['store_flags'][$k]) || !$_flag['show_in_entry']) { continue; } ?>
                                    <span class="tag tag-<?php echo $_flag['code']; ?>"><?php echo $_flag['name']; ?></span>
                                    <?php $i++; endforeach; ?>
                                </div>
                            </th>
                            <?php else: ?>
                            <th><span><?php echo $entry_fields[$machine_code]['text']; ?></span></th>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <?php do_action('vcff_report_entries_tfoot_right',$this); ?>
                            <th class="col-date"><span><?php echo date('Y/m/d H:i',$entry['store_entry']['time_created']); ?><br><?php echo $entry['store_entry']['status']; ?></span></th>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    
    </form>
    </div>
    
</div>