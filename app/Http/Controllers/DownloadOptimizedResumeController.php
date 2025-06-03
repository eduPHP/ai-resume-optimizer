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

        return $pdf->download("Resume {$optimization->role_name} - {$optimization->role_company} {$date}.pdf");
    }

    public function sample(): \Illuminate\Contracts\View\View
    {
        return view('pdfs.optimized-resume', ['optimization' => Optimization::find('01jwvh5shkdff9ke9py9evm89q')]);
    }
}
