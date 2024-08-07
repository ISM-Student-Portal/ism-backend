<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class PdfExportController extends Controller
{
    //
    public function admissionLetter(Request $request)
    {
        $user = User::with('profile')->where('id', auth()->user()->id)->first()->toArray();
        $pdf = Pdf::loadView('pdf.admission_letter', $user);
        return $pdf->download('admission_letter.pdf');
    }

    public function certificate(Request $request)
    {
        $user = User::with('profile')->where('id', auth()->user()->id)->first()->toArray();
        $pdf = Pdf::loadView('pdf.certificate', $user)->setOption(["default_paper_orientation" => "landscape"]);
        return $pdf->download('certificate.pdf');
    }
}
