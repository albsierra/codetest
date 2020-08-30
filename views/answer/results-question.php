{% extends "mainTemplate.php" %}
{% block pageTitle %}
{{ OUTPUT.pageTitle('Results <small>by Question</small>', true, false) }}
{% endblock %}
{% block content %}
        <section id="questionResponses">
            <div class="list-group">
                {% for question in questions %}
                    {% set questionId = question.getQuestionId() %}
                    {% set numberResponses = question.getNumberAnswers() %}
                    <div class="list-group-item response-list-group-item">
                        <div class="row">
                            <div class="col-sm-3 header-col">
                                <a class="h4 response-collapse-link" data-toggle="collapse" data-target="#responses{{ questionId }}" onclick="getAnswersFromQuestion({{ questionId }})">
                                    Question {{ question.getQuestionNum() }}
                                    <span class="fa fa-chevron-down rotate" aria-hidden="true"></span>
                                </a>
                            </div>
                            <div class="col-sm-offset-1 col-sm-8 header-col">
                                <div class="flx-cntnr flx-row flx-nowrap flx-start">
                                    {% autoescape false %}
                                    <span class="flx-grow-all">{{ question.getQuestionTxt() }}</span>
                                    {% endautoescape %}
                                    <span class="badge response-badge">{{ numberResponses }}</span>
                                </div>
                            </div>
                            <div id="responses{{ questionId }}" class="col-xs-12 results-collapse collapse">
                            </div>
                        </div>
                    </div>
                {% endfor %}
            </div>
        </section>
    </div>
{% endblock %}
