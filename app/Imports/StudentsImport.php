<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public $grade;

    public function __construct($grade)
    {
        $this->grade = $grade;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Student([
            'name'                => $row['name'] ?? null,
            'nationality'         => $row['nationality'] ?? null,
            'grade'               => $this->grade,
            'semester'            => $row['semester'] ?? null,
            'parent_mobile_1_key' => $row['parent_mobile_1_key'] ?? null,
            'parent_mobile_1'     => $row['parent_mobile_1'] ?? null,
            'parent_mobile_2_key' => $row['parent_mobile_2_key'] ?? null,
            'parent_mobile_2'     => $row['parent_mobile_2'] ?? null,
            'parent_mobile_3_key' => $row['parent_mobile_3_key'] ?? null,
            'parent_mobile_3'     => $row['parent_mobile_3'] ?? null,
            'can_share_opinion'   => isset($row['can_share_opinion']) ? (bool) $row['can_share_opinion'] : false,
        ]);
    }
}
