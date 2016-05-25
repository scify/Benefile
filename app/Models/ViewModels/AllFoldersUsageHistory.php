<?php

namespace App\Models\ViewModels;

class AllFoldersUsageHistory{

    private $folderNumber;
    private $benefiterName;
    private $userName;
    private $speciality;
    private $location;
    private $date;
    private $comments;
    private $folderName;

    function __construct($folderNumber, $benefiterName, $userName, $speciality, $location, $date, $comments, $folderName)
    {
        $this->folderNumber = $folderNumber;
        $this->benefiterName = $benefiterName;
        $this->speciality = $speciality;
        $this->comments = $this->removeSpecialCharacters($comments);
        $this->date = $date;
        $this->folderName = $folderName;
        $this->location = $location;
        $this->userName = $userName;
    }

    private function removeSpecialCharacters($string){
        $string = str_replace("\r\n", " ", $string);
        $string = str_replace("\r", " ", $string);
        $string = str_replace("\n", " ", $string);
        return $string;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getFolderName()
    {
        return $this->folderName;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function getBenefiterName()
    {
        return $this->benefiterName;
    }

    public function getFolderNumber()
    {
        return $this->folderNumber;
    }

    public function getSpeciality()
    {
        return $this->speciality;
    }


}
