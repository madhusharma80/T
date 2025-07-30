<?php

namespace App\Http\Controllers;

abstract class Controller
{
    function users(){
    $users= DB:: select ('select * from users');
    return view('Welcome',['users => users']);
    }
}
