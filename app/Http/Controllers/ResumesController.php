<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Contracts\ResumeParser;

class ResumesController
{
    public function __construct(private ResumeParser $parser)
    {
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $resumes = $request->user()->resumes()->latest('created_at')->limit(5)->get()->map(fn($resume) => [
            'id' => $resume->id,
            'href' => route('resumes.show', $resume),
            'name' => $resume->name,
            'created' => $resume->created_at->format('Y-m-d g:i A'),
        ])->values();

        return response()->json($resumes);
    }

    public function store(Request $request)
    {
        // sleep(5);
        $request->validate([
            'upload' => 'required|file|mimes:pdf,docx,doc|max:10240'
        ], [
            'upload.required' => 'Please select a file to upload',
            'upload.mimes' => 'Please select a valid file type',
            'upload.max' => 'Please select a file smaller than 10MB',
        ]);

        $uploadedFile = $request->file('upload');

        $path = $uploadedFile->store('resumes', 'local');

        if (!$path) {
            return response()->json(['message' => 'Failed to upload file'], 500);
        }

        // todo: parse content
        $content = str($this->parser->getText(Storage::disk('local')->path($path)))
            ->replaceMatches('/[^0-9A-z\n -.\/]/', '')
            ->replaceMatches('/\n\s*\n\s*\n/', '')
        ;

        $resume = $request->user()->resumes()->create([
            'name' => $uploadedFile->getClientOriginalName(),
            'type' => $uploadedFile->getClientMimeType(),
            'size' => $uploadedFile->getSize(),
            'path' => $path,
            'detected_content' => $content,
        ]);

        return response()->json([
            'id' => $resume->id,
            'href' => route('resumes.show', $resume),
            'name' => $resume->name,
            'content' => $content,
            'created' => $resume->created_at->format('Y-m-d g:i A'),
        ]);
    }

    public function destroy(Request $request, Resume $resume)
    {
        //
    }

    public function show(Request $request, Resume $resume)
    {
        //
    }
}
