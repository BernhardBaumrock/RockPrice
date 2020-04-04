<?php namespace ProcessWire;
$nomultiline = $field->nomultiline ? 'nomultiline' : '';
$singleline = $price->items->count() > 1 ? '' : 'single-line';
$items = $price->items->count() ? $price->items : [new RockPrice()];
?>

<div class="RockPrice <?= $nomultiline ?> <?= $singleline ?>" data-digits="<?= $field->digits ?>"
  data-really="<?= __('Do you really want to delete this row?') ?>"
  data-last="<?= __('Last row can not be deleted!') ?>"
  data-namealert="<?= __('Please provide a name for your template') ?>"
  data-tplsaveerror="<?= __('Error saving template') ?>"
  data-fieldname="<?= $field->name ?>"
  data-url="<?= $pages->get(2)->url ?>"
  >
  <div class="tpl uk-margin">
    <select class="uk-form-small">
      <option><?= __('Choose Template') ?></option>
      <?= $field->renderTemplates() ?>
    </select><button class="uk-button uk-button-default uk-form-small">
      <i class="fa fa-trash-o" aria-hidden="true"></i>
    </button>

    <input class="uk-input uk-form-small uk-text-left uk-margin-left" type="text" name="tplname"
    ><button class="uk-button uk-button-default uk-form-small" name="save">
      <?= __('Save') ?>
    </button>
  </div>

  <table class="header">
    <tr>
      <td class="icon"></td>
      <td class="tax"><?= __('TAX (%)') ?></td>
      <td class="vat"><?= __('VAT') ?></td>
      <td class="net"><?= __('NET') ?></td>
      <td class="gross"><?= __('GROSS') ?></td>
      <td class="copy"></td>
      <td class="trash"></td>
    </tr>
  </table>
  <div class="rp-rows" uk-sortable="cls-custom: RockPriceDrag;">
  <?php foreach($items as $row): ?>
    <div class="rp-row">
      <table>
        <tr>
          <td class="icon">
            <i class="fa fa-arrows" aria-hidden="true"></i>
          </td>
          <td class="tax" data-type="tax"><?= $field->renderInputTax($row->tax) ?></td>
          <td class="vat" data-type="vat"><?= $field->renderInputVat($row->vat) ?></td>
          <td class="net" data-type="net"><?= $field->renderInputNet($row->net) ?></td>
          <td class="gross" data-type="gross"><?= $field->renderInputGross($row->gross) ?></td>
          <td><input type="text" name="rowdata" class="rp-data" disabled></td>
          <td class="clone" data-type="clone">
            <a href='#'><i class="fa fa-clone" aria-hidden="true"></i></a>
          </td>
          <td class="trash" data-type="trash">
            <a href='#'><i class="fa fa-trash" aria-hidden="true"></i></a>
          </td>
        </tr>
      </table>
    </div>
  <?php endforeach; ?>
  </div>
  <table class="totals">
    <tr>
      <td class="icon"></td>
      <td class="tax"></td>
      <td class="vat"><input type="number" disabled></td>
      <td class="net"><input type="number" disabled></td>
      <td class="gross"><input type="number" disabled></td>
    </tr>
  </table>
  <input type="text" name="<?= $name ?>" class="total rp-data">
</div>
<script>
// trigger calculation of inputfields (including decimals)
$(function() {
  $('#<?= $field->id ?> .RockPrice .tax input').change();
  $('#<?= $field->id ?> .RockPrice .tax select').change();
  $('#<?= $field->id ?> .RockPrice .net input').change();
  setTimeout(function() {
    $('#<?= $field->id ?>').removeClass('InputfieldStateChanged');
  }, 50);
});
</script>
