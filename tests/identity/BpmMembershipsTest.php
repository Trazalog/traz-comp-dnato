<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Test de regresión para el fix c=10 → c=100 en BPM::getMemeberships().
 *
 * Ejecutar:
 *   ./vendor/bin/phpunit tests/identity/BpmMembershipsTest.php
 */
class BpmMembershipsTest extends TestCase
{
    /**
     * El archivo BPM.php de traz-tools debe tener c=100, no c=10.
     */
    public function testMembershipsCountIsNotTruncatedAt10(): void
    {
        $bpmFile = __DIR__ . '/../../../../traz-tools/application/libraries/BPM.php';

        if (!is_file($bpmFile)) {
            $this->markTestSkipped('traz-tools no disponible en este entorno.');
        }

        $source = file_get_contents($bpmFile);

        $this->assertStringContainsString(
            'c=100',
            $source,
            'BPM::getMemeberships() debe usar c=100 (no c=10) para no truncar memberships'
        );

        $this->assertStringNotContainsString(
            'membership?p=0&c=10&',
            $source,
            'c=10 no debe aparecer en la URL de memberships (bug P03 corregido)'
        );
    }
}
