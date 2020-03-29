<?php

declare(strict_types=1);

namespace App\Stream;

use Generator;
use Psr\Http\Message\StreamInterface;

final class GeneratorStream implements StreamInterface
{
    public const READ_MODE_AS_IS = 1;
    public const READ_MODE_FIRST_YIELD = 2;

    private int $readMode = self::READ_MODE_AS_IS;

    private ?Generator $stream;

    private bool $readable;

    private ?int $size = null;

    private int $caret = 0;

    private bool $started = false;


    public function __construct(Generator $body)
    {
        $this->stream = $body;
        $this->readable = true;
    }

    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }

            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }

    public function close(): void
    {
        if (isset($this->stream)) {
            $this->detach();
        }
    }

    public function detach()
    {
        if (!isset($this->stream)) {
            return null;
        }
        $result = $this->stream;
        unset($this->stream);
        $this->size = null;
        $this->caret = 0;
        $this->started = false;
        $this->readable = false;
        return $result;
    }

    public function getSize(): ?int
    {
        if (null !== $this->size) {
            return $this->size;
        }

        if (!isset($this->stream)) {
            return null;
        }

        return null;
    }

    public function tell(): int
    {
        return $this->caret;
    }

    public function eof(): bool
    {
        return $this->stream === null || !$this->stream->valid();
    }

    public function isSeekable(): bool
    {
        return false;
    }

    public function seek($offset, $whence = \SEEK_SET): void
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable');
        }
    }

    public function rewind(): void
    {
        $this->stream->rewind();
        $this->caret = 0;
        $this->started = false;
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write($string): int
    {
        if (!$this->isWritable()) {
            throw new \RuntimeException('Cannot write to a non-writable stream');
        }
        return 0;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function setReadMode(int $mode): void
    {
        $this->readMode = $mode;
    }

    public function read($length): string
    {
        if (!$this->readable) {
            throw new \RuntimeException('Cannot read from non-readable stream');
        }
        if ($this->eof()) {
            throw new \RuntimeException('Cannot read from ended stream');
        }
        if (!$this->started) {
            $this->started = true;
            $read = (string)$this->stream->current();
            $this->caret += strlen($read);
            return $read;
        }
        if ($this->readMode === self::READ_MODE_FIRST_YIELD) {
            $content = '';
            while (!$this->eof()) {
                $content .= $this->stream->send(null);
            }
            return $content;
        }
        $read = (string)$this->stream->send(null);
        $this->caret += strlen($read);
        if ($this->eof()) {
            $this->size = $this->caret;
        }
        return $read;
    }

    public function getContents(): string
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Unable to read stream contents');
        }
        $content = '';
        while (!$this->eof()) {
            $content .= $this->read(PHP_INT_MAX);
        }
        return $content;
    }

    public function getMetadata($key = null)
    {
        if (!isset($this->stream)) {
            return $key ? null : [];
        }

        $meta = [
            'seekable' => $this->isSeekable(),
            'eof' => $this->eof(),
        ];

        if (null === $key) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }
}