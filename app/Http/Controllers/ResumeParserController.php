<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;

class ResumeParserController
{
    public function __invoke(Request $request, Resume $resume): JsonResponse
    {
        $content = str(Pdf::getText(Storage::disk('local')->path($resume->path)))
            ->replaceMatches('/[^0-9A-z\n -.\/]/', '')
            ->replaceMatches('/\n\s*\n\s*\n/', '')
        ;

        $resume->update([
            'detected_content' => $content,
        ]);

        return response()->json([
            'content' => $content,
        ]);
    }
}
