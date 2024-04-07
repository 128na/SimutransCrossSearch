<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use MarvinLabs\DiscordLogger\Converters\SimpleRecordConverter;
use MarvinLabs\DiscordLogger\Discord\Embed;
use MarvinLabs\DiscordLogger\Discord\Message;

final class ConvertDiscord extends SimpleRecordConverter
{
    /**
     * @param  array{datetime:\DateTime,level_name:int,message:string,context:array<int|string,mixed>}  $record
     */
    protected function addMessageContent(Message $message, array $record): void
    {
        try {
            $stacktrace = $this->getStacktrace($record);
            if ($stacktrace !== null && $stacktrace !== '' && $stacktrace !== '0') {
                $this->makeErrorMessage($message, $record, $stacktrace);
            } else {
                $this->makeInfoMesage($message, $record);
            }
        } catch (\Throwable $throwable) {
            report($throwable);
        }
    }

    /**
     * @param  array{datetime:\DateTime,level_name:int,message:string,context:array<int|string,mixed>}  $record
     */
    private function makeErrorMessage(Message $message, array $record, string $stacktrace): void
    {
        $message
            ->content(sprintf(
                '[%s] %s: %s',
                $record['datetime']->format('Y-m-d H:i:s'),
                $record['level_name'],
                $record['message'],
            ))
            ->file($stacktrace, $this->getStacktraceFilename($record) ?? '');
    }

    /**
     * @param  array{datetime:\DateTime,level_name:int,message:string,context:array<int|string,mixed>}  $record
     */
    private function makeInfoMesage(Message $message, array $record): void
    {
        $embed = Embed::make();

        $rawMessages = explode("\n", $record['message']);

        $embed
            ->color($this->getRecordColor($record))
            ->title(sprintf(
                '[%s] %s: %s',
                $record['datetime']->format('Y-m-d H:i:s'),
                $record['level_name'],
                array_shift($rawMessages),
            ));

        if ($rawMessages !== []) {
            $embed->description(implode("\n", $rawMessages));
        }

        foreach ($record['context'] as $key => $value) {
            if (! is_string($value) && ! is_numeric($value)) {
                $value = json_encode($value);
            }

            $embed->field((string) $key, (string) $value);
        }

        $message->embed($embed);
    }
}
