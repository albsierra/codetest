                                    <label for="exerciseType">Exercise Type</label>
                                    <select name="exercise[exercise_type]" id="exerciseType">
                                        {% for sqlType in main.getTypeProperty('sqlTypes') %}
                                        <option value="{{ sqlType }}" {{ exercise.getExerciseType() == sqlType ? "selected" : "" }} . >{{ sqlType }}</option>
                                        {% endfor %}
                                    </select>
                                    <label for="exerciseDBMS">Exercise DBMS</label>
                                    <select name="exercise[exercise_dbms]" id="exerciseDBMS">
                                        {% for index, dbms in main.getTypeProperty('dbConnections') %}
                                        <option value="{{ index }}" {{ exercise.getExerciseDbms() == index ? "selected" : "" }} . >{{ dbms.name }}</option>
                                        {% endfor %}
                                    </select>
                                    <label for="exerciseDatabase">Exercise Database</label>
                                    <input type="text" name="exercise[exercise_database]" value="{{ exercise.getExerciseDatabase() }}">
                                    <label for="exerciseSolution">Exercise Solution</label>
                                    <textarea class="form-control" name="exercise[exercise_solution]" rows="4" required>{{ exercise.getExerciseSolution() }}</textarea>
                                    <label for="exerciseProbe">Exercise Probe</label>
                                    <textarea class="form-control" name="exercise[exercise_probe]" rows="4">{{ exercise.getExerciseProbe() }}</textarea>
                                    <label for="exerciseOnfly">Database Onfly</label>
                                    <textarea class="form-control" name="exercise[exercise_onfly]" rows="4">{{ exercise.getExerciseOnfly() }}</textarea>
