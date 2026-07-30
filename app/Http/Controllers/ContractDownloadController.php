<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Services\ContractPdfService;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractDownloadController extends Controller
{
    public function __invoke(Contract $contract, ContractPdfService $pdf): StreamedResponse
    {
        Gate::authorize('download', $contract);

        return $pdf->download($contract);
    }
}
