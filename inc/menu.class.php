<?php

class PluginProtocolsmanagerMenu extends CommonGLPI {

    static $rightname = 'entity';

    static function getMenuName() {
       return __("Protocols Manager", "protocolsmanager");
    }

    static function getIcon() {
        return "fas fa-briefcase";
        // https://fontawesome.com/v4/icon/briefcase
        // https://fontawesome.com/v4/icon/id-card-o
        // https://fontawesome.com/v4/icon/archive
     }
 
    static function getMenuContent() {
 
       if (!Session::haveRight('entity', READ)) {
          return;
       }
       
 
       $front_fields = Plugin::getPhpDir('protocolsmanager', false)."/front";
       $menu = [
          'title' => self::getMenuName(),
          'page'  => "$front_fields/config.form.php",
          'icon'  => self::getIcon(),
       ];
 
    //    $itemtypes = ['PluginProtocolsmanagerConfig' => 'config'];
 
    //    foreach ($itemtypes as $itemtype => $option) {
    //       $menu['options'][$option] = [
    //          'title' => $itemtype::getTypeName(2),
    //          'page'  => $itemtype::getSearchURL(false),
    //          'links' => [
    //             'search' => $itemtype::getSearchURL(false)
    //          ]
    //       ];
 
    //       if ($itemtype::canCreate()) {
    //          $menu['options'][$option]['links']['add'] = $itemtype::getFormURL(false);
    //       }
 
    //    }
       return $menu;
    }

}