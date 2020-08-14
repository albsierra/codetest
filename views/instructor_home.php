{% extends "mainTemplate.php" %}
{% block pageTitle %}
{{ include('main/mainTitle.php') }}
{% endblock %}
{% block content %}
<section id="theQuestions">
    {% for question in questions %}
        {{ include('question/instructorQuestion.php') }}
    {% endfor %}
    {{ include('question/newQuestionForm.php') }}
</section>
<section id="addQuestions">
    {{ include('question/addQuestion.php') }}
</section>

<input type="hidden" id="sess" value="{{ phpsessid }}">
{{ include('dao/help.php') }}
{{ include('question/importModal.php') }}
{% endblock %}