<?php

namespace CT;

use \Tsugi\Core\Result;

class CT_Usage implements \JsonSerializable
{
    private $id;
    private $idExercise;
    private $ctId;
    private $date;
    private $user;
    private $understandabilityScore;
    private $difficultyScore;
    private $timeScore;

public function __construct(){
 
}

public static function constructValues($idExercise, $user, $understandabilityScore, $difficultyScore, $timeScore){
    $instance = new Self();
    $instance->idExercise = $idExercise;
    $instance->user = $user;
    $instance->understandabilityScore = $understandabilityScore;
    $instance->difficultyScore = $difficultyScore;
    $instance->timeScore = $timeScore;
    $instance->ctId = $_SESSION['ct_id'];
    return $instance;
}


public function save() {
        global $CFG;
      
        //url to save the usage
        $url = $CFG->repositoryUrl . "/api/usage/tickets";
        //url to update the Test score
        $urlUpdateTest = $CFG->repositoryUrl . "/api/usage/updateTest";
        
        //url to update the exercises score
        $urlUpdateExercise = $CFG->repositoryUrl . "/api/usage/updateExercise";
        
        //save Usage
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
        
        //takes the url for a test or exercise
        if($test->exercises){
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($test));
        curl_setopt($curl, CURLOPT_URL, $urlUpdateTest);
        
        }else{
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this));
            curl_setopt($curl, CURLOPT_URL, $urlUpdateExercise);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $code =curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        return $code;
        
    }
    
    //Updates the score of the test or exercise
public function saveAverageGrade() {
        global $CFG;
        
        //call to recover the object from the repo
        $url = $CFG->repositoryUrl . "/api/tests/getTestExerciseId1/".$this->getIdExercise();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', CT_Test::getToken()));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this));
 
        curl_setopt($curl, CURLOPT_URL, $url);
       
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        
        $response = json_decode($result);
        
        //if obejct->exercises is a Test object
        if ($response->exercises) {
            foreach ($response->exercises as $key => $exercise) {
                if ($response->exercises[$key]->id == $this->getIdExercise()) {
                    //takes the current number of votes and scores
                    $nVotes = $response->exercises[$key]->numberVotes;
                    $understability = $response->exercises[$key]->averageGradeUnderstability;
                    $difficulty = $response->exercises[$key]->averageGradeDifficulty;
                    $time = $response->exercises[$key]->averageGradeTime;

                    //update the scores
                    $response->exercises[$key]->averageGradeUnderstability = (($nVotes * $understability) + $this->getUnderstandabilityScore()) / ($nVotes + 1);

                    $response->exercises[$key]->averageGradeDifficulty = (($nVotes * $difficulty) + $this->getDifficultyScore()) / ($nVotes + 1);

                    $response->exercises[$key]->averageGradeTime = (($nVotes * $time) + $this->getTimeScore()) / ($nVotes + 1);

                    $response->exercises[$key]->numberVotes = $nVotes + 1;

                    $response->exercises[$key]->averageGrade = ($response->exercises[$key]->averageGradeUnderstability + $response->exercises[$key]->averageGradeDifficulty + $response->exercises[$key]->averageGradeTime) / 3;
                }
            }
            //else is a Exercise object
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
    
     static function MapJsonToUsagesArray($json) {
        $response = json_decode($json);
        $usages = array();
            foreach ($response as $usage) {  
               
                $CTUsage = new CT_Usage();
                $CTUsage->setId($usage->id);
                $CTUsage->setIdExercise($usage->idExercise);
                $CTUsage->setCtId($usage->ctId);
                $CTUsage->setDate($usage->date);
                $user = new CT_User();
                $user->setUserId($usage->user->id);
                $user->setDisplayname($usage->user->displayname);
                $user->setEmail($usage->user->email);
                $user->setProfileId($usage->user->profile_id);
                $CTUsage->setUser($user);
                $CTUsage->setUnderstandabilityScore($usage->understandabilityScore);
                $CTUsage->setDifficultyScore($usage->difficultyScore);
                $CTUsage->setTimeScore($usage->timeScore);
               
                array_push($usages, $CTUsage);
        }
        return $usages;
    }

    public function jsonSerialize(){
        return [
            'idExercise' =>  $this->getIdExercise(),
            'ctId' => $this->getCtId(),
            'user' => $this->getUser(),
            'understandabilityScore' => $this->getUnderstandabilityScore(),
            'difficultyScore' => $this->getDifficultyScore(),
            'timeScore' => $this->getTimeScore(),
        ];
    }
    
    
    static function getUsages($exercises, $students) {
        global $CFG;

        $exercises1 = array_map(function ($a) {
            return $a->getExerciseId();
        }, $exercises);

        $students1 = array_map(function ($a) {
            return $a->getUserId();
        }, $students);

        $ctId=[$_SESSION['ct_id']];
        $url = $CFG->repositoryUrl . "/api/usage/getUsageByIds";
          
        $array = [$exercises1, $students1, $ctId];
//        array_push($array, $exercises, $students, $_SESSION['ct_id']);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', CT_Test::getToken()));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($array));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);
        
        return self::MapJsonToUsagesArray($result);
    }

    public function getId() {
    return $this->id;
}

public function getIdExercise() {
    return $this->idExercise;
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

public function setIdExercise($idExercise): void {
    $this->idExercise = $idExercise;
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
