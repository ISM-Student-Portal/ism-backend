<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;

class SeventhSheetImport implements ToArray
{
    /**
    * @param Collection $collection
    */
    public function array(array $array)
    {
        //
    }
}
