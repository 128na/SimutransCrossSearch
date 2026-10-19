<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\ConfigurationUrlParser;

final class PortalDatabaseConfigurationTest extends TestCase
{
    /** @var array<string, array{process: string|false, env: mixed, server: mixed, has_env: bool, has_server: bool}> */
    private array $environment = [];

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['DB_URL', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DB_SOCKET', 'PORTAL_DB_URL', 'PORTAL_DB_HOST', 'PORTAL_DB_PORT', 'PORTAL_DB_DATABASE', 'PORTAL_DB_USERNAME', 'PORTAL_DB_PASSWORD', 'PORTAL_DB_SOCKET'] as $key) {
            $this->environment[$key] = [
                'process' => getenv($key),
                'env' => $_ENV[$key] ?? null,
                'server' => $_SERVER[$key] ?? null,
                'has_env' => array_key_exists($key, $_ENV),
                'has_server' => array_key_exists($key, $_SERVER),
            ];
            $this->setEnvironment($key, null);
        }
    }

    #[\Override]
    protected function tearDown(): void
    {
        foreach ($this->environment as $key => $original) {
            putenv($original['process'] === false ? $key : $key.'='.$original['process']);
            unset($_ENV[$key], $_SERVER[$key]);
            if ($original['has_env']) {
                $_ENV[$key] = $original['env'];
            }
            if ($original['has_server']) {
                $_SERVER[$key] = $original['server'];
            }
        }

        parent::tearDown();
    }

    public function test_portal_uses_dedicated_connection_values(): void
    {
        $this->setLegacyValues();
        $values = ['host' => 'portal-host', 'port' => '3308', 'database' => 'portal-data', 'username' => 'portal-reader', 'password' => 'portal-secret', 'socket' => '/portal/mysql.sock'];
        foreach ($values as $key => $value) {
            $this->setEnvironment('PORTAL_DB_'.strtoupper($key), $value);
        }

        $portal = $this->configuration()['connections']['portal'];

        foreach ($values as $key => $value) {
            $this->assertSame($value, $portal[$key === 'socket' ? 'unix_socket' : $key]);
        }
    }

    public function test_missing_portal_values_preserve_legacy_fallback(): void
    {
        $this->setLegacyValues();
        $config = $this->configuration();

        foreach (['host', 'port', 'database', 'username', 'password', 'unix_socket'] as $key) {
            $this->assertSame($config['connections']['mysql'][$key], $config['connections']['portal'][$key]);
        }
    }

    public function test_explicit_empty_credentials_and_socket_do_not_fall_back(): void
    {
        $this->setLegacyValues();
        foreach (['USERNAME', 'PASSWORD', 'SOCKET'] as $key) {
            $this->setEnvironment('PORTAL_DB_'.$key, '');
        }

        $portal = $this->configuration()['connections']['portal'];

        foreach (['username', 'password', 'unix_socket'] as $key) {
            $this->assertSame('', $portal[$key]);
        }
    }

    public function test_common_database_url_cannot_override_portal_connection(): void
    {
        $this->setLegacyValues();
        $this->setEnvironment('DB_URL', 'mysql://main-user:main-password@main-host:3307/main-data');
        $this->setEnvironment('PORTAL_DB_DATABASE', 'portal-data');
        $this->setEnvironment('PORTAL_DB_USERNAME', 'portal-reader');
        $config = $this->configuration();
        $portal = (new ConfigurationUrlParser)->parseConfiguration($config['connections']['portal']);

        $this->assertNotNull($config['connections']['mysql']['url']);
        $this->assertNull($config['connections']['portal']['url']);
        $this->assertSame('portal-data', $portal['database']);
        $this->assertSame('portal-reader', $portal['username']);
    }

    public function test_url_cannot_bypass_explicit_portal_credentials(): void
    {
        $this->setLegacyValues();
        $this->setEnvironment('DB_URL', 'mysql://main-user:main-password@main-host:3307/main-data');
        $this->setEnvironment('PORTAL_DB_URL', 'mysql://portal-reader:portal-password@portal-host:3308/portal-data');
        $this->setEnvironment('PORTAL_DB_USERNAME', 'explicit-reader');
        $this->setEnvironment('PORTAL_DB_PASSWORD', 'explicit-secret');
        $portal = (new ConfigurationUrlParser)->parseConfiguration($this->configuration()['connections']['portal']);

        $this->assertSame('explicit-reader', $portal['username']);
        $this->assertSame('explicit-secret', $portal['password']);
        $this->assertSame('legacy-host', $portal['host']);
        $this->assertSame('3307', $portal['port']);
        $this->assertNull($portal['url']);
    }

    private function setLegacyValues(): void
    {
        foreach (['HOST' => 'legacy-host', 'PORT' => '3307', 'DATABASE' => 'legacy-data', 'USERNAME' => 'legacy-user', 'PASSWORD' => 'legacy-password', 'SOCKET' => '/legacy/mysql.sock'] as $key => $value) {
            $this->setEnvironment('DB_'.$key, $value);
        }
    }

    private function setEnvironment(string $key, ?string $value): void
    {
        unset($_ENV[$key], $_SERVER[$key]);
        putenv($value === null ? $key : $key.'='.$value);
        if ($value !== null) {
            $_ENV[$key] = $_SERVER[$key] = $value;
        }
    }

    /** @return array{connections: array<string, array<string, mixed>>} */
    private function configuration(): array
    {
        return require dirname(__DIR__, 2).'/config/database.php';
    }
}
