{% extends "mainTemplate.php" %}
{% block pageTitle %}
{{ OUTPUT.pageTitle(toolTitle, true, false) }}
{% endblock %}
{% block content %}

{% if questions|length > 0 %}
        {% for question in questions %}
            {% set questionId = question.getQuestionId() %}
            {% set answer = user.getAnswerForQuestion(questionId) %}
            <h2 class="small-hdr {{ question.getQuestionNum() == 1 ? 'hdr-notop-mrgn' : '' }}">
                <small>Question {{ question.getQuestionNum() }}</small>
            </h2>
            <div id="questionAnswer{{ questionId }}">
                {% if not answer or not answer.getAnswerId > 0 %}
                    <form id="answerForm{{ questionId }}" action="actions/AnswerQuestion.php"
                          method="post">
                        <input type="hidden" name="questionId" value="{{ questionId }}">
                        <div class="form-group">
                            <label class="h3"
                                   for="answerText{{ questionId }}">{{ question.getQuestionTxt() }}</label>
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
        {% endfor %}
    </div>
{% else %}
        <p class="lead">Your instructor has not yet configured this learning app.</p>
    </div>
{% endif %}
{% endblock %}
