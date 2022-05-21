<?php
namespace CatPaw\Environment\Services;

use function Amp\call;
use function Amp\File\exists;

use Amp\File\File;

use function Amp\File\openFile;
use Amp\Promise;
use CatPaw\Attributes\Entry;

use CatPaw\Attributes\Service;
use CatPaw\Environment\Exceptions\EnvironmentNotFoundException;

#[Service]
class EnvironmentService {
    /** @var array<string,string|null> */
    private array $variables = [];

    private static string $fileName = '';

    public static function setFileName(string $fileName):void {
        self::$fileName = $fileName;
    }

    /**
     * 
     * @return Promise<void>
     */
    #[Entry] public function sync():Promise {
        return call(function() {
            $fileName = self::$fileName;
            if (!yield exists($fileName)) {
                throw new EnvironmentNotFoundException("Environment file \"$fileName\" not found.");
            }

            /** @var File $file */
            $file = yield openFile($fileName, 'r');

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