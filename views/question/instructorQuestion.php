<?php
$questionId = $question->getQuestionId();
?>
<div id="questionRow<?=$questionId?>" class="h3 inline flx-cntnr flx-row flx-nowrap flx-start question-row" data-question-number="<?=$question->getQuestionNum()?>">
    <div class="question-number"><?=$question->getQuestionNum()?>.</div>
    <div class="flx-grow-all question-text">
        <span class="question-text-span" onclick="editQuestionText(<?=$questionId?>)" id="questionText<?=$questionId?>" tabindex="0"><?= $question->getQuestionTxt() ?></span>
        <form id="questionTextForm<?=$questionId?>" onsubmit="return confirmDeleteQuestionBlank(<?=$questionId?>)" action="actions/AddOrEditQuestion.php" method="post" style="display:none;">
            <input type="hidden" name="questionId" value="<?=$questionId?>">
            <label for="questionTextInput<?=$questionId?>" class="sr-only">Question Text</label>
            <textarea class="form-control" id="questionTextInput<?=$questionId?>" name="questionText" rows="2" required><?=$question->getQuestionTxt()?></textarea>
        </form>
    </div>
    <a id="questionEditAction<?=$questionId?>" href="javascript:void(0);" onclick="editQuestionText(<?=$questionId?>)">
        <span class="fa fa-fw fa-pencil" aria-hidden="true"></span>
        <span class="sr-only">Edit Question Text</span>
    </a>
    <a id="questionReorderAction<?=$questionId?>" href="javascript:void(0);" onclick="moveQuestionUp(<?=$questionId?>)">
        <span class="fa fa-fw fa-chevron-circle-up" aria-hidden="true"></span>
        <span class="sr-only">Move Question Up</span>
    </a>
    <a id="questionDeleteAction<?=$questionId?>" href="javascript:void(0);" onclick="deleteQuestion(<?=$questionId?>)">
        <span aria-hidden="true" class="fa fa-fw fa-trash"></span>
        <span class="sr-only">Delete Question</span>
    </a>
    <a id="questionSaveAction<?=$questionId?>" href="javascript:void(0);" style="display:none;">
        <span aria-hidden="true" class="fa fa-fw fa-save"></span>
        <span class="sr-only">Save Question</span>
    </a>
    <a id="questionCancelAction<?=$questionId?>" href="javascript:void(0);" style="display: none;">
        <span aria-hidden="true" class="fa fa-fw fa-times"></span>
        <span class="sr-only">Cancel Question</span>
    </a>
</div>
