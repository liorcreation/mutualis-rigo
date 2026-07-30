<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ContractVerificationController extends Controller
{
    public function __invoke(Request $request, Contract $contract): View
    {
        $expectedHash = substr((string) $contract->document_hash, 0, 12);
        $authentic = $contract->document_hash !== null
            && hash_equals($expectedHash, (string) $request->query('h'));

        abort_unless($authentic, 404);

        return view('contracts.verify', ['contract' => $contract->load('project')]);
    }
}
