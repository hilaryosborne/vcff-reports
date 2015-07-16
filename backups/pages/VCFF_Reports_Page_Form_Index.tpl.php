<style>
    .report-item {}
    .form-title {}
    .form-title strong { font-size:14px;font-weight:bold; }
    th.column-count { width:15%; }
    th.column-last { width:15%; }
</style>
<div class="wrap">
    <h2>Form Reports</h2>
    <?php echo $this->Get_Alerts_HTML(); ?>
    <table class="wp-list-table widefat fixed posts">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-cb check-column" style=""><label class="screen-reader-text">Select All</label><input id="cb-select-all-1" type="checkbox"></th>
                <th scope="col" class="manage-column column-title sortable desc" style=""><a href=""><span>Title</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" class="manage-column column-count sortable asc" style=""><a href=""><span>Entries</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" class="manage-column column-last sortable asc" style=""><a href=""><span>Last Entry</span><span class="sorting-indicator"></span></a></th>	
            </tr>    
        </thead>
        <tfoot>
            <tr>
                <th scope="col" class="manage-column column-cb check-column" style=""><label class="screen-reader-text">Select All</label><input id="cb-select-all-1" type="checkbox"></th>
                <th scope="col" class="manage-column column-title sortable desc" style=""><a href=""><span>Title</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" class="manage-column column-count sortable asc" style=""><a href=""><span>Entries</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" class="manage-column column-last sortable asc" style=""><a href=""><span>Last Entry</span><span class="sorting-indicator"></span></a></th>	
            </tr>
        </tfoot>
        <tbody id="the-list">
            <?php foreach ($forms as $k => $post): ?>
                <?php
                    // Create a new helper
                    $reports_helper_form = new VCFF_Reports_Helper_Forms();
                    // Retrieve all of the reports
                    $reports = $reports_helper_form
                        ->Set_Form_Post($post)
                        ->Get_Reports();
                    // If there are no reports, move on
                    if (!$reports || !is_array($reports)) { continue; }
                ?>
                <tr class="form-item level-0">
                    <td class="form-title column-title" colspan="4"><strong>Form: <?php echo $post->post_title; ?></strong></td>
                </tr>
                <?php foreach ($reports as $_k => $action_instance): ?>
                    <?php $event_instance = $action_instance->Get_Selected_Event_Instance(); ?>
                    <tr class="report-item alternate iedit level-0">
                        <th scope="row" class="check-column">
                            <input type="checkbox" name="report[]" value="<?php echo $action_instance->Get_ID(); ?>">
                        </th>
                        <td class="post-title page-title column-title">
                            <strong><a class="row-title" href="<?php print admin_url('index.php?page=vcff_reports_report_entries&form='.$post->ID.'&report='.$action_instance->Get_ID()); ?>" title="Edit “Contact Us Form”"><?php echo $action_instance->Get_Name(); ?></a></strong>
                            <div class="row-actions"><span class="view-entries"><a href="<?php print admin_url('index.php?page=vcff_reports_report_entries&form='.$post->ID.'&report='.$action_instance->Get_ID()); ?>" title="View Report Entries">View Report Entries</a> | </span><span class="trash"><a class="purge-entries" title="Purge ALL report entries" href="">Purge Entries</a> | </span><span class="export-entries"><a href="">Export Report Entries</a></span></div>
                        </td>
                        <td class="column-count">
                            <strong><?php echo $event_instance->Get_ALL_Entry_Count(); ?> Entries</strong><br><?php echo $event_instance->Get_Unread_Entry_Count(); ?> Unread Entries
                        </td>	
                        <td class="column-last">
                            <abbr title="2015/04/28 8:00:28 AM"><?php echo $event_instance->Get_Last_Entry(); ?></abbr>
                        </td>	
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>