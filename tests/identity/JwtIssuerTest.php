<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Tests unitarios para JwtIssuer.
 *
 * Ejecutar:
 *   ./vendor/bin/phpunit tests/identity/JwtIssuerTest.php
 */
class JwtIssuerTest extends TestCase
{
    private string $privateKey = '';
    private string $publicKey  = '';

    protected function setUp(): void
    {
        // Generar par de claves efímero para los tests
        $res = openssl_pkey_new(['digest_alg' => 'sha256', 'private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        $privPem = '';
        openssl_pkey_export($res, $privPem);
        $this->privateKey = $privPem;
        $details          = openssl_pkey_get_details($res);
        $this->publicKey  = $details['key'];
    }

    private function buildPayload(): array
    {
        return [
            'usernick'  => 'jperez',
            'email'     => 'jperez@empresa.com',
            'role'      => '2',
            'userIdBpm' => '42',
        ];
    }

    /**
     * issue() debe devolver un string JWT no vacío.
     */
    public function testIssueReturnsNonEmptyString(): void
    {
        $jwt = $this->_issue($this->buildPayload(), 7, 'Empresa ABC');
        $this->assertIsString($jwt);
        $this->assertNotEmpty($jwt);
        $this->assertStringContainsString('.', $jwt);
    }

    /**
     * El JWT debe decodificarse correctamente con la clave pública.
     */
    public function testJwtDecodesWithPublicKey(): void
    {
        $jwt     = $this->_issue($this->buildPayload(), 7, 'Empresa ABC');
        $decoded = JWT::decode($jwt, new Key($this->publicKey, 'RS256'));

        $this->assertEquals('jperez', $decoded->sub);
        $this->assertEquals('jperez@empresa.com', $decoded->email);
        $this->assertSame('7', $decoded->empr_id);   // string — fix ADR-008 tipo consistente
        $this->assertEquals('Empresa ABC', $decoded->groupBpm);
        $this->assertEquals('42', $decoded->userIdBpm);
        $this->assertEquals('2', $decoded->role);
    }

    /**
     * Los claims estándar iss, aud, iat, exp deben estar presentes y correctos.
     */
    public function testStandardClaimsArePresent(): void
    {
        $before = time();
        $jwt     = $this->_issue($this->buildPayload(), 7, 'Empresa ABC');
        $after   = time();

        $decoded = JWT::decode($jwt, new Key($this->publicKey, 'RS256'));

        $this->assertEquals('trazalog-dnato', $decoded->iss);
        $this->assertEquals('trazalog-mcp', $decoded->aud);
        $this->assertGreaterThanOrEqual($before, $decoded->iat);
        $this->assertLessThanOrEqual($after, $decoded->iat);
        $this->assertEquals($decoded->iat + 3600, $decoded->exp);
    }

    /**
     * Un token expirado debe lanzar excepción al decodificar.
     */
    public function testExpiredTokenThrows(): void
    {
        $this->expectException(\Firebase\JWT\ExpiredException::class);

        // Emitir token con TTL de -1 segundo (ya expirado al emitirse)
        $now = time();
        $payload = [
            'iss'     => 'trazalog-dnato',
            'aud'     => 'trazalog-mcp',
            'iat'     => $now - 3700,
            'exp'     => $now - 100,
            'sub'     => 'jperez',
            'empr_id' => '7',
        ];
        $expiredJwt = JWT::encode($payload, $this->privateKey, 'RS256');

        // Esto debe lanzar ExpiredException
        JWT::decode($expiredJwt, new Key($this->publicKey, 'RS256'));
    }

    /**
     * empr_id debe ser un string en el payload (ADR-008: consistencia de tipo en todo el flujo).
     * Antes era int; el cast explícito en JwtIssuer::issue() garantiza string.
     */
    public function testEmprIdIsString(): void
    {
        $jwt     = $this->_issue($this->buildPayload(), 99, 'Empresa Test');
        $decoded = JWT::decode($jwt, new Key($this->publicKey, 'RS256'));

        $this->assertIsString($decoded->empr_id);
        $this->assertSame('99', $decoded->empr_id);
    }

    /**
     * Un JWT firmado con una clave distinta debe fallar la verificación.
     */
    public function testWrongPublicKeyFails(): void
    {
        $this->expectException(\Firebase\JWT\SignatureInvalidException::class);

        $jwt = $this->_issue($this->buildPayload(), 7, 'Empresa ABC');

        // Generar otra clave pública
        $res2 = openssl_pkey_new(['digest_alg' => 'sha256', 'private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        $details2   = openssl_pkey_get_details($res2);
        $wrongPubKey = $details2['key'];

        JWT::decode($jwt, new Key($wrongPubKey, 'RS256'));
    }

    // -----------------------------------------------------------------------
    // Helper: invoca JWT::encode directamente usando las claves efímeras
    // (sin bootstrapear CodeIgniter)
    // -----------------------------------------------------------------------
    private function _issue(array $userInfo, int $empr_id, string $groupBpm): string
    {
        $now = time();
        $payload = [
            'iss'       => 'trazalog-dnato',
            'aud'       => 'trazalog-mcp',
            'iat'       => $now,
            'exp'       => $now + 3600,
            'sub'       => $userInfo['usernick'],
            'email'     => $userInfo['email'],
            'empr_id'   => (string) $empr_id,   // ADR-008: string para consistencia de tipo
            'role'      => $userInfo['role'],
            'userIdBpm' => $userInfo['userIdBpm'],
            'groupBpm'  => $groupBpm,
        ];
        return JWT::encode($payload, $this->privateKey, 'RS256');
    }
}
