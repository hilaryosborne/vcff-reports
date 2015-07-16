<div class="bootstrap">

<div class="container-fluid container-header">
    
    <?php do_action('vcff_report_entry_pre_header',$this); ?>
    
    <div class="row">
        <div class="col-md-10">
            <h2><strong>View Entry</strong></h2>
        </div>
        <div class="col-md-2">
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li><a href="#">Library</a></li>
                <li class="active">Data</li>
            </ol>
        </div>
    </div>
    
    <?php do_action('vcff_report_entry_post_header',$this); ?>
    
</div>

<div class="container-fluid container-contents">
    
    <?php do_action('vcff_report_entry_pre_contents',$this); ?>
    
    <div class="row">

        <div class="col-sidebar col-md-4">
            <?php do_action('vcff_report_entry_pre_sidebar',$this); ?>
            <div>
                <h4>About This Submission</h4>
            </div>
            <?php do_action('vcff_report_entry_post_sidebar',$this); ?>
        </div>

        <div class="col-contents col-md-8">
            <?php do_action('vcff_report_entry_pre_contents',$this); ?>
            <div>
                <h4>About This Submission</h4>
            </div>
            <?php do_action('vcff_report_entry_post_contents',$this); ?>
        </div>

    </div>
    
    <?php do_action('vcff_report_entry_pre_contents',$this); ?>

</div>

</div>