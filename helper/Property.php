<?php

namespace CoMa\Helper;

use CoMa\Base\PropertyDialog\Field\CodeEditor;
use CoMa\Base\PropertyDialog\Field\Link;

class Property
{

  /**
   * Gibt alle Eigenschaftsnamen mit dem angegebenen Namen im Code-Editor zurück.
   * @param $name
   * @return array
   */
  public static function getCodeEditorProperties($name)
  {
    return [$name . CodeEditor::PROPERTY_CODE_MODE];
  }


}
