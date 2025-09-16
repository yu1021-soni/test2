<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function purchase($item) {
        return view ('purchase');
    }
}
