                                   <select name="question[question_language]" id="questionLanguage">
                                    {% for index, language in main.getTypeProperty('codeLanguages') %}
                                        <option value="{{ index }}" {{ question.getQuestionLanguage() == index ? "selected" : "" }} . >{{ language.name }}</option>
                                    {% endfor %}
                                    </select>
                                    <br />
                                    <label for="questionInputTest">Input for student</label>
                                    <textarea class="form-control" name="question[question_input_test]" id="questionInputTest" rows="4" required>{{ question.getQuestionInputTest() }}</textarea>
                                    <label for="questionInputGrade">Input for grade</label>
                                    <textarea class="form-control" name="question[question_input_grade]" id="questionInputGrade" rows="4" required>{{ question.getQuestionInputGrade }}</textarea>
                                    <label for="questionSolution">Code Solution</label>
                                    <textarea class="form-control" name="question[question_solution]" id="questionSolution" rows="10" required>{{ question.getQuestionSolution }}</textarea>
