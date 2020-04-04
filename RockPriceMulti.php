<?php namespace ProcessWire;
class RockPriceMulti extends WireData {
  public $items;

  public $vat = 0;
  public $net = 0;
  public $gross = 0;

  public function __construct($data = []) {
    $this->items = new WireArray();
    $this->import($data);
  }

  /**
   * Import data
   */
  public function import($data) {
    if(is_string($data)) $data = (array)json_decode($data);
    if(!is_array($data)) throw new WireException("Invalid data");
    foreach($data as $row) {
      $price = new RockPrice($row[0], $row[1]);
      $this->items->add($price);
    }
    $this->setTotals();
  }

  /**
   * Set totals
   */
  public function setTotals() {
    $this->vat = 0;
    $this->net = 0;
    $this->gross = 0;
    foreach($this->items as $item) {
      $this->vat += $item->vat;
      $this->net += $item->net;
      $this->gross += $item->gross;
    }
  }

  /**
   * Check if this price is equal to another
   * @return bool
   */
  public function equals($item) {
    if(!$item instanceof RockPriceMulti) {
      throw new WireException("Argument must be of type RockPriceMulti");
    }
  }


  public function __debugInfo() {
    return [
      'items' => $this->items,
      'vat' => $this->vat,
      'net' => $this->net,
      'gross' => $this->gross,
    ];
  }
}
