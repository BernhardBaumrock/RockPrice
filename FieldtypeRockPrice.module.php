<?php namespace ProcessWire;
/**
 * Price Fieldtype + Inputfield for ProcessWire CMS
 *
 * @author Bernhard Baumrock, 01.04.2020
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
require_once('RockPrice.php');
require_once('RockPriceMulti.php');
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
      return new RockPriceMulti();
    }

    /**
     * Save value to database
     */
    public function sleepValue($page, $field, $value) {
      $value = new RockPriceMulti($value);
      return [
        'data' => $value->net,
        'vat' => $value->vat,
        'gross' => $value->gross,
        'items' => $value->getJsonString(),
      ];
    }
    
    public function wakeupValue($page, $field, $value) {
      $price = new RockPriceMulti($value['items']);
      return $price;
    }

    public function getDatabaseSchema(Field $field) {
      $schema = parent::getDatabaseSchema($field);
      $schema['data'] = 'DECIMAL(12,3) NOT NULL'; // NET value
      $schema['vat'] = 'DECIMAL(12,3) NOT NULL';
      $schema['gross'] = 'DECIMAL(12,3) NOT NULL';
      $schema['items'] = 'TEXT NOT NULL';
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