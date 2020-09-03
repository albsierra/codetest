{% extends "mainTemplate.php" %}
{% block pageTitle %}
{{ include('main/mainTitle.php') }}
{% endblock %}
{% block content %}
<section id="theQuestions">
    {% for question in questions %}
        {{ include('question/instructorQuestion.php') }}
    {% endfor %}
    <div id="newQuestionRow" class="h3 inline flx-cntnr flx-row flx-nowrap flx-start question-row" style="display:none;" data-question-number="{{ newQuestionNumber }}">

    </div>
</section>
<section id="addQuestions">
    {{ include('question/addQuestion.php') }}
</section>

{{ include('question/import/importModal.php') }}
{% endblock %}
