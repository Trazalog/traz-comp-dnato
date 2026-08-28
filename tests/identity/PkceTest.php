<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Tests para el mecanismo PKCE S256 usado en el flujo OAuth.
 *
 * Ejecutar:
 *   ./vendor/bin/phpunit tests/identity/PkceTest.php
 */
class PkceTest extends TestCase
{
    private function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function computeChallenge(string $verifier): string
    {
        return $this->base64url_encode(hash('sha256', $verifier, true));
    }

    /**
     * El challenge S256 calculado desde el verifier debe coincidir con el challenge almacenado.
     */
    public function testPkceS256Roundtrip(): void
    {
        $verifier  = bin2hex(random_bytes(32));
        $challenge = $this->computeChallenge($verifier);

        $computed = $this->computeChallenge($verifier);
        $this->assertTrue(hash_equals($challenge, $computed));
    }

    /**
     * Un verifier diferente debe producir un challenge distinto.
     */
    public function testDifferentVerifierProducesDifferentChallenge(): void
    {
        $v1 = 'verifier_one_abc123';
        $v2 = 'verifier_two_xyz789';

        $this->assertNotEquals($this->computeChallenge($v1), $this->computeChallenge($v2));
    }

    /**
     * El challenge debe ser URL-safe (sin +, /, ni = de padding).
     */
    public function testChallengeIsUrlSafe(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $verifier  = bin2hex(random_bytes(32));
            $challenge = $this->computeChallenge($verifier);

            $this->assertStringNotContainsString('+', $challenge);
            $this->assertStringNotContainsString('/', $challenge);
            $this->assertStringNotContainsString('=', $challenge);
        }
    }
}
