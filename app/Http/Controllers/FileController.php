<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Handle CKEditor file uploads (temporary storage).
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'upload' => 'required|file|mimes:jpg,jpeg,png|max:2048', // max 5MB
        ]);

        $file = $request->file('upload');

        // Generate unique file name
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Save to storage/app/public/tmp
        $path = $file->storeAs('tmp', $fileName, 'public');

        // Return URL for CKEditor
        return response()->json([
            'uploaded' => 1,
            'fileName' => $fileName,
            'url' => asset('storage/' . $path)
        ]);
    }

    /**
     * Handle file deletion from CKEditor (temporary files).
     */
    public function delete(Request $request): JsonResponse
    {
        $fileName = $request->input('fileName');
        $filePath = 'tmp/' . $fileName; // only delete from tmp folder

        if ($fileName && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return response()->json([
                'deleted' => true,
                'message' => 'File deleted successfully.'
            ]);
        }

        return response()->json([
            'deleted' => false,
            'message' => 'File not found.'
        ], 404);
    }
}
