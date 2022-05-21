<?php
namespace Tests;

use Amp\Loop;
use CatPaw\Environment\Attributes\Environment;
use CatPaw\Environment\Services\EnvironmentService;
use CatPaw\Environment\Services\EnvironmentService as ServicesEnvironmentService;
use CatPaw\Utilities\Container;
use PHPUnit\Framework\TestCase;

class EnvironmentServiceTest extends TestCase {
    public function testEntry() {
        Loop::run(function() {
            /** @var EnvironmentService $environmentService */
            $environmentService = yield Container::create(EnvironmentService::class, __DIR__."/.env");

            $env = $environmentService->getVariables();

            $this->assertEquals("127.0.0.1", $env['HOSTNAME']);
            $this->assertEquals("8080", $env['PORT']);
        });
    }

    public function testAttribute() {
        Loop::run(function() {
            ServicesEnvironmentService::setFileName(__DIR__."/.env");
            yield Container::run(function(
                #[Environment("HOSTNAME")] string $hostname,
                #[Environment("PORT")] string $port,
            ) {
                $this->assertEquals("127.0.0.1", $hostname);
                $this->assertEquals("8080", $port);
            });
        });
    }
}