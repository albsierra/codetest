{% extends "mainTemplate.php" %}
{% block pageTitle %}
{{ OUTPUT.pageTitle(main.getTitle(), true, false) }}
{% endblock %}
{% block content %}

{% if questions|length > 0 %}
        {% for question in questions %}
            {{ include('question/studentQuestion.php') }}
        {% endfor %}
{% else %}
        <p class="lead">Your instructor has not yet configured this learning app.</p>
{% endif %}
{% endblock %}
