<?php namespace ProcessWire;
/**
 * Price Fieldtype + Inputfield for ProcessWire CMS
 *
 * @author Bernhard Baumrock, 01.04.2020
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
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

    /**
    * Sanitize value for storage
    * 
    * @param Page $page
    * @param Field $field
    * @param string $value
    * @return string
    */
    public function sanitizeValue(Page $page, Field $field, $value) {
      return $value;
    }

    public function getDatabaseSchema(Field $field) {
      $schema = parent::getDatabaseSchema($field); 
      $schema['data'] = 'float NOT NULL';
      return $schema;
    }
    
    public function getInputfield(Page $page, Field $field) {
      $inputfield = $this->modules->get('InputfieldRockPrice'); 
      $inputfield->class = $this->className();
      $inputfield->defaultTax = $field->defaultTax;
      $inputfield->taxStep = $field->taxStep;
      $inputfield->taxSelect = $field->taxSelect;
      $inputfield->precision = $field->precision;
      return $inputfield; 
    }
    
    /**
     * No compatible Fieldtypes
     */
    public function ___getCompatibleFieldtypes(Field $field) {
      return false;
    }

  /** HELPER METHODS */
}