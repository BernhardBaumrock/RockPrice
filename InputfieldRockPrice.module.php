<?php namespace ProcessWire;
/**
 * Inputfield for RockPrice Fieldtype
 *
 * @author Bernhard Baumrock, 01.04.2020
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class InputfieldRockPrice extends InputfieldMarkup {
  const defaultTax = 20;
  const defaultDigits = 2;
  const nomultiline = false;

  public static function getModuleInfo() {
    return [
      'title' => 'RockPrice Inputfield',
      'version' => '0.0.1',
      'summary' => 'Inputfield for RockPrice Fieldtype',
      'icon' => 'money',
      'requires' => ['FieldtypeRockPrice'],
      'installs' => [],
    ];
  }

  public function __construct() {
    parent::__construct();
    $this->icon = 'money';
    $this->defaultTax = self::defaultTax;
    $this->digits = self::defaultDigits;
    $this->nomultiline = self::nomultiline;
  }

  /**
   * Render ready
   * 
   * @param Inputfield $parent
   * @param bool $renderValueMode
   * @return bool
   * @throws WireException
   */
  public function renderReady(Inputfield $parent = null, $renderValueMode = false) {
    $this->wire('modules')->get('JqueryUI')->use('vex');

    $url = $this->config->urls($this);
    $this->config->scripts->add($url.$this->className.".js");

    $file = $url.$this->className.".less";
    $less = $this->modules->get('RockLESS');
    if($less) $less->addToConfig($file);
    else $this->config->styles->add("$file.css");
  }

  /**
  * Render the Inputfield
  * @return string
  */
  public function ___render() {
    return $this->files->render(__DIR__."/markup.php", [
      'field' => $this,
      'price' => $this->value,
      'name' => $this->name,
    ]);
  }

  /**
  * Process the Inputfield's input
  * @return $this
  */
  public function ___processInput($input) {
    $old = $this->value;
    $json = $input->get($this->name);
    $new = new RockPriceMulti($json);
    if(!$old->equals($new)) {
      $this->trackChange('value');
      $this->value = $new;
    }
  }

  // MARKUP HELPER METHODS

  /**
   * Render tax input
   */
  public function renderInputTax($val) {
    $select = '';
    foreach($this->getTaxSelectValues() as $tax) {
      $tax = trim($tax);
      if(!strlen($tax)) continue;
      $selected = $tax == $val ? ' selected="selected"' : '';
      $select .= "<option value='$tax'$selected>$tax%</option>";
    }
    if($select) return "<select>$select</select>";
    return "<input type='number' step='{$this->taxStep}' value='$val'>";
  }

  /**
   * Render vat input
   */
  public function renderInputVat($val) {
    return "<input type='number' {$this->getStep()} value='$val' disabled>";
  }
  /**
   * Render net input
   */
  public function renderInputNet($val) {
    return "<input type='number' {$this->getStep()} value='$val'>";
  }
  /**
   * Render gross input
   */
  public function renderInputGross($val) {
    return "<input type='number' {$this->getStep()} value='$val'>";
  }

  // END MARKUP HELPER METHODS

  /**
   * Get step markup from precision
   */
  public function getStep() {
    $step = 1 / pow(10, $this->digits);
    return " step='$step'";
  }

  /**
   * Get array of tax select
   * @return array
   */
  public function getTaxSelectValues() {
    $val = trim($this->taxSelect);
    return explode("\n", $val);
  }

  /**
   * Get field configuration
   * 
   * @return InputfieldWrapper
   * @throws WireException
   * 
   */
  public function ___getConfigInputfields() {
    $inputfields = parent::___getConfigInputfields();

    /** @var InputfieldInteger $f */
    $f = $this->modules->get('InputfieldInteger');
    $f->name = 'defaultTax';
    $f->label = $this->_('Default Tax');
    $f->value = $this->defaultTax;
    $f->size = 2;
    $f->notes = $this->_('For 20% tax type 20.');
    $f->columnWidth = 25;
    $inputfields->append($f);
    
    /** @var InputfieldFloat $f */
    $f = $this->modules->get('InputfieldFloat');
    $f->name = 'taxStep';
    $f->label = $this->_('Tax Step');
    $f->value = $this->taxStep ?? 1;
    $f->notes = $this->_('Setting for HTML input tag.');
    $f->size = 2;
    $f->columnWidth = 25;
    $inputfields->append($f);

    /** @var InputfieldInteger $f */
    $f = $this->modules->get('InputfieldInteger');
    $f->name = 'digits';
    $f->label = $this->_('Precision');
    $f->value = $this->digits ?? 2;
    $f->notes = $this->_('Digits after comma. Max precision in the database is 3 digits.');
    $f->size = 2;
    $f->columnWidth = 25;
    $inputfields->append($f);
    
    /** @var InputfieldCheckbox $f */
    $f = $this->modules->get('InputfieldCheckbox');
    $f->name = 'nomultiline';
    $f->label = $this->_('Do not allow multiline input');
    $f->attr('checked', $this->nomultiline ? 'checked' : '');
    $f->columnWidth = 25;
    $inputfields->append($f);
    
    /** @var InputfieldTextarea $f */
    $f = $this->modules->get('InputfieldTextarea');
    $f->name = 'taxSelect';
    $f->label = $this->_('Show Tax as Selectbox');
    $f->value = $this->taxSelect;
    $f->notes = $this->_('Enter one percentage per line.');
    $inputfields->append($f);

    return $inputfields; 
  }

}