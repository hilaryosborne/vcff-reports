<div class="bootstrap page_import_export">

    <div class="container-fluid container-header">

        <?php do_action('vcff_form_import_export_pre_header',$this); ?>

        <div class="row">
            <div class="col-md-10">
                <h2><strong>Import/Export</strong></h2>
            </div>
            <div class="col-md-2">
                <ol class="breadcrumb">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Library</a></li>
                    <li class="active">Data</li>
                </ol>
            </div>
        </div>

        <?php do_action('vcff_form_import_export_post_header',$this); ?>
        
    </div>
    
    <div class="container-fluid container-contents">
    
        <?php do_action('vcff_form_import_export_pre_contents',$this); ?>
    
        <?php echo $this->Get_Alerts_HTML(); ?>
    
        <div class="row">
        
            <div class="col-md-2">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc suscipit fermentum odio, et dapibus est vehicula a. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc nec leo scelerisque, varius erat id, porttitor ligula. Nunc vitae purus ac augue vehicula viverra. Ut ut velit lacus. Vivamus congue risus enim. Nullam lorem odio, porta nec commodo ac, lacinia vel urna</p>
                <?php do_action('vcff_form_import_export_instructions',$this); ?>
            </div>
            
            <div class="col-md-4">
                
                <div class="vcff-field-group">
                    <div class="vcff-field-header clearfix">
                        <h4><strong>Import Backup</strong></h4><a href="" target="vcff_hint" class="help-lnk"><span class="dashicons dashicons-editor-help"></span> Help</a>
                    </div>
                    <div class="vcff-field-contents clearfix">
                    <form method="POST" enctype="multipart/form-data" action="">
                        <div class="form-group">
                            <label>A valid JSON backup file</label>
                            <input type="file" name="import_file">
                        </div>
                        <div class="form-group checkbox-inputs">
                            <h4>Which form components to import?</h4>
                            <p>If you are unsure, leave all as they currently are.</p>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="settings[import_forms]" value="y" checked="checked"> Import Forms
                                </label>
                            </div>
                            <?php do_action('vcff_form_import_form_inputs',$this); ?>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Import Data</button>
                            <input type="hidden" name="vcff_import" value="true">
                        </div>
                    </form>
                    </div>
                </div>
                
            </div>
            
            <div class="col-md-4">
            
                <div class="vcff-field-group">
                    <div class="vcff-field-header clearfix">
                        <h4><strong>Export Backup</strong></h4><a href="" target="vcff_hint" class="help-lnk"><span class="dashicons dashicons-editor-help"></span> Help</a>
                    </div>
                    <div class="vcff-field-contents clearfix">
                        <form method="POST" enctype="multipart/form-data" action="">
                            <div class="form-group">
                                <label>Which forms would you like to export</label>
                                <select name="forms[]" multiple="multiple" class="form-control">
                                    <?php if ($form_list && is_array($form_list)): ?>
                                    <?php foreach($form_list as $k => $_data): ?>
                                    <option value="<?php echo $_data->ID; ?>"><?php echo $_data->post_title; ?></option>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <option value="">No Forms</option>
                                    <?php endif; ?>
                                </select>
                                <a href="">Select All Forms</a>
                            </div>
                            <div class="form-group checkbox-inputs">
                                <h4>Which form components to export?</h4>
                                <p>If you are unsure, leave all as they currently are.</p>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="settings[export_forms]" value="y" checked="checked"> Export Forms
                                    </label>
                                </div>
                                <?php do_action('vcff_form_export_form_inputs',$this); ?>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-default">Export Data</button>
                                <input type="hidden" name="vcff_export" value="true">
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
            
        </div>
        
        <?php do_action('vcff_form_import_export_post_contents',$this); ?>
    
    </div>
    
</div>