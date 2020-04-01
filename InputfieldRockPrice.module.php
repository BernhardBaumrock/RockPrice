<?php namespace ProcessWire;
/**
 * Inputfield for RockPrice Fieldtype
 *
 * @author Bernhard Baumrock, 01.04.2020
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class InputfieldRockPrice extends InputfieldMarkup {
  public $tax = 20;

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

  /**
  * Render the Inputfield
  * @return string
  */
  public function ___render() {
    $price = $this->value;
    $name = $this->name;
    return "<table class='RockPrice' data-precision='{$this->precision}'>
      <tbody>
        <tr class='head'>
          <td><small>".__('Tax')." (%)</small></td>
          <td><small>".__('Vat')."</small></td>
          <td><small>".__('Net')."</small></td>
          <td><small>".__('Gross')."</small></td>
        </tr>
        <tr>
          <td>{$this->renderTaxInput()}</td>
          <td class='vat'>{$price->vat}</td>
          <td><input type='number' name='{$name}_net' class='net' step='0.01' value='{$price->net}'></td>
          <td><input type='number' name='{$name}_gross' class='gross' step='0.01' value='{$price->gross}'></td>
        </tr>
      </tbody>
    </table>
    <script>$(function() { $('#Inputfield_$name').find('.tax').change(); });</script>";
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
    $this->config->styles->add($this->config->urls($this).$this->className.".css");
    $this->config->scripts->add($this->config->urls($this).$this->className.".js");
  }

  /**
   * Render tax input
   */
  public function renderTaxInput() {
    $val = $this->value->tax;
    $name = $this->name."_tax";
    $select = '';
    foreach($this->getTaxSelectValues() as $tax) {
      $tax = trim($tax);
      if(!strlen($tax)) continue;
      $selected = $tax == $val ? ' selected="selected"' : '';
      $select .= "<option value='$tax'$selected>$tax%</option>";
    }
    if($select) return "<select name='$name' class='tax'>$select</select>";
    return "<input type='number' name='$name' class='tax' step='{$this->taxStep}' value='$val'>";
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
  * Process the Inputfield's input
  * @return $this
  */
  public function ___processInput($input) {
    $old = $this->value;
    $net = $input->get($this->name."_net");
    $tax = $input->get($this->name."_tax");
    $new = new RockPrice($net, $tax);
    if(!$old->equals($new)) {
      $this->trackChange('value');
      $this->value = $new;
    }
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
    $f->columnWidth = 34;
    $inputfields->append($f);
    
    /** @var InputfieldFloat $f */
    $f = $this->modules->get('InputfieldFloat');
    $f->name = 'taxStep';
    $f->label = $this->_('Tax Step');
    $f->value = $this->taxStep ?? 1;
    $f->notes = $this->_('Setting for HTML input tag.');
    $f->size = 2;
    $f->columnWidth = 33;
    $inputfields->append($f);

    /** @var InputfieldInteger $f */
    $f = $this->modules->get('InputfieldInteger');
    $f->name = 'precision';
    $f->label = $this->_('Precision');
    $f->value = $this->precision ?? 2;
    $f->notes = $this->_('Digits after comma. Max precision in the database is 3 digits.');
    $f->size = 2;
    $f->columnWidth = 33;
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