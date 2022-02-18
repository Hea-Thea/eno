<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_notification'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('app_settings/edit_notification/' . $notification->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>

        
            <div class="form-group">
                <?= lang('notification_name', 'name'); ?>
                <?= form_input('name', set_value('name', $notification->name), 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>


            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description', $notification->description), 'class="form-control tip" id="description" required="required"'); ?>
            </div>

             <div class="form-group">
                <?= lang('notification_link', 'link'); ?>
                <?= form_input('link', set_value('link', $notification->link), 'class="form-control" id="link"'); ?>
            </div>


            <div class="form-group">
                <?= lang('notification_image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_notification', lang('edit_notification'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
