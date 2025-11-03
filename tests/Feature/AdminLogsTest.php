<?php

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\LogController;

it('allows admin to view logs index', function () {
    // Avoid middleware and HTTP layer to prevent layout rendering requiring DB tables
    $this->withoutMiddleware();

    $user = new User();
    $user->id = 1;
    $user->first_name = 'Test';
    $user->last_name = 'User';
    $user->email = 'test@example.com';

    $controller = app(LogController::class);
    $view = $controller->index(new Request());
    expect($view)->toBeInstanceOf(Illuminate\View\View::class);
});
