<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use RuntimeException;

/**
 * Cliente SMTP próprio (sem dependências externas).
 *
 * As credenciais SMTP são lidas da tabela `settings` (nunca hardcoded).
 * Suporta criptografia SSL (implícita) e TLS (STARTTLS) e autenticação LOGIN.
 */
final class MailService
{
    /** @var array<string,string|null> */
    private array $smtp;

    public function __construct(?array $smtpConfig = null)
    {
        $this->smtp = $smtpConfig ?? Setting::group('smtp');
    }

    public function isConfigured(): bool
    {
        return !empty($this->smtp['smtp_host'])
            && !empty($this->smtp['smtp_from_email']);
    }

    /**
     * Envia um e-mail HTML. Lança RuntimeException em caso de falha.
     */
    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, ?string $replyTo = null, ?string $replyToName = null): void
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('SMTP não configurado. Preencha as configurações no painel.');
        }

        $host = (string) $this->smtp['smtp_host'];
        $port = (int) ($this->smtp['smtp_port'] ?: 587);
        $user = (string) ($this->smtp['smtp_username'] ?? '');
        $pass = (string) ($this->smtp['smtp_password'] ?? '');
        $encryption = strtolower((string) ($this->smtp['smtp_encryption'] ?? 'tls'));
        $fromEmail = (string) $this->smtp['smtp_from_email'];
        $fromName = (string) ($this->smtp['smtp_from_name'] ?: 'DMR Assessoria Imobiliária');

        $transport = ($encryption === 'ssl') ? "ssl://{$host}" : $host;
        $timeout = 20;

        $socket = @stream_socket_client(
            "{$transport}:{$port}",
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT
        );

        if (!$socket) {
            throw new RuntimeException("Não foi possível conectar ao servidor SMTP ({$errstr}).");
        }

        stream_set_timeout($socket, $timeout);

        try {
            $this->expect($socket, 220);

            $ehloHost = $this->ehloName();
            $this->command($socket, "EHLO {$ehloHost}", 250);

            if ($encryption === 'tls') {
                $this->command($socket, 'STARTTLS', 220);
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
                    throw new RuntimeException('Falha ao iniciar TLS com o servidor SMTP.');
                }
                $this->command($socket, "EHLO {$ehloHost}", 250);
            }

            if ($user !== '') {
                $this->command($socket, 'AUTH LOGIN', 334);
                $this->command($socket, base64_encode($user), 334);
                $this->command($socket, base64_encode($pass), 235);
            }

            $this->command($socket, "MAIL FROM:<{$fromEmail}>", 250);
            $this->command($socket, "RCPT TO:<{$toEmail}>", [250, 251]);
            $this->command($socket, 'DATA', 354);

            $message = $this->buildMessage($fromEmail, $fromName, $toEmail, $toName, $subject, $htmlBody, $replyTo, $replyToName);
            $this->write($socket, $message . "\r\n.");
            $this->expect($socket, 250);

            $this->command($socket, 'QUIT', 221);
        } finally {
            if (is_resource($socket)) {
                fclose($socket);
            }
        }
    }

    private function buildMessage(string $fromEmail, string $fromName, string $toEmail, string $toName, string $subject, string $htmlBody, ?string $replyTo, ?string $replyToName): string
    {
        $boundary = 'dmr_' . bin2hex(random_bytes(8));
        $date = date('r');
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $fromHeader = $this->formatAddress($fromEmail, $fromName);
        $toHeader = $this->formatAddress($toEmail, $toName);

        $headers = [];
        $headers[] = "Date: {$date}";
        $headers[] = "From: {$fromHeader}";
        $headers[] = "To: {$toHeader}";
        if ($replyTo) {
            $headers[] = 'Reply-To: ' . $this->formatAddress($replyTo, $replyToName ?? '');
        }
        $headers[] = "Subject: {$encodedSubject}";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = "Message-ID: <" . bin2hex(random_bytes(12)) . "@" . $this->ehloName() . ">";
        $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";

        $plain = trim(strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $htmlBody) ?? ''));

        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($plain)) . "\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($htmlBody)) . "\r\n";
        $body .= "--{$boundary}--";

        return implode("\r\n", $headers) . "\r\n\r\n" . $body;
    }

    private function formatAddress(string $email, string $name): string
    {
        if ($name === '') {
            return "<{$email}>";
        }
        $encodedName = '=?UTF-8?B?' . base64_encode($name) . '?=';
        return "{$encodedName} <{$email}>";
    }

    private function ehloName(): string
    {
        $host = $_SERVER['SERVER_NAME'] ?? ($_SERVER['HTTP_HOST'] ?? 'localhost');
        return preg_replace('/[^a-zA-Z0-9.\-]/', '', $host) ?: 'localhost';
    }

    /** @param resource $socket @param int|array<int,int> $expectedCode */
    private function command($socket, string $cmd, int|array $expectedCode): void
    {
        $this->write($socket, $cmd);
        $this->expect($socket, $expectedCode);
    }

    /** @param resource $socket */
    private function write($socket, string $data): void
    {
        fwrite($socket, $data . "\r\n");
    }

    /** @param resource $socket @param int|array<int,int> $expectedCode */
    private function expect($socket, int|array $expectedCode): void
    {
        $codes = is_array($expectedCode) ? $expectedCode : [$expectedCode];
        $response = '';

        while (($line = fgets($socket, 512)) !== false) {
            $response .= $line;
            // A última linha de uma resposta multilinha usa espaço após o código.
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        $code = (int) substr($response, 0, 3);
        if (!in_array($code, $codes, true)) {
            throw new RuntimeException('Resposta SMTP inesperada: ' . trim($response));
        }
    }
}
