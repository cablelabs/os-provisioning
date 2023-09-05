<?php

namespace App\Utilities;

use Closure;
use Exception;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;

class ConcatenatePdfs extends Process
{
    private array $files = [];
    private ?string $outputFile = null;
    private string $temporaryShellFile;
    private bool $shouldTrackProgress = false;
    private int $totalPages;
    private int $processedPages = 0;
    private Closure $progressCallback;

    public function __construct(array $files = [], ?string $outputFile = null, ?float $timeout = null)
    {
        $this->files = $files;
        $this->setOutputFile($outputFile)->setTemporaryShellFile()->writeShellCommand();

        parent::__construct(['sh', $this->temporaryShellFile], null, null, null, $timeout);
    }

    public static function make(array $files, ?string $outputFile = null, ?float $timeout = null)
    {
        return new static($files, $outputFile, $timeout);
    }

    private function setTemporaryShellFile(): static
    {
        if (! isset($this->temporaryShellFile)) {
            $filename = implode('-', [class_basename(static::class), count($this->files), str()->random(10)]);
            $this->temporaryShellFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.$filename.'.sh';
        }

        return $this;
    }

    private function writeShellCommand(): static
    {
        throw_unless(count($this->files), Exception::class, 'No file provided for concatenation');

        $args = [
            '-dNumRenderingThreads=6',
            '-dBATCH',
            '-dNOPAUSE',
            '-dRENDERTTNOTDEF',
            $this->shouldTrackProgress ? null : '-q', // Progress tracking relies on verbose output
            '-sDEVICE=pdfwrite',
            '-sOutputFile='.$this->outputFile,
            implode(' ', $this->files), // Files to be concatenated
        ];

        $command = 'nice gs '.collect($args)->filter()->implode(' ');
        File::put($this->temporaryShellFile, $command);

        return $this;
    }

    private function setOutputFile(?string $filepath = null): static
    {
        if (! $filepath) {
            $filepath = (string) str(storage_path('app/tmp/'))->append('to-concate-'.str()->random(10));
        }

        $this->outputFile = str($filepath)->finish('.pdf');

        return $this;
    }

    private function setTotalPages()
    {
        if (isset($this->totalPages)) {
            return;
        }

        /**
         * Match all the "/Page" occurances in PDF content to guess page count
         *
         * We might also use ghostscript to achieve this as well,
         * but we have to run command for each file (processes within process)
         */
        $pageGuesser = fn ($path) => preg_match_all('/\/Page\W/', file_get_contents($path));
        $this->totalPages = collect($this->files)->map($pageGuesser)->sum();
    }

    private function runProgressCallbackIfApplicable(string $line)
    {
        $line = str($line);
        if ($line->trim()->isEmpty()) {
            return;
        }

        /**
         * GhostScript outputs "Page {no}" for each page processed, per line
         * We'll check if the output matches the pattern
         * and add to the processed pages
         */
        $pattern = '/^Page \d+\n/m';
        $processedPages = $line->matchAll($pattern)->count();

        if ($processedPages && is_callable($this->progressCallback)) {
            $this->processedPages += $processedPages;
            value($this->progressCallback, $this->getProgress(), $this->processedPages, $this->totalPages);
        }
    }

    private function getProgress(): int
    {
        return isset($this->totalPages) && $this->totalPages > 0 ? ceil(($this->processedPages / $this->totalPages) * 100) : 0;
    }

    public function getOutputFile()
    {
        throw_unless(is_file($this->outputFile), Exception::class, 'PDF concatenation is either in progress or failed '.__FUNCTION__);

        return $this->outputFile;
    }

    public function addOutput(string $line)
    {
        if ($this->shouldTrackProgress) {
            $this->runProgressCallbackIfApplicable($line);
        }

        parent::addOutput($line);
    }

    public function onProgress(callable $progressCallback): static
    {
        throw_if($this->isRunning(), RuntimeException::class, 'Process is already running');
        $this->shouldTrackProgress = true;
        $this->progressCallback = $progressCallback;
        $this->setTotalPages();
        $this->writeShellCommand(); // We need to re-write the command to file to have process output

        return $this;
    }

    public function doCleanup()
    {
        File::delete($this->temporaryShellFile);
    }

    public function __destruct()
    {
        $this->doCleanup();
    }
}
