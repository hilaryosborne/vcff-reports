<div class="bootstrap vcff-reports-entries vcff-admin-panel">
    
    <?php do_action('vcff_reports_entries_pre_header',$this); ?>
    <div class="row">
        <div class="col-md-12">
            <h2>Report Entries</h2>
        </div>
    </div>
    <?php do_action('vcff_reports_entries_post_header',$this); ?>
    
    <div class="row row-contents">
    <form method="POST" action="">
    
        <div class="row">
            
            <div class="col-md-2">
            
            </div>
            
            <div class="col-md-10">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="col-select"><input id="n_1" type="checkbox"></th>
                            <?php do_action('vcff_report_entries_thead_left',$this); ?>
                            <?php foreach($fields as $machine_code => $field_instance): ?>
                            <th class="col-field"><span><?php echo $field_instance->Get_Label(); ?></span></th>
                            <?php endforeach; ?>
                            <?php do_action('vcff_report_entries_thead_right',$this); ?>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="col-select"><input id="n_2" type="checkbox"></th>
                            <?php do_action('vcff_report_entries_tfoot_left',$this); ?>
                            <?php foreach($fields as $machine_code => $field_instance): ?>
                            <th><span><?php echo $field_instance->Get_Label(); ?></span></th>
                            <?php endforeach; ?>
                            <?php do_action('vcff_report_entries_tfoot_right',$this); ?>
                        </tr>
                    </tfoot>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
            
        </div>
    
    </form>
    </div>
    
</div>