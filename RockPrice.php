<?php namespace ProcessWire;
class RockPrice extends WireData {
  public $tax;
  public $vat;
  public $net;
  public $gross;

  private $factor;
  private $digits;

  public function __construct($net = 0, $tax = 0, $digits = null) {
    $this->setDigits($digits);
    $this->setNet($net);
    $this->setTax($tax);
    $this->setGross();
  }

  public function round($val) {
    return round($val*1, $this->digits);
  }

  public function setDigits($digits) {
    if($digits === null) $digits = InputfieldRockPrice::defaultDigits;
    $this->digits = $digits;
  }

  public function setTax($val) {
    $val = (float)$val;
    $this->tax = $this->round($val);
    $this->factor = 1+($val/100);
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
    if($val !== null) $this->gross = $this->round($val);
    else $this->gross = $this->round($this->net * $this->factor);
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
    ];
  }
}
