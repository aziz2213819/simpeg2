<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TpsReport;

class AdminTpsReportController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $reports = TpsReport::with(['worker', 'tps'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('worker', function($q) use ($search) {
                    $q->where('nama_petugas', 'like', "%{$search}%");
                })->orWhereHas('tps', function($q) use ($search) {
                    $q->where('nama_tps', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.tps_reports.index', compact('reports', 'search'));
    }
}
