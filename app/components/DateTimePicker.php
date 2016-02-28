<?php
namespace App\Components;

use Latte\Runtime\Html;
use Nette\Forms\Controls\TextInput;
use Nette\Templating\Helpers;

/**
 * DateTimePicker input control
 *
 * @package   Nette\Extras\DateTimePicker
 * @example   http://nettephp.com/extras/datetimepicker
 * @version   $Id: DateTimePicker.php,v 1.0.0 2010/02/25 18:11:08 dostal Exp $
 * @author    Ing. Radek Dostál <radek.dostal@gmail.com>
 * @copyright Copyright (c) 2009 Radek Dostál
 * @license   GNU Lesser General Public License
 * @link      http://www.radekdostal.cz
 */
class DateTimePicker extends TextInput
{
  /**
   * Konstruktor
   *
   * @access public
   *
   * @param string $label label
   * @param int $maxLength parametr maximální počet znaků
   */
  public function __construct($label, $maxLength = null)
  {
    parent::__construct($label, $maxLength);
  }

  /**
   * Vrácení hodnoty pole
   *
   * @access public
   *
   * @return mixed
   */
  public function getValue()
  {
    if (strlen($this->value)) {

      // Formát pro databázi: Y-m-d H:i:s
      return Helpers::date($this->value, "Y-m-d H:i:s");
    }

    return $this->value;
  }

  /**
   * Nastavení hodnoty pole
   *
   * @access public
   *
   * @param string $value hodnota
   *
   * @return void
   */
  public function setValue($value)
  {
    $value = preg_replace('~([0-9]{4})-([0-9]{2})-([0-9]{2})~', '$3.$2.$1', $value);

    parent::setValue($value);
  }

  /**
   * Generování HTML elementu
   *
   * @access public
   *
   * @return Html
   */
  public function getControl()
  {
    $control = parent::getControl();

    $control->class = 'datetimepicker';

    return $control;
  }
}

?>