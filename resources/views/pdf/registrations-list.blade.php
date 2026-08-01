<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $camp->name }} — Liste des inscrits</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: sans-serif; font-size: 11px; color: #1a1a2e; }
        .header { display: table; width: 100%; margin-bottom: 12px; border-bottom: 2px solid #106165; padding-bottom: 8px; }
        .header .logo { display: table-cell; width: 50px; vertical-align: middle; }
        .header .logo img { height: 40px; }
        .header .titles { display: table-cell; vertical-align: middle; padding-left: 10px; }
        .header .titles h1 { margin: 0; font-size: 18px; color: #106165; }
        .header .titles p { margin: 2px 0 0; font-size: 11px; color: #555; }
        .meta { margin-bottom: 10px; font-size: 11px; }
        .meta span { margin-right: 18px; }
        .categories { margin-bottom: 12px; }
        .categories span {
            display: inline-block; background: #106165; color: #fff; border-radius: 4px;
            padding: 4px 10px; margin: 0 6px 6px 0; font-size: 10px;
        }
        table.registrations { width: 100%; border-collapse: collapse; }
        table.registrations th, table.registrations td {
            border: 1px solid #ccc; padding: 4px 6px; text-align: left; font-size: 10px;
        }
        table.registrations th { background: #106165; color: #fff; }
        table.registrations tr:nth-child(even) { background: #f5f5f5; }
        .footer { margin-top: 10px; font-size: 9px; color: #777; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('images/yl-logo.png') }}" alt="YL">
        </div>
        <div class="titles">
            <h1>{{ $camp->name }}</h1>
            <p>Zone : {{ $camp->zone->name }}</p>
            @if ($arrondissement ?? null)
                <p>Arrondissement : {{ $arrondissement->name }}</p>
            @endif
        </div>
    </div>

    <div class="meta">
        @if ($camp->date_debut)
            <span><strong>Début :</strong> {{ $camp->date_debut->format('d/m/Y') }}</span>
        @endif
        @if ($camp->date_fin)
            <span><strong>Fin :</strong> {{ $camp->date_fin->format('d/m/Y') }}</span>
        @endif
        @if ($camp->nb_nuits)
            <span><strong>Nuits :</strong> {{ $camp->nb_nuits }}</span>
        @endif
        <span><strong>Total inscrits :</strong> {{ $registrations->count() }}</span>
    </div>

    @if ($camp->categories->isNotEmpty())
        <div class="categories">
            @foreach ($camp->categories as $category)
                <span>
                    {{ $category->name }} : {{ $category->registrations_count }}{{ $category->quota !== null ? " sur {$category->quota}" : '' }}
                </span>
            @endforeach
        </div>
    @endif

    <table class="registrations">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Sexe</th>
                <th>Naissance</th>
                <th>Téléphone</th>
                <th>Club</th>
                <th>Arrondissement</th>
                <th>Rôle</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registrations as $i => $registration)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $registration->nom }}</td>
                    <td>{{ $registration->sexe?->getLabel() }}</td>
                    <td>{{ optional($registration->date_naissance)->format('d/m/Y') }}</td>
                    <td>{{ $registration->telephone }}</td>
                    <td>{{ $registration->club?->name }}</td>
                    <td>{{ $registration->arrondissement?->name }}</td>
                    <td>{{ $registration->campCategory?->name }}</td>
                    <td>{{ $registration->statut?->getLabel() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
