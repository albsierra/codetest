<html>
	<body>
		<h3>lines preceded by -: expected output</h3>
		<h3>lines preceded by +: got output</h3>
		<pre>
<?php
require_once "../../initTsugi.php";

$exercise = new \CT\CT_Exercise($_GET['exerciseId']);
$answer = new \CT\CT_Answer($_GET['answerId']);
$exercise = $exercise->getExerciseByType();
$class =  get_class($exercise);
if($class == "CT\CT_ExerciseCode") {
	$solution = $exercise->getOutputFromCode(
		$answer->getAnswerTxt(), $exercise->getExerciseLanguage(), $exercise->getExerciseInputGrade()
	);
	if ($USER->instructor) {
		$answertxt = $exercise->getExerciseOutputGrade();
	} else {
		$answertxt = $exercise->getExerciseOutputTest();
	}
	echo \CT\CT_Answer::getDiffWithSolution($solution, $answertxt);
} elseif($class == "CT\CT_ExerciseSQL") {
	$solution = $exercise->getQueryResult();
	$answertxt = $exercise->getQueryResult($answer->getAnswerTxt());
	if(is_array($solution) && is_array($answertxt)) {
		echo \CT\CT_Answer::getDiffWithSolution(var_dump($solution), var_dump($answertxt));
	}
}
?>
		</pre>