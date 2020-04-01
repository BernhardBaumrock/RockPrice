<?php namespace ProcessWire;
class RockPrice extends WireData {
  public $tax;
  public $vat;
  public $net;
  public $gross;

  private $factor;

  public function __construct($net = 0, $tax = 0) {
    $this->setNet($net);
    $this->setTax($tax);
    $this->setGross();
  }

  public function setTax($val) {
    $val = (float)$val;
    $this->tax = $val;
    $this->factor = 1+($val/100);
    $this->setVat();
    $this->setGross();
  }

  public function setVat() {
    $this->vat = $this->net * $this->tax/100;
  }

  public function setNet($val) {
    $this->net = (float)$val;
  }

  public function setGross($val = null) {
    if($val !== null) $this->gross = $val;
    else $this->gross = $this->net * $this->factor;
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
