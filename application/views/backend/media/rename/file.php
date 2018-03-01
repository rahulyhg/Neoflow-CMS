<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Rename file'); ?>
            </h4>
            <div class="card-body">

                <form id="formRenameFile" method="post" action="<?= generate_url('backend_media_update_file'); ?>">

                    <input value="<?= $relativeFilePath; ?>" type="hidden" name="file" />

                    <div class="form-group row">
                        <label for="inputName" class="col-sm-3 col-form-label">
                            <?= translate('Filename'); ?>
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="text" name="name" minlength="1" value="<?= $currentFile->getName(false); ?>"  maxlength="50" id="inputName" required class="form-control" />
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <?= $currentFile->getExtension(); ?>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputExtension" class="col-sm-3 col-form-label">
                            <?= translate('File extension'); ?>
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <?= $currentFile->getName(false); ?>
                                    </span>
                                </div>
                                <input type="text" name="extension" value="<?= $currentFile->getExtension(); ?>"  maxlength="20" id="inputExtension" class="form-control" />

                            </div>

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
