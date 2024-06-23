<?php

namespace Tests;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PHPUnit\Framework\Assert;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addTestingMacros();
    }

    private function addTestingMacros()
    {
        EloquentCollection::macro('assertContains', function ($value) {
            Assert::assertTrue($this->contains($value), 'Failed asserting that the collection contains the specified value.');
        });

        EloquentCollection::macro('assertNotContains', function ($value) {
            Assert::assertFalse($this->contains($value), 'Failed asserting that the collection does not contain the specified value.');
        });

    }
}
