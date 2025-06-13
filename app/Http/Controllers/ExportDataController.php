<?php

namespace App\Http\Controllers;

use App\Exports\BorrowingExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExportDataController extends Controller
{
    // File: ExportDataController.php
    public function index()
    {
        return Excel::download(new BorrowingExport, 'borrowings.xlsx', \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
