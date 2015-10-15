<?php
/**
 * This file is part of the ATK distribution on GitHub.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package atk
 * @subpackage attributes
 *
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *
 * @version $Revision: 6309 $
 * $Id$
 */

/**
 * The atkMlNumberAttribute class represents an multilanguage
 * attribute of a node that can have a numeric value.
 *
 * Based on atkNumberAttribute.
 *
 * @author Peter Verhage <peter@ibuildings.nl>
 * @package atk
 * @subpackage attributes
 *
 */
class Atk_MlNumberAttribute extends Atk_MlAttribute
{

    /**
     * Constructor
     * @param string $name name of the attribute
     * @param integer $flags flags for this attribute
     * @param integer $size The size of this attribute
     */
    function atkMlNumberAttribute($name, $flags = 0, $size = 0)
    {
        $this->atkMlAttribute($name, $flags, $size); // base class constructor
    }

    /**
     * Validates if value is numeric
     * @param array $record Record that contains value to be validated.
     *                 Errors are saved in this record
     * @param string $mode can be either "add" or "update"
     */
    function validate(&$record, $mode)
    {
        $languages = Atk_Config::getGlobal("supported_languages");
        $value = $record[$this->fieldName()];

        foreach ($languages as $language) {
            if (!is_numeric($value[$language]) && $value[$language] != "") {
                Atk_Tools::triggerError($record, $this->fieldName(), 'error_notnumeric');
            }
        }
    }

}


