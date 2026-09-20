<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
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
     * @return Model|null
     */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        return new Student([
            'name' => $row['name'] ?? null,
            'nationality' => $row['nationality'] ?? null,
            'grade' => $this->grade,
            'semester' => $row['semester'] ?? 1,
            'parent_mobile_1_key' => '+966',
            'parent_mobile_1' => $this->cleanPhone($row['phone_1'] ?? null),
            'parent_mobile_2_key' => '+966',
            'parent_mobile_2' => $this->cleanPhone($row['phone_2'] ?? null),
            'parent_mobile_3_key' => '+966',
            'parent_mobile_3' => $this->cleanPhone($row['phone_3'] ?? null),
            'can_share_opinion' => false,
        ]);
    }

    private function cleanPhone($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-numeric characters first
        $phone = preg_replace('/[^0-9]/', '', (string)$phone);
        $phone = preg_replace('/^(\+|00)?966/', '', $phone);
        $phone = ltrim($phone, '0');

        return $phone ?: null;
    }
}
