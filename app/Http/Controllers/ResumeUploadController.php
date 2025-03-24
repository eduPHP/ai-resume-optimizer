<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResumeUploadController extends Controller
{
    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        $file = $request->file('resume');
        $user = $request->user();
        $path = "{$user->id}/resumes";

        $file->storeAs($path, $file->hashName(), 'local');

        $fullPath = $path . '/' . $file->hashName();
        $uploadedFile = $user->resumes()->create([
            'name' => $file->getClientOriginalName(),
            'type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'path' => $fullPath,
        ]);

        return response()->json([
            'message' => 'Resume uploaded successfully',
            'resume' => $uploadedFile->toArray(),
        ]);
    }
}
