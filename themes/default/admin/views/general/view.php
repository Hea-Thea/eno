<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        var cTable = $('#CusData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('general/getgeneral') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[0];
                nRow.className = "";
                return nRow;
            },
            "aoColumns": [{
                "bSortable": false,
                "mRender": checkbox
            },null, null,null,{"bSortable": false, "mRender": general_img},null,null,null]
        }).dtFilter([
            // {column_number: 1, filter_default_label: "[<?=lang('category');?>]", filter_type: "text", data: []},
            // {column_number: 2, filter_default_label: "[<?=lang('title');?>]", filter_type: "text", data: []},
            // {column_number: 3, filter_default_label: "[<?=lang('description');?>]", filter_type: "text", data: []},
            // {column_number: 4, filter_default_label: "[<?=lang('img_url');?>]", filter_type: "text", data: []},
            // {column_number: 5, filter_default_label: "[<?=lang('video_url');?>]", filter_type: "text", data: []},
            // {column_number: 6, filter_default_label: "[<?=lang('audio_url');?>]", filter_type: "text", data: []},
        ], "footer");
        $('#myModal').on('hidden.bs.modal', function () {
            cTable.fnDraw( false );
        });
    });
</script>
<?php if ($Owner || ($GP && $GP['bulk_actions'])) {
    echo admin_form_open('general/general_actions', 'id="action-form"');
} ?>
<div class="box"> 
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('General'); ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?= admin_url('general/action_user'); ?>" id="add">
                                <i class="fa fa-plus-circle"></i> <?= lang('add_new_item'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= admin_url('general/import_csv'); ?>" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-file-excel-o"></i> <?= lang('import_by_excel_file')?>
                            </a>
                        </li>
                        <?php if ($Owner) {
                            ?>
                        <li class="divider"></li>
                        <li>
                            <a href="#" class="bpo" title="<b><?= $this->lang->line('delete_type_of_payment') ?></b>"
                                data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>" data-html="true" data-placement="left">
                                <i class="fa fa-trash-o"></i> <?= lang('delete_item') ?>
                            </a>
                        </li>   
                        <?php }?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">
                    <table id="CusData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th style="min-width:30px; width:30px !improtant;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang('category'); ?></th>
                            <th><?= lang('title'); ?></th>
                            <th><?= lang('Description'); ?></th>
                            <th><?= lang('Image'); ?></th>
                            <th><?= lang('Video_url'); ?></th>
                            <th><?= lang('Audio_url'); ?></th>
                            <th><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="12" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                            <tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($Owner || ($GP && $GP['bulk_actions'])) {
    ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
    <?php
} ?>
<?php if ($action && $action == 'add') {
        echo '<script>$(document).ready(function(){$("#add").trigger("click");});</script>';
}
?>


