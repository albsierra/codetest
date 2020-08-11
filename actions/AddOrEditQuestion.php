<?php
require_once "../config.php";
require_once('../dao/CT_DAO.php');
require_once('../dao/CT_Main.php');
require_once('../dao/CT_Question.php');

use \Tsugi\Core\LTIX;
use \CT\dao\CT_DAO;
use \CT\dao\CT_Main;
use \CT\dao\CT_Question;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

if ($USER->instructor) {

    $result = array();

    $questionId = $_POST["questionId"];
    $questionText = $_POST["questionText"];

    if (isset($questionText) && trim($questionText) != '') {
        if ($questionId > -1) {
            // Existing question
            $question = new CT_Question($questionId);
            $question->setQuestionTxt($questionText);
            $question->save();
        } else {
            // New question
            $main = new CT_Main($_SESSION["ct_id"]);
            $question = $main->createQuestion($questionText);

            // Create new question markup
            ob_start();
            ?>
            <div id="questionRow<?=$question->getQuestionId()?>" class="h3 inline flx-cntnr flx-row flx-nowrap flx-start question-row" data-question-number="<?=$question->getQuestionNum()?>">
                <div class="question-number"><?=$question->getQuestionNum()?>.</div>
                <div class="flx-grow-all question-text">
                    <span class="question-text-span" onclick="editQuestionText(<?=$question->getQuestionId()?>)" id="questionText<?=$question->getQuestionId()?>"><?= $question->getQuestionTxt() ?></span>
                    <form id="questionTextForm<?=$question->getQuestionId()?>" onsubmit="return confirmDeleteQuestionBlank(<?=$question->getQuestionId()?>)" action="actions/AddOrEditQuestion.php" method="post" style="display:none;">
                        <input type="hidden" name="questionId" value="<?=$question->getQuestionId()?>">
                        <label for="questionTextInput<?=$question->getQuestionId()?>" class="sr-only">Question Text</label>
                        <textarea class="form-control" id="questionTextInput<?=$question->getQuestionId()?>" name="questionText" rows="2" required><?=$question->getQuestionTxt()?></textarea>
                    </form>
                </div>
                <a id="questionEditAction<?=$question->getQuestionId()?>" href="javascript:void(0);" onclick="editQuestionText(<?=$question->getQuestionId()?>)">
                    <span class="fa fa-fw fa-pencil" aria-hidden="true"></span>
                    <span class="sr-only">Edit Question Text</span>
                </a>
                <a id="questionReorderAction<?=$question->getQuestionId()?>" href="javascript:void(0);" onclick="moveQuestionUp(<?=$question->getQuestionId()?>)">
                    <span class="fa fa-fw fa-chevron-circle-up" aria-hidden="true"></span>
                    <span class="sr-only">Move Question Up</span>
                </a>
                <a id="questionDeleteAction<?=$question->getQuestionId()?>" href="javascript:void(0);" onclick="deleteQuestion(<?=$question->getQuestionId()?>)">
                    <span aria-hidden="true" class="fa fa-fw fa-trash"></span>
                    <span class="sr-only">Delete Question</span>
                </a>
                <a id="questionSaveAction<?=$question->getQuestionId()?>" href="javascript:void(0);" style="display:none;">
                    <span aria-hidden="true" class="fa fa-fw fa-save"></span>
                    <span class="sr-only">Save Question</span>
                </a>
                <a id="questionCancelAction<?=$question->getQuestionId()?>" href="javascript:void(0);" style="display: none;">
                    <span aria-hidden="true" class="fa fa-fw fa-times"></span>
                    <span class="sr-only">Cancel Question</span>
                </a>
            </div>
            <?php
            $result["new_question"] = ob_get_clean();
        }
        $_SESSION['success'] = 'Question Saved.';
    } else {
        if ($questionId > -1) {
            // Blank text means delete question
            $question = new CT_Question($questionId);
            $question->delete();
            // Set question id to false to remove question line
            $questionId = false;
            $_SESSION['success'] = 'Question Deleted.';
        } else {
            $_SESSION['error'] = 'Unable to save blank question.';
        }
    }

    $OUTPUT->buffer=true;
    $result["flashmessage"] = $OUTPUT->flashMessages();

    header('Content-Type: application/json');

    echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG);

    exit;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}

