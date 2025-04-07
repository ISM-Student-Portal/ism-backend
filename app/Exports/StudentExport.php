<?php

namespace App\Exports;

use App\Models\Payments;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentExport implements WithMapping, FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct()
    {

    }
    public function map($student): array
    {
        // dd($user);
        return [
            $student->email ?? null,
            $student->matric_no ?? null,
            $student->group_no ?? null,
            $student->first_name ?? null,
            $student->last_name ?? null,
            $student->phone ?? null,
            $student->country ?? null,
            $student->city ?? null,
            $student->gender ?? null,
            $student->is_alumni ? 'Yes' : 'No',
            $student->payment_complete === 1 ? 'Yes' : 'No',
            $student->payments->sum('amount') ?? null,

        ];
    }


    public function headings(): array
    {
        return [
            [
                'Students List  '
            ],

            [
                'Student Email',
                'Reg No',
                'Group No',
                'First Name',
                'Last Name',
                'Phone',
                'Country',
                'City',
                'Gender',
                'Alumni',
                'Payment Completed',
                'Total Payment',
            ]

        ];
    }



    // public function properties(): array
    // {
    //     $username = Profile::where('user_id', '=', auth()->user()->id)->first();

    //     return [
    //         'creator' => $username->first_name ?? " " . ' ' . $username->last_name ?? " ",
    //         'title' => 'Attendance List',
    //     ];
    // }

    function collection()
    {
        $payments = Student::with(['payments'])->where('payment_complete', '=', true)->orWhereNotNull('balance')->get();

        return $payments;
    }
}
