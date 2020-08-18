{% extends "mainTemplate.php" %}
{% block pageTitle %}
{{ OUTPUT.pageTitle('Grade', true, false) }}
{% endblock %}
{% block content %}
<h3>Set Points Possible <small>Default 100</small></h3>
<form class="form-inline" action="actions/UpdatePointsPossible.php" method="post">
    <div class="form-group">
        <label for="points_possible">Points Possible: </label>
        <input type="text" class="form-control" id="points_possible" name="points_possible" value="{{ maxPoints }}">
    </div>
    <button type="submit" class="btn btn-default">Submit</button>
</form>
<h3>Grade Students</h3>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
        <th class="col-sm-5">Student Name</th>
        <th class="col-sm-2">Last Updated</th>
        <th class="col-sm-2">Completed</th>
        <th class="col-sm-3">Grade</th>
        </thead>
        <tbody>
        {% for student in students %}
            {% if not student.isInstructor %}
                <tr>
                    <td>{{ student.user.getDisplayname() }}</td>
                    <td>{{ student.formattedMostRecentDate }}</td>
                    <td>{{ student.numberAnswered }}  / {{ totalQuestions }}</td>
                    <td>
                        <form class="form-inline" action="actions/GradeStudent.php" method="post">
                            <input type="hidden" name="student_id" value="{{ student.user.getUserId() }}">
                            <div class="form-group">
                                <label>
                                    <input type="text" class="form-control" name="grade" value="{{ student.grade}}">/{{ pointsPossible }}
                                </label>
                            </div>
                            <button type="submit" class="btn btn-default">Update</button>
                        </form>
                    </td>
                </tr>
            {% endif %}
        {% endfor %}
        </tbody>
    </table>
</div>

{% endblock %}