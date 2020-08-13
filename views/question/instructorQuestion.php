<div id="questionRow{{ question.getQuestionId() }}" class="h3 inline flx-cntnr flx-row flx-nowrap flx-start question-row" data-question-number="{{ question.getQuestionNum() }}">
    <div class="question-number">{{ question.getQuestionNum() }}.</div>
    <div class="flx-grow-all question-text">
        <span class="question-text-span" onclick="editQuestionText({{ question.getQuestionId() }})" id="questionText{{ question.getQuestionId() }}" tabindex="0">{{ question.getQuestionTxt() }}</span>
        <form id="questionTextForm{{ question.getQuestionId() }}" onsubmit="return confirmDeleteQuestionBlank({{ question.getQuestionId() }})" action="actions/AddOrEditQuestion.php" method="post" style="display:none;">
            <input type="hidden" name="questionId" value="{{ question.getQuestionId() }}">
            <label for="questionTextInput{{ question.getQuestionId() }}" class="sr-only">Question Text</label>
            <textarea class="form-control" id="questionTextInput{{ question.getQuestionId() }}" name="questionText" rows="2" required>{{ question.getQuestionTxt() }}</textarea>
        </form>
    </div>
    <a id="questionEditAction{{ question.getQuestionId() }}" href="javascript:void(0);" onclick="editQuestionText({{ question.getQuestionId() }})">
        <span class="fa fa-fw fa-pencil" aria-hidden="true"></span>
        <span class="sr-only">Edit Question Text</span>
    </a>
    <a id="questionReorderAction{{ question.getQuestionId() }}" href="javascript:void(0);" onclick="moveQuestionUp({{ question.getQuestionId() }})">
        <span class="fa fa-fw fa-chevron-circle-up" aria-hidden="true"></span>
        <span class="sr-only">Move Question Up</span>
    </a>
    <a id="questionDeleteAction{{ question.getQuestionId() }}" href="javascript:void(0);" onclick="deleteQuestion({{ question.getQuestionId() }})">
        <span aria-hidden="true" class="fa fa-fw fa-trash"></span>
        <span class="sr-only">Delete Question</span>
    </a>
    <a id="questionSaveAction{{ question.getQuestionId() }}" href="javascript:void(0);" style="display:none;">
        <span aria-hidden="true" class="fa fa-fw fa-save"></span>
        <span class="sr-only">Save Question</span>
    </a>
    <a id="questionCancelAction{{ question.getQuestionId() }}" href="javascript:void(0);" style="display: none;">
        <span aria-hidden="true" class="fa fa-fw fa-times"></span>
        <span class="sr-only">Cancel Question</span>
    </a>
</div>
