<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\DB;

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
        $task->order = $request->order;
        $task->success = false;
        $task->save();

        return response()->json([
            'task' => $task
        ], 201);
    }

    public function getTasks(Request $request): JsonResponse
    {
        $user_id = $request->header('user-id');
        $tasks = DB::table('todos')->orderBy('created_at', 'desc')->where('user_id', $user_id)->get();

        return response()->json($tasks, 200);
    }

    public function setSuccess(Request $request): JsonResponse
    {
        $id = $request->id;
        $success = $request->value;

        DB::table('todos')
            ->where('id', $id)
            ->update(['success' => $success]);

        $task = DB::table('todos')
            ->where('id', $id)
            ->first();

        return response()->json($task, 200);
    }

    public function removeTask(Request $request): JsonResponse
    {
        $id = $request->id;

        DB::table('todos')->where('id', $id)->delete();

        return response()->json($id, 200);
    }
}
