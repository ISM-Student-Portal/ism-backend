<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;

class FirstSheetImport implements ToArray
{
    /**
    * @param array $array
    */
    public function array(array $array)
    {
        //
    }
}
