{% set exerciseId = exercise.getExerciseId() %}
<div id="exerciseAnswer{{ exerciseId }}">
    <h2 class="small-hdr {{ exercise.getExerciseNum() == 1 ? 'hdr-notop-mrgn' : '' }}">
        <small>Exercise {{ exercise.getExerciseNum() }}</small>
    </h2>
    {% autoescape false %}
    <span class="exerciseText"> {{ exercise.getExerciseTxt() }}</span>
    {% endautoescape %}

    {#    {% if not answer or (not answer.getAnswerId() > 0) or (not answer.getAnswerSuccess()) %} #}
    <form id="answerForm{{ exerciseId }}" action="actions/AnswerExercise.php"
          method="post">

    {{ include (CFG.CT_Types.studentsPath ~ main.getTypeProperty('studentView')) }}
    <div>
        {% if exercise.getExerciseMust() %}
            <h4>Your solution must contain</h4>
            <pre>{{ exercise.getExerciseMust()|nl2br }}</pre>
        {% endif %}
        {% if exercise.getExerciseMusnt() %}
            <h4>Your solution shouldn't contain</h4>
            <pre>{{ exercise.getExerciseMusnt()|nl2br }}</pre>
        {% endif %}
    </div>
            <input type="hidden" name="exerciseId" value="{{ exerciseId }}">
            <div class="form-group">
                <label for="answerText{{ exerciseId }}">Your solution is:</label>
                <textarea class="form-control" id="answerText{{ exerciseId }}"
                          name="answerText" rows="5"></textarea>
            </div>
            <button type="button" class="btn btn-success"
                    onclick="answerExercise({{ exerciseId }})">Submit
            </button>
        </form>
{#    {% endif %} #}
    {% if answer %}
        <div class="h4 inline flx-cntnr flx-row flx-nowrap flx-start exercise-row">
            <div class="flx-grow-all exercise-solution">
                <h4>Answer:</h4>
                <pre>{{ answer.getAnswerTxt() }}</pre>
            </div>
            <div class="flx-grow-all exercise-text">
                <p>{{ answer.getModified()|date("m/d/Y | h:i A") }}</p>
                <span aria-hidden="true" class="fas fa-thumbs-{{answer.getAnswerSuccess() ? 'up' : 'down'}} text-success"></span>
            </div>
        </div>
    {% endif %}
</div>
