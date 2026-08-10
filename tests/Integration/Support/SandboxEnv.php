<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Integration\Support;

use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;

/**
 * Reads the project-root `.env` (Claude Code sandbox testing only — see
 * CLAUDE.md §26/§27) at runtime. Values never get copied into source,
 * fixtures, or any tracked file — this class only reads them into memory
 * for the duration of a single test run.
 *
 * Integration tests must be skipped, not failed, when `.env` or a required
 * value is missing, so the suite stays runnable for contributors who don't
 * have sandbox credentials.
 */
final class SandboxEnv
{
    private function __construct(
        public readonly int $partnerId,
        public readonly string $partnerKey,
        public readonly string $apiUrl,
        public readonly string $authUrl,
        public readonly int $shopId,
        public readonly string $accessToken,
        public readonly string $refreshToken,
        public readonly int $accessTokenExpiresAt,
    ) {
    }

    /**
     * @return array<string, string>
     */
    private static function parse(string $path): array
    {
        $values = [];

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $values[trim($key)] = trim($value);
        }

        return $values;
    }

    /**
     * Loads `.env` and returns populated credentials, or marks the given
     * test skipped and never returns if `.env` or a required value is missing.
     */
    public static function loadOrSkip(TestCase $test): self
    {
        $path = dirname(__DIR__, 3) . '/.env';

        if (!is_file($path)) {
            $test->markTestSkipped('.env not present — sandbox integration tests require local Claude Code testing credentials.');
        }

        $values = self::parse($path);
        $required = ['PARTNER_ID', 'PARTNER_KEY', 'API_URL', 'AUTH_URL', 'SHOP_ID', 'ACCESS_TOKEN', 'REFRESH_TOKEN', 'ACCESS_TOKEN_EXPIRES_AT'];

        foreach ($required as $key) {
            if (($values[$key] ?? '') === '') {
                $test->markTestSkipped(sprintf('.env is missing "%s" — sandbox integration tests require it.', $key));
            }
        }

        return new self(
            partnerId: (int) $values['PARTNER_ID'],
            partnerKey: $values['PARTNER_KEY'],
            apiUrl: $values['API_URL'],
            authUrl: $values['AUTH_URL'],
            shopId: (int) $values['SHOP_ID'],
            accessToken: $values['ACCESS_TOKEN'],
            refreshToken: $values['REFRESH_TOKEN'],
            accessTokenExpiresAt: (int) $values['ACCESS_TOKEN_EXPIRES_AT'],
        );
    }

    public function accessTokenObject(): AccessToken
    {
        return new AccessToken(
            $this->accessToken,
            $this->refreshToken,
            $this->accessTokenExpiresAt,
            shopId: $this->shopId,
        );
    }

    /**
     * Persists a freshly refreshed token pair back to `.env`, since
     * Shopee's refresh_token is single-use — after refreshing once, the
     * old value in `.env` is dead and would break the next local run.
     * Only ever writes to `.env` itself, never to any tracked file.
     */
    public function persistRefreshedToken(AccessToken $token): void
    {
        $path = dirname(__DIR__, 3) . '/.env';
        $contents = file_get_contents($path);

        $contents = preg_replace('/^ACCESS_TOKEN=.*$/m', 'ACCESS_TOKEN=' . $token->accessToken, $contents, 1);
        $contents = preg_replace('/^REFRESH_TOKEN=.*$/m', 'REFRESH_TOKEN=' . $token->refreshToken, $contents, 1);
        $contents = preg_replace('/^ACCESS_TOKEN_EXPIRES_AT=.*$/m', 'ACCESS_TOKEN_EXPIRES_AT=' . $token->expiresAt, $contents, 1);

        file_put_contents($path, $contents);
    }
}
