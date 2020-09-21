                                    <label for="questionDatabase">Question Database</label>
                                    <input type="text" name="question[question_database]" value="{{ question.getQuestionDatabase() }}">
                                    <label for="questionDBMS">Question DBMS</label>
                                    <select name="question[question_dbms]" id="questionDBMS">
                                        {% for index, dbms in main.getTypeProperty('dbConnections') %}
                                        <option value="{{ index }}" {{ question.getQuestionDbms() == index ? "selected" : "" }} . >{{ dbms.name }}</option>
                                        {% endfor %}
                                    </select>
                                    <label for="questionType">Question Type</label>
                                    <select name="question[question_type]" id="questionType">
                                        {% for sqlType in main.getTypeProperty('sqlTypes') %}
                                        <option value="{{ sqlType }}" {{ question.getQuestionType() == sqlType ? "selected" : "" }} . >{{ sqlType }}</option>
                                        {% endfor %}
                                    </select>
                                    <label for="questionSolution">Question Solution</label>
                                    <textarea class="form-control" name="question[question_solution]" rows="4" required>{{ question.getQuestionSolution() }}</textarea>
                                    <label for="questionProbe">Question Probe</label>
                                    <textarea class="form-control" name="question[question_probe]" rows="4">{{ question.getQuestionProbe() }}</textarea>
