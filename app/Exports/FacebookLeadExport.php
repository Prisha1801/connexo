<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FacebookLeadExport implements FromArray, WithHeadings
{
    protected $vendors;

    public function headings(): array
    {
        return [
            'name',
            'details',
            'last_activity',
            'date_added',
            'your_requirement',
            'phone_number',
            'email',
            'city',
        ];
    }

    public function __construct(array $vendors)
    {
        $this->vendors = $vendors;
    }

    public function array(): array
    {
        return $this->vendors;
    }
}
