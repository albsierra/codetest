<?php

require_once('../config.php');
require_once('dao/CT_DAO.php');
require_once('dao/CT_Main.php');
require_once('dao/CT_Question.php');
require_once('dao/CT_User.php');

use \Tsugi\Core\LTIX;
use \CT\DAO\CT_DAO;
use \CT\DAO\CT_Main;
use \CT\DAO\CT_Question;
use \CT\DAO\CT_User;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

$students = $CT_DAO->getUsersWithAnswers($_SESSION["ct_id"]);
$studentAndDate = array();
foreach($students as $student) {
    $studentAndDate[$student["user_id"]] = new DateTime($CT_DAO->getMostRecentAnswerDate($student["user_id"], $_SESSION["ct_id"]));
}


$main = new CT_Main($_SESSION["ct_id"]);
$questions = $main->getQuestions();
$totalQuestions = count($questions);

include("menu.php");

// Start of the output
$OUTPUT->header();

include("tool-header.html");

$OUTPUT->bodyStart();

$OUTPUT->topNav($menu);

echo '<div class="container-fluid">';

$OUTPUT->flashMessages();

$OUTPUT->pageTitle('Results <small>by Student</small>', true, false);

?>
        <section id="studentResponses">
            <div class="panel panel-info">
                <div class="panel-heading response-panel-header">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4 class="results-table-hdr">Student Name</h4>
                        </div>
                        <div class="col-xs-3 text-center">
                            <h4 class="results-table-hdr">Last Updated</h4>
                        </div>
                        <div class="col-xs-3 text-center">
                            <h4 class="results-table-hdr">Completed</h4>
                        </div>
                    </div>
                </div>
                <div class="list-group">
                    <?php
                    // Sort students by mostRecentDate desc
                    arsort($studentAndDate);
                    foreach ($studentAndDate as $student_id => $mostRecentDate) {
                        $user = new CT_User($student_id);
                        if (!$user->isInstructor($CONTEXT->id)) {
                            $formattedMostRecentDate = $mostRecentDate->format("m/d/y") . " | " . $mostRecentDate->format("h:i A");
                            $numberAnswered = $CT_DAO->getNumberQuestionsAnswered($student_id, $_SESSION["ct_id"]);
                            ?>
                            <div class="list-group-item response-list-group-item">
                                <div class="row">
                                    <div class="col-xs-6 header-col">
                                        <a href="#responses<?= $student_id ?>" class="h4 response-collapse-link" data-toggle="collapse">
                                            <?= $CT_DAO->findDisplayName($student_id) ?>
                                            <span class="fa fa-chevron-down rotate" aria-hidden="true"></span>
                                        </a>
                                    </div>
                                    <div class="col-xs-3 text-center header-col">
                                        <span class="h5 inline"><?= $formattedMostRecentDate ?></span>
                                    </div>
                                    <div class="col-xs-3 text-center header-col">
                                        <span class="h5 inline"><?= $numberAnswered . '/' . $totalQuestions ?></span>
                                    </div>
                                    <div id="responses<?= $student_id ?>" class="col-xs-12 results-collapse collapse">
                                        <?php
                                        foreach ($questions as $question) {
                                            $response = $CT_DAO->getStudentAnswerForQuestion($question->getQuestionId(), $student_id);
                                            ?>
                                            <div class="row response-row">
                                                <div class="col-sm-3">
                                                    <h4 class="small-hdr hdr-notop-mrgn">
                                                        <small>Question <?= $question->getQuestionNum() ?></small>
                                                    </h4>
                                                    <h5 class="sub-hdr"><?= $question->getQuestionTxt() ?></h5>
                                                </div>
                                                <div class="col-sm-offset-1 col-sm-8">
                                                    <p class="response-text"><?= $response["answer_txt"] ?></p>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>
<?php

$OUTPUT->helpModal("Code Test Help", __('
                        <h4>Viewing Results</H4>
                        <p>You are viewing the results by student. Click on a student below to see how that student answered each question.</p>
                        <p>Students are sorted with the most recently submitted at the top of the list.</p>'));


$OUTPUT->footerStart();

include("tool-footer.html");

$OUTPUT->footerEnd();
