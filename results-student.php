<?php

require_once('config.php');

use \Tsugi\Core\LTIX;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new \CT\CT_DAO();

$students = \CT\CT_User::getUsersWithAnswers($_SESSION["ct_id"]);
$studentAndDate = array();
foreach($students as $student) {
    $studentAndDate[$student->getUserId()] = new DateTime($student->getMostRecentAnswerDate($_SESSION["ct_id"]));
}


$main = new \CT\CT_Main($_SESSION["ct_id"]);
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
                        $user = new \CT\CT_User($student_id);
                        if (!$user->isInstructor($CONTEXT->id)) {
                            $formattedMostRecentDate = $mostRecentDate->format("m/d/y") . " | " . $mostRecentDate->format("h:i A");
                            $numberAnswered = $user->getNumberQuestionsAnswered($_SESSION["ct_id"]);
                            ?>
                            <div class="list-group-item response-list-group-item">
                                <div class="row">
                                    <div class="col-xs-6 header-col">
                                        <a href="#responses<?= $user->getUserId() ?>" class="h4 response-collapse-link" data-toggle="collapse">
                                            <?= $user->getDisplayname() ?>
                                            <span class="fa fa-chevron-down rotate" aria-hidden="true"></span>
                                        </a>
                                    </div>
                                    <div class="col-xs-3 text-center header-col">
                                        <span class="h5 inline"><?= $formattedMostRecentDate ?></span>
                                    </div>
                                    <div class="col-xs-3 text-center header-col">
                                        <span class="h5 inline"><?= $numberAnswered . '/' . $totalQuestions ?></span>
                                    </div>
                                    <div id="responses<?= $user->getUserId() ?>" class="col-xs-12 results-collapse collapse">
                                        <?php
                                        foreach ($questions as $question) {
                                            $answer = $user->getAnswerForQuestion($question->getQuestionId());
                                            ?>
                                            <div class="row response-row">
                                                <div class="col-sm-3">
                                                    <h4 class="small-hdr hdr-notop-mrgn">
                                                        <small>Question <?= $question->getQuestionNum() ?></small>
                                                    </h4>
                                                    <h5 class="sub-hdr"><?= $question->getQuestionTxt() ?></h5>
                                                </div>
                                                <div class="col-sm-offset-1 col-sm-8">
                                                    <p class="response-text"><?= $answer->getAnswerTxt() ?></p>
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
