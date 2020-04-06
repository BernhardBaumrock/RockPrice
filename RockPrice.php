<?php namespace ProcessWire;
class RockPrice extends WireData {
  public $tax;
  public $vat;
  public $net;
  public $gross;

  private $digits;

  public function __construct($net = 0, $tax = 0, $digits = null) {
    $this->setDigits($digits);
    $this->setNet($net);
    $this->setTax($tax);
  }

  public function round($val, $digits = null) {
    return round($val*1, $digits ?: $this->digits);
  }

  public function setDigits($digits) {
    if($digits === null) $digits = InputfieldRockPrice::defaultDigits;
    $this->digits = $digits;
  }

  public function setTax($val) {
    $this->tax = $tax = $this->round($val, 5);
    $this->setVat();
    $this->setGross();
  }

  public function setVat() {
    $this->vat = $this->round($this->net * $this->tax/100);
  }

  public function setNet($val) {
    $this->net = $this->round($val);
  }

  public function setGross($val = null) {
    $this->gross = $this->round($this->net + $this->vat);
  }

  /**
   * Get json string of this item
   * @return string
   */
  public function getJsonString() {
    return json_encode([
      'net' => $this->net,
      'tax' => $this->tax,
    ]);
  }

  /**
   * Check if this price is equal to another
   * @return bool
   */
  public function equals($price) {
    $equal = true;
    if($price->tax !== $this->tax) $equal = false;
    if($price->net !== $this->net) $equal = false;
    return $equal;
  }

  public function __debugInfo() {
    return [
      'tax' => $this->tax,
      'vat' => $this->vat,
      'net' => $this->net,
      'gross' => $this->gross,
      'digits' => $this->digits,
    ];
  }
}
