<?php

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\FailedJobController;

it('lists, shows, retries and deletes failed jobs', function () {
    // Disable middleware to bypass permission/auth checks in this integration-style test
    $this->withoutMiddleware();

    // Ensure failed_jobs table exists for test (avoid running full migrations)
    if (!Schema::hasTable('failed_jobs')) {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('connection')->nullable();
            $table->string('queue')->nullable();
            $table->text('payload');
            $table->longText('exception')->nullable();
            $table->timestamp('failed_at')->nullable();
        });
    }

    // Create an in-memory user to authenticate without DB
    $user = new User();
    $user->id = 1;
    $user->first_name = 'Test';
    $user->last_name = 'User';
    $user->email = 'test@example.com';

    // Insert a failed job
    $id = DB::table('failed_jobs')->insertGetId([
        'connection' => 'sync',
        'queue' => 'default',
        'payload' => json_encode(['test' => 'payload']),
        'exception' => 'TestException: test',
        'failed_at' => now(),
    ]);

    $controller = app(FailedJobController::class);

    // Index via controller (avoids rendering layout)
    $view = $controller->index(new Request());
    expect($view)->toBeInstanceOf(Illuminate\View\View::class);
    $data = $view->getData();
    expect(collect($data['jobs'])->pluck('id')->contains($id))->toBeTrue();

    // Show via controller
    $viewShow = $controller->show($id);
    expect($viewShow)->toBeInstanceOf(Illuminate\View\View::class);
    $job = $viewShow->getData()['job'];
    expect($job->id)->toBe($id);

    // Retry (should remove the failed job)
    $resp = $controller->retry($id);
    expect(DB::table('failed_jobs')->where('id', $id)->exists())->toBeFalse();

    // Re-insert to test delete
    $id2 = DB::table('failed_jobs')->insertGetId([
        'connection' => 'sync',
        'queue' => 'default',
        'payload' => json_encode(['test' => 'payload']),
        'exception' => 'TestException: test2',
        'failed_at' => now(),
    ]);

    $resp = $controller->destroy($id2);
    expect(DB::table('failed_jobs')->where('id', $id2)->exists())->toBeFalse();
});
