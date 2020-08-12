<?php

require_once('initTsugi.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$pointsPossible = $main->getPoints();

$students = \CT\CT_User::getUsersWithAnswers($_SESSION["ct_id"]);
$studentAndDate = array();
foreach($students as $student) {
    $studentAndDate[$student->getUserId()] = new DateTime($student->getMostRecentAnswerDate($_SESSION["ct_id"]));
}

$questions = $main->getQuestions();
$totalQuestions = count($questions);

include('views/dao/menu.php');

// Start of the output
$OUTPUT->header();

include('views/dao/tool-header.html');

$OUTPUT->bodyStart();

$OUTPUT->topNav($menu);

echo '<div class="container-fluid">';

$OUTPUT->flashMessages();

$OUTPUT->pageTitle('Grade', false, false);

?>
<h3>Set Points Possible <small>Default 100</small></h3>
<form class="form-inline" action="actions/UpdatePointsPossible.php" method="post">
    <div class="form-group">
        <label for="points_possible">Points Possible: </label>
        <input type="text" class="form-control" id="points_possible" name="points_possible" value="<?=$pointsPossible?>">
    </div>
    <button type="submit" class="btn btn-default">Submit</button>
</form>
<h3>Grade Students</h3>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
        <th class="col-sm-5">Student Name</th>
        <th class="col-sm-2">Last Updated</th>
        <th class="col-sm-2">Completed</th>
        <th class="col-sm-3">Grade</th>
        </thead>
        <tbody>
<?php
// Sort students by mostRecentDate desc
arsort($studentAndDate);
foreach ($studentAndDate as $student_id => $mostRecentDate) {
    $user = new \CT\CT_User($student_id);
    if (!$user->isInstructor($CONTEXT->id)) {
        $formattedMostRecentDate = $mostRecentDate->format("m/d/y") . " | " . $mostRecentDate->format("h:i A");
        $numberAnswered = $user->getNumberQuestionsAnswered($_SESSION["ct_id"]);
        $grade = $user->getGrade($_SESSION["ct_id"]);
        ?>
        <tr>
            <td><?= $user->getDisplayname() ?></td>
            <td><?= $formattedMostRecentDate ?></td>
            <td><?= $numberAnswered . '/' . $totalQuestions ?></td>
            <td>
                <form class="form-inline" action="actions/GradeStudent.php" method="post">
                    <input type="hidden" name="student_id" value="<?=$user->getUserId()?>">
                    <div class="form-group">
                        <label>
                        <input type="text" class="form-control" name="grade" value="<?=$grade->getGrade()?>">/<?=$pointsPossible?>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-default">Update</button>
                </form>
            </td>
        </tr>
        <?php
    }
}
?>
        </tbody>
    </table>
</div>
<?php

echo ("</div>"); // End container

$OUTPUT->footerStart();

include('views/dao/tool-footer.html');

$OUTPUT->footerEnd();
