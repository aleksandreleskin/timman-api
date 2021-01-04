<?php

namespace App\Http\Controllers;

use App\Models\Notes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotesController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required'
        ]);

        $note = new Notes();
        $note->title = "";
        $note->value = "";
        $note->user_id = $request->user_id;
        $note->save();

        $note = DB::table('notes')
            ->orderBy('id', 'desc')
            ->where('user_id', $note->user_id)
            ->first();

        return response()->json([
            'note' => $note
        ], 201);
    }

    public function getNotes(Request $request): JsonResponse
    {
        $user_id = $request->header('user_id');
        $notes = DB::table('notes')->where('user_id', $user_id)->get();

        return response()->json($notes, 200);
    }

    public function getNoteValue(Request $request) {
        $id = $request->id;
        $note_value = DB::table('notes')
            ->orderBy('id', 'desc')
            ->where('id', $id)
            ->value('value');

        return response()->json($note_value, 200);
    }
}
