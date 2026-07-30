<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contrat {{ $contract->contract_number }}</title>
    <style>
        @page { margin: 90px 50px 70px 50px; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
        }

        .watermark {
            position: fixed;
            top: 320px;
            left: 60px;
            font-size: 90px;
            font-weight: bold;
            color: #4f46e5;
            opacity: 0.06;
            transform: rotate(-35deg);
            z-index: -1;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 70px;
        }

        .brand {
            display: inline-block;
            width: 34px;
            height: 34px;
            background-color: #4338ca;
            border-radius: 8px;
            color: #ffffff;
            text-align: center;
            line-height: 34px;
            font-weight: bold;
            font-size: 14px;
        }

        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .brand-name { font-size: 16px; font-weight: bold; color: #0f172a; }
        .brand-sub { font-size: 8px; letter-spacing: 1px; text-transform: uppercase; color: #4f46e5; }
        .meta { text-align: right; font-size: 9px; color: #64748b; }
        .meta strong { color: #0f172a; font-size: 11px; }

        hr.sep { border: none; border-top: 1px solid #e2e8f0; margin: 10px 0 16px 0; }

        h1.title {
            font-size: 18px;
            color: #0f172a;
            margin: 0 0 4px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            background-color: #d1fae5;
            color: #065f46;
        }

        .clause { margin-bottom: 14px; }
        .clause-title { font-size: 12px; font-weight: bold; color: #312e81; margin-bottom: 4px; }
        .clause-body { color: #334155; }

        table.parties { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.parties td {
            width: 50%;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            vertical-align: top;
        }
        table.parties .role { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; }
        table.parties .name { font-size: 12px; font-weight: bold; color: #0f172a; margin-top: 2px; }

        .amount-box {
            margin-top: 6px;
            padding: 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            text-align: center;
        }
        .amount-box .amount { font-size: 22px; font-weight: bold; color: #0f172a; }
        .amount-box .currency { font-size: 10px; color: #64748b; }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 60px;
        }

        table.signature { width: 100%; border-collapse: collapse; border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 10px; }
        table.signature td { vertical-align: middle; font-size: 8px; color: #64748b; }
        .qr-cell { width: 70px; text-align: right; }
        .qr-cell img { width: 60px; height: 60px; }
        .hash { font-family: 'Courier', monospace; font-size: 8px; color: #94a3b8; word-break: break-all; }
    </style>
</head>
<body>
    <div class="watermark">{{ strtoupper($contract->status->value) }}</div>

    <header>
        <table class="header-table">
            <tr>
                <td style="width: 40px;"><span class="brand">M</span></td>
                <td>
                    <div class="brand-name">MUTUALIS</div>
                    <div class="brand-sub">Espace de partage — Mutualisation</div>
                </td>
                <td class="meta">
                    <strong>{{ $contract->contract_number }}</strong><br>
                    Émis le {{ now()->format('d/m/Y') }}
                </td>
            </tr>
        </table>
        <hr class="sep">
    </header>

    <h1 class="title">Contrat de Mutualisation</h1>
    <span class="status-badge">{{ strtoupper($contract->status->value) }}</span>

    <div class="clause" style="margin-top: 16px;">
        <div class="clause-title">1. Identité des parties</div>
        <table class="parties">
            <tr>
                <td>
                    <div class="role">Porteur de projet</div>
                    <div class="name">{{ $contract->project?->user?->name ?? '—' }}</div>
                    <div>{{ $contract->project?->user?->email ?? '' }}</div>
                </td>
                <td>
                    <div class="role">Contributeur</div>
                    <div class="name">{{ $contract->user->name }}</div>
                    <div>{{ $contract->user->email }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="clause">
        <div class="clause-title">2. Objet de la mutualisation</div>
        <div class="clause-body">
            Le présent contrat formalise l'apport financier proposé par le contributeur au bénéfice du projet
            « {{ $contract->project?->titre ?? 'Projet' }} », dans le cadre du dispositif de mutualisation des ressources de la plateforme MUTUALIS.
            <br>{{ $contract->project?->description }}
        </div>
    </div>

    <div class="clause">
        <div class="clause-title">3. Montant et modalités</div>
        <div class="amount-box">
            <div class="amount">{{ number_format((float) $contract->amount, 0, ',', ' ') }} {{ $contract->currency }}</div>
            <div class="currency">Montant total de l'apport, réglé en une fois</div>
        </div>
    </div>

    <div class="clause">
        <div class="clause-title">4. Durée &amp; conditions générales</div>
        <div class="clause-body">
            Le présent contrat prend effet à la date de confirmation du paiement mentionnée ci-dessous et engage les
            deux parties pour toute la durée du projet de mutualisation. Toute contestation doit être adressée à
            l'administration de la plateforme MUTUALIS dans un délai raisonnable suivant la signature.
        </div>
    </div>

    <footer>
        <table class="signature">
            <tr>
                <td>
                    <strong>Signature électronique</strong><br>
                    Acceptée le {{ $contract->terms_accepted_at?->format('d/m/Y à H:i') }} depuis l'adresse IP {{ $contract->signature_ip }}
                    <div class="hash">Empreinte SHA-256 : {{ $contract->document_hash }}</div>
                </td>
                <td class="qr-cell">
                    <img src="{{ $qrCode }}" alt="QR de vérification">
                </td>
            </tr>
        </table>
    </footer>
</body>
</html>
