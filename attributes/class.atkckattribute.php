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
 */

Atk_Tools::useattrib("atktextattribute");

/**
 * Attribute wrapper for CKEditor (the successor of FCK Editor)
 * See http://ckeditor.com
 *
 */
class Atk_CKAttribute extends Atk_TextAttribute
{
    /**
     * @var array CKEditor configuration (default)
     */
    private $ckOptions = [
        // the toolbar groups arrangement
        'toolbarGroups' => [
            ['name' => 'clipboard', 'groups' => ['clipboard', 'undo', 'document']],
            ['name' => 'editing', 'groups' => ['find', 'selection', 'spellchecker']],
            ['name' => 'links'],
            ['name' => 'insert'],
            '/',
            ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
            ['name' => 'paragraph', 'groups' => ['list', 'indent', 'align']],
            ['name' => 'styles'],
            ['name' => 'colors']
        ],
        // remove some buttons
        'removeButtons' => 'Save,NewPage,Preview,Anchor,Flash,Smiley,PageBreak,Iframe,Subscript,Superscript,Font,Styles',
        // remove display of html tags on bottom bar
        'removePlugins' => 'elementspath',
        // simplify the dialog windows
        'removeDialogTabs' => 'image:advanced;link:advanced',
        // set the size
        'width' => 800,
        'height' => 250
    ];

    /**
     * Constructor
     * @param string $name Name of the attribute
     * @param int $flags Flags for the attribute
     * @param array $options CKEditor configuration options (overrides default)
     */
    function __construct($name, $flags = 0, $options = null)
    {
        /** update CKEditor configuration options */
        $this->ckOptions['language'] = Atk_Language::getLanguage();
        $this->ckOptions['wsc_lang'] = $this->ckOptions['scayt_sLang'] = $this->getSpellCheckerLang(Atk_Language::getLanguage());
        // global config override
        if (is_array(Atk_config::getGlobal('ck_options'))) {
            $this->ckOptions = array_merge($this->ckOptions, Atk_config::getGlobal('ck_options'));
        }
        // instance override
        if (is_array($options)) {
            $this->ckOptions = array_merge($this->ckOptions, $options);
        }

        parent::__construct($name, 0, $flags);
    }

    function edit($record = "", $fieldprefix = "", $mode = "")
    {
        $page = $this->getOwnerInstance()->getPage();

        $id = $this->getHtmlId($fieldprefix);
        $this->registerKeyListener($id, KB_CTRLCURSOR);

        // register CKEditor main script
        $page->register_script(Atk_Config::getGlobal("atkroot") . 'atk/attributes/ckeditor/ckeditor.js');

        // activate CKEditor
        $options = json_encode($this->ckOptions);
        $page->register_loadscript("CKEDITOR.replace('$id', $options);");

        return parent::edit($record, $fieldprefix, $mode);
    }

    function display($record, $mode = "")
    {
        return Atk_Tools::atkArrayNvl($record, $this->fieldName(), "");
    }

    function value2db($rec)
    {
        if (is_array($rec) && isset($rec[$this->fieldName()])) {
            $dbval = $this->escapeSQL(preg_replace("/\&quot;/Ui", "\"", $rec[$this->fieldName()]));
            return $dbval;
        }
        return null;
    }

    /**
     * Check if a record has an empty value for this attribute.
     *
     * If the record only contains tags or spaces, we consider it empty. We exclude the div
     * tag, since it is often used as (for instance) a placeholder for script results.
     *
     * @param array $record The record that holds this attribute's value.
     * @return boolean
     */
    function isEmpty($record)
    {
        $record[$this->fieldName()] = trim(strip_tags($record[$this->fieldName()], '<div>'));
        return parent::isEmpty($record);
    }

    private function getSpellCheckerLang($atkLang)
    {
        switch ($atkLang) {
            case 'da';
                return 'da_DK'; // Danish
            case 'de':
                return 'de_DE'; // German
            case 'el':
                return 'el_GR'; // Greek
            case 'en':
                return 'en_US'; // English
            case 'es':
                return 'es_ES'; // Spanish
            case 'fi':
                return 'fi_FI'; // Finnish
            case 'fr':
                return 'fr_FR'; // French
            case 'it':
                return 'it_IT'; // Italian
            case 'nl':
                return 'nl_NL'; // Dutch
            case 'no':
                return 'nb_NO'; // Norwegian
            case 'pt':
                return 'pt_PT'; // Portuguese
            case 'sv':
                return 'sv_SE'; // Swedish
            default:
                return 'en_US'; // Default: English
        }
    }
}