<html>
	<body>
		<h3>lines preceded by -: expected output</h3>
		<h3>lines preceded by +: got output</h3>
		<pre>
<?php
require_once "../../initTsugi.php";

$question = new \CT\CT_Question($_GET['questionId']);
$answer = new \CT\CT_Answer($_GET['answerId']);
$question = $question->getQuestionByType();
$class =  get_class($question);
if($class == "CT\CT_QuestionCode") {
	$solution = $question->getOutputFromCode(
		$answer->getAnswerTxt(), $question->getQuestionLanguage(), $question->getQuestionInputGrade()
	);
	if ($USER->instructor) {
		$answertxt = $question->getQuestionOutputGrade();
	} else {
		$answertxt = $question->getQuestionOutputTest();
	}
	echo \CT\CT_Answer::getDiffWithSolution($solution, $answertxt);
} elseif($class == "CT\CT_QuestionSQL") {
	$solution = $question->getQueryResult();
	$answertxt = $question->getQueryResult($answer->getAnswerTxt());
	if(is_array($solution) && is_array($answertxt)) {
		echo \CT\CT_Answer::getDiffWithSolution(var_dump($solution), var_dump($answertxt));
	}
}
?>
		</pre>