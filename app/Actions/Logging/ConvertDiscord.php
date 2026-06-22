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
            // context['exception'] が Throwable のままの状態で先に取得する。
            // scrubArray() は Throwable を文字列化してしまうため、先に呼ぶと取得できなくなる。
            $stacktrace = $this->getStacktrace($record);

            // Discord は外部サービスへ送出されるため、組み立て前に機密値を伏字化する。
            $record['message'] = $this->secretScrubber->scrub($record['message']);
            $record['context'] = $this->secretScrubber->scrubArray($record['context']);

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
     * 親クラスの addMessageStacktrace は未伏字化の生スタックトレースで
     * $message->file を上書きしてしまうため、何もしないようにする
     * （伏字化済みのスタックトレースは addMessageContent 内で既に添付済み）。
     *
     * @param  array{datetime:\DateTime,level_name:int,message:string,context:array<int|string,mixed>}  $record
     */
    #[\Override]
    protected function addMessageStacktrace(Message $message, array $record): void
    {
        // no-op: see method docblock.
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
