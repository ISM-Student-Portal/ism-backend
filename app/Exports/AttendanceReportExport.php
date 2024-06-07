<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceReportExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $report;

    public function __construct(Collection  $report)
    {
        $this->report = $report;
    }
    public function collection()
    {
        //
        return $this->report;
    }

    public function map($item): array
    {
        // dd($user);
        return [
            $item->profile->first_name ?? null,
            $item->profile->last_name ?? null,
            $item->email,
            $item->reg_no,
            $item->attendance_count,           
            $item->attendance_count_percent,
        ];
    }


    public function headings(): array
    {
        return [
            [
                'Attendance Report For ISM 2024' 
            ],            
            [
                'First Name',                
                'Last Name',
                'Email',
                'Reg No',
                'Classes Attended',
                'Percent Class Attended',
            ]

        ];
    }
}
