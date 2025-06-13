<?php

namespace App\Http\Controllers;

use App\Exports\ReturningExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExportReturningController extends Controller
{
    
    public function index()
    {
        return Excel::download(new ReturningExport, 'returning.xlsx', \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
