$(function() {
  var $table;

  var round = function(val) {
    return val.toFixed($table.data('precision'));
  }
  
  var tax = function() {
    return 1+($table.find('.tax').val() || 0)/100;
  }

  var setNet = function(val) {
    $table.find('.gross').val(round(val*tax()));
    $table.find('.vat').text(round(val*(tax()-1)));
  }

  var setGross = function(val) {
    $table.find('.net').val(round(val/tax()));
    $table.find('.vat').text(round($table.find('.net').val()*(tax()-1)));
  }

  var setTax = function(val) {
    // trigger both to update all fields (net + gross) to have same decimals
    $table.find('.net').change();
    $table.find('.gross').change();
  }

  $(document).on('keyup change', '.RockPrice input, .RockPrice select', function(e) {
    var $field = $(this);
    $table = $field.closest('table');
    var val = $field.val();
    if($field.hasClass('net')) setNet(val);
    if($field.hasClass('gross')) setGross(val);
    if($field.hasClass('tax')) setTax(val);
  });
});
