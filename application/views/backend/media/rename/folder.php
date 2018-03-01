<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Rename folder'); ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('backend_media_update_folder'); ?>">

                    <input value="<?= $relativeFolderPath; ?>" type="hidden" name="dir" />

                    <div class="form-group row">
                        <label for="inputName" class="col-sm-3 col-form-label">
                            <?= translate('Name'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="name" minlength="1" value="<?= $currentFolder->getName(); ?>"  maxlength="50" id="inputName" required class="form-control" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-save"></i>
                                </span>
                                <?= translate('Save'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>
