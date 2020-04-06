<?php namespace ProcessWire;
use \Tester\Assert;
use \Tester\TestCase;

/**
 * @testCase
 */
class RockPriceTest extends TestCase {

  public function testGeneral() {
    for($digits = 0; $digits <= 2; $digits++) {
      for($tax = 0; $tax <= 100; $tax+=0.5) {
        for($net = 0; $net <= 1000; $net+=0.5) {
          $p = new RockPrice($net, $tax, $digits);
          $str = "new RockPrice($net, $tax, $digits)";

          // test net
          $net = round($net, $digits);
          Assert::true($net == $p->net, "net $net == p->net {$p->net}, $str");

          // test tax
          $tax = round($tax, RockPrice::taxDigits);
          Assert::true($tax == $p->tax, "tax $tax, p->tax {$p->tax}, $str");

          // test vat
          $vat = round($net * $tax / 100, $digits);
          Assert::true($vat == $p->vat, "vat $vat, p->vat {$p->vat}, $str");
          
          // test gross
          $gross = round($net + $vat, $digits);
          Assert::true($gross == $p->gross, "gross $gross, p->gross {$p->gross}, $str");
        }
      }
    }
  }
}

(new RockPriceTest())->run();
