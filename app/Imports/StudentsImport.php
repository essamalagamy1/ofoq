<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class StudentsImport implements ToModel, WithStartRow
{
    protected $grade;

    public function __construct(int $grade)
    {
        $this->grade = $grade;
    }

    public function model(array $row)
    {
        // Name, Nationality, Semester, Phone1, Phone2, Phone3
        // row[0] = Name
        // row[1] = Nationality
        // row[2] = Semester
        // row[3] = Phone1
        // row[4] = Phone2
        // row[5] = Phone3

        if (!isset($row[0])) {
            return null; // Skip empty rows
        }

        return new Student([
            'name' => $row[0],
            'nationality' => $row[1] ?? null,
            'grade' => $this->grade,
            'semester' => isset($row[2]) ? (int) $row[2] : 1,
            'parent_mobile_1' => $row[3] ?? null,
            'parent_mobile_2' => $row[4] ?? null,
            'parent_mobile_3' => $row[5] ?? null,
            'can_share_opinion' => false,
        ]);
    }

    public function startRow(): int
    {
        return 1; // Assuming no headers based on requirements
    }
}
