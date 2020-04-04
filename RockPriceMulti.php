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
    // if data is already an instance of RockPriceMulti we convert it to a json
    if($data instanceof RockPriceMulti) $data = $data->getJsonString();

    // value provided as single array
    // eg RockPriceMulti([200, 20]);
    if(is_array($data) AND count($data) === 2
      AND is_numeric($data[0]) AND is_numeric($data[1])) {
      // wrap value in an array as it it was a multi value
      $data = [$data];
    }

    if(is_string($data)) $data = (array)json_decode($data);
    if(!is_array($data)) throw new WireException("Invalid data");

    // data is now an array like this
    // RockPriceMulti([
    //   [200, 20],
    //   [300, 10],
    // ]);
    foreach($data as $row) {
      if(is_object($row)) $row = [$row->net, $row->tax];
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
  public function equals($other, $ignoreSort = false) {
    if(!$other instanceof RockPriceMulti) $other = new RockPriceMulti($other);
    
    if($ignoreSort) {
      // we only compare totals
      return $this->vat === $other->vat
        AND $this->net === $other->net
        AND $this->gross === $other->gross;
    }
    else {
      foreach($this->items as $i=>$item) {
        if(!$item->equals($other->items->eq($i))) return false;
      }
      return true;
    }
  }

  /**
   * Get data string for storing in the DB
   * @return string
   */
  public function getJsonString() {
    $arr = [];
    foreach($this->items as $item) {
      $arr[] = json_decode($item->getJsonString());
    }
    return json_encode($arr);
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
