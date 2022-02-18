<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= $agency->company && $agency->company != '-' ? $agency->company : $agency->name; ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" style="margin-bottom:0;">
                    <tbody>
                    <tr>
                        <td><strong><?= lang('company'); ?></strong></td>
                        <td><?= $agency->company; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('name'); ?></strong></td>
                        <td><?= $agency->name; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('vat_no'); ?></strong></td>
                        <td><?= $agency->vat_no; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('gst_no'); ?></strong></td>
                        <td><?= $agency->gst_no; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('email'); ?></strong></td>
                        <td><?= $agency->email; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('phone'); ?></strong></td>
                        <td><?= $agency->phone; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('address'); ?></strong></td>
                        <td><?= $agency->address; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('city'); ?></strong></td>
                        <td><?= $agency->city; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('state'); ?></strong></td>
                        <td><?= $agency->state; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('postal_code'); ?></strong></td>
                        <td><?= $agency->postal_code; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('country'); ?></strong></td>
                        <td><?= $agency->country; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('scf1'); ?></strong></td>
                        <td><?= $agency->cf1; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('scf2'); ?></strong></td>
                        <td><?= $agency->cf2; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('scf3'); ?></strong></td>
                        <td><?= $agency->cf3; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('scf4'); ?></strong></td>
                        <td><?= $agency->cf4; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('scf5'); ?></strong></td>
                        <td><?= $agency->cf5; ?></strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close'); ?></button>
                <?php if ($Owner || $Admin || $GP['reports-agencies']) {
    ?>
                    <a href="<?=admin_url('reports/agency_report/' . $agency->id); ?>" target="_blank" class="btn btn-primary"><?= lang('agencies_report'); ?></a>
                <?php
} ?>
                <?php if ($Owner || $Admin || $GP['agencies-edit']) {
        ?>
                    <a href="<?=admin_url('agencies/edit/' . $agency->id); ?>" data-toggle="modal" data-target="#myModal2" class="btn btn-primary"><?= lang('edit_agency'); ?></a>
                <?php
    } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
