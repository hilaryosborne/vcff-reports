<div class="bootstrap vcff-reports-index vcff-admin-panel">
    
    <?php do_action('vcff_reports_index_pre_header',$this); ?>
    <div class="row row-header">
        <div class="col-md-12">
            <h2>Entry Reports</h2>
        </div>
    </div>
    <?php do_action('vcff_reports_index_post_header',$this); ?>
    
    <?php do_action('vcff_reports_index_pre_contents',$this); ?>
    <div class="row row-contents">
        
        <div class="col-md-9">
            <?php echo $this->Get_Alerts_HTML(); ?>
            <?php foreach ($reports as $k => $report): ?>
            <div class="form-item vcff-field-group">
                <div class="vcff-field-header clearfix">
                    <h4><strong><?php echo $report['form_name'];?></strong></h4><a href="" target="vcff_hint" class="help-lnk"><span class="dashicons dashicons-editor-help"></span> Help</a>
                </div>
                <div class="vcff-field-contents clearfix">
                <?php do_action('vcff_reports_index_item_contents',$this,$report); ?>
                <div class="row">
                    <div class="col-sm-9">
                        <?php do_action('vcff_reports_index_item_pre_table',$this,$report); ?>
                        <table class="wp-list-table vcff-table widefat fixed posts">
                            <thead>
                                <tr>
                                    <th scope="col" class="manage-column column-cb check-column" style=""><input id="cb-select-all-1" type="checkbox"></th>
                                    <th scope="col" class="manage-column column-title" style=""><span>Title</span></th>
                                    <?php do_action('vcff_reports_index_item_table_thead',$this,$report); ?>
                                    <th scope="col" class="manage-column column-count" style=""><span>Entries</span></th>
                                    <th scope="col" class="manage-column column-last" style=""><span>Last Entry</span></th>
                                    <?php do_action('vcff_reports_index_item_table_thead_end',$this,$report); ?>
                                </tr>    
                            </thead>
                            <tfoot>
                                <tr>
                                    <th scope="col" class="manage-column column-cb check-column" style=""><input id="cb-select-all-1" type="checkbox"></th>
                                    <th scope="col" class="manage-column column-title" style=""><span>Title</span></th>
                                    <?php do_action('vcff_reports_index_item_table_tfoot',$this,$report); ?>
                                    <th scope="col" class="manage-column column-count" style=""><span>Entries</span></th>
                                    <th scope="col" class="manage-column column-last" style=""><span>Last Entry</span></th>
                                    <?php do_action('vcff_reports_index_item_table_tfoot_end',$this,$report); ?>
                                </tr>
                            </tfoot>
                            <tbody id="the-list">
                                <?php $i=0; ?>
                                <?php foreach ($report['report_list'] as $_k => $report_item): ?>
                                <tr class="report-item <?php if ($i % 2 == 0): ?>alternate<?php endif; ?> iedit level-0">
                                    <th scope="row" class="check-column">
                                        <input type="checkbox" name="report[]" value="">
                                    </th>
                                    <td class="post-title page-title column-title">
                                        <strong><a class="row-title" href="<?php print admin_url('index.php?page=vcff_reports_entries&form_uuid='.$report['form_uuid'].'&report_id='.$report_item['report_id']); ?>" title="View Report Entries"><?php echo $report_item['report_name']; ?></a></strong>
                                        <div class="row-actions"><span class="view-entries"><a href="<?php print admin_url('index.php?page=vcff_reports_entries&form_uuid='.$report['form_uuid'].'&report_id='.$report_item['report_id']); ?>" title="View Report Entries">View Report Entries</a> | </span><span class="trash"><a class="purge-entries" title="Purge ALL report entries" href="">Purge Entries</a> | </span><span class="export-entries"><a href="">Export Report Entries</a></span></div>
                                    </td>
                                    <?php do_action('vcff_reports_index_item_table_item',$this,$report,$report_item); ?>
                                    <td class="column-count">
                                        <strong><?php echo $report_item['entries_total']; ?> Entries</strong><br><?php echo $report_item['entries_unread']; ?> Unread Entries
                                    </td>	
                                    <td class="column-last">
                                        <?php $last_entry = $report_item['entries_last']; ?>
                                        <?php if ($last_entry):  ?>
                                        <abbr title="2015/04/28 8:00:28 AM">Submitted <?php echo $last_entry['store_entry']['date_created']; ?></abbr>
                                        <?php else: ?>
                                        -
                                        <?php endif; ?>
                                    </td>
                                    <?php do_action('vcff_reports_index_item_table_item_end',$this,$report,$report_item); ?>
                                </tr>
                                <?php $i++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php do_action('vcff_reports_index_item_post_table',$this,$report); ?>
                    </div>
                    <div class="col-sm-3">
                        <h4><strong>Instructions</strong></h4>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu enim porta, placerat nisi id, tincidunt libero. Ut hendrerit dui erat. Mauris aliquet, urna sed</p>
                        <?php do_action('vcff_reports_index_item_sidebar',$this); ?>
                    </div>
                </div>
                
                
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="col-md-3">
            <div class="postbox" style="margin-right:55px;">
                <h3 class="hndle ui-sortable-handle"><span>About VCFF</span></h3>
                <div class="inside">
                    <p class="misc-plugin-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu enim porta, placerat nisi id, tincidunt libero. Ut hendrerit dui erat. Mauris aliquet, urna sed consequat vehicula, felis nisl suscipit massa</p>
                    <div class="misc-plugin-info">
                        Version
                    </div>
                    <div class="major-publishing-actions">
                        <button type="submit" class="btn btn-primary" data-loading-text="Updating...">Update Settings</button>
                    </div>
                </div>
            </div>
            <?php do_action('vcff_reports_index_sidebar',$this); ?>
        </div>
        
    </div>
    <?php do_action('vcff_reports_index_post_contents',$this); ?>
    
</div>