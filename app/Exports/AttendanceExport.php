<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Classroom;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements WithMapping, FromQuery, WithHeadings, ShouldAutoSize
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */

    private $id;
    public function __construct(string $id)
    {
        $this->id = $id;
    }
    public function map($user): array
    {
        // dd($user);
        return [
            $user->email,
            $user->matric_no,
            $user->first_name ?? null,
            $user->last_name ?? null,
            $user->student_attendance->created_at ?? null,
        ];
    }


    public function headings(): array
    {
        $classroom = Classroom::find($this->id);
        return [
            [
                'Attendance List for the class ' . $classroom->title
            ],
            [
                'Description: ' . $classroom->description
            ],
            [
                'Email',
                'Matric No',
                'First Name',
                'Last Name',
                'Clock In Time',
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

    function query()
    {
        $attendance = Attendance::where('classroom_id', $this->id)->first();

        return $attendance->students()->distinct();
    }
}
