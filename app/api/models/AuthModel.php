<?php

class AuthModel extends ModelBase 
{
    public $Email = ['Required', 'Email'];
    public $Password = ['Required'];

    function __construct()
    {
        parent::__construct();
    }
}
