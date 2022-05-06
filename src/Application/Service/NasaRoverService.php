<?php
declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Factory\RoverFactory;
use App\Application\Repository\RoverRepositoryInterface;
use App\Command\NasaRovers\Dto\RoverInformationDto;
use Exception;

class NasaRoverService
{
    public function __construct(
        private RoverFactory $roverFactory,
        private RoverRepositoryInterface $roverRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(RoverInformationDto $roverInformation): RoverPositionDto
    {
        $rover = $this->roverFactory->create(
            $roverInformation->getXCoordinate(),
            $roverInformation->getYCoordinate(),
            $roverInformation->getDirection()
        );

        foreach(\str_split($roverInformation->getActions()) as $action) {
            $rover = match($action) {
                'M' => $this->roverRepository->move($rover),
                'L' => $this->roverRepository->turnLeft($rover),
                'R' => $this->roverRepository->turnRight($rover),
                default => throw new Exception(
                    'Invalid action. Please, use only M, L or R'
                )
            };
        }

        return new RoverPositionDto(
            $rover->getCoordinateX(),
            $rover->getCoordinateY(),
            $rover->getDirection()
        );
    }
}
