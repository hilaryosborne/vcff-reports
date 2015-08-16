<div data-event-code="<?php echo $this->type; ?>" class="event-item event-simple-report">
    
    <div class="action-field-group">
        <div class="action-group-header">
            <h4><strong><?php echo __('Summary Fields', VCFF_NS); ?></strong></h4><a href="" target="vcff_hint" class="help-lnk"><span class="dashicons dashicons-editor-help"></span> Help</a>
        </div>
        <div class="action-group-contents">
            
            <div class="row">
                <div class="col-sm-4">
                    <p>Instructions</p>
                </div>
                <div class="col-sm-8 field-selection">
                    <div class="field-list form-group fields-available">
                        <h4>Avaliable Fields</h4>
                        <select multiple="multiple" class="form-control available-field-list">
                            <?php $selected_fields = $this->_Get_Field_Items(); ?>
                            <?php $form_fields = $this->form_instance->fields; ?>
                            <?php foreach($form_fields as $machine_code => $field_instance): ?>
                            <?php if (in_array($machine_code,$selected_fields)) { continue; } ?>
                            <option value="<?php echo $machine_code; ?>"><?php echo $machine_code; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="toolbar">
                            <button type="button" class="field-add">&#62;&#62;</button>
                            <button type="button" class="field-remove">&#60;&#60;</button>
                        </div>
                    </div>
                    <div class="field-list form-group fields-selected">
                        <h4>Selected Fields</h4>
                        <select multiple="multiple" class="form-control selected-field-list">
                            <?php if ($selected_fields && is_array($selected_fields)): ?>
                            <?php foreach($selected_fields as $k => $machine_code): ?>
                            <option value="<?php echo $machine_code; ?>"><?php echo $machine_code; ?></option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="toolbar">
                            <button type="button" class="field-up">U</button>
                            <button type="button" class="field-down">D</button>
                        </div>
                    </div>
                    <input type="hidden" name="event_action[events][report_simple][fields]" value="<?php echo $this->_Get_Field_List(); ?>" class="selected-fields">
                </div>
            </div>
            
        </div>
    </div>
    
    <div class="action-field-group">
        <div class="action-group-header">
            <h4><strong><?php echo __('Summary Text', VCFF_NS); ?></strong></h4><a href="" target="vcff_hint" class="help-lnk"><span class="dashicons dashicons-editor-help"></span> Help</a>
        </div>
        <div class="action-group-contents">
            <div class="row">
                <div class="col-sm-4">
                    <p>Instructions</p>
                </div>
                <div class="col-sm-8">
                    <div class="form-group">
                        <?php echo vcff_curly_editor_textarea($this->form_instance,'event_action[events][report_simple][summary]',$this->_Get_Summary()); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>