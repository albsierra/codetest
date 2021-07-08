{% extends "mainTemplate.php" %}
{% block pageTitle %}
{{ OUTPUT.pageTitle(main.getTitle(), true, false) }}
{% endblock %}
{% block content %}

{% if questions|length > 0 %}
<div id="navQuestions">
    <p> List of questions:</p>
        <ol>
            {% for i in 0..totalQuestions - 1 %}
            <li>
                <a href="student-home.php?questionNumber={{ i }}">
                    {% set answer = user.getAnswerForQuestion(questions[i].getQuestionId()) %}
                    <div class="navQuestion">
                        {{ i + 1 }} <span aria-hidden="true" class="fas fa-thumbs-{{answer.getAnswerSuccess() ? 'up' : 'down'}} text-success"></span>
                    </div>
                </a>
            </li>
            {% endfor %}
        </ol>
</div>
<div style="clear: both"></div>
<div>
    {% set question = questions[currentQuestionNumber].getQuestionByType() %}
    {% set questionId = question.getQuestionId() %}
    {% set answer = user.getAnswerForQuestion(questionId) %}
    {{ include('question/studentQuestion.php') }}
</div>
{% else %}
        <p class="lead">Your instructor has not yet configured this learning app.</p>
{% endif %}

{% endblock %}
