<?php

namespace App\Imports;


use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AssignmentSubmissionImport implements WithMultipleSheets
{
    use WithConditionalSheets;
    
    public function conditionalSheets(): array{

        return [
            0 => new FirstSheetImport(),
            1 => new SecondSheetImport(),
            2 => new ThirdSheetImport()
        ];
    } 
}
