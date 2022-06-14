<?php
namespace CatPaw\Environment\Attributes;

use function Amp\call;
use Amp\Promise;
use Attribute;
use CatPaw\Attributes\Entry;
use CatPaw\Attributes\Interfaces\AttributeInterface;
use CatPaw\Attributes\Traits\CoreAttributeDefinition;
use CatPaw\Environment\Services\EnvironmentService;

use ReflectionParameter;

#[Attribute]
class Environment implements AttributeInterface {
    use CoreAttributeDefinition;

    private EnvironmentService $environmentService;

    public function __construct(
        private string $variableName
    ) {
    }

    #[Entry] public function setup(
        EnvironmentService $environmentService
    ) {
        $this->environmentService = $environmentService;
    }

    public function onParameter(ReflectionParameter $reflection, mixed &$value, mixed $http): Promise {
        return call(function() use ($reflection, &$value, $http) {
            $variables = $this->environmentService->getVariables();
            $value     = $variables[$this->variableName] ?? false;
        });
    }
}