<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Edit items'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('enter_info'); ?></p>

        <?php
            $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
            echo admin_form_open_multipart('general/edit/'.$general_detail->id, $attrib);
        ?>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" name="category" id="category" required>
                            <option value="<?= $general_detail->sma_kce_category_general_id ?>">
                                <?php foreach($get_tbl_general->result() as $x){
                                    if($x->id == $general_detail->sma_kce_category_general_id){
                                        echo $x->name;
                                    }
                                }?>
                            </option>
                            <?php foreach($get_tbl_general->result() as $x){ 
                                if($x->id != $general_detail->sma_kce_category_general_id)
                                {
                            ?>
                                <option value="<?php echo $x->id?>"><?php echo $x->name ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="category">Title</label>
                        <input class="form-control" name='title' value="<?= $general_detail->title ?>"  type="text" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="category">Video_url</label>
                        <input class="form-control" value="<?= $general_detail->video_url ?>" name='video_url' type="text">
                    </div>
                </div>
                <div class="col-md-8"> 
                    <div class="form-group">
                        <label for="edit_img">Attachments</label>
                        <input id="edit_img" type="file" data-browse-label="<?= lang('browse'); ?>" name="general_img" multiple data-show-upload="false" data-show-preview="false" class="form-control file">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="category">Audio_url</label>
                        <input class="form-control" value="<?= $general_detail->audio_url ?>" name='audio_url' type="text">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <?= lang('Descriptions', 'Descriptions'); ?>
                        <?php echo form_textarea('description_general',$general_detail->description, ($_POST['description_general'] ?? ''),'class="form-control" id="slinnote" style="margin-top: 10px; height: 100px;"'); ?>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div
                        class="fprom-group"><?php echo form_submit('update_items', lang('update'), 'id="update_items" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                        <input type="button" data-toggle="modal" data-target="#cancel" class="btn btn-danger" value="<?= lang('cancel') ?>">
                    </div>
                </div>


                <div class="modal fade" id = "cancel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                        <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span></button>
                                <p  id="myModal">Are you sure ? </p>
                        </div>
                            <div class="modal-footer">
                            <button data-bb-handler="cancel" type="button" class="btn btn-default">Cancel</button>
                            <a  href="<?php echo admin_url('general/index');?>" bb-handler="confirm" type="reset" class="btn btn-primary">OK</a>
                            </div>
                        </div>
                    </div>
                </div>
        <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

