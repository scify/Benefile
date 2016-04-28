<?php

namespace App\Models\ViewModels;

class LegalSessionsHistory{

    private $userName, $location, $date, $lawyer_actions, $comments;

    public function __construct($userName, $location, $date, $lawyer_actions, $comments){
        $this->userName = $userName;
        $this->location = $location;
        $this->date = $date;
        $this->lawyer_actions = $lawyer_actions;
        $this->comments = $comments;
    }

    public function getComments(){
        return $this->comments;
    }

    public function getDate(){
        return $this->date;
    }

    public function getLawyerActions(){
        return $this->lawyer_actions;
    }

    public function getLocation(){
        return $this->location;
    }

    public function getUserName(){
        return $this->userName;
    }
}
