<?php

require_once('../config.php');
require_once('dao/CT_DAO.php');
require_once('dao/CT_Main.php');
require_once('dao/CT_Question.php');
require_once('dao/CT_Answer.php');

use \Tsugi\Core\LTIX;
use \CT\DAO\CT_DAO;
use \CT\DAO\CT_Main;
use CT\DAO\CT_Question;
use CT\DAO\CT_Answer;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

include("menu.php");

// Start of the output
$OUTPUT->header();

include("tool-header.html");

$OUTPUT->bodyStart();

$main = new CT_Main($_SESSION["ct_id"]);

$toolTitle = $main->getTitle();

if (!$toolTitle) {
    $toolTitle = "Code Test";
}

$questions = $main->getQuestions();

// Clear any preview responses if there are questions
if ($questions) {
    $instructors = $CT_DAO->findInstructors($CONTEXT->id);
    foreach($instructors as $instructor) {
        CT_Answer::deleteAnswers($questions, $instructor["user_id"]);
    }
}

$OUTPUT->topNav($menu);

echo('<div class="container-fluid">');

$OUTPUT->flashMessages();

?>
        <div id="toolTitle" class="h1">
            <button id="helpButton" type="button" class="btn btn-link pull-right" data-toggle="modal" data-target="#helpModal"><span class="fa fa-question-circle" aria-hidden="true"></span> Help</button>
            <span class="flx-cntnr flx-row flx-nowrap flx-start">
                <span class="title-text-span" onclick="editTitleText();" tabindex="0"><?=$toolTitle?></span>
                <a id="toolTitleEditLink" class="toolTitleAction" href="javascript:void(0);" onclick="editTitleText();">
                    <span class="fa fa-fw fa-code" aria-hidden="true"></span>
                    <span class="sr-only">Edit Title Text</span>
                </a>
            </span>
        </div>
        <form id="toolTitleForm" action="actions/UpdateMainTitle.php" method="post" style="display:none;">
                <label for="toolTitleInput" class="sr-only">Title Text</label>
                <div class="h1 flx-cntnr flx-row flx-nowrap flx-start">
                    <textarea class="title-edit-input flx-grow-all" id="toolTitleInput" name="toolTitle" rows="2"><?=$toolTitle?></textarea>
                    <a id="toolTitleSaveLink" class="toolTitleAction" href="javascript:void(0);">
                        <span class="fa fa-fw fa-save" aria-hidden="true"></span>
                        <span class="sr-only">Save Title Text</span>
                    </a>
                    <a id="toolTitleCancelLink" class="toolTitleAction" href="javascript:void(0);">
                        <span class="fa fa-fw fa-times" aria-hidden="true"></span>
                        <span class="sr-only">Cancel Title Text</span>
                    </a>
                </div>
        </form>
        <p class="lead">Add questions to quickly collect feedback from your students.</p>
        <section id="theQuestions">
            <?php
            foreach ($questions as $question) {
                $questionId = $question->getQuestionId();
                ?>
                <div id="questionRow<?=$questionId?>" class="h3 inline flx-cntnr flx-row flx-nowrap flx-start question-row" data-question-number="<?=$question->getQuestionNum()?>">
                    <div class="question-number"><?=$question->getQuestionNum()?>.</div>
                    <div class="flx-grow-all question-text">
                        <span class="question-text-span" onclick="editQuestionText(<?=$questionId?>)" id="questionText<?=$questionId?>" tabindex="0"><?= $question->getQuestionTxt() ?></span>
                        <form id="questionTextForm<?=$questionId?>" onsubmit="return confirmDeleteQuestionBlank(<?=$questionId?>)" action="actions/AddOrEditQuestion.php" method="post" style="display:none;">
                            <input type="hidden" name="questionId" value="<?=$questionId?>">
                            <label for="questionTextInput<?=$questionId?>" class="sr-only">Question Text</label>
                            <textarea class="form-control" id="questionTextInput<?=$questionId?>" name="questionText" rows="2" required><?=$question->getQuestionTxt()?></textarea>
                        </form>
                    </div>
                    <a id="questionEditAction<?=$questionId?>" href="javascript:void(0);" onclick="editQuestionText(<?=$questionId?>)">
                        <span class="fa fa-fw fa-pencil" aria-hidden="true"></span>
                        <span class="sr-only">Edit Question Text</span>
                    </a>
                    <a id="questionReorderAction<?=$questionId?>" href="javascript:void(0);" onclick="moveQuestionUp(<?=$questionId?>)">
                        <span class="fa fa-fw fa-chevron-circle-up" aria-hidden="true"></span>
                        <span class="sr-only">Move Question Up</span>
                    </a>
                    <a id="questionDeleteAction<?=$questionId?>" href="javascript:void(0);" onclick="deleteQuestion(<?=$questionId?>)">
                        <span aria-hidden="true" class="fa fa-fw fa-trash"></span>
                        <span class="sr-only">Delete Question</span>
                    </a>
                    <a id="questionSaveAction<?=$questionId?>" href="javascript:void(0);" style="display:none;">
                        <span aria-hidden="true" class="fa fa-fw fa-save"></span>
                        <span class="sr-only">Save Question</span>
                    </a>
                    <a id="questionCancelAction<?=$questionId?>" href="javascript:void(0);" style="display: none;">
                        <span aria-hidden="true" class="fa fa-fw fa-times"></span>
                        <span class="sr-only">Cancel Question</span>
                    </a>
                </div>
                <?php
            }
            ?>
            <div id="newQuestionRow" class="h3 inline flx-cntnr flx-row flx-nowrap flx-start question-row" style="display:none;" data-question-number="<?=$questions ? count($questions)+1 : 1?>">
                <div id="newQuestionNumber"><?=$questions ? count($questions)+1 : 1?>.</div>
                <div class="flx-grow-all question-text">
                    <form id="questionTextForm-1" action="actions/AddOrEditQuestion.php" method="post">
                        <input type="hidden" name="questionId" value="-1">
                        <label for="questionTextInput-1" class="sr-only">Question Text</label>
                        <textarea class="form-control" id="questionTextInput-1" name="questionText" rows="2" required></textarea>
                    </form>
                </div>
                <a id="questionSaveAction-1" href="javascript:void(0);">
                    <span aria-hidden="true" class="fa fa-fw fa-save"></span>
                    <span class="sr-only">Save Question</span>
                </a>
                <a id="questionCancelAction-1" href="javascript:void(0);">
                    <span aria-hidden="true" class="fa fa-fw fa-times"></span>
                    <span class="sr-only">Cancel Question</span>
                </a>
            </div>
        </section>
        <section id="addQuestions">
            <span class="h3"><a href="javascript:void(0);" id="addQuestionLink" onclick="showNewQuestionRow();" class="btn btn-success"><span class="fa fa-plus" aria-hidden="true"></span> Add Question</a></span>
            <span class="h4 import-link"><a href="#importModal" data-toggle="modal"><span class="fa fa-files-o import-icon" aria-hidden="true"></span> Reuse Previous Question(s)</a></span>
        </section>
    </div>

    <input type="hidden" id="sess" value="<?php echo($_GET["PHPSESSID"]) ?>">
<?php

include("help.php");
include("import.php");

$OUTPUT->footerStart();

include("tool-footer.html");

$OUTPUT->footerEnd();
