<?php

declare(strict_types=1);

namespace Costa\DomainPackage\Tests\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Testing\TestResponse;

trait TestResource
{
    protected function assertResource(TestResponse $response, JsonResource $resource)
    {
        $response->assertJson($resource->response()->getData(true));
    }
}
