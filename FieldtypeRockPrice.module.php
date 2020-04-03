<?php namespace ProcessWire;
/**
 * Price Fieldtype + Inputfield for ProcessWire CMS
 *
 * @author Bernhard Baumrock, 01.04.2020
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
require_once('RockPrice.php');
class FieldtypeRockPrice extends Fieldtype {

  public static function getModuleInfo() {
    return [
      'title' => 'RockPrice',
      'version' => '0.0.1',
      'summary' => 'Price Fieldtype + Inputfield for ProcessWire CMS',
      'icon' => 'money',
      'requires' => [],
      'installs' => ['InputfieldRockPrice'],
    ];
  }

  public function init() {
    parent::init();
  }

  /** FIELDTYPE METHODS */

    public function sanitizeValue(Page $page, Field $field, $value) {
      return $value;
    }

    public function getBlankValue(Page $page, Field $field) {
      return new RockPrice(0, $field->defaultTax);
    }

    public function sleepValue($page, $field, $value) {
      if(is_array($value) AND count($value) === 2) {
        // value was provided as array, eg setAndSave('price', [1000, 20]);
        $value = new RockPrice($value[0], $value[1]);
      }
      if(!$value instanceof RockPrice) throw new WireException("Invalid value");
      
      return [
        'data' => $value->net,
        'gross' => $value->gross,
        'tax' => $value->tax,
        'vat' => $value->vat,
      ];
    }
    
    public function wakeupValue($page, $field, $value) {
      $price = new RockPrice($value['data'], $value['tax']);
      // round values to given digits for usage in inputfield
      $price->tax = round($price->tax, $field->digits);
      $price->vat = round($price->vat, $field->digits);
      $price->net = round($price->net, $field->digits);
      $price->gross = round($price->gross, $field->digits);
      return $price;
    }

    public function getDatabaseSchema(Field $field) {
      $schema = parent::getDatabaseSchema($field);
      $schema['data'] = 'DECIMAL(12,3) NOT NULL'; // net value
      $schema['gross'] = 'DECIMAL(12,3) NOT NULL';
      $schema['tax'] = 'DECIMAL(12,3) NOT NULL';
      $schema['vat'] = 'DECIMAL(12,3) NOT NULL';
      return $schema;
    }
    
    public function getInputfield(Page $page, Field $field) {
      $inputfield = $this->modules->get('InputfieldRockPrice'); 
      $inputfield->class = $this->className();
      $inputfield->defaultTax = $field->defaultTax;
      $inputfield->taxStep = $field->taxStep;
      $inputfield->taxSelect = $field->taxSelect;
      $inputfield->digits = $field->digits;
      $inputfield->nomultiline = $field->nomultiline;
      return $inputfield; 
    }
    
    public function ___getCompatibleFieldtypes(Field $field) {
      return false;
    }

  /** HELPER METHODS */
}