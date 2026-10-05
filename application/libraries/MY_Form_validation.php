<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Extension de CI_Form_validation con reglas custom.
 *
 * Reglas adicionales:
 *   - password_strong : valida una password robusta (>=10, may/min/num/sim).
 *
 * Mensajes en application/language/spanish/form_validation_lang.php
 * (y english/ por compatibilidad con la carga por defecto de CI).
 */
class MY_Form_validation extends CI_Form_validation
{
    public function __construct($rules = array())
    {
        parent::__construct($rules);
    }

    /**
     * Valida que la password cumpla con la politica robusta.
     *  - >= 10 caracteres
     *  - al menos una mayuscula (A-Z)
     *  - al menos una minuscula (a-z)
     *  - al menos un digito (0-9)
     *  - al menos un caracter no alfanumerico
     *
     * IMPORTANTE: si se modifican las reglas hay que reflejar el cambio en
     * public/js/password-strength.js (RULES) y en este metodo, asi cliente y
     * servidor quedan sincronizados.
     *
     * @param string $value
     * @return bool
     */
    public function password_strong($value)
    {
        $value = is_string($value) ? $value : (string) $value;

        if (strlen($value) < 10) {
            return false;
        }
        if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }
        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }
        if (!preg_match('/\d/', $value)) {
            return false;
        }
        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            return false;
        }
        return true;
    }
}
