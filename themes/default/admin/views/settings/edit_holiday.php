<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_holiday'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('system_settings/edit_holiday/' . $holiday->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>

        	
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', $holiday->name, 'class="form-control" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('from_date', 'from_date'); ?>
                <?= form_input('from_date', (isset($_POST['from_date']) ? $_POST['from_date'] : $this->sma->hrld($holiday->from_date)), 'class="form-control datetime" id="from_date" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('to_date', 'to_date'); ?>
                <?= form_input('to_date', (isset($_POST['to_date']) ? $_POST['to_date'] : $this->sma->hrld($holiday->to_date)), 'class="form-control datetime" id="to_date" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description', $holiday->description), 'class="form-control tip" id="description" required="required"'); ?>
            </div>

            <?php echo form_hidden('id', $holiday->id); ?>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_holiday', lang('edit_holiday'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'holiday');
        });
    });
</script>
