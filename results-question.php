<?php

require_once('../config.php');
require_once('dao/CT_DAO.php');
require_once('dao/CT_Main.php');
require_once('dao/CT_Question.php');

use CT\DAO\CT_DAO;
use CT\DAO\CT_Main;
use CT\DAO\CT_Question;
use Tsugi\Core\LTIX;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

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

$OUTPUT->pageTitle('Results <small>by Question</small>', true, false);

?>
        <section id="questionResponses">
            <div class="list-group">
                <?php
                foreach ($questions as $question) {
                    $questionId = $question->getQuestionId();
                    $responses = $CT_DAO->getAllAnswersToQuestion($questionId);
                    $numberResponses = count($responses);
                    ?>
                    <div class="list-group-item response-list-group-item">
                        <div class="row">
                            <div class="col-sm-3 header-col">
                                <a href="#responses<?=$questionId?>" class="h4 response-collapse-link" data-toggle="collapse">
                                    Question <?=$question->getQuestionNum()?>
                                    <span class="fa fa-chevron-down rotate" aria-hidden="true"></span>
                                </a>
                            </div>
                            <div class="col-sm-offset-1 col-sm-8 header-col">
                                <div class="flx-cntnr flx-row flx-nowrap flx-start">
                                    <span class="flx-grow-all"><?=$question->getQuestionTxt()?></span>
                                    <span class="badge response-badge"><?=$numberResponses?></span>
                                </div>
                            </div>
                            <div id="responses<?=$questionId?>" class="col-xs-12 results-collapse collapse">
                                <?php
                                // Sort by modified date with most recent at the top
                                usort($responses, 'response_date_compare');
                                foreach ($responses as $response) {
                                    if (!$CT_DAO->isUserInstructor($CONTEXT->id, $response["user_id"])) {
                                        $responseDate = new DateTime($response["modified"]);
                                        $formattedResponseDate = $responseDate->format("m/d/y")." | ".$responseDate->format("h:i A");
                                        ?>
                                        <div class="row response-row">
                                            <div class="col-sm-3">
                                                <h5><?=$CT_DAO->findDisplayName($response["user_id"])?></h5>
                                                <p><?=$formattedResponseDate?></p>
                                            </div>
                                            <div class="col-sm-offset-1 col-sm-8">
                                                <p class="response-text"><?=$response["answer_txt"]?></p>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </section>
    </div>

<?php

$OUTPUT->helpModal("Quick Write Help", __('
                        <h4>Viewing Results</H4>
                        <p>You are viewing the results by question. Click on a question below to see what students answered for that question.</p>
                        <p>For each question, students are sorted with the most recently modified at the top.</p>'));

$OUTPUT->footerStart();

include("tool-footer.html");

$OUTPUT->footerEnd();

function response_date_compare($response1, $response2) {
    $time1 = strtotime($response1['modified']);
    $time2 = strtotime($response2['modified']);
    // Most recent at top
    return $time2 - $time1;
}