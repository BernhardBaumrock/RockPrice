// LOGIC
RockPrice = function() {
  this.tax = 0;
  this.vat = 0;
  this.net = 0;
  this.gross = 0;

  this.digits = 2; // precision (digits after comma)
  this.c = 1; // tax coefficient
}

RockPrice.prototype.round = function(val) {
  return parseFloat(val).toFixed(this.digits)*1;
}

RockPrice.prototype.setC = function() {
  this.c = 1 + (this.tax/100);
}

RockPrice.prototype.setDigits = function(val) {
  this.digits = val;
}

RockPrice.prototype.setTax = function(val) {
  this.tax = this.round(val);
  this.setC();
  this.setVat();
  this.setGross();
}

RockPrice.prototype.setVat = function() {
  this.vat = this.round(this.net / 100 * this.tax);
}

RockPrice.prototype.setNet = function(val) {
  if(val) {
    this.net = this.round(val);
    this.setVat();
    this.setGross();
  }
  else {
    this.net = this.round(this.gross / this.c);
  }
}

RockPrice.prototype.setGross = function(val) {
  if(val) {
    this.gross = this.round(val);
    this.setNet();
    this.setVat();
  }
  else {
    this.gross = this.round(this.net * this.c);
  }
}

RockPrice.prototype.getJson = function() {
  return JSON.stringify({
    tax: this.tax,
    vat: this.vat,
    net: this.net,
    gross: this.gross,
  });
}

// GUI
$(function() {
  var $table;
  var RP = new RockPrice();
  var $RP;

  // log function for debugging
  var log = function(...data) {
    if(!ProcessWire.config.debug) return;
    return;
    console.log(...data);
  }

  var timer;
  var update = function(type, val) {
    // reset rockprice object
    var digits = $table.closest('.RockPrice').data('digits');
    RP.setDigits(digits);
    RP.setTax($table.find('.tax input').val());
    RP.setNet($table.find('.net input').val());
    if(type === 'gross') RP.setGross(val);

    log('update!', RP);
    $table.find('input[name=rowdata]').val(RP.getJson());
    if(type !== 'tax') $table.find('.tax input').val(RP.tax.toFixed(digits));
    if(type !== 'vat') $table.find('.vat input').val(RP.vat.toFixed(digits));
    if(type !== 'net') $table.find('.net input').val(RP.net.toFixed(digits));
    if(type !== 'gross') $table.find('.gross input').val(RP.gross.toFixed(digits));

    clearTimeout(timer);
    timer = setTimeout(function() {
      updateTotals();
    }, 10);
  }

  var updateTotals = function() {
    var totals = [];
    var rows = $RP.find('.rp-row');
    log($RP, rows);

    $.each(rows, function(i, el) {
      var val = $(el).find('input[name=rowdata]').val();
      totals.push(JSON.parse(val));
    });
    var val = JSON.stringify(totals);
    $RP.find('input.total').val(val);
    $RP.closest('.Inputfield').addClass('InputfieldStateChanged');
  }

  // update row data
  $(document).on('keyup change focusout', '.RockPrice input, .RockPrice select', function(e) {
    var $el = $(e.target);
    var $td = $el.closest('td');
    var val = $el.val();
    $table = $el.closest('table');
    $RP = $table.closest('.RockPrice');

    // reset type so that all inputs get redrawn on focusout
    var type = $td.data('type');
    if(e.type === 'focusout') type = null;

    update(type, val);
  });

  // monitor sortable
  $('[uk-sortable]').on('moved', updateTotals);

  // handle clicks on the clone icon
  $(document).on('click', '.RockPrice a', function(e) {
    var $el = $(e.target);
    var $row = $el.closest('.rp-row');
    var $rows = $row.parent();
    var type = $el.closest('td').data('type');
    $table = $el.closest('table');
    $RP = $table.closest('.RockPrice');
    
    if(type === 'clone') {
      $rows.append($row.clone());
      updateTotals();
    }

    if(type === 'trash') {
      if($RP.find('.rp-row').length > 1) {
        ProcessWire.confirm($RP.data('really'), function() {
          $row.remove();
          updateTotals();
        });
      }
      else {
        ProcessWire.alert($RP.data('last'));
      }
    }

    e.preventDefault();
  });
});
