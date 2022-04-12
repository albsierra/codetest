                                    <label for="exerciseInputTest">Input for student</label>
                                    <textarea class="form-control" name="exercise[exercise_input_test]" id="exerciseInputTest" rows="4" required>{{ exercise.getExerciseInputTest() }}</textarea>
                                    <label for="exerciseInputGrade">Input for grade</label>
                                    <textarea class="form-control" name="exercise[exercise_input_grade]" id="exerciseInputGrade" rows="4" required>{{ exercise.getExerciseInputGrade }}</textarea>
                                    <br />
                                    <select name="exercise[exercise_language]" id="exerciseLanguage">
                                        {% for index, language in main.getProperty('codeLanguages') %}
                                        <option value="{{ index }}" {{ exercise.getExerciseLanguage() == index ? "selected" : "" }} . >{{ language.name }}</option>
                                        {% endfor %}
                                    </select>
                                    <label for="exerciseSolution">Code Solution</label>
                                    <textarea class="form-control" name="exercise[exercise_solution]" id="exerciseSolution" rows="10" required>{{ exercise.getExerciseSolution }}</textarea>
