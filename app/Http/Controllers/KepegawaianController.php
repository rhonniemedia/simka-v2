<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KepegawaianController extends Controller
{
    public function index()
    {
        return view('contents.master.kepegawaian.index');
    }
}
