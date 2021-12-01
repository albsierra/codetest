{% extends "mainTemplate.php" %}
{% block pageTitle %}
{{ OUTPUT.pageTitle(main.getTitle(), true, false) }}
{% endblock %}
{% block content %}

{% if exercises|length > 0 %}
<div id="navExercises">
    <p> List of exercises:</p>
        <ol>
            {% for i in 0..totalExercises - 1 %}
            <li>
                <a href="student-home.php?exerciseNumber={{ i }}">
                    {% set answer = user.getAnswerForExercise(exercises[i].getExerciseId()) %}
                    <div class="navExercise">
                        {{ i + 1 }} <span aria-hidden="true" class="fas fa-thumbs-{{answer.getAnswerSuccess() ? 'up' : 'down'}} text-success"></span>
                    </div>
                </a>
            </li>
            {% endfor %}
        </ol>
</div>
<div style="clear: both"></div>
<div>
    {% set exercise = exercises[currentExerciseNumber].getExerciseByType() %}
    {% set exerciseId = exercise.getExerciseId() %}
    {% set answer = user.getAnswerForExercise(exerciseId) %}
    {{ include('exercise/studentExercise.php') }}
</div>
{% else %}
        <p class="lead">Your instructor has not yet configured this learning app.</p>
{% endif %}

{% endblock %}
