<?php

declare(strict_types=1);

namespace App\Tests\App\Command;

use App\Command\CreateTodoCommand;
use App\Entity\TodoId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CreateTodoCommandTest extends TestCase
{
    private const EXPECTED_TITLE = 'Some title';

    private const EXPECTED_DESCRIPTION = 'Some description';

    /**
     * @var \App\Entity\TodoId
     */
    private $expectedId;

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        parent::setUp();

        $uuidString = Uuid::uuid1()->toString();

        $this->expectedId = TodoId::fromUuidString($uuidString);
    }

    public function testGetId()
    {
        $command = $this->createCommand();

        $this->assertSame(
            $this->expectedId,
            $command->getId()
        );
    }

    public function testGetTitle()
    {
        $command = $this->createCommand();

        $this->assertSame(
            self::EXPECTED_TITLE,
            $command->getTitle()
        );
    }

    public function testGetDescription()
    {
        $command = $this->createCommand();

        $this->assertSame(
            self::EXPECTED_DESCRIPTION,
            $command->getDescription()
        );
    }

    private function createCommand(): CreateTodoCommand
    {
        return new CreateTodoCommand(
            $this->expectedId,
            self::EXPECTED_TITLE,
            self::EXPECTED_DESCRIPTION
        );
    }
}
