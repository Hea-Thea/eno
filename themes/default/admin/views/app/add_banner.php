<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_banner'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('app_settings/add_banner', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang('banner_code', 'code'); ?>
                <?= form_input('code', set_value('code'), 'class="form-control' . ($Settings->use_code_for_slug ? ' gen_slug' : '') . '" id="code" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('banner_name', 'name'); ?>
                <?= form_input('name', set_value('name'), 'class="form-control' . ($Settings->use_code_for_slug ? '' : ' gen_slug') . '" id="name" required="required"'); ?>
            </div>

            

            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description'), 'class="form-control tip" id="description" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('banner_image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_banner', lang('add_banner'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>