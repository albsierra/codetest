<html>
	<body>
		<pre>
<?php
require_once "../../initTsugi.php";

if ($USER->instructor) {
    $question = new \CT\CT_Question($_GET['questionId']);
    $answer = new \CT\CT_Answer($_GET['answerId']);
	$question = $question->getQuestionByType();
	$class =  get_class($question);
	if($class == "CT\CT_QuestionCode") {
		$solution = $question->getOutputFromCode(
            	$answer->getAnswerTxt(), $question->getQuestionLanguage(), $question->getQuestionInputGrade()
        	);
		$answertxt = $question->getQuestionOutputGrade();
	} elseif($class == "CT\CT_QuestionSQL") {
		$solution = $question->getQueryResult();
		$answertxt = $question->getQueryResult($answer->getAnswerTxt());
		$solution = is_array($solution) ? var_dump($solution) : $solution;
		$answertxt = is_array($answertxt) ? var_dump($answertxt) : $answertxt;
	}
    echo \CT\CT_Answer::getDiffWithSolution($solution, $answertxt);
}
?>
		</pre>