<?php

namespace App\Http\Controllers;

use App\Models\Optimization;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadOptimizedResumeController
{
    public function __invoke(Request $request, Optimization $optimization): \Illuminate\Http\Response
    {
        $pdf = Pdf::setBasePath(public_path())->loadView('pdfs.optimized-resume', ['optimization' => $optimization]);
        $date = now()->format('Y-m-d H:i');

        $request->headers->set('Content-Type', 'application/pdf');
        $filename = "Resume {$optimization->role_name} - {$optimization->role_company} {$date}.pdf";

        $result = $pdf->download($filename);

        $result->header('Content-Type', 'application/pdf');
        $result->header('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $result;
    }

    public function sample(): \Illuminate\Contracts\View\View
    {
        return view('pdfs.optimized-resume', ['optimization' => Optimization::find('01jwvh5shkdff9ke9py9evm89q')]);
    }
}
