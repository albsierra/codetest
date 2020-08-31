{% extends "mainTemplate.php" %}
{% block pageTitle %}
{{ OUTPUT.pageTitle('Results <small>by Student</small>', true, false) }}
{% endblock %}
{% block content %}
<section id="studentResponses">
    <div class="panel panel-info">
        <div class="panel-heading response-panel-header">
            <div class="row">
                <div class="col-xs-6">
                    <h4 class="results-table-hdr">Student Name</h4>
                </div>
                <div class="col-xs-3 text-center">
                    <h4 class="results-table-hdr">Last Updated</h4>
                </div>
                <div class="col-xs-3 text-center">
                    <h4 class="results-table-hdr">Completed</h4>
                </div>
            </div>
        </div>
        <div class="list-group">
            {% for student in students %}
                {% if not student.isInstructor %}
                    <div class="list-group-item response-list-group-item">
                        <div class="row">
                            <div class="col-xs-6 header-col">
                                <a href="#responses{{ student.user.getUserId() }}" class="h4 response-collapse-link" data-toggle="collapse">
                                    {{ student.user.getDisplayname() }}
                                    <span class="fa fa-chevron-down rotate" aria-hidden="true"></span>
                                </a>
                            </div>
                            <div class="col-xs-3 text-center header-col">
                                <span class="h5 inline">{{ student.formattedMostRecentDate }}</span>
                            </div>
                            <div class="col-xs-3 text-center header-col">
                                <span class="h5 inline">{{ student.numberAnswered }}  / {{ totalQuestions }}</span>
                            </div>
                            <div id="responses{{ student.user.getUserId() }}" class="col-xs-12 results-collapse collapse">
                                {% for question in questions %}
                                    {% set answer = student.user.getAnswerForQuestion(question.getQuestionId()) %}
                                    <div class="row response-row">
                                        <div class="col-sm-5">
                                            <h4 class="small-hdr hdr-notop-mrgn">
                                                <small>Question {{ question.getQuestionNum() }}</small>
                                            </h4>
                                            {% autoescape false %}
                                            <div>{{ question.getQuestionTxt() }}</div>
                                            {% endautoescape %}
                                        </div>
                                        <div class="col-sm-offset-1 col-sm-5">
                                            <p class="response-text">{{ answer.getAnswerTxt() }}</p>
                                        </div>
                                        <div class="col-sm-1">
                                            <span aria-hidden="true" class="fas fa-thumbs-{{answer.getAnswerSuccess() ? 'up' : 'down'}} text-success"></span>
                                        </div>
                                    </div>
                                {% endfor %}
                            </div>
                        </div>
                    </div>
                {% endif %}
            {% endfor %}
        </div>
    </div>
</section>
</div>
{% endblock %}
