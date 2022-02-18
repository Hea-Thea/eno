<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('create_loan'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('create_loan'); ?></p>

                <?php $attrib = ['class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open('loans/add', $attrib);
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-5">
                            <?php if ($Owner || $Admin) {
                            ?>

                                <div class="form-group">
                                    <?= lang('date', 'date'); ?>
                                    <?= form_input('date', ($_POST['date'] ?? ''), 'class="form-control datetime" id="date" required="required"'); ?>
                                </div>
                            <?php
                            }
                            ?>

                            <div class="form-group">
                                <?php echo lang('reference', 'reference'); ?>
                                <div class="controls">
                                    <?php echo form_input('reference', '', 'class="form-control" id="reference"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="loan_type"><?php echo $this->lang->line('loan_type'); ?></label>
                                <?php
                                foreach ($loan_types as $loan_type) {
                                    $lt[$loan_type->id] = $loan_type->name;
                                }
                                echo form_dropdown('loan_type', $lt, ($_POST['loan_type'] ?? ''), 'class="form-control select" id="loan_type" style="width:100%;" required="required"');
                                ?>
                            </div>

                            <div class="form-group">
                                <?= lang('customer', 'gccustomer'); ?>
                                <?php echo form_input('gccustomer', ($_POST['gccustomer'] ?? ''), 'id="gccustomer" data-placeholder="' . lang('select') . ' ' . lang('customer') . '" required="required" class="form-control input-tip" style="width:100%;"'); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('description', 'description'); ?>
                                <?php echo form_textarea('description', ($_POST['description'] ?? ''), 'class="form-control" id="description"'); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('apply_date', 'apply_date'); ?>
                                <?= form_input('apply_date', (isset($_POST['apply_date']) ? $_POST['apply_date'] : date("d/m/Y")), 'class="form-control date" id="apply_date" required="required"'); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('agency', 'agency'); ?>
                                <?php
                                $agc[''] = '';
                                if($agencies){
                                    foreach ($agencies as $row) {
                                    $agc[$row->id] = $row->name;
                                    }
                                }
                                
                                echo form_dropdown('agency', $agc, ($_POST['agency'] ?? ''), 'class="form-control select" id="agency" required="required" '); ?>
                            </div>

                            <!-- <div class="form-group">
                                <?= lang('logo', 'biller_logo'); ?>
                                <?php
                                $biller_logos[''] = '';
                                foreach ($logos as $key => $value) {
                                    $biller_logos[$value] = $value;
                                }
                                //echo form_dropdown('logo', $biller_logos, '', 'class="form-control select" id="biller_logo" required="required" '); ?>
                            </div> -->

                            

                            <div class="form-group">
                                <?= lang('remark', 'remark'); ?>
                                <?php echo form_textarea('remark', ($_POST['remark'] ?? ''), 'class="form-control" id="remark"'); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('status', 'status'); ?>
                                <?php
                                $opt = ['pending' => lang('pending'), 'approved' => lang('approved')];
                                echo form_dropdown('status', $opt, ($_POST['status'] ?? ''), 'id="status" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>

                            
                        </div>
                        <div class="col-md-5 col-md-offset-1">

                            <div class="form-group">
                                <label class="control-label" for="currency"><?php echo $this->lang->line('currency'); ?></label>
                                <?php
                                foreach ($currencies as $currency) {
                                    $cr[$currency->id] = $currency->name;
                                }
                                echo form_dropdown('currency', $cr, ($_POST['currency'] ?? ''), 'class="form-control select" id="currency" style="width:100%;" required="required"');
                                ?>
                            </div>

                            <div class="form-group">
                                <?php echo lang('apply_amount', 'apply_amount'); ?>
                                <div class="controls">
                                    <?php echo form_input('apply_amount', ($_POST['apply_amount'] ?? ''), 'class="form-control" id="apply_amount" required="required"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('interest_rate', 'interest_rate'); ?>
                                <div class="controls">
                                    <?php echo form_input('interest_rate', ($_POST['interest_rate'] ?? ''), 'class="form-control" id="interest_rate" required="required"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="interest_type"><?php echo $this->lang->line('interest_type'); ?></label>
                                <?php
                                foreach ($interest_types as $interest_type) {
                                    $itrt[$interest_type->id] = $interest_type->name;
                                }
                                echo form_dropdown('interest_type', $itrt, ($_POST['interest_type'] ?? ''), 'class="form-control select" id="interest_type" style="width:100%;" required="required"');
                                ?>
                            </div>

                            <div class="form-group">
                                <?php echo lang('term', 'term'); ?>
                                <div class="controls">
                                    <?php echo form_input('term', '', 'class="form-control" id="term" required="required"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="loan_term"><?php echo $this->lang->line('loan_term'); ?></label>
                                <?php
                                foreach ($loan_terms as $loan_term) {
                                    $tm[$loan_term->id] = $loan_term->name;
                                }
                                echo form_dropdown('loan_term', $tm, ($_POST['loan_term'] ?? ''), 'class="form-control select" id="loan_term" style="width:100%;" required="required"');
                                ?>
                            </div>

                            <div class="form-group">
                                <?= lang('first_payment_date', 'first_payment_date'); ?>
                                <?= form_input('first_payment_date', (isset($_POST['first_payment_date']) ? $_POST['first_payment_date'] : date("d/m/Y")), 'class="form-control date" id="first_payment_date" required="required"'); ?>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="guarantor"><?php echo $this->lang->line('guarantor'); ?></label>
                                <?php
                                 $grt[''] = '';
                                if($guarantors){
                                    foreach ($guarantors as $guarantor) {
                                        $grt[$guarantor->id] = $guarantor->name;
                                    }
                                }
                                
                                echo form_dropdown('guarantor', $grt, ($_POST['guarantor'] ?? ''), 'class="form-control select" id="guarantor" style="width:100%;" required="required"');
                                ?>
                            </div>

                            <div class="form-group">
                                <?php echo lang('customer_colleteral', 'customer_colleteral'); ?>
                                <div class="controls">
                                    <?php echo form_input('customer_colleteral', ($_POST['customer_colleteral'] ?? ''), 'class="form-control" id="customer_colleteral" required="required"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?= lang('purpose', 'purpose'); ?>
                                <?php echo form_textarea('purpose', ($_POST['purpose'] ?? ''), 'class="form-control" id="purpose"'); ?>
                            </div>

                            

                        </div>
                    </div>
                </div>

                <p><?php echo form_submit('add_loan', lang('add_loan'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>

<script type="text/javascript">
    $(document).ready(function (e) {
        $('#gccustomer').select2({
            minimumInputLength: 1,
            ajax: {
                url: site.base_url + "customers/suggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
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

        $("#date").datetimepicker({
            format: site.dateFormats.js_ldate,
            fontAwesome: true,
            language: 'sma',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0
        }).datetimepicker('update', new Date());
    });
</script>
