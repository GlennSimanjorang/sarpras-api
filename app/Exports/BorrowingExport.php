<?php

namespace App\Exports;

use App\Models\Borrowing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BorrowingExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Borrowing::with(['user', 'item'])->get()->map(function ($record) {
            return [
                'ID' => $record->id,
                'User' => $record->user->username,
                'Item' => $record->item->name,
                'Quantity' => $record->quantity,
                'Status' => $record->status,
                'Approved By' => $record->approved_by,
                'Approved At' => $record->approved_at,
                'Due Date' => $record->due_date,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'User',
            'Item',
            'Quantity',
            'Status',
            'Approved By',
            'Approved At',
            'Due Date',
        ];
    }
}