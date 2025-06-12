<?php

namespace App\Http\Controllers;

use App\Models\Optimization;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadOptimizedResumeController
{
    public function resume(Optimization $optimization): \Illuminate\Http\Response
    {
        $pdf = Pdf::setBasePath(public_path())->loadView('pdfs.optimized-resume', ['optimization' => $optimization]);

        $filename = $optimization->optimizedResumeFileName();

        $result = $pdf->download($filename);

        $result->header('Content-Type', 'application/pdf');
        $result->header('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $result;
    }

    public function cover(Optimization $optimization): \Illuminate\Http\Response
    {
        $pdf = Pdf::setBasePath(public_path())->loadView('pdfs.cover-letter', ['optimization' => $optimization]);

        $filename = $optimization->coverLetterFileName();

        $result = $pdf->download($filename);

        $result->header('Content-Type', 'application/pdf');
        $result->header('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $result;
    }

    public function sample(): \Illuminate\Contracts\View\View
    {
        return view('pdfs.cover-letter', ['optimization' => Optimization::find('01jx0kt0m6n77zsvd7gd9ytf4t')]);
    }
}
