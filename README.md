# RockPrice

Price Fieldtype + Inputfield for ProcessWire CMS

![img](https://i.imgur.com/ARpD5u7.gif)

## Settings

![img](https://i.imgur.com/VrSvVwt.png)

## Usage

![img](https://i.imgur.com/yskmEUn.png)

Where `tax` stands for the tax rate in percent and `vat` for the actual tax value (eg Euros or Dollars).

## API

```php
$p1 = new RockPrice(1000, 20);
$p2 = new RockPrice(1000, 0);
$p1->equals($p2); // false
```
