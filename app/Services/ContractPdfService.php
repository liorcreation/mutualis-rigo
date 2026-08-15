<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Rend le contrat en PDF juridique, le stocke sur le disque privé et
 * fournit le téléchargement. Le hash d'intégrité (document_hash) est fixé
 * à la signature (ContractService::sign) et seulement affiché ici : il ne
 * peut pas être calculé sur le PDF final sans se contenir lui-même.
 */
class ContractPdfService
{
    public function generate(Contract $contract): string
    {
        $contract->loadMissing(['project.user', 'user', 'contribution']);

        $qrCode = $this->verificationQrCode($contract);

        $pdf = Pdf::loadView('pdf.contract', [
            'contract' => $contract,
            'qrCode' => $qrCode,
        ])->setPaper('a4');

        $path = "contracts/{$contract->contract_number}.pdf";
        Storage::disk('local')->put($path, $pdf->output());

        $contract->update(['pdf_path' => $path]);

        return $path;
    }

    public function download(Contract $contract): StreamedResponse
    {
        abort_unless($contract->pdf_path && Storage::disk('local')->exists($contract->pdf_path), 404);

        return Storage::disk('local')->download($contract->pdf_path, "{$contract->contract_number}.pdf");
    }

    private function verificationQrCode(Contract $contract): string
    {
        $verificationHash = substr((string) $contract->document_hash, 0, Contract::VERIFICATION_HASH_LENGTH);

        $url = route('contracts.verify', [
            'contract' => $contract->contract_number,
            'h' => $verificationHash,
        ]);

        $result = (new Builder(
            writer: new SvgWriter,
            data: $url,
            size: 160,
            margin: 4,
        ))->build();

        return $result->getDataUri();
    }
}
