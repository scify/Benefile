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

}
