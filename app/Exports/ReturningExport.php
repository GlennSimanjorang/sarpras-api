<?php

namespace App\Exports;

use App\Models\Returning;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReturningExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Returning::with(['borrowing.user', 'borrowing.item', 'admin'])->get()->map(function ($record) {
            return [
                'Returning ID'   => $record->id,
                'Borrowing ID'   => $record->borrow_id,
                'User'           => $record->borrowing->user->username ?? '-',
                'Item'           => $record->borrowing->item->name ?? '-',
                'Returned Qty'   => $record->returned_quantity,
                'Status'         => $record->borrowing->status,
                'Returned At'    => $record->created_at,
                'Handled By'     => $record->admin->username ?? '-', 
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Returning ID',
            'Borrowing ID',
            'User',
            'Item',
            'Returned Qty',
            'Status',
            'Returned At',
            'Handled By',
        ];
    }
}
