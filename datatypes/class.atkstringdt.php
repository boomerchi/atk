<?php
/**
 * This file is part of the ATK distribution on GitHub.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package atk
 * @subpackage datatypes
 *
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *
 * @version $Revision: 6318 $
 * $Id$
 */
Atk_Tools::atkimport('atk.datatypes.atkdatatype');

/**
 * The 'string' datatype.
 * Useful for performing various small operations on strings fluently.
 *
 * @deprecated Scheduled for removal.
 * @author Boy Baukema <boy@achievo.org>
 * @package atk
 * @subpackage datatypes
 */
class Atk_StringDt extends Atk_DataType
{
    /**
     * @var string The internal value of the current string object
     */
    protected $m_string = "";

    /*     * *************** BASICS **************** */

    /**
     * The 'string' datatype for easy manipulation of strings.
     *
     * @param string $string
     */
    public function __construct($string)
    {
        $this->m_string = $string;
    }

    /*     * *************** OPERATIONS **************** */

    /**
     * Replace search value(s) with replace value(s).
     *
     * @param string|array $search What to search on
     * @param string|array $replace What to replace
     * @return atkString The current string object
     */
    public function replace($search, $replace)
    {
        $this->m_string = str_replace($search, $replace, $this->m_string);
        return $this;
    }

    /**
     * Parse data into a string with the atkStringParser
     *
     * @param array $data The data to parse into the string
     * @return atkString The current (modified) string object
     */
    public function parse($data)
    {
        $this->m_string = Atk_Tools::atknew('atk.utils.atkstringparser', $this->m_string)->parse($data);
        return $this;
    }

    /*     * *************** GETTERS **************** */

    /**
     * Get the current string.
     *
     * @return string The current string
     */
    public function getString()
    {
        return $this->m_string;
    }

    /**
     * To string. Returns the string representation for this object
     * which is ofcourse the internal string.
     *
     * @return string internal string
     */
    public function __toString()
    {
        return $this->m_string;
    }

}


