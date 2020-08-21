<div id="newQuestionRow" class="h3 inline flx-cntnr flx-row flx-nowrap flx-start question-row" style="display:none;" data-question-number="{{ newQuestionNumber }}">
    <div id="newQuestionNumber">{{ newQuestionNumber }}.</div>
    <div class="flx-grow-all question-text">
        <form id="questionTextForm-1" action="actions/AddOrEditQuestion.php" method="post">
            <input type="hidden" name="question[questionId]" value="-1">
            <label for="questionTextInput-1" class="sr-only">Question Text</label>
            <textarea class="form-control" id="questionTextInput-1" name="question[questionTxt]" rows="2" required></textarea>
        </form>
    </div>
    <a id="questionSaveAction-1" href="javascript:void(0);">
        <span aria-hidden="true" class="fa fa-fw fa-save"></span>
        <span class="sr-only">Save Question</span>
    </a>
    <a id="questionCancelAction-1" href="javascript:void(0);">
        <span aria-hidden="true" class="fa fa-fw fa-times"></span>
        <span class="sr-only">Cancel Question</span>
    </a>
</div>