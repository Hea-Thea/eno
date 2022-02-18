<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_type of speech'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('typeofspeech/edit/'.$partspeech->id, $attrib ); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-md-12">
       
                <div class="form-group">
                        <?= lang('Type of speech', 'Type of speech'); ?>
                        <input type="text" name="typeofspeech" class="form-control" required="required" id="typeofspeech"
                               value="<?= $partspeech->name ?>"/>
                    </div>
                </div>
        

        </div>
        <div class="modal-footer">
            <?php echo form_submit('speech_submit', lang('Edit_customers'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>

