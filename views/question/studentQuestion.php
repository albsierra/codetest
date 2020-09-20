{% set questionId = question.getQuestionId() %}
<div id="questionAnswer{{ questionId }}">
    <h2 class="small-hdr {{ question.getQuestionNum() == 1 ? 'hdr-notop-mrgn' : '' }}">
        <small>Question {{ question.getQuestionNum() }}</small>
    </h2>
    {% autoescape false %}
    <span class="questionText"> {{ question.getQuestionTxt() }}</span>
    {% endautoescape %}
    {{ include (CFG.CT_Types.studentsPath ~ main.getTypeProperty('studentView')) }}
    <div>
        {% if question.getQuestionMust() %}
            <h4>Your solution must contain</h4>
            <pre>{{ question.getQuestionMust()|nl2br }}</pre>
        {% endif %}
        {% if question.getQuestionMusnt() %}
            <h4>Your solution shouldn't contain</h4>
            <pre>{{ question.getQuestionMusnt()|nl2br }}</pre>
        {% endif %}
    </div>

    {% if not answer or (not answer.getAnswerId() > 0) or (not answer.getAnswerSuccess()) %}
        <form id="answerForm{{ questionId }}" action="actions/AnswerQuestion.php"
              method="post">
            <input type="hidden" name="questionId" value="{{ questionId }}">
            <div class="form-group">
                <label for="answerText{{ questionId }}">Your solution is:</label>
                <textarea class="form-control" id="answerText{{ questionId }}"
                          name="answerText" rows="5"></textarea>
            </div>
            <button type="button" class="btn btn-success"
                    onclick="answerQuestion({{ questionId }})">Submit
            </button>
        </form>
    {% endif %}
    {% if answer %}
        <div class="h4 inline flx-cntnr flx-row flx-nowrap flx-start question-row">
            <div class="flx-grow-all question-solution">
                <h4>Answer:</h4>
                <pre>{{ answer.getAnswerTxt() }}</pre>
            </div>
            <div class="flx-grow-all question-text">
                <p>{{ answer.getModified()|date("m/d/Y | h:i A") }}</p>
                <span aria-hidden="true" class="fas fa-thumbs-{{answer.getAnswerSuccess() ? 'up' : 'down'}} text-success"></span>
            </div>
        </div>
    {% endif %}
</div>
