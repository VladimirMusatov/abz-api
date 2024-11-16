<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;


class MainController extends Controller
{
    public function positions()
    {

        $postitons = Position::select(['id', 'name'])->get();

        return response()->json([
            "success" => true,
            "positions" => $postitons,
        ], 200); 

    }
}
