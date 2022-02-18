<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_Word category'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('word_category/editWord/'.$wordcategory->id, $attrib ); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-md-12">
       
                <div class="form-group">
                        <?= lang('Word Category', 'Word Category'); ?>
                        <input type="text" name="wordname" class="form-control" required="required" id="typeofspeech"
                               value="<?= $wordcategory->name ?>"/>
                    </div>
                </div>
        

        </div>
        <div class="modal-footer">
            <?php echo form_submit('word_submit', lang('edit_customers'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>