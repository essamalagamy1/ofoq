<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/admin/students', 'GET');
$kernel->handle($request);
Auth::login(\App\Models\User::first());
$html = Livewire\Livewire::mount('dashboard.student.student-progress');
file_put_contents('test_view.html', $html);
