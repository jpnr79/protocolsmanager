<?php

class PluginProtocolsmanagerMenu extends CommonGLPI
{

  static $rightname = 'plugin_protocolsmanager';

  static function getMenuName()
  {
    return __("Protocols Manager", "protocolsmanager");
  }

  static function getIcon()
  {
    return "fas fa-briefcase";
    // https://fontawesome.com/v4/icon/briefcase
    // https://fontawesome.com/v4/icon/id-card-o
    // https://fontawesome.com/v4/icon/archive
  }

  static function getMenuContent()
  {

    global $CFG_GLPI;
    $menu                      = [];
    $menu['title']             = self::getMenuName();
    $menu['page']              = "/" . Plugin::getWebDir('protocolsmanager', false) . "/front/config.form.php";
    $menu['icon']              = self::getIcon();

    return $menu;
  }
}
