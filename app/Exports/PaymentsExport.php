<?php

namespace App\Exports;

use App\Models\Payments;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements WithMapping, FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct()
    {

    }
    public function map($payment): array
    {
        // dd($user);
        return [
            $payment->student->email ?? null,
            $payment->student->matric_no ?? null,
            $payment->reference ?? null,
            $payment->amount ?? null,
            $payment->status ?? null,
            $payment->payment_method ?? null,
            $payment->created_at ?? null,

        ];
    }


    public function headings(): array
    {
        return [
            [
                'Payment List  '
            ],

            [
                'Student Email',
                'Matric No',
                'Reference',
                'Amount',
                'Payment Status',
                'Payment Method',
                'Payment Date',
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
        $payments = Payments::with(['student'])->get();

        return $payments;
    }
}
