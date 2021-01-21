<?php

namespace App\Http\Controllers;

use App\Models\Documents;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentsController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required',
            'file' => 'required',
        ]);

        if (!file_exists(storage_path() . '/app/public/documents/' . $request->user_id . $request->file_name)) {
            if ($file = $request->file->storeAs('public/documents/' . $request->user_id, $request->file_name)) {
                $document = new Documents();
                $document->title = $request->file_name;
                $document->user_id = $request->user_id;
                $document->save();

                $document = DB::table('documents')
                    ->orderBy('id', 'desc')
                    ->where('user_id', $document->user_id)
                    ->first();

                return response()->json([
                    'document' => $document
                ], 201);
            }
            return response()->json([], 400);
        }


        function setNewName($request, $counter = 1)
        {
            $explodeFileName = explode('.', $request->file_name);
            $explodeFileName[count($explodeFileName) - 2] =
                $explodeFileName[count($explodeFileName) - 2] . '(' . $counter . ')';

            $newFileName = implode('.', $explodeFileName);
            if (!file_exists(storage_path() . '/app/public/documents/' . $request->user_id . '/' . $newFileName)) {
                if ($file = $request->file->storeAs('public/documents/' . $request->user_id, $newFileName)) {
                    $document = new Documents();
                    $document->title = $newFileName;
                    $document->user_id = $request->user_id;
                    $document->save();

                    $document = DB::table('documents')
                        ->orderBy('id', 'desc')
                        ->where('user_id', $document->user_id)
                        ->first();

                    return $document;
                }

            } else {
                return setNewName($request, ++$counter);
            }
            return response()->json([], 400);
        }

        return response()->json([
            "document" => setNewName($request)
        ], 201);
    }

    public function getDocuments(Request $request): JsonResponse
    {
        $user_id = $request->header('user_id');
        $documents = DB::table('documents')->where('user_id', $user_id)->get();

        return response()->json($documents, 200);
    }

    public function downloadDocument(Request $request): BinaryFileResponse
    {
        $id = $request->id;
        $document = DB::table('documents')->where('id', $id)->first();
        $pathToDocument = storage_path() . '/app/public/documents/' . $document->user_id . '/' . $document->title;

        return response()->download($pathToDocument);
    }

    public function removeDocument(Request $request): JsonResponse
    {
        $id = $request->id;
        $document = DB::table('documents')->where('id', $id)->first();
        $pathToDocument = storage_path() . '/app/public/documents/' . $id . '/' . $document->title;

        if (is_file($pathToDocument)) {
            unlink($pathToDocument);
        }
        DB::table('documents')->where('id', $id)->delete();

        return response()->json([$id], 200);
    }
}
