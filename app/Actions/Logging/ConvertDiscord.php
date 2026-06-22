<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use Illuminate\Contracts\Config\Repository;
use MarvinLabs\DiscordLogger\Converters\SimpleRecordConverter;
use MarvinLabs\DiscordLogger\Discord\Embed;
use MarvinLabs\DiscordLogger\Discord\Message;

final class ConvertDiscord extends SimpleRecordConverter
{
    public function __construct(Repository $config, private readonly SecretScrubber $secretScrubber)
    {
        parent::__construct($config);
    }

    /**
     * @param  array{datetime:\DateTime,level_name:int,message:string,context:array<int|string,mixed>}  $record
     */
    #[\Override]
    protected function addMessageContent(Message $message, array $record): void
    {
        try {
            // Discord は外部サービスへ送出されるため、組み立て前に機密値を伏字化する。
            $record['message'] = $this->secretScrubber->scrub($record['message']);
            $record['context'] = $this->secretScrubber->scrubArray($record['context']);

            $stacktrace = $this->getStacktrace($record);
            if ($stacktrace !== null) {
                $stacktrace = $this->secretScrubber->scrub($stacktrace);
            }

            if (! in_array($stacktrace, [null, '', '0'], true)) {
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
