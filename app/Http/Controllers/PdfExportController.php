<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class PdfExportController extends Controller
{
    //
    public function admissionLetter(Request $request)
    {
        $user = Student::where('id', auth()->user()->id)->first()->toArray();
        $pdf = Pdf::loadView('pdf.admission_letter', $user);
        return $pdf->download('admission_letter.pdf');
    }

    public function certificate(Request $request)
    {
        $user = Student::where('id', auth()->user()->id)->first()->toArray();
        $pdf = Pdf::loadView('pdf.certificate', $user)->setPaper('a4', 'landscape');
        return $pdf->download('certificate.pdf');
    }

    public function certificateOru(Request $request)
    {
        $user = Student::where('id', auth()->user()->id)->first()->toArray();
        $pdf = Pdf::loadView('pdf.certificate_oru', $user)->setPaper('a4', 'landscape');
        return $pdf->download('certificate_oru.pdf');
    }

    public function transcript(Request $request)
    {
        $user = Student::where('id', auth()->user()->id)->first();
        $data = [
            'user' => $user->toArray(),
            'transcript' => $user->transcript(),
        ];
        $pdf = Pdf::loadView('pdf.transcript', $data)->setOption(["default_paper_orientation" => "landscape"]);
        return $pdf->download('transcript.pdf');
    }

    
}
