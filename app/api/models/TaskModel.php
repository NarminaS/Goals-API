<?php

class TaskModel extends ModelBase
{
    public $Id;
    public $MissionId= ['Required'];
    public $Text = ['Required'];
    public $Done;
}