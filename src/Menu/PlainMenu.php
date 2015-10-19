<?php namespace Sintattica\Atk\Menu;

use Sintattica\Atk\Core\Tools;
use Sintattica\Atk\Core\Config;
use Sintattica\Atk\Ui\Theme;
use Sintattica\Atk\Ui\Page;
use Sintattica\Atk\Ui\Ui;
use Sintattica\Atk\Session\SessionManager;

/**
 * Implementation of the plaintext menu.
 *
 * @author Ber Dohmen <ber@ibuildings.nl>
 * @author Sandy Pleyte <sandy@ibuildings.nl>
 * @package atk
 * @subpackage menu
 */
class PlainMenu extends MenuInterface
{
    var $m_height;

    /**
     * Constructor
     *
     * @return PlainMenu
     */
    function __construct()
    {
        $this->m_height = "50";
    }

    /**
     * Render the menu
     * @return String HTML fragment containing the menu.
     */
    function render()
    {
        $page = Page::getInstance();
        $page->addContent($this->getMenu());
        return $page->render("Menu", true);
    }

    /**
     * Get the menu
     *
     * @return string The menu
     */
    function getMenu()
    {
        global $ATK_VARS, $g_menu, $g_menu_parent;
        $atkmenutop = Tools::atkArrayNvl($ATK_VARS, "atkmenutop", "main");
        $theme = Theme::getInstance();
        $page = Page::getInstance();
        $delimiter = '';

        $menu = $this->getHeader($atkmenutop);
        if (is_array($g_menu[$atkmenutop])) {
            usort($g_menu[$atkmenutop], array(__CLASS__, "menu_cmp"));
            $menuitems = array();
            for ($i = 0; $i < count($g_menu[$atkmenutop]); $i++) {
                if ($i == count($g_menu[$atkmenutop]) - 1) {
                    $delimiter = "";
                } else {
                    $delimiter = Config::getGlobal("menu_delimiter");
                }
                $name = $g_menu[$atkmenutop][$i]["name"];
                $menuitems[$i]["name"] = $name;
                $url = $g_menu[$atkmenutop][$i]["url"];
                $enable = $this->isEnabled($g_menu[$atkmenutop][$i]);
                $modname = $g_menu[$atkmenutop][$i]["module"];

                $menuitems[$i]["enable"] = $enable;

                /* delimiter ? */
                if ($name == "-") {
                    $menu .= $delimiter;
                } /* submenu ? */
                else {
                    if (empty($url) && $enable) {
                        $url = $theme->getAttribute('menufile',
                                Config::getGlobal("menufile", 'menu.php')) . '?atkmenutop=' . $name;
                        $menu .= Tools::href($url, $this->getMenuTranslation($name, $modname),
                                SessionManager::SESSION_DEFAULT) . $delimiter;
                    } else {
                        if (empty($url) && !$enable) {
                            //$menu .=text("menu_$name").$config_menu_delimiter;
                        } /* normal menu item */ else {
                            if ($enable) {
                                $menu .= Tools::href($url, $this->getMenuTranslation($name, $modname), SessionManager::SESSION_NEW,
                                        false, $theme->getAttribute('menu_params',
                                            Config::getGlobal('menu_params', 'target="main"'))) . $delimiter;
                            } else {
                                //$menu .= text("menu_$name").$config_menu_delimiter;
                            }
                        }
                    }
                }
                $menuitems[$i]["url"] = Tools::session_url($url);
            }
        }
        /* previous */
        if ($atkmenutop != "main") {
            $parent = $g_menu_parent[$atkmenutop];
            $menu .= Config::getGlobal("menu_delimiter");
            $menu .= Tools::href($theme->getAttribute('menufile',
                        Config::getGlobal("menufile", 'menu.php')) . '?atkmenutop=' . $parent,
                    Tools::atktext("back_to", "atk") . ' ' . $this->getMenuTranslation($parent, $modname),
                    SessionManager::SESSION_DEFAULT) . $delimiter;
        }
        $menu .= $this->getFooter($atkmenutop);
        $page->register_style($theme->stylePath("style.css"));
        $page->register_script(Config::getGlobal("assets_url") . "javascript/menuload.js");
        $ui = Ui::getInstance();

        return $ui->renderBox(array(
            "title" => $this->getMenuTranslation($atkmenutop, $modname),
            "content" => $menu,
            "menuitems" => $menuitems
        ), "menu");
    }

    /**
     * Compare two menuitems
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    function menu_cmp($a, $b)
    {
        if ($a["order"] == $b["order"]) {
            return 0;
        }
        return ($a["order"] < $b["order"]) ? -1 : 1;
    }

    /**
     * Get the height for this menu
     *
     * @return int The height of the menu
     */
    function getHeight()
    {
        return $this->m_height;
    }

    /**
     * Get the menu position
     *
     * @return int The menu position (Menu::MENU_RIGHT, Menu::MENU_TOP, Menu::MENU_BOTTOM or Menu::MENU_LEFT)
     */
    function getPosition()
    {
        switch (Config::getGlobal("menu_pos", "left")) {
            case "right":
                return self::MENU_RIGHT;
            case "top":
                return self::MENU_TOP;
            case "bottom":
                return self::MENU_BOTTOM;
        }
        return self::MENU_LEFT;
    }

    /**
     * Is this menu scrollable?
     *
     * @return int MENU_SCROLLABLE or MENU_UNSCROLLABLE
     */
    function getScrollable()
    {
        return self::MENU_SCROLLABLE;
    }

    /**
     * Is this menu multilevel?
     *
     * @return int MENU_MULTILEVEL or MENU_NOMULTILEVEL
     */
    function getMultilevel()
    {
        return self::MENU_MULTILEVEL;
    }

}


