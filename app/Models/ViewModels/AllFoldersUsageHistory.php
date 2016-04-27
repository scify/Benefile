<?php

namespace App\Models\ViewModels;

class AllFoldersUsageHistory{

    private $userName;
    private $location;
    private $date;
    private $comments;
    private $folderName;

    function __construct($userName, $location, $date, $comments, $folderName)
    {
        $this->comments = $comments;
        $this->date = $date;
        $this->folderName = $folderName;
        $this->location = $location;
        $this->userName = $userName;
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
}
