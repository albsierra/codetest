<?php

require_once('../config.php');
require_once('dao/CT_DAO.php');
require_once('dao/CT_Main.php');
require_once('dao/CT_Question.php');

use \Tsugi\Core\LTIX;
use \CT\DAO\CT_DAO;
use \CT\DAO\CT_Main;
use \CT\DAO\CT_Question;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

$SetID = $_SESSION["ct_id"];

$main = new CT_Main($_SESSION["ct_id"]);

$toolTitle = $main->getTitle();

if (!$toolTitle) {
    $toolTitle = "Code Test";
}

$questions = $main->getQuestions();
$totalQuestions = count($questions);

$moreToSubmit = false;

include("menu.php");

// Start of the output
$OUTPUT->header();

include("tool-header.html");

$OUTPUT->bodyStart();

$OUTPUT->topNav($menu);

echo '<div class="container-fluid">';

$OUTPUT->flashMessages();

$OUTPUT->pageTitle($toolTitle, true, false);

if ($totalQuestions > 0) {
        foreach ($questions as $question) {
            $questionId = $question->getQuestionId();
            $answer = $CT_DAO->getStudentAnswerForQuestion($questionId, $USER->id);
            ?>
            <h2 class="small-hdr <?= $question->getQuestionNum() == 1 ? 'hdr-notop-mrgn' : '' ?>">
                <small>Question <?= $question->getQuestionNum() ?></small>
            </h2>
            <div id="questionAnswer<?= $questionId ?>">
                <?php
                if (!$answer) {
                    ?>
                    <form id="answerForm<?= $questionId ?>" action="actions/AnswerQuestion.php"
                          method="post">
                        <input type="hidden" name="questionId" value="<?= $questionId ?>">
                        <div class="form-group">
                            <label class="h3"
                                   for="answerText<?= $questionId ?>"><?= $question->getQuestionTxt() ?></label>
                            <textarea class="form-control" id="answerText<?= $questionId ?>"
                                      name="answerText" rows="5"></textarea>
                        </div>
                        <button type="button" class="btn btn-success"
                                onclick="answerQuestion(<?= $questionId ?>);">Submit
                        </button>
                    </form>
                    <?php
                } else {
                    $dateTime = new DateTime($answer['modified']);
                    $formattedDate = $dateTime->format("m/d/y") . " | " . $dateTime->format("h:i A");
                    ?>
                    <h3 class="sub-hdr"><?= $question->getQuestionTxt() ?></h3>
                    <p><?= $formattedDate ?></p>
                    <p><?= $answer["answer_txt"] ?></p>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
} else {
    ?>
        <p class="lead">Your instructor has not yet configured this learning app.</p>
    </div>
    <?php
}

if ($USER->instructor) {
    $OUTPUT->helpModal("Code Test Help", __('
                        <h4>Student View</h4>
                        <p>You are seeing what a student will see when they access this tool. However, your answers will be cleared once you leave student view.</p>
                        <p>Your answers will not show up in any of the results.</p>'));
} else {
    $OUTPUT->helpModal("Code Test Help", __('
                        <h4>What do I do?</h4>
                        <p>Answer each question below. You must submit every question individually. Once you submit an answer to a question you cannot edit your answer.</p>'));
}

$OUTPUT->footerStart();

include("tool-footer.html");

$OUTPUT->footerEnd();
