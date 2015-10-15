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
 * @copyright (c) 2000-2007 Ibuildings.nl BV
 *
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 */

/**
 * Grid event.
 *
 * @see Atk_DGListener
 * @see Atk_DataGrid
 *
 * @author Peter C. Verhage <peter@achievo.org>
 * @package atk
 * @subpackage datagrid
 */
class Atk_DGEvent
{
    /**
     * Event will be triggered at the start of the call to Atk_DataGrid::render,
     * before the grid or any of it's components have been rendered.
     */
    const PRE_RENDER = "preRender";

    /**
     * Event will be triggered at the end of the call to Atk_DataGrid::render,
     * after all component and the grid itself have been rendered.
     */
    const POST_RENDER = "postRender";

    /**
     * Event will be triggered at the start of the call to
     * Atk_DataGrid::loadRecords, before the records are loaded.
     */
    const PRE_LOAD = "preLoad";

    /**
     * Event will be triggered at the end of the call to
     * Atk_DataGrid::loadRecords, after the records are loaded.
     */
    const POST_LOAD = "postLoad";

    /**
     * Grid.
     *
     * @var Atk_DataGrid
     */
    private $m_grid;

    /**
     * Event identifier.
     *
     * @var int
     */
    private $m_event;

    /**
     * Constructs a new event
     *
     * @param Atk_DataGrid $grid grid
     * @param string $event event identifier
     */
    public function __construct(Atk_DataGrid $grid, $event)
    {
        $this->m_grid = $grid;
        $this->m_event = $event;
    }

    /**
     * Returns the grid for this event.
     *
     * @return Atk_DataGrid grid
     */
    public function getGrid()
    {
        return $this->m_grid;
    }

    /**
     * Returns the event identifier.
     *
     * @return string event
     */
    public function getEvent()
    {
        return $this->m_event;
    }

}
