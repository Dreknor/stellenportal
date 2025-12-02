<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase; // sorgt dafür, dass die DB für jeden Test migriert wird

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('permission.testing', true);
    }
}
