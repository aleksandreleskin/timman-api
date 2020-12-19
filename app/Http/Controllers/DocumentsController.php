<?php

namespace App\Http\Controllers;

use App\Models\Documents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentsController extends Controller
{
    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'user_id' => 'required',
            'file' => 'required|mimes:pdf',
        ]);

        if ($files = $request->file('file')) {
            $file = $request->file->store('public/documents');

            $document = new Documents();
            $document->title = $file;
            $document->user_id = $request->user_id;
            $document->save();

            $document = DB::table('documents')
                ->orderBy('id', 'desc')
                ->where('user_id', $document->user_id)
                ->first();

            return response()->json([
                "document" => $document
            ], 201);
        }
    }

    public function getDocuments(Request $request): \Illuminate\Http\JsonResponse
    {
        $user_id = $request->header('user_id');
        $documents = DB::table('documents')->where('user_id', $user_id)->get();

        return response()->json($documents, 200);
    }

    public function downloadDocument(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $id = $request->id;
        $document = DB::table('documents')->where('id', $id)->first();
        $pathToDocument = storage_path() . "/app/" . $document->title;

        return response()->download($pathToDocument);
    }
}
