<?php

use App\Filament\Resources\CampResource;
use App\Models\Arrondissement;
use App\Models\Camp;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/leve-fonds', function () {
    return view('leve-fonds');
})->name('leve-fonds');

Route::get('/a-propos', function () {
    return view('a-propos');
})->name('a-propos');

Route::get('/galerie', function () {
    return view('gallery');
})->name('gallery');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'subject' => ['nullable', 'string', 'max:255'],
        'message' => ['required', 'string', 'max:5000'],
    ]);

    ContactMessage::create($data);

    return back()->with('contact_sent', true);
})->name('contact.send');

// Plain GET download endpoint (not a Filament/Livewire action) so the
// "Imprimer" popup can link straight to a PDF per arrondissement — matches
// the legacy campyl print flow of one click per button, no form submit.
Route::get('/admin/camps/{camp}/imprimer', function (Camp $camp, Request $request) {
    abort_unless(auth()->check(), 403);
    abort_unless(auth()->user()->can('view', $camp), 403);

    $arrondissement = null;

    if ($arrondissementId = $request->integer('arrondissement')) {
        $arrondissement = Arrondissement::findOrFail($arrondissementId);
    }

    return CampResource::exportRegistrationsPdf($camp, $arrondissement);
})->name('camps.print-list');

// One-time production setup for hosts with no shell/SSH access: runs
// migrate + seed + storage:link over plain HTTP. Guarded by DEPLOY_TOKEN
// (config/app.php) — remove that env var once setup is done to disable
// this route entirely; it 403s whenever the token is unset.
Route::get('/deploy/{token}', function (string $token) {
    abort_unless(
        filled(config('app.deploy_token')) && hash_equals(config('app.deploy_token'), $token),
        403
    );

    $log = [];

    foreach ([
        ['migrate', ['--force' => true]],
        ['db:seed', ['--force' => true]],
        ['storage:link', []],
    ] as [$command, $params]) {
        Artisan::call($command, $params);
        $log[] = "\$ php artisan {$command}\n".Artisan::output();
    }

    return response('<pre>'.e(implode("\n", $log)).'</pre>');
})->name('deploy.run');
