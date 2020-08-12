<div id="toolTitle" class="h1">
    <button id="helpButton" type="button" class="btn btn-link pull-right" data-toggle="modal" data-target="#helpModal">
        <span class="fa fa-question-circle" aria-hidden="true"></span> Help</button>
    <span class="flx-cntnr flx-row flx-nowrap flx-start">
                <span class="title-text-span" onclick="editTitleText();" tabindex="0"><?=$main->getTitle()?></span>
                <a id="toolTitleEditLink" class="toolTitleAction" href="javascript:void(0);" onclick="editTitleText();">
                    <span class="fa fa-fw fa-code" aria-hidden="true"></span>
                    <span class="sr-only">Edit Title Text</span>
                </a>
                <span class="mainType-text-span" id="mainType" onclick="editTitleText();"><?= $CFG->CT_mainTypes[$main->getType()] ?></span>
            </span>

</div>
<form id="toolTitleForm" action="actions/UpdateMainTitle.php" method="post" style="display:none;">
    <div class="h1 flx-cntnr flx-row flx-nowrap flx-start">
        <label for="toolTitleInput" class="sr-only">Title Text</label>
        <textarea class="title-edit-input flx-grow-all" id="toolTitleInput" name="toolTitle" rows="2"><?=$main->getTitle()?></textarea>
        <a id="toolTitleSaveLink" class="toolTitleAction" href="javascript:void(0);">
            <span class="fa fa-fw fa-save" aria-hidden="true"></span>
            <span class="sr-only">Save Title Text</span>
        </a>
        <a id="toolTitleCancelLink" class="toolTitleAction" href="javascript:void(0);">
            <span class="fa fa-fw fa-times" aria-hidden="true"></span>
            <span class="sr-only">Cancel Title Text</span>
        </a>
    </div>
    <div class="h3 flx-cntnr flx-row flx-nowrap flx-start">
        <label for="mainType">Type:&nbsp;</label>
        <?php if(count($questions) > 0) : ?>
            <input type="hidden" name="mainType" value="<?= $main->getType() ?>">
            <span class="title-edit-input flx-grow-all" id="mainType"><?= $CFG->CT_mainTypes[$main->getType()] ?></span>
        <?php else : ?>
            <select class="title-edit-input" id="mainType" name="mainType">
                <?php foreach ($CFG->CT_mainTypes as $index => $type) : ?>
                    <option value="<?= $index ?>" <?php echo($main->getType() == $index ? 'selected' : '') ?>>
                        <?= $type ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

    </div>
</form>
<p class="lead">Add questions to quickly collect feedback from your students.</p>
