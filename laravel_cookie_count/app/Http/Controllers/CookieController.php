<?php

namespace App\Http\Controllers;

class CookieController extends Controller
{
    public function cookie_counter(string $name) {
        return view("times_visited", compact("name"));
    }

}
