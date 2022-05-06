<?php
declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Enum\Direction;
use App\Application\Factory\RoverFactory;
use App\Application\Repository\RoverRepositoryInterface;
use App\Application\Service\NasaRoverService;
use App\Application\Service\RoverPositionDto;
use App\Command\NasaRovers\Dto\RoverInformationDto;
use App\Domain\Entity\Rover;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NasaRoverServiceTest extends TestCase
{
    private RoverFactory|MockObject $roverFactory;
    private RoverRepositoryInterface|MockObject $roverRepository;
    private NasaRoverService $nasaRoverService;

    protected function setUp(): void
    {
        $this->roverFactory = $this->createMock(RoverFactory::class);
        $this->roverRepository = $this->createMock(RoverRepositoryInterface::class);

        $this->nasaRoverService = new NasaRoverService(
            $this->roverFactory,
            $this->roverRepository
        );

        parent::setUp();
    }

    /**
     * @throws Exception
     *
     * @dataProvider roverCoordinatesDataProvider
     */
    public function testSuccessRoverMoving(
        int $xCoordinate,
        int $yCoordinate,
        Direction $direction
    ): void {
        $rover = new Rover($xCoordinate, $yCoordinate, $direction);
        $rover2 = new Rover($xCoordinate, $yCoordinate+1, $direction);
        $finalRoverObject = new Rover($xCoordinate, $yCoordinate+2, $direction);

        $this->roverFactory->expects(self::once())
            ->method('create')
            ->with($xCoordinate, $yCoordinate, $direction)
            ->willReturn($rover);

        $this->roverRepository->expects(self::exactly(2))
            ->method('move')
            ->withConsecutive(
                [$rover],
                [$rover2]
            )
            ->willReturnOnConsecutiveCalls(
                $rover2,
                $finalRoverObject
            );

        $this->roverRepository->expects(self::never())
            ->method('turnLeft');

        $this->roverRepository->expects(self::never())
            ->method('turnRight');

        $expectedResult = new RoverPositionDto($xCoordinate, $yCoordinate+2, Direction::North);

        $roverInformationDto = new RoverInformationDto($xCoordinate, $yCoordinate, $direction);
        $roverInformationDto->setActions('MM');
        $actualResult = $this->nasaRoverService->process($roverInformationDto);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @throws Exception
     *
     * @dataProvider roverCoordinatesDataProvider
     */
    public function testSuccessTurnLeft(
        int $xCoordinate,
        int $yCoordinate
    ): void {
        $direction = Direction::North;

        $rover = new Rover($xCoordinate, $yCoordinate, $direction);
        $rover2 = new Rover($xCoordinate, $yCoordinate+1, Direction::West);
        $finalRoverObject = new Rover($xCoordinate, $yCoordinate+2, Direction::South);

        $this->roverFactory->expects(self::once())
            ->method('create')
            ->with($xCoordinate, $yCoordinate, $direction)
            ->willReturn($rover);

        $this->roverRepository->expects(self::exactly(2))
            ->method('turnLeft')
            ->withConsecutive(
                [$rover],
                [$rover2]
            )
            ->willReturnOnConsecutiveCalls(
                $rover2,
                $finalRoverObject
            );

        $this->roverRepository->expects(self::never())
            ->method('move');

        $this->roverRepository->expects(self::never())
            ->method('turnRight');

        $expectedResult = new RoverPositionDto($xCoordinate, $yCoordinate+2, Direction::South);

        $roverInformationDto = new RoverInformationDto($xCoordinate, $yCoordinate, $direction);
        $roverInformationDto->setActions('LL');
        $actualResult = $this->nasaRoverService->process($roverInformationDto);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @throws Exception
     *
     * @dataProvider roverCoordinatesDataProvider
     */
    public function testSuccessTurnRight(
        int $xCoordinate,
        int $yCoordinate
    ): void {
        $direction = Direction::North;

        $rover = new Rover($xCoordinate, $yCoordinate, $direction);
        $rover2 = new Rover($xCoordinate, $yCoordinate+1, Direction::East);
        $finalRoverObject = new Rover($xCoordinate, $yCoordinate+2, Direction::South);

        $this->roverFactory->expects(self::once())
            ->method('create')
            ->with($xCoordinate, $yCoordinate, $direction)
            ->willReturn($rover);

        $this->roverRepository->expects(self::exactly(2))
            ->method('turnRight')
            ->withConsecutive(
                [$rover],
                [$rover2]
            )
            ->willReturnOnConsecutiveCalls(
                $rover2,
                $finalRoverObject
            );

        $this->roverRepository->expects(self::never())
            ->method('move');

        $this->roverRepository->expects(self::never())
            ->method('turnLeft');

        $expectedResult = new RoverPositionDto($xCoordinate, $yCoordinate+2, Direction::South);

        $roverInformationDto = new RoverInformationDto($xCoordinate, $yCoordinate, $direction);
        $roverInformationDto->setActions('RR');
        $actualResult = $this->nasaRoverService->process($roverInformationDto);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @throws Exception
     *
     * @dataProvider roverCoordinatesDataProvider
     */
    public function testInvalidAction(
        int $xCoordinate,
        int $yCoordinate,
        Direction $direction
    ): void {
        $rover = new Rover($xCoordinate, $yCoordinate, $direction);

        $this->roverFactory->expects(self::once())
            ->method('create')
            ->with($xCoordinate, $yCoordinate, $direction)
            ->willReturn($rover);

        $this->roverRepository->expects(self::never())
            ->method('turnRight');

        $this->roverRepository->expects(self::never())
            ->method('move');

        $this->roverRepository->expects(self::never())
            ->method('turnLeft');

        $roverInformationDto = new RoverInformationDto($xCoordinate, $yCoordinate, $direction);
        $roverInformationDto->setActions('DD');

        $this->expectException(Exception::class);

        $this->nasaRoverService->process($roverInformationDto);
    }

    public function roverCoordinatesDataProvider(): array
    {
        return [
            [1, 2, Direction::North]
        ];
    }
}