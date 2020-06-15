<?php

class MissionModel extends ModelBase 
{
    public $Id;
    public $Name = ['Required'];
    public $Added;
    public $Deadline;
    public $Description;
    public $TotalSum;

    function __construct()
    {
        parent::__construct();
    }
}