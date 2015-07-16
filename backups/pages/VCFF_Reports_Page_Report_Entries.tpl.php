<div class="bootstrap">

<div class="container-fluid container-header">
    
    <?php do_action('vcff_report_entries_pre_header',$this); ?>
    
    <div class="row">
        <div class="col-md-10">
            <h2><strong>Entries</strong></h2>
        </div>
        <div class="col-md-2">
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li><a href="#">Library</a></li>
                <li class="active">Data</li>
            </ol>
        </div>
    </div>
    
    <?php do_action('vcff_report_entries_post_header',$this); ?>
    
</div>

<div class="container-fluid container-contents">
<form method="POST" action="">

    <?php do_action('vcff_report_entries_pre_contents',$this); ?>
    
    <div class="row row-top">
        <div class="col-md-2">
            <a href="<?php echo get_site_url(false,'index.php?page=vcff_preview_form&form_uuid='.$form_uuid); ?>" target="_new_entry" class="btn btn-primary">New Entry</a>
        </div>
        <div class="col-md-5">
            <div class="text-left">
                <div class="dropdown" style="display:inline-block;">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                        Select By
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu select-by-tag" role="menu" aria-labelledby="dropdownMenu1">
                        <?php foreach ($tag_list as $k => $tag_instance): ?>
                        <?php if (!$tag_instance->show_menu_refine) { continue; } ?>
                        <li role="presentation"><a role="menuitem" data-tag="<?php echo $tag_instance->code; ?>" tabindex="-1" href="#"><?php echo $tag_instance->name; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <span class="spacer">|</span>
                <span class="form-inline">
                    <select name="action" class="form-control">
                        <?php foreach ($action_list as $code => $action): ?>
                        <option value="<?php echo $code; ?>"><?php echo $action[0]; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary">Update</button>
                </span>
                <?php do_action('vcff_report_entries_buttons',$this); ?>
            </div>
        </div>
        <div class="col-md-5">
            <div class="text-right">
                <?php do_action('vcff_report_entries_traverse',$this); ?>
                <span>
                    <?php $page_numbers = $this->_Get_Page_Numbers(); ?>
                    <strong><?php echo $page_numbers['showing_start'] ?> - <?php echo $page_numbers['showing_end'] ?> of <?php echo $page_numbers['showing_total'] ?></strong>
                    <span class="btn-group" role="group" >
                        <?php if ($page_numbers['page_previous']): ?>
                        <a href="<?php echo add_query_arg(array('page' => $page_numbers['page_previous'])); ?>" class="btn btn-default">L</a>
                        <?php else: ?>
                        <span class="btn btn-default">L</span>
                        <?php endif; ?>
                        <?php if ($page_numbers['page_next']): ?>
                        <a href="<?php echo add_query_arg(array('page' => $page_numbers['page_next'])); ?>" class="btn btn-default">L</a>
                        <?php else: ?>
                        <span class="btn btn-default">R</span>
                        <?php endif; ?>
                    </span>
                </span>
            </div>
        </div>
    </div>

    <div class="row row-bottom">
        <div class="col-md-2">
            <?php do_action('vcff_report_entries_pre_links',$this); ?>
            <ul class="list-group">
                <?php $weighted_list = $this->_Get_Weighted_Tag_List(); ?>
                <?php foreach ($weighted_list as $k => $tag_instance): ?>
                <?php if (!$tag_instance->show_menu_view) { continue; } ?>
                <a href="<?php echo add_query_arg(array('tag' => $tag_instance->code)); ?>" class="list-group-item group-<?php echo $tag_instance->code; ?>">
                    <?php $tag_count = $tag_instance->Get_Count(); ?>
                    <?php if ($tag_count > 0): ?>
                    <span class="badge"><?php echo $tag_count; ?></span>
                    <?php endif; ?>
                    <?php echo $tag_instance->name; ?>
                </a>
                <?php endforeach; ?>
            </ul>
            <?php do_action('vcff_report_entries_post_links',$this); ?>
        </div>
        <div class="col-md-10">
            <?php echo $this->Get_Alerts_HTML(); ?>
            <?php do_action('vcff_report_entries_pre_items',$this); ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="col-select"><input id="n_1" type="checkbox"></th>
                        <?php do_action('vcff_report_entries_thead_left',$this); ?>
                        <?php foreach($index_fields as $machine_code => $field_instance): ?>
                        <th class="col-field"><span><?php echo $field_instance->Get_Label(); ?></span></th>
                        <?php endforeach; ?>
                        <?php do_action('vcff_report_entries_thead_right',$this); ?>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th class="col-select"><input id="n_2" type="checkbox"></th>
                        <?php do_action('vcff_report_entries_tfoot_left',$this); ?>
                        <?php foreach($index_fields as $machine_code => $field_instance): ?>
                        <th><span><?php echo $field_instance->Get_Label(); ?></span></th>
                        <?php endforeach; ?>
                        <?php do_action('vcff_report_entries_tfoot_right',$this); ?>
                    </tr>
                </tfoot>
                <tbody>
                    <?php if ($tag_entries && is_array($tag_entries)): ?>
                    <?php foreach($tag_entries as $k => $entry_instance): ?>
                    <tr data-entry-id="<?php echo $entry_instance->Get_ID(); ?>" class="entry-item">
                        <td class="col-select"><input name="entries[]" value="<?php echo $entry_instance->Get_ID(); ?>" class="<?php echo implode(' ',preg_filter('/^/', 'tag-', $entry_instance->Get_Tag_Codes())); ?> tag-id" type="checkbox"></td>
                        <?php do_action('vcff_report_entries_tbody_left',$this,$entry_instance); ?>
                        <?php $i=0; foreach($index_fields as $machine_code => $field_instance): ?>
                        <?php if ($i == 0): ?>
                        <th><a href="<?php print admin_url('index.php?page=vcff_report_view_entry&entry_id='.$entry_instance->Get_ID()); ?>"><?php do_action('vcff_report_entries_tbody_first_col',$this,$entry_instance); ?><span><?php echo $entry_instance->Get_Field_Text_Value($field_name); ?></span></a></th>
                        <?php else: ?>
                        <th><a href="<?php print admin_url('index.php?page=vcff_report_view_entry&entry_id='.$entry_instance->Get_ID()); ?>"><span><?php echo $entry_instance->Get_Field_Text_Value($machine_code); ?></span></a></th>
                        <?php endif; ?>
                        <?php $i++; ?>
                        <?php endforeach; ?>
                        <?php do_action('vcff_report_entries_tbody_right',$this,$entry_instance); ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php do_action('vcff_report_entries_post_items',$this); ?>
        </div>
    </div>

    <?php do_action('vcff_report_entries_post_contents',$this); ?>

</form>
</div>

</div>