<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_language'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-customer-form'];
        echo admin_form_open_multipart('languages/add_language', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group company">
                        <?= lang('language', 'language'); ?>
                        <?php echo form_input('language', '', 'class="form-control tip" id="company" data-bv-notempty="true"'); ?>
                    </div>

                    <!-- <div class="form-group">
                        <?= lang('language', 'language'); ?>
                        <input type="text" name="language" class="form-control" required="required" id="language"/>
                    </div> -->

                </div>
                
            </div>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_language', lang('add_language'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function (e) {
        $('#add-customer-form').bootstrapValidator({
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            }, excluded: [':disabled']
        });
        $('select.select').select2({minimumResultsForSearch: 7});
        fields = $('.modal-content').find('.form-control');
        $.each(fields, function () {
            var id = $(this).attr('id');
            var iname = $(this).attr('name');
            var iid = '#' + id;
            if (!!$(this).attr('data-bv-notempty') || !!$(this).attr('required')) {
                $("label[for='" + id + "']").append(' *');
                $(document).on('change', iid, function () {
                    $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', iname);
                });
            }
        });
    });
</script>
