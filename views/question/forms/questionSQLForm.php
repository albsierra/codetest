                                    <label for="questionDatabase">Question Database</label>
                                    <input type="text" name="question[question_database]" value="{{ question.getQuestionDatabase() }}">
                                    <label for="questionType">Question Type</label>
                                    <select name="question[question_type]" id="questionType">
                                        <option value="SELECT" {{ question.getQuestionType() == 'SELECT' ? "selected" : "" }}>SELECT</option>
                                        <option value="DML" {{ question.getQuestionType() == 'DML' ? "selected" : "" }}>DML</option>
                                    </select>
                                    <label for="questionSolution">Question Solution</label>
                                    <textarea class="form-control" name="question[question_solution]" rows="4" required>{{ question.getQuestionSolution() }}</textarea>
                                    <label for="questionProbe">Question Probe</label>
                                    <textarea class="form-control" name="question[question_probe]" rows="4">{{ question.getQuestionProbe() }}</textarea>
