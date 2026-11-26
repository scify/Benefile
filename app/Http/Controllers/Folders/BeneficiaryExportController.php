<?php

namespace App\Http\Controllers\Folders;

use App\Http\Controllers\Controller;
use App\Services\BeneficiaryCsvExporter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The CSV downloads of the administrators.
 */
class BeneficiaryExportController extends Controller
{
    public function __construct(private readonly BeneficiaryCsvExporter $exporter)
    {
    }

    public function beneficiaries(Request $request): Response
    {
        return $this->download($this->exporter->beneficiaries($request->user()), 'benefiters');
    }

    public function folderHistory(Request $request): Response
    {
        return $this->download($this->exporter->folderHistory($request->user()), 'benefiters_folders_history');
    }

    private function download(string $csv, string $name): Response
    {
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$name.'_'.now()->format('Y-m-d_His').'.csv"',
            'Cache-Control' => 'no-store',
        ]);
    }
}
