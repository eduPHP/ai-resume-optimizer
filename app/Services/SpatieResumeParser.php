<?php
namespace App\Services;

use App\Contracts\ResumeParser;
use Spatie\PdfToText\Pdf;

class SpatieResumeParser implements ResumeParser
{
    public function getText(string $path): string
    {
        return Pdf::getText($path);
    }
}
