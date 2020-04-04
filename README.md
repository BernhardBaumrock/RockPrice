# RockPrice

Price Fieldtype + Inputfield for ProcessWire CMS

![img](https://i.imgur.com/l38LSuV.gif)

## Settings

![img](https://i.imgur.com/anXgl01.png)

## Usage

The field always returns a `RockPriceMulti` object. This object contains an array of items and the totals vor `vat`, `net` and `gross` (where `tax` stands for the tax rate in percent and `vat` for the actual tax value, eg. Euros or Dollars):

```php
d($page->price);
```
![img](https://i.imgur.com/NmQ8Gl5.png)

```php
d($page->price->items->first());
```
![img](https://i.imgur.com/6hSQumY.png)

## API

Saving field value:

```php
$page->setAndSave('price', [1000, 20]);
```
![img](https://i.imgur.com/KBoAEx6.png)

```php
$page->setAndSave('price', [
  [1000, 20],
  [3000, 10],
]);
```
![img](https://i.imgur.com/VmUlUMn.png)


## Comparisons

```php
$p1 = new RockPrice(1000, 20);
$p2 = new RockPrice(1000, 10);
d($p1->equals($p2)); // false

$m1 = new RockPriceMulti([$p1, $p2]);
$m2 = new RockPriceMulti([$p1, $p2]);
$m3 = new RockPriceMulti([$p2, $p1]); // flipped!
d($m1->equals($m2)); // true
d($m1->equals($m3)); // false
d($m1->equals($m3, true)); // true (ignoring sort order)
```

## User templates

You can save custom user templates to easily restore complicated recurring pricings:

![img](https://i.imgur.com/N5J0hqc.gif)
