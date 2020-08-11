<?php

require_once('config.php');
require_once('dao/CT_DAO.php');
require_once('dao/CT_Main.php');
require_once('dao/CT_Question.php');
require_once('dao/CT_Answer.php');
require_once('dao/CT_User.php');

use \Tsugi\Core\LTIX;
use \CT\dao\CT_DAO;
use \CT\dao\CT_Main;
use \CT\dao\CT_Question;
use \CT\dao\CT_Answer;
use \CT\dao\CT_User;

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
    $instructors = CT_User::findInstructors($CONTEXT->id);
    foreach($instructors as $instructor) {
        CT_Answer::deleteAnswers($questions, $instructor->getUserId());
    }
}

$OUTPUT->topNav($menu);

echo('<div class="container-fluid">');

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

include("help.php");
include("import.php");

$OUTPUT->footerStart();

include("tool-footer.html");

$OUTPUT->footerEnd();
