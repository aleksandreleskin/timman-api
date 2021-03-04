<?php

namespace App\Http\Controllers;

use App\Models\Documents;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentsController extends Controller
{
    private function getFilesSize($user_id)
    {
        $documents = DB::table('documents')->where('user_id', $user_id)->get();

        $size = 0;

        foreach ($documents as $document) {
            $pathToDocument = storage_path() . '/app/public/documents/' . $document->user_id . '/' . $document->title;
            $size += filesize($pathToDocument);
        }

        $size = $size / 1024 / 1024;

        return $size;
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required',
            'file' => 'required',
        ]);

        if ($this->getFilesSize($request->user_id) + (filesize($request->file) / 1024 / 1024) < 10) {
            if (!file_exists(storage_path() . '/app/public/documents/' . $request->user_id . '/' . $request->file_name)) {
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
                        'document' => $document,
                        'size' => $this->getFilesSize($request->user_id)
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
                "document" => setNewName($request),
                'size' => $this->getFilesSize($request->user_id)
            ], 201);
        }

        return response()->json([], 400);
    }

    public function getDocuments(Request $request): JsonResponse
    {
        $user_id = $request->header('user-id');
        $documents = DB::table('documents')->where('user_id', $user_id)->get();

        return response()->json(['documents' => $documents, 'size' => $this->getFilesSize($user_id)], 200);
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
        $user_id = $request->header('user-id');
        $id = $request->id;
        $document = DB::table('documents')->where('id', $id)->first();
        $pathToDocument = storage_path() . '/app/public/documents/' . $user_id . '/' . $document->title;

        if (is_file($pathToDocument)) {
            unlink($pathToDocument);
        }
        DB::table('documents')->where('id', $id)->delete();

        return response()->json(['id' => $id, 'size' => $this->getFilesSize($user_id)], 200);
    }

    public function shareDocument(Request $request): JsonResponse
    {
        $user_id = $request->user_id;
        $id = $request->id;

        $document = DB::table('documents')->where('id', $id)->first();
        $pathToDocument = storage_path() . '/app/public/documents/' . $user_id . '/' . $document->title;

        return response()->json(base64_encode($pathToDocument), 200);
    }

    public function getShareDocument(Request $request): JsonResponse
    {
        $id = $request->id;

        if (is_file(base64_decode($id))) {
            $exploded = explode('/', base64_decode($id));

            return response()->json($exploded[count($exploded) - 1], 200);
        }

        return response()->json([], 404);
    }
}
