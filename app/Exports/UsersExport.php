<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private ?string $startDate = null, private ?string $endDate = null) {}

    public function collection(): Collection
    {
        $q = User::query();
        if ($this->startDate) $q->whereDate('created_at', '>=', $this->startDate);
        if ($this->endDate) $q->whereDate('created_at', '<=', $this->endDate);
        return $q->orderBy('created_at')->get(['id','name','email','phone','created_at']);
    }

    public function headings(): array
    {
        return ['ID','Name','Email','Phone','Registered At'];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone,
            optional($user->created_at)->toDateTimeString(),
        ];
    }
}


