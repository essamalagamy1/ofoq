<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            if (empty($row['name'])) {
                continue;
            }

            $user = clone User::create([
                'name' => $row['name'],
                'phone_key' => '+966',
                'phone' => $this->cleanPhone($row['phone'] ?? null),
                'assigned_subject' => !empty($row['subject']) ? $row['subject'] : null,
                'assigned_grade' => !empty($row['grade']) ? $row['grade'] : null,
                'is_substitute' => (bool) ($row['is_substitute'] ?? false),
                'type' => 'teacher',
                'requires_password' => false,
            ]);
            
            $user->assignRole('teacher');
        }
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
