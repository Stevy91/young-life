<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche — {{ $registration->nom }}</title>
    <style>
        @page { margin: 30px; }
        body { font-family: sans-serif; font-size: 12px; color: #1a1a2e; }
        .header { display: table; width: 100%; margin-bottom: 16px; border-bottom: 2px solid #106165; padding-bottom: 10px; }
        .header .logo { display: table-cell; width: 60px; vertical-align: middle; }
        .header .logo img { height: 50px; }
        .header .titles { display: table-cell; vertical-align: middle; padding-left: 12px; }
        .header .titles h1 { margin: 0; font-size: 20px; color: #106165; }
        .header .titles p { margin: 2px 0 0; font-size: 12px; color: #555; }
        .card { display: table; width: 100%; }
        .photo-cell { display: table-cell; width: 120px; vertical-align: top; }
        .photo-cell img { width: 100px; height: 100px; object-fit: cover; border: 1px solid #ccc; }
        .info-cell { display: table-cell; vertical-align: top; padding-left: 16px; }
        table.fields { width: 100%; border-collapse: collapse; }
        table.fields td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        table.fields td.label { width: 160px; font-weight: bold; color: #106165; }
        .footer { margin-top: 20px; font-size: 9px; color: #777; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('images/yl-logo.png') }}" alt="YL">
        </div>
        <div class="titles">
            <h1>Fiche d'inscription</h1>
            <p>{{ $registration->camp->name }}</p>
        </div>
    </div>

    <div class="card">
        <div class="photo-cell">
            @if ($registration->photo)
                <img src="{{ public_path('storage/' . $registration->photo) }}" alt="Photo">
            @endif
        </div>
        <div class="info-cell">
            <table class="fields">
                <tr><td class="label">Nom complet</td><td>{{ $registration->nom }}</td></tr>
                <tr><td class="label">Sexe</td><td>{{ $registration->sexe?->getLabel() }}</td></tr>
                <tr><td class="label">Date de naissance</td><td>{{ optional($registration->date_naissance)->format('d/m/Y') }}</td></tr>
                <tr><td class="label">Lieu de naissance</td><td>{{ $registration->lieu_naissance }}</td></tr>
                <tr><td class="label">Statut</td><td>{{ $registration->statut?->getLabel() }}</td></tr>
                <tr><td class="label">Téléphone</td><td>{{ $registration->telephone }}</td></tr>
                <tr><td class="label">Nif / Cin</td><td>{{ $registration->nif_cin }}</td></tr>
                <tr><td class="label">Email</td><td>{{ $registration->email }}</td></tr>
                <tr><td class="label">Adresse</td><td>{{ $registration->adresse }}</td></tr>
                <tr><td class="label">Arrondissement</td><td>{{ $registration->arrondissement?->name }}</td></tr>
                <tr><td class="label">Club</td><td>{{ $registration->club?->name }}</td></tr>
                <tr><td class="label">Rôle</td><td>{{ $registration->campCategory?->name }}</td></tr>
                @if ($registration->role_responsable)
                    <tr><td class="label">Type de responsable</td><td>{{ $registration->role_responsable }}</td></tr>
                @endif
                <tr><td class="label">Leader</td><td>{{ $registration->leader }}</td></tr>
                <tr><td class="label">Campus</td><td>{{ $registration->campus }}</td></tr>
                <tr><td class="label">Adresse du campus</td><td>{{ $registration->adresse_campus }}</td></tr>
                <tr><td class="label">Camp de jour</td><td>{{ $registration->camp_de_jour === null ? '' : ($registration->camp_de_jour ? 'Oui' : 'Non') }}</td></tr>
                <tr><td class="label">Type de camp</td><td>{{ $registration->type_camp }}</td></tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
