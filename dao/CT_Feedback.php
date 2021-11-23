<?php

namespace CT;

use \Tsugi\Core\Result;

class CT_Feedback implements \JsonSerializable
{
    private $id;
    private $idQuestion;
    private $ctId;
    private $date;
    private $user;
    private $understandabilityScore;
    private $difficultyScore;
    private $timeScore;

public function __construct(){
 
}

public static function constructValues($idQuestion, $user, $understandabilityScore, $difficultyScore, $timeScore){
    $instance = new Self();
    $instance->idQuestion = $idQuestion;
    $instance->user = $user;
    $instance->understandabilityScore = $understandabilityScore;
    $instance->difficultyScore = $difficultyScore;
    $instance->timeScore = $timeScore;
    $instance->ctId = $_SESSION['ct_id'];
    return $instance;
}


public function save() {
        global $CFG;
      
        //url to save the feedback
        $url = $CFG->repositoryUrl . "/api/feedback/tickets";
        //url to update the Test score
        $urlUpdateTest = $CFG->repositoryUrl . "/api/feedback/updateTest";
        
        //url to update the questions score
        $urlUpdateQuestion = $CFG->repositoryUrl . "/api/feedback/updateQuestion";
        
        //save Feedback
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', CT_Test::getToken()));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($curl);
        curl_close($curl);
        
        //Method to update the score
        $test = $this->saveAverageGrade();
        
        //update score on the repo
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', CT_Test::getToken()));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT' );
        
        //takes the url for a test or question
        if($test->questions){
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($test));
        curl_setopt($curl, CURLOPT_URL, $urlUpdateTest);
        
        }else{
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this));
            curl_setopt($curl, CURLOPT_URL, $urlUpdateQuestion);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $code =curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        return $code;
        
    }
    
    //Updates the score of the test or question
public function saveAverageGrade() {
        global $CFG;
        
        //call to recover the object from the repo
        $url = $CFG->repositoryUrl . "/api/tests/getTestQuestionId1/".$this->getIdQuestion();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', CT_Test::getToken()));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this));
 
        curl_setopt($curl, CURLOPT_URL, $url);
       
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        
        $response = json_decode($result);
        
        //if obejct->questions is a Test object
        if ($response->questions) {
            foreach ($response->questions as $key => $question) {
                if ($response->questions[$key]->id == $this->getIdQuestion()) {
                    //takes the current number of votes and scores
                    $nVotes = $response->questions[$key]->numberVotes;
                    $understability = $response->questions[$key]->averageGradeUnderstability;
                    $difficulty = $response->questions[$key]->averageGradeDifficulty;
                    $time = $response->questions[$key]->averageGradeTime;

                    //update the scores
                    $response->questions[$key]->averageGradeUnderstability = (($nVotes * $understability) + $this->getUnderstandabilityScore()) / ($nVotes + 1);

                    $response->questions[$key]->averageGradeDifficulty = (($nVotes * $difficulty) + $this->getDifficultyScore()) / ($nVotes + 1);

                    $response->questions[$key]->averageGradeTime = (($nVotes * $time) + $this->getTimeScore()) / ($nVotes + 1);

                    $response->questions[$key]->numberVotes = $nVotes + 1;

                    $response->questions[$key]->averageGrade = ($response->questions[$key]->averageGradeUnderstability + $response->questions[$key]->averageGradeDifficulty + $response->questions[$key]->averageGradeTime) / 3;
                }
            }
            //else is a Question object
        } else {
            //takes the current number of votes and scores
            $nVotes = $response->numberVotes;
            $understability = $response->averageGradeUnderstability;
            $difficulty = $response->averageGradeDifficulty;
            $time = $response->averageGradeTime;

            //update the scores
            $response->averageGradeUnderstability = (($nVotes * $understability) + $this->getUnderstandabilityScore()) / ($nVotes + 1);

            $response->averageGradeDifficulty = (($nVotes * $difficulty) + $this->getDifficultyScore()) / ($nVotes + 1);

            $response->averageGradeTime = (($nVotes * $time) + $this->getTimeScore()) / ($nVotes + 1);

            $response->numberVotes = $nVotes + 1;

            $response->averageGrade = ($response->averageGradeUnderstability + $response->averageGradeDifficulty + $response->averageGradeTime) / 3;
        }
        
        return $response;
    }
    
     static function MapJsonToFeedbacksArray($json) {
        $response = json_decode($json);
        $feedbacks = array();
            foreach ($response as $feedback) {  
               
                $CTFeedback = new CT_Feedback();
                $CTFeedback->setId($feedback->id);
                $CTFeedback->setIdQuestion($feedback->idQuestion);
                $CTFeedback->setCtId($feedback->ctId);
                $CTFeedback->setDate($feedback->date);
                $user = new CT_User();
                $user->setUserId($feedback->user->id);
                $user->setDisplayname($feedback->user->displayname);
                $user->setEmail($feedback->user->email);
                $user->setProfileId($feedback->user->profile_id);
                $CTFeedback->setUser($user);
                $CTFeedback->setUnderstandabilityScore($feedback->understandabilityScore);
                $CTFeedback->setDifficultyScore($feedback->difficultyScore);
                $CTFeedback->setTimeScore($feedback->timeScore);
               
                array_push($feedbacks, $CTFeedback);
        }
        return $feedbacks;
    }

    public function jsonSerialize(){
        return [
            'idQuestion' =>  $this->getIdQuestion(),
            'ctId' => $this->getCtId(),
            'user' => $this->getUser(),
            'understandabilityScore' => $this->getUnderstandabilityScore(),
            'difficultyScore' => $this->getDifficultyScore(),
            'timeScore' => $this->getTimeScore(),
        ];
    }
    
    
    static function getFeedbacks($questions, $students) {
        global $CFG;

        $questions1 = array_map(function ($a) {
            return $a->getQuestionId();
        }, $questions);

        $students1 = array_map(function ($a) {
            return $a->getUserId();
        }, $students);

        $ctId=[$_SESSION['ct_id']];
        $url = $CFG->repositoryUrl . "/api/feedback/getFeedbackByIds";
          
        $array = [$questions1, $students1, $ctId];
//        array_push($array, $questions, $students, $_SESSION['ct_id']);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', CT_Test::getToken()));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($array));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);
        
        return self::MapJsonToFeedbacksArray($result);
    }

    public function getId() {
    return $this->id;
}

public function getIdQuestion() {
    return $this->idQuestion;
}

public function getDate() {
    return $this->date;
}

public function getUser() {
    return $this->user;
}

public function getUnderstandabilityScore() {
    return $this->understandabilityScore;
}

public function getDifficultyScore() {
    return $this->difficultyScore;
}

public function getTimeScore() {
    return $this->timeScore;
}

public function setId($id): void {
    $this->id = $id;
}

public function setIdQuestion($idQuestion): void {
    $this->idQuestion = $idQuestion;
}

public function setDate($date): void {
    $this->date = $date;
}

public function setUser($user): void {
    $this->user = $user;
}

public function setUnderstandabilityScore($understandabilityScore): void {
    $this->understandabilityScore = $understandabilityScore;
}

public function setDifficultyScore($difficultyScore): void {
    $this->difficultyScore = $difficultyScore;
}

public function setTimeScore($timeScore): void {
    $this->timeScore = $timeScore;
}

public function getCtId() {
    return $this->ctId;
}

public function setCtId($ctId): void {
    $this->ctId = $ctId;
}



}
