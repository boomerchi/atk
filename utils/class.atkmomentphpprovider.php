<?php

$base = Atk_Config::getGlobal('atkroot') . 'atk/ext/momentphp/src/Moment/';

include_once($base . 'Moment.php');
include_once($base . 'MomentException.php');
include_once($base . 'FormatsInterface.php');
include_once($base . 'CustomFormats/MomentJs.php');

class Atk_MomentphpProvider
{


    public static function getFormatInstance()
    {
        static $s_instance = null;
        if ($s_instance == null) {
            Atk_Tools::atkdebug("Created a new \Moment\CustomFormats\MomentJs instance");
            $s_instance = new \Moment\CustomFormats\MomentJs();
        }
        return $s_instance;
    }
}