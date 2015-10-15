<?php
/**
 * This file is part of the ATK distribution on GitHub.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package atk
 * @subpackage utils
 *
 * @copyright (c)2005 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *
 * @version $Revision: 4362 $
 * $Id$
 */

/**
 * The atkTriggerListener base class for handling trigger events on records.
 *
 * The most useful purpose of the atkTriggerListener is to serve as a base
 * class for custom trigger listeners. Extend this class and implement
 * postUpdate, preDelete etc. functions that will automatically be called
 * when such a trigger occurs. For more flexibility, override only
 * the notify($trigger, $record) method which catches every trigger.
 * Using Atk_Node::addListener you can add listeners that catch evens such as
 * records updates and additions.
 * This is much like the classic atk postUpdate/postAdd triggers, only much
 * more flexible.
 *
 * @author Martin Roest <martin@ibuildings.nl>
 * @author Peter C. Verhage <peter@achievo.org>
 * @package atk
 * @subpackage utils
 */
class Atk_TriggerListener
{
    /**
     * The owning node of the listener.
     * @access private
     * @var Atk_Node
     */
    var $m_node = null;

    /**
     * Base constructor.
     *
     * @return Atk_TriggerListener
     */
    function __construct()
    {

    }

    /**
     * Set the owning node of the listener.
     *
     * When using Atk_Node::addListener to add a listener to a node it is not
     * necessary to call this method as addListener will do that for you.
     *
     * @param Atk_Node $node The node to set as owner
     */
    function setNode(&$node)
    {
        $this->m_node = &$node;
    }

    /**
     * Notify the listener of any action on a record.
     *
     * This method is called by the framework for each action called on a
     * node. Depending on the actionfilter passed in the constructor, the
     * call is forwarded to the actionPerformed($action, $record) method.
     *
     * @param String $trigger The trigger being performed
     * @param array $record The record on which the trigger is performed
     * @param string $mode The mode (add/update)
     * @return boolean Result of operation.
     */
    function notify($trigger, &$record, $mode = null)
    {
        if (method_exists($this, $trigger)) {
            Atk_Tools::atkdebug("Call listener " . get_class($this) . " for trigger $trigger on " . $this->m_node->atkNodeType() . " (" . $this->m_node->primaryKey($record) . ")");
            return $this->$trigger($record, $mode);
        } else {
            return true;
        }
    }

}

