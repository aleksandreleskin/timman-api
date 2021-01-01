<?php

namespace App\Http\Controllers;

use App\Models\Documents;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    public function create(Request $request)
    {
        $note = new Documents();
    }
}
