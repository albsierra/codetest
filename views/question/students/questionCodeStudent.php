                                <p class="h4 inline flx-cntnr flx-row flx-nowrap flx-start">
                                    <span><strong>Input: </strong></span><pre>{{ question.getQuestionInputTest() }}</pre>
                                </p>
                                <p class="h4 inline flx-cntnr flx-row flx-nowrap flx-start">
                                    <span><strong>Output: </strong></span><pre>{{ question.getQuestionOutputTest() }}</pre>
                                </p>
                                <select name="answer_language" id="answerLanguage">
                                    {% for index, language in main.getTypeProperty('codeLanguages') %}
                                    {% if
                                    (answer and answer.getAnswerLanguage() == index)
                                    or ((not answer or not answer.getAnswerLanguage()) and question.getQuestionLanguage() == index)
                                    %}
                                    {% set selected = "selected" %}
                                    {% else %}
                                    {% set selected = "" %}
                                    {% endif %}
                                    <option value="{{ index }}" {{ selected }} . >{{ language.name }}</option>
                                    {% endfor %}
                                </select>
