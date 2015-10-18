<?php namespace Sintattica\Atk\Ui;

use Sintattica\Atk\Core\Tools;
use Sintattica\Atk\Core\Config;
use Sintattica\Atk\Core\Module;

/**
 * Theme loader
 *
 * @author Ivo Jansch <ivo@achievo.org>
 * @author Boy Baukema <boy@ibuildings.nl>
 * @package atk
 * @subpackage ui
 *
 */
class Theme
{
    var $m_name = '';
    var $m_theme = array();

    /**
     * Function to get an Instance of the Theme class,
     * ensures that there is never more than one instance (Singleton pattern)
     *
     * @param bool $reset Always reset and return a new instance
     * @return Theme theme instance
     */
    public static function &getInstance($reset = false)
    {
        static $s_instance = null;
        if ($s_instance == null || $reset) {
            $s_instance = new self();
        }
        return $s_instance;
    }

    /**
     * Constructor, initializes class and certain values
     * @access private
     */
    function __construct()
    {
        global $g_theme;
        Tools::atkdebug("Created a new Theme instance");
        if (isset($g_theme["Name"]) && $g_theme["Name"] != "") {
            $this->m_name = $g_theme["Name"];
        } else {
            $this->m_name = Config::getGlobal("defaulttheme");
        }
        $this->_loadTheme();
    }

    /**
     * Convert a relative theme path to an absolute path.
     *
     * If a relative path starts with 'module/something' this method converts
     * the start of the path to the location where the module 'something' is
     * actually installed.
     *
     * @static
     * @param String $relpath The relative path to convert
     * @param String $location
     * @return String The absolute path
     */
    public static function absPath($relpath, $location = '')
    {
        if ($relpath == "") {
            return "";
        }
        if (preg_match("!module/(.*?)/(.*)!", $relpath, $matches)) {
            return Module::moduleDir($matches[1]) . $matches[2];
        }

        if (substr($relpath, 0, 4) === 'atk/') {
            $location = 'atk';
        } else {
            if (substr($relpath, 0, 7) === 'themes/') {
                $location = 'app';
            }
        }

        $return = ($location === 'app' ? Config::getGlobal('application_dir') : Config::getGlobal("atkroot")) . $relpath;
        return $return;
    }

    /**
     * Load the theme information into memory.
     *
     * If a cached file with theme information doesn't exist, it is compiled
     * from the theme dir.
     */
    function _loadTheme()
    {
        if (!count($this->m_theme)) {
            $filename = Config::getGlobal("atktempdir") . "themes/" . $this->m_name . ".php";
            if (!file_exists($filename) || Config::getGlobal("force_theme_recompile")) {
                $compiler = new ThemeCompiler();
                $compiler->compile($this->m_name);
            }
            $theme = array();
            include($filename);
            $this->m_theme = $theme; // $theme is set by include($filename);
        }
    }

    /**
     * Returns the value for themevalue
     * Example: getAttribute("highlight");
     *          returns "#eeeeee"
     * @param string $attribname the name of the attribute in the themedefinition
     * @param string $default the default to fall back on
     * @return mixed the value of the attribute in the themedefinition
     */
    function getAttribute($attribname, $default = "")
    {
        return (isset($this->m_theme["attributes"][$attribname]) ? $this->m_theme["attributes"][$attribname]
            : $default);
    }

    /**
     * Retrieve the location of a file
     * @access private
     *
     * @param string $type the type of the file
     * @param string $name the name of the themefile
     * @param string $module the name of the module requesting the file
     */
    function getFileLocation($type, $name, $module = "")
    {
        if ($module != "" && isset($this->m_theme["modulefiles"][$module][$type][$name])) {
            return Module::moduleDir($module) . "themes/" . $this->m_theme["modulefiles"][$module][$type][$name];
        } else {
            if (isset($this->m_theme["files"][$type][$name])) {
                return Theme::absPath($this->m_theme["files"][$type][$name]);
            }
        }
        return "";
    }

    /**
     * Returns full path for themed template file
     * @param string $tpl the template name
     * @param string $module the name of the module requesting the file
     * @return string the full path of the template file
     */
    function tplPath($tpl, $module = "")
    {
        return $this->getFileLocation("templates", $tpl, $module);
    }

    /**
     * Returns full path for themed image file
     * @param string $img the image name
     * @param string $module the name of the module requesting the file
     * @return string the full path of the image file
     */
    function imgPath($img, $module = "")
    {
        $exts = array('', '.png', '.gif', '.jpg');

        foreach ($exts as $ext) {
            $location = $this->getFileLocation("images", $img . $ext, $module);
            if ($location != null) {
                return $location;
            }
        }

        return null;
    }

    /**
     * Returns full path for themed style file
     * @param string $style the name of the CSS file
     * @param string $module the name of the module requesting the file
     * @return string the full path of the style file
     */
    function stylePath($style, $module = "")
    {
        return $this->getFileLocation("styles", $style, $module);
    }

    /**
     * Returns full path for themed icon file
     * @param string $icon the icon name (no extension)
     * @param string $type the icon type (example: "recordlist")
     * @param string $module the name of the module requesting the file
     * @param string $ext the extension of the file,
     *                       if this is empty, Theme will check several
     *                       extensions.
     * @param boolean $useDefault use default icon fallback if not found?
     * @return string the full path of the icon file
     */
    function iconPath($icon, $type, $module = "", $ext = '', $useDefault = null)
    {
        if ($useDefault === null) {
            $useDefault = true;
        }

        // Check module themes for icon
        $iconfile = $this->getIconFileFromModuleTheme($icon, $type, $ext);
        if ($module != "" && $iconfile) {
            return Module::moduleDir($module) . "themes/" . $iconfile;
        }

        // Check the default theme for icon
        $iconfile = $this->getIconFileFromTheme($icon, $type, $this->m_theme['files'], $ext);
        if ($iconfile) {
            return Theme::absPath($iconfile);
        }

        if ($useDefault) {
            // Check the default theme for default icon
            $iconfile = $this->getIconFileFromTheme('default', $type, $this->m_theme['files'], $ext);
            if ($iconfile) {
                return Theme::absPath($iconfile);
            }
        }

        return false;
    }

    /**
     * Get the icon file from the module theme
     *
     * @param string $icon the name of the icon
     * @param string $type the type of the icon
     * @param string $ext the file extension
     * @return String iconfile
     */
    function getIconFileFromModuleTheme($icon, $type, $ext = "")
    {
        if (!isset($this->m_theme['modulefiles'])) {
            return false;
        }
        $modules = Module::atkGetModules();
        $modulenames = array_keys($modules);
        foreach ($modulenames as $modulename) {
            if (isset($this->m_theme['modulefiles'][$modulename])) {
                $iconfile = $this->getIconFileFromTheme($icon, $type, $this->m_theme['modulefiles'][$modulename], $ext);
                if ($iconfile) {
                    return $iconfile;
                }
            }
        }
        return false;
    }

    /**
     * Get the icon file from the theme
     *
     * @param string $iconname the name of the icon
     * @param string $type the type of the icon
     * @param array $theme the theme array containing all files
     * @param string $ext the file extension
     * @return String iconfile
     */
    function getIconFileFromTheme($iconname, $type, $theme, $ext = "")
    {
        if ($ext) {
            return $this->_getIconFileWithExtFromTheme($iconname, $ext, $type, $theme);
        }

        $allowediconext = array('png', 'gif', 'jpg');
        foreach ($allowediconext as $ext) {
            $iconfile = $this->_getIconFileWithExtFromTheme($iconname, $ext, $type, $theme);
            if ($iconfile) {
                return $iconfile;
            }
        }
        return false;
    }

    /**
     * Get the icon file from this theme
     *
     * @param string $iconname the iconname
     * @param string $ext the file extension
     * @param string $type the icon type
     * @param array $theme the theme array containing all files
     * @return String iconfile
     */
    function _getIconFileWithExtFromTheme($iconname, $ext, $type, $theme)
    {
        if (isset($theme['icons'][$type][$iconname . "." . $ext])) {
            $iconfile = $theme['icons'][$type][$iconname . "." . $ext];
            if ($iconfile) {
                return $iconfile;
            }
        }
        return false;
    }

    /**
     * Gets the directory of the current theme
     * @return string full path of the current theme
     */
    function themeDir()
    {
        return Theme::absPath($this->getAttribute("basepath"));
    }


    function cssIcon($icon, $type, $module, $useDefault)
    {
        // Check the default theme for css icon
        $iconcss = $this->getIconCssFromTheme($icon, $type, $this->getAttribute('cssicons'));
        if ($iconcss) {
            return $iconcss;
        }
    }

    function getIconCssFromTheme($icon, $type, $cssicons)
    {
        if (isset($cssicons[$type]) && $cssicons[$type][$icon]) {
            return $cssicons[$type][$icon];
        }
        return false;
    }

    function getIcon($icon, $type, $module = '', $ext = '', $useDefault = null, $label = '', $attrs = array())
    {
        if ($useDefault === null) {
            $useDefault = true;
        }

        //CSS Icons
        if ($this->getAttribute('usecssicons')) {
            if ($label) {
                if (!$attrs['title']) {
                    $attrs['title'] = $label;
                }
            }
            $icon = $this->cssIcon($icon, $type, $module, $useDefault);
            if (!$icon) {
                return false;
            }
            $ret = '<span class="' . $icon . '"';
            foreach ($attrs as $k => $v) {
                $ret .= ' ' . $k . '="' . $v . '"';
            }
            $ret .= ' ></span>';
            return $ret;
        }

        //File Icons
        if ($label) {
            if (!$attrs['title']) {
                $attrs['title'] = $label;
            }
            if (!$attrs['alt']) {
                $attrs['alt'] = $label;
            }
        }
        $icon = $this->iconPath($icon, $type, $module, $ext, $useDefault);
        if (!$icon) {
            return false;
        }
        $ret = '<img src="' . $icon . '"';
        foreach ($attrs as $k => $v) {
            $ret .= ' ' . $k . '="' . $v . '"';
        }
        $ret .= ' />';
        return $ret;
    }

}
