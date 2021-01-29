<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Todo;

class TodoController extends Controller
{
    public function createTask(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required',
            'value' => 'required'
        ]);

        $task = new Todo();
        $task->value = $request->value;
        $task->user_id = $request->user_id;
        $task->success = false;

        return response()->json([
            'task' => $task
        ], 201);
    }
}
