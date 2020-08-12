<?php
require_once('initTsugi.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);

if (!$main->getTitle()) {
    $main->setTitle("Code Test");
    $main->save();
}

$questions = $main->getQuestions();

// Clear any preview responses if there are questions
if ($questions) \CT\CT_Answer::deleteInstructorAnswers($questions, $CONTEXT->id);

// Start of the output
$OUTPUT->header();

include('views/dao/tool-header.html');

$OUTPUT->bodyStart();

include('views/dao/menu.php');
$OUTPUT->topNav($menu);
?>
    <div class="container-fluid">
<?php
$OUTPUT->flashMessages();

include("views/main/mainTitle.php");
?>
        <section id="theQuestions">
            <?php foreach ($questions as $question) {
                include("views/question/instructorQuestion.php");
            }
            include("views/question/newQuestionForm.php");
            ?>
        </section>
        <section id="addQuestions">
            <?php include("views/question/addQuestion.php");?>
        </section>
    </div>

    <input type="hidden" id="sess" value="<?php echo($_GET["PHPSESSID"]) ?>">
<?php

include('views/dao/help.php');
include('views/dao/import.php');

$OUTPUT->footerStart();

include('views/dao/tool-footer.html');

$OUTPUT->footerEnd();
