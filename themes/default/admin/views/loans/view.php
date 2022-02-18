<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.sledit', function (e) {
            if (localStorage.getItem('slitems')) {
                e.preventDefault();
                var href = $(this).attr('href');
                bootbox.confirm("<?=lang('you_will_loss_sale_data')?>", function (result) {
                    if (result) {
                        window.location.href = href;
                    }
                });
            }
        });
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-file"></i><?= lang('loan_no') . ' ' . $inv->id; ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>">
                        </i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?= admin_url('loans/edit/' . $inv->id) ?>" class="sledit">
                                <i class="fa fa-edit"></i> <?= lang('edit_loan') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= admin_url('loans/payments/' . $inv->id) ?>" data-target="#myModal" data-toggle="modal">
                                <i class="fa fa-money"></i> <?= lang('view_payments') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= admin_url('loans/add_payment/' . $inv->id) ?>" data-target="#myModal" data-toggle="modal">
                                <i class="fa fa-dollar"></i> <?= lang('add_payment') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= admin_url('loans/email/' . $inv->id) ?>" data-target="#myModal" data-toggle="modal">
                                <i class="fa fa-envelope-o"></i> <?= lang('send_email') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= admin_url('loans/pdf/' . $inv->id) ?>">
                                <i class="fa fa-file-pdf-o"></i> <?= lang('export_to_pdf') ?>
                            </a>
                        </li>

                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                
                <div class="print-only col-xs-12">
                    <img src="<?= base_url() . 'assets/uploads/logos/' . $biller->logo; ?>" alt="<?= $biller->company && $biller->company != '-' ? $biller->company : $biller->name; ?>">
                </div>
                <div class="well well-sm">

                    <div class="col-xs-6 border-right">

                        <div class="col-xs-2"><i class="fa fa-3x fa-user padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h2>
                            <?= $customer->company              && $customer->company != '-' ? '' : 'Attn: ' . $customer->name ?>

                            <?php
                            echo $customer->address . '<br>' . $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . '<br>' . $customer->country;

                            echo '<p>';

                            if ($customer->vat_no != '-' && $customer->vat_no != '') {
                                echo '<br>' . lang('vat_no') . ': ' . $customer->vat_no;
                            }
                            if ($customer->gst_no != '-' && $customer->gst_no != '') {
                                echo '<br>' . lang('gst_no') . ': ' . $customer->gst_no;
                            }
                            if ($customer->cf1 != '-' && $customer->cf1 != '') {
                                echo '<br>' . lang('ccf1') . ': ' . $customer->cf1;
                            }
                            if ($customer->cf2 != '-' && $customer->cf2 != '') {
                                echo '<br>' . lang('ccf2') . ': ' . $customer->cf2;
                            }
                            if ($customer->cf3 != '-' && $customer->cf3 != '') {
                                echo '<br>' . lang('ccf3') . ': ' . $customer->cf3;
                            }
                            if ($customer->cf4 != '-' && $customer->cf4 != '') {
                                echo '<br>' . lang('ccf4') . ': ' . $customer->cf4;
                            }
                            if ($customer->cf5 != '-' && $customer->cf5 != '') {
                                echo '<br>' . lang('ccf5') . ': ' . $customer->cf5;
                            }
                            if ($customer->cf6 != '-' && $customer->cf6 != '') {
                                echo '<br>' . lang('ccf6') . ': ' . $customer->cf6;
                            }

                            echo '</p>';
                            echo lang('tel') . ': ' . $customer->phone . '<br>' . lang('email') . ': ' . $customer->email;
                            ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="col-xs-6 border-right">

                        <div class="col-xs-2"><i class="fa fa-3x fa-building padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= $biller->company && $biller->company != '-' ? $biller->company : $biller->name; ?></h2>
                            <?= $biller->company ? '' : 'Attn: ' . $biller->name ?>

                            <?php
                            echo $biller->address . '<br>' . $biller->city . ' ' . $biller->postal_code . ' ' . $biller->state . '<br>' . $biller->country;

                            echo '<p>';

                            if ($biller->vat_no != '-' && $biller->vat_no != '') {
                                echo '<br>' . lang('vat_no') . ': ' . $biller->vat_no;
                            }
                            if ($biller->gst_no != '-' && $biller->gst_no != '') {
                                echo '<br>' . lang('gst_no') . ': ' . $biller->gst_no;
                            }
                            if ($biller->cf1 != '-' && $biller->cf1 != '') {
                                echo '<br>' . lang('bcf1') . ': ' . $biller->cf1;
                            }
                            if ($biller->cf2 != '-' && $biller->cf2 != '') {
                                echo '<br>' . lang('bcf2') . ': ' . $biller->cf2;
                            }
                            if ($biller->cf3 != '-' && $biller->cf3 != '') {
                                echo '<br>' . lang('bcf3') . ': ' . $biller->cf3;
                            }
                            if ($biller->cf4 != '-' && $biller->cf4 != '') {
                                echo '<br>' . lang('bcf4') . ': ' . $biller->cf4;
                            }
                            if ($biller->cf5 != '-' && $biller->cf5 != '') {
                                echo '<br>' . lang('bcf5') . ': ' . $biller->cf5;
                            }
                            if ($biller->cf6 != '-' && $biller->cf6 != '') {
                                echo '<br>' . lang('bcf6') . ': ' . $biller->cf6;
                            }

                            echo '</p>';
                            echo lang('tel') . ': ' . $biller->phone . '<br>' . lang('email') . ': ' . $biller->email;
                            ?>
                        </div>
                        <div class="clearfix"></div>

                    </div>

                    
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
                <?php if ($Settings->invoice_view == 1) {
                                ?>
                    <div class="col-xs-12 text-center">
                        <h1><?= lang('tax_invoice'); ?></h1>
                    </div>
                <?php
                            } ?>
                <div class="clearfix"></div>
                <div class="col-xs-7 pull-right">
                    <div class="col-xs-12 text-right order_barcodes">
                        <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                        <?= $this->sma->qrcode('link', urlencode(admin_url('loans/view/' . $inv->id)), 2); ?>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="col-xs-5">
                    <div class="col-xs-2"><i class="fa fa-3x fa-file-text-o padding010 text-muted"></i></div>
                    <div class="col-xs-10">
                        <h2 class=""><?= lang('ref'); ?>: <?= $inv->reference_no; ?></h2>
                       

                        <p style="font-weight:bold;"><?= lang('date'); ?>: <?= $this->sma->hrld($inv->date); ?></p>

                        <p style="font-weight:bold;"><?= lang('loan_status'); ?>: <?= lang($inv->status); ?></p>

                        <p style="font-weight:bold;"><?= lang('payment_status'); ?>
                            : <?= lang($inv->payment_status); ?></p>
                        <?php if ($inv->payment_status != 'paid' && $inv->due_date) {
                                echo '<p>' . lang('due_date') . ': ' . $this->sma->hrsd($inv->due_date) . '</p>';
                            } ?>

                        <p>&nbsp;</p>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped print-table order-table">

                        <thead>

                        <tr>
                            <th><?= lang('no.'); ?></th>
                            <th><?= lang('date'); ?> </th>
                            <th><?= lang('reference_no'); ?> </th>
                            <th><?= lang('amount'); ?></th>
                            <th style="padding-right:20px;"><?= lang('interest'); ?></th>
                            <th style="padding-right:20px;"><?= lang('subtotal'); ?></th>
                        </tr>

                        </thead>

                        <tbody>

                        <?php $r = 1;
                        
                            ?>
                            <tr>
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                     <?= $inv->date; ?>
                                </td>
                                <td style="vertical-align:middle;">
                                     <?= $inv->reference_no; ?>
                                </td>
                                
                                <td style="width: 100px; text-align:center; vertical-align:middle;">
                                     <?= $this->sma->formatMoney($inv->amount); ?>
                                </td>
                                
                                
                                <td style="text-align:right; width:120px; padding-right:10px;">
                                   
                                    <?= $this->sma->formatMoney($inv->amount_interest); ?>
                                </td>
                                
                                <td style="text-align:right; width:120px; padding-right:10px;"><?= $this->sma->formatMoney($inv->grand_total); ?></td>
                            </tr>
                            
                        </tbody>
                        <tfoot>
                        <?php
                        $col = 5;
                        $tcol = $col;
                        ?>
                        <?php if ($inv->grand_total != $inv->paid) {
                            ?>
                            <tr>
                                <td colspan="<?= $tcol; ?>"
                                    style="text-align:right; padding-right:10px;"><?= lang('total'); ?>
                                    (<?= $default_currency->code; ?>)
                                </td>
                                
                            
                            </tr>
                        <?php
                        } ?>
                        

                     
                        <tr>
                            <td colspan="<?= $col; ?>"
                                style="text-align:right; font-weight:bold;"><?= lang('total_amount'); ?>
                                (<?= $default_currency->code; ?>)
                            </td>
                            <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($inv->grand_total); ?></td>
                        </tr>
                        <tr>
                            <td colspan="<?= $col; ?>"
                                style="text-align:right; font-weight:bold;"><?= lang('paid'); ?>
                                (<?= $default_currency->code; ?>)
                            </td>
                            <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($inv->paid); ?></td>
                        </tr>
                        <tr>
                            <td colspan="<?= $col; ?>"
                                style="text-align:right; font-weight:bold;"><?= lang('balance'); ?>
                                (<?= $default_currency->code; ?>)
                            </td>
                            <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($inv->grand_total) - ($inv->paid)); ?></td>
                        </tr>

                        </tfoot>
                    </table>
                </div>

                <div class="row">
                    <div class="col-xs-6">
                        <?php
                        if ($inv->description || $inv->description != '') {
                            ?>
                            <div class="well well-sm">
                                <p class="bold"><?= lang('note'); ?>:</p>

                                <div><?= $this->sma->decode_html($inv->description); ?></div>
                            </div>
                        <?php
                        }
                        if ($inv->staff_note || $inv->staff_note != '') {
                            ?>
                            <div class="well well-sm staff_note">
                                <p class="bold"><?= lang('staff_note'); ?>:</p>

                                <div><?= $this->sma->decode_html($inv->staff_note); ?></div>
                            </div>
                        <?php
                        } ?>

                        <?php if ($customer->award_points != 0 && $Settings->each_spent > 0) {
                            ?>
                        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-6">
                            <div class="well well-sm">
                                <?=
                                '<p>' . lang('this_loan') . ': ' . floor(($inv->grand_total / $Settings->each_spent) * $Settings->ca_point)
                                . '<br>' .
                                lang('total') . ' ' . lang('award_points') . ': ' . $customer->award_points . '</p>'; ?>
                            </div>
                        </div>
                        <?php
                        } ?>
                    </div>

                    <div class="col-xs-6">
                        <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax + $return_sale->product_tax : $inv->product_tax)) : ''; ?>
                        <div class="well well-sm">
                            <p><?= lang('created_by'); ?>: <?= $inv->created_by ? $created_by->first_name . ' ' . $created_by->last_name : $customer->name; ?> </p>
                            <p><?= lang('date'); ?>: <?= $this->sma->hrld($inv->date); ?></p>
                            <?php if ($inv->updated_by) {
                            ?>
                                <p><?= lang('updated_by'); ?>
                                    : <?= $updated_by->first_name . ' ' . $updated_by->last_name; ?></p>
                                <p><?= lang('update_at'); ?>: <?= $this->sma->hrld($inv->updated_at); ?></p>
                            <?php
                        } ?>
                        </div>
                    </div>
                </div>

                <?php //include(dirname(__FILE__) . '/../partials/attachments.php'); ?>
                <?php if ($inv->payment_status != 'paid') {
                            ?>
                    <div id="payment_buttons" class="row text-center padding10 no-print">

                      

                       
                        <div class="clearfix"></div>
                    </div>
                <?php
                        } ?>
                <?php if ($payments) {
                            ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-condensed print-table">
                                    <thead>
                                    <tr>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('payment_reference') ?></th>
                                        <th><?= lang('paid_by') ?></th>
                                        <th><?= lang('amount') ?></th>
                                        <th><?= lang('created_by') ?></th>
                                        <th><?= lang('type') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($payments as $payment) {
                                ?>
                                        <tr <?= $payment->type == 'returned' ? 'class="warning"' : ''; ?>>
                                            <td><?= $this->sma->hrld($payment->date) ?></td>
                                            <td><?= $payment->reference_no; ?></td>
                                            <td><?= lang($payment->paid_by);
                                if ($payment->paid_by == 'gift_card' || $payment->paid_by == 'CC') {
                                    echo ' (' . $payment->cc_no . ')';
                                } elseif ($payment->paid_by == 'Cheque') {
                                    echo ' (' . $payment->cheque_no . ')';
                                } ?></td>
                                            <td><?= $this->sma->formatMoney($payment->amount); ?></td>
                                            <td><?= $payment->first_name . ' ' . $payment->last_name; ?></td>
                                            <td><?= lang($payment->type); ?></td>
                                        </tr>
                                    <?php
                            } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php
                        } ?>
            </div>
        </div>
        <?php if (!$Supplier || !$Customer) {
                            ?>
            <div class="buttons">
                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <a href="<?= admin_url('loans/payments/' . $inv->id) ?>" data-toggle="modal" data-target="#myModal" class="tip btn btn-primary tip" title="<?= lang('view_payments') ?>">
                            <i class="fa fa-money"></i> <span class="hidden-sm hidden-xs"><?= lang('view_payments') ?></span>
                        </a>
                    </div>
                    <div class="btn-group">
                        <a href="<?= admin_url('loans/add_payment/' . $inv->id) ?>" data-toggle="modal" data-target="#myModal" class="tip btn btn-primary tip" title="<?= lang('add_payment') ?>">
                            <i class="fa fa-money"></i> <span class="hidden-sm hidden-xs"><?= lang('add_payment') ?></span>
                        </a>
                    </div>
                    <div class="btn-group">
                        <a href="<?= admin_url('loans/email/' . $inv->id) ?>" data-toggle="modal" data-target="#myModal" class="tip btn btn-primary tip" title="<?= lang('email') ?>">
                            <i class="fa fa-envelope-o"></i> <span class="hidden-sm hidden-xs"><?= lang('email') ?></span>
                        </a>
                    </div>
                    <div class="btn-group">
                        <a href="<?= admin_url('loans/pdf/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('download_pdf') ?>">
                            <i class="fa fa-download"></i> <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                        </a>
                    </div>
                    
                    <div class="btn-group">
                        <a href="<?= admin_url('loans/edit/' . $inv->id) ?>" class="tip btn btn-warning tip sledit" title="<?= lang('edit') ?>">
                            <i class="fa fa-edit"></i> <span class="hidden-sm hidden-xs"><?= lang('edit') ?></span>
                        </a>
                    </div>
                    <div class="btn-group">
                        <a href="#" class="tip btn btn-danger bpo"
                            title="<b><?= $this->lang->line('delete_loan') ?></b>"
                            data-content="<div style='width:150px;'><p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' href='<?= admin_url('loans/delete/' . $inv->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button></div>"
                            data-html="true" data-placement="top"><i class="fa fa-trash-o"></i>
                            <span class="hidden-sm hidden-xs"><?= lang('delete') ?></span>
                        </a>
                    </div>
                   
                    <!--<div class="btn-group"><a href="<?= admin_url('loans/excel/' . $inv->id) ?>" class="tip btn btn-primary"  title="<?= lang('download_excel') ?>"><i class="fa fa-download"></i> <?= lang('excel') ?></a></div>-->
                </div>
            </div>
        <?php
                        } ?>
    </div>
</div>
