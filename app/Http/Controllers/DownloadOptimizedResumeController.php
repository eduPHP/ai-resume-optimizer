<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadOptimizedResumeController
{
    public function __invoke(Request $request, Resume $resume): \Illuminate\Http\Response
    {
        $pdf = Pdf::setBasePath(public_path())->loadView('pdfs.optimized-resume', ['resume' => $resume]);
        return $pdf->download('resume.pdf');
    }

    public function sample()
    {
        return view('pdfs.optimized-resume', ['resume' => Resume::find(4)]);
    }
}
