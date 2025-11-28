<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Vite;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock Vite para evitar el error de manifest en tests
        // Mock Vite para evitar el error de manifest en tests
        // Vite::macro('useHotFile', function () {
        //     return $this;
        // });
        
        // Vite::macro('useBuildDirectory', function () {
        //     return $this;
        // });
        
        // Vite::macro('withEntryPoints', function () {
        //     return $this;
        // });
    }
}
