<?php
namespace CatPaw\Environment\Services;

use function Amp\call;
use function Amp\File\exists;

use Amp\File\File;

use function Amp\File\openFile;
use Amp\Promise;

use CatPaw\Attributes\Service;
use CatPaw\Environment\Exceptions\EnvironmentNotFoundException;

#[Service]
class EnvironmentService {
    /** @var array<string,string|null> */
    private array $variables = [];

    private string $fileName = '';

    public function setFileName(string $fileName):void {
        $this->fileName = $fileName;
    }

    /**
     * 
     * @return Promise<void>
     */
    public function sync():Promise {
        return call(function() {
            if (!yield exists($this->fileName)) {
                throw new EnvironmentNotFoundException("Environment file \"$this->fileName\" not found.");
            }

            /** @var File $file */
            $file = yield openFile($this->fileName, 'r');

            $contents = '';

            while ($chunk = yield $file->read(65536)) {
                $contents .= $chunk;
            }
            $this->variables = \Dotenv\Dotenv::parse($contents);
        });
    }

    /**
     * Get all the environment variables;
     * @return array<string,string|null>
     */
    public function getVariables():array {
        return $this->variables;
    }
}