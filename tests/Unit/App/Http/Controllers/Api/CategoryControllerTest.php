<?php

namespace Tests\Unit\App\Http\Controllers\Api;

use App\Http\Controllers\Api\CategoryController as Controller;
use Core\Category\UseCase;
use Costa\DomainPackage\UseCase\DTO\Paginate\Output as PaginateOutput;
use Illuminate\Http\Request;
use Mockery;
use Tests\Unit\TestCase;

class CategoryControllerTest extends TestCase
{
    public function test_example()
    {
        $mockRequest = Mockery::spy(Request::class);
        $mockRequest->shouldReceive('get')->andReturn('test');

        $mockOutput = Mockery::spy(PaginateOutput::class, [
            [], 1, 1, 1, 1, 1, 1, 1,
        ]);

        $mockUse = Mockery::spy(UseCase\PaginateUseCase::class);
        $mockUse->shouldReceive('execute')->andReturn($mockOutput);

        $controller = new Controller();
        $response = $controller->index($mockRequest, $mockUse);

        $this->assertIsObject($response->resource);
        $this->assertArrayHasKey('meta', $response->additional);

        $mockUse->shouldHaveReceived('execute')->times(1);
    }
}
