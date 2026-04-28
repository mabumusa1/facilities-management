<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

/**
 * Simple import that converts an Excel sheet to a plain array.
 * Used by ImportUnitService to read raw cell values.
 */
class RawSheetImport implements ToArray
{
    /** @param  list<list<mixed>>  $array */
    public function array(array $array): void
    {
        // No-op: Excel::toArray() collects the return from array() internally
    }
}
