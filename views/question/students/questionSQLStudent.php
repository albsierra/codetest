                                <div class="h4 inline flx-cntnr flx-row flx-nowrap flx-start">
                                <div class="flx-grow-all"><strong>Database: </strong><span>{{ question.getQuestionDatabase() }}</span></div>
                                <div class="flx-grow-all"><strong>Type: </strong><span>{{ question.getQuestionType() }}</span></div>
                                </div>
                                {% set queryTable = question.getQueryTable() %}
                                {% if queryTable %}
                                    <div class="flx-grow-all"><strong>Output: </strong>
                                        {% autoescape false %}
                                        <p>{{ queryTable }}</p>
                                        {% endautoescape %}
                                    </div>
                                {% endif %}
