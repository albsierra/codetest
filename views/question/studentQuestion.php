{% set question = question.getQuestionByType() %}
{% set questionId = question.getQuestionId() %}
{% set answer = user.getAnswerForQuestion(questionId) %}
<h2 class="small-hdr {{ question.getQuestionNum() == 1 ? 'hdr-notop-mrgn' : '' }}">
    <small>Question {{ question.getQuestionNum() }}</small>
</h2>
<div id="questionAnswer{{ questionId }}">
    {% if not answer or not answer.getAnswerId() > 0 %}
    <form id="answerForm{{ questionId }}" action="actions/AnswerQuestion.php"
          method="post">
        <input type="hidden" name="questionId" value="{{ questionId }}">
        <div class="form-group">
            {% autoescape false %}
            <label class="h3"
                   for="answerText{{ questionId }}">{{ question.getQuestionTxt() }}</label>
            {% endautoescape %}

            {{ include (CFG.CT_Types.studentsPath ~ main.getTypeProperty('studentView')) }}
            <textarea class="form-control" id="answerText{{ questionId }}"
                      name="answerText" rows="5"></textarea>
        </div>
        <button type="button" class="btn btn-success"
                onclick="answerQuestion({{ questionId }})">Submit
        </button>
    </form>
    {% else %}
    <h3 class="sub-hdr">{{ question.getQuestionTxt() }}</h3>
    <p>{{ answer.getModified()|date("m/d/Y | h:i A") }}</p>
    <p>{{ answer.getAnswerTxt() }}</p>
    {% endif %}
</div>
