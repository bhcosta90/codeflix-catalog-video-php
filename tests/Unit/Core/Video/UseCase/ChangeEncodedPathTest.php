<?php

namespace Tests\Unit\Core\Video\UseCase;

use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Domain\ValueObject\Enum\Status;
use Core\Video\Domain\ValueObject\Media;
use Core\Video\UseCase\ChangeEncodedPath;
use Core\Video\UseCase\DTO\ChangeEncodedPath as DTO;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;
use Costa\DomainPackage\ValueObject\Uuid;
use Mockery;
use Tests\Unit\TestCase;

class ChangeEncodedPathTest extends TestCase
{
    public function testExceptionFindId()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID 123 not found.');

        $uc = new ChangeEncodedPath(
            repository: $this->createMockRepository(
                id: '123',
                successFind: false
            ),
        );

        $uc->execute(new DTO\Input(
            id: '123',
            pathVideo: '/tmp/test.txt',
            pathTrailer: '/tmp/test.txt',
        ));

        $this->assertTrue(true);
    }

    public function testSpies()
    {
        $uc = new ChangeEncodedPath(
            repository: $this->createMockRepository(
                id: $id = Uuid::random()
            ),
        );

        $response = $uc->execute(new DTO\Input(
            id: (string) $id,
            pathVideo: '/tmp/test.txt',
            pathTrailer: '/tmp/test.txt',
        ));

        $this->assertInstanceOf(DTO\Output::class, $response);
        $this->assertNotEmpty($response->pathTrailer);
        $this->assertNotEmpty($response->pathVideo);
        $this->assertTrue($response->success);
        $this->assertEquals(2, $response->quantity);
    }

    protected function createMockRepository($id = null, $successUpdate = true, $successFind = true)
    {
        $mock = Mockery::spy(stdClass::class, VideoRepositoryInterface::class);
        if ($successFind) {
            $mock->shouldReceive('updateMedia')->times(1)->andReturn($successUpdate);
        }
        $mock->shouldReceive('findById')
            ->times(1)
            ->with((string) $id)
            ->andReturn($successFind ? new Video([
                'id' => $id,
                'title' => 'title',
                'description' => 'description',
                'yearLaunched' => 2020,
                'duration' => 50,
                'opened' => true,
                'rating' => Rating::L,
                'videoFile' => new Media('/tmp/video-fake.mp4', Status::PENDING),
                'trailerFile' => new Media('/tmp/trailer-fake.mp4', Status::PENDING),
            ]) : null);
        return $mock;
    }
}
