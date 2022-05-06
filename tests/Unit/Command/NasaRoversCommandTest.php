<?php
declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\NasaRoversCommand;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class NasaRoversCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        /** @var NasaRoversCommand $command */
        $command = $application->find('app:nasa-rovers');
        $this->commandTester = new CommandTester($command);

        parent::setUp();
    }

    public function testSuccessExecute(): void
    {
        $this->commandTester->setInputs([
            '5 5',
            "1 2 N\nMM"
        ]);

        $this->commandTester->execute([]);

        $this->commandTester->assertCommandIsSuccessful();
    }

    public function testSuccessExecuteWithoutRovers(): void
    {
        $this->commandTester->setInputs([
            '5 5',
            ''
        ]);

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Rovers are empty', $output);

        $this->commandTester->assertCommandIsSuccessful();
    }

    public function testInvalidTopRightXCoordinate(): void
    {
        $this->commandTester->setInputs([
            '0 5'
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Top-right X coordinate should be more than 0');

        $this->commandTester->execute([]);
    }

    public function testInvalidTopRightYCoordinate(): void
    {
        $this->commandTester->setInputs([
            '4 0'
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Top-right Y coordinate should be more than 0');

        $this->commandTester->execute([]);
    }

    public function testInvalidRoverInformationNotEnoughLines(): void
    {
        $this->commandTester->setInputs([
            '5 5',
            '1 2 N'
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Every rover should have 2 lines: one with coordinates and one with actions. You have 1 lines'
        );

        $this->commandTester->execute([]);
    }

    public function testInvalidRoverXCoordinates(): void
    {
        $this->commandTester->setInputs([
            '5 5',
            "10 2 N\nMM"
        ]);

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Error in the line #0', $output);
    }

    public function testInvalidRoverYCoordinates(): void
    {
        $this->commandTester->setInputs([
            '5 5',
            "2 20 N\nMM"
        ]);

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Error in the line #0', $output);
    }

    public function testRoverLeftMars(): void
    {
        $this->commandTester->setInputs([
            '5 5',
            "4 4 N\nMM"
        ]);

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Rover left Mars:', $output);
    }
}