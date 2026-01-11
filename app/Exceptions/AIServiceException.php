<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class AIServiceException extends Exception
{
    protected ?string $provider = null;

    protected ?string $errorCode = null;

    protected ?array $context = null;

    public function __construct(
        string $message = 'Ein Fehler ist bei der KI-Verarbeitung aufgetreten.',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create exception for API errors.
     */
    public static function apiError(string $message, ?string $provider = null, ?string $errorCode = null): self
    {
        $exception = new self(
            $message ?: __('Die KI-API ist derzeit nicht verfügbar. Bitte versuchen Sie es später erneut.')
        );
        $exception->provider = $provider;
        $exception->errorCode = $errorCode;

        return $exception;
    }

    /**
     * Create exception for parsing errors.
     */
    public static function parsingError(string $message = '', ?array $context = null): self
    {
        $exception = new self(
            $message ?: __('Die KI-Antwort konnte nicht verarbeitet werden.')
        );
        $exception->context = $context;

        return $exception;
    }

    /**
     * Create exception for rate limiting.
     */
    public static function rateLimited(?int $retryAfterSeconds = null): self
    {
        $message = __('Zu viele Anfragen. Bitte warten Sie einen Moment.');
        if ($retryAfterSeconds) {
            $message = __('Zu viele Anfragen. Bitte warten Sie :seconds Sekunden.', [
                'seconds' => $retryAfterSeconds,
            ]);
        }

        return new self($message, 429);
    }

    /**
     * Create exception for invalid configuration.
     */
    public static function invalidConfiguration(string $detail = ''): self
    {
        return new self(
            __('Die KI-Konfiguration ist ungültig.') . ($detail ? " {$detail}" : '')
        );
    }

    /**
     * Create exception for content filtering.
     */
    public static function contentFiltered(): self
    {
        return new self(
            __('Die Anfrage wurde aufgrund von Inhaltsrichtlinien abgelehnt.')
        );
    }

    /**
     * Get the provider that caused the error.
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * Get the error code from the provider.
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Get additional context about the error.
     */
    public function getContext(): ?array
    {
        return $this->context;
    }
}
