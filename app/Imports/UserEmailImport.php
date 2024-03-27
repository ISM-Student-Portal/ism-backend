<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class UserEmailImport implements ToArray
{
    public function array(array $array)
    {
        return $array;
    }
}
