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

/**
 * Get a number that contains only dots as comma and does not
 * contain any other thousands separators!
 */
RockPrice.prototype.getDotNumber = function(val) {
  // make sure that we have no non-numeric chars
  val = val.replace(/[^\d\,\.]/g,'');

  var comma = val.indexOf(',');
  var dot = val.indexOf('.');

  // if we only have one or zero separators we return the value directly
  if(comma < 0 || dot < 0) return val.replace(',','.');

  // otherwise we only keep the latter separator
  if(comma < dot) return val.replace(/,/g,'');
  else return val.replace(/\./g,'').replace(',','.');
}

// GUI
$(function() {
  var $table;
  var RP = new RockPrice();
  var $RP;

  // log function for debugging
  var log = function(...data) {
    if(!ProcessWire.config.debug) return;
    console.log(...data);
  }

  var timer;
  var update = function(type, val) {
    // reset rockprice object
    var digits = $table.closest('.RockPrice').data('digits');
    RP.setDigits(digits);
    var tax = $table.find('.tax input').length
      ? $table.find('.tax input').val()
      : $table.find('.tax select').val()
      ;
    RP.setTax(tax);
    RP.setNet($table.find('.net input').val());
    if(type === 'gross') RP.setGross(val);

    // log('update!', RP);
    $table.find('input[name=rowdata]').val(RP.getJson());
    if(type !== 'tax') {
      $table.find('.tax input').val(RP.tax.toFixed(digits));
      $table.find('.tax select').val(RP.tax);
    }
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
    var digits = $RP.data('digits');
    // log($RP, rows);

    // reset all sum inputs
    $RP.find('.totals input').val(null);
    var inputTotals = {
      vat: 0,
      net: 0,
      gross: 0,
    };

    // update hidden data input
    $.each(rows, function(i, el) {
      var val = $(el).find('input[name=rowdata]').val();
      var rowdata = JSON.parse(val);
      totals.push(rowdata);

      inputTotals.vat = inputTotals.vat*1 + rowdata.vat*1;
      inputTotals.net = inputTotals.net*1 + rowdata.net*1;
      inputTotals.gross = inputTotals.gross*1 + rowdata.gross*1;
    });
    var val = JSON.stringify(totals);
    $RP.find('input.total').val(val);

    // add single line class for CSS
    if($RP.find('.rp-row').length < 2) {
      $RP.addClass('single-line');
      $RP.find('.rp-row').addClass('uk-sortable-nodrag');
    }
    else {
      $RP.find('.rp-row').removeClass('uk-sortable-nodrag');
      $RP.removeClass('single-line');
    }

    $RP.find('.totals .vat input').val(inputTotals.vat.toFixed(digits));
    $RP.find('.totals .net input').val(inputTotals.net.toFixed(digits));
    $RP.find('.totals .gross input').val(inputTotals.gross.toFixed(digits));

    // set inputfield state changed
    $RP.closest('.Inputfield').addClass('InputfieldStateChanged');
  }


  // Here we make sure that the html number input does always get a number
  // with a dot as comma! Otherwise pasting common german numbers would fail.
  // These examples all work:
  // 5.432,00
  // 1.234.567,00
  // 1,234,567.00
  $(document).on('paste', '.RockPrice .rp-rows input, .RockPrice .rp-rows select', function(e) {
    $RP = $(e.target).closest('.RockPrice');
    var data = e.originalEvent.clipboardData.getData('text/plain');
    var RP = new RockPrice();
    var number = RP.getDotNumber(data);
    setTimeout(function() {
      $(e.target).val(number);
    }, 0);
  });

  $(document).on('keyup change focusout', '.RockPrice .rp-rows input, .RockPrice .rp-rows select', function(e) {
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

  // keep selected attr in sync with select value
  // this is important for the clone feature
  $(document).on('change', '.RockPrice .rp-rows select', function(e) {
    var $select = $(e.target);
    var val = $select.val();
    $select.find('option').attr('selected', null);
    $select.find('option[value='+val+']').attr('selected', 'selected');
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
      var shiftHeld = e.shiftKey;
      if($RP.find('.rp-row').length > 1) {
        if(shiftHeld) {
          $row.remove();
          updateTotals();
        }
        else {
          ProcessWire.confirm($RP.data('really'), function() {
            $row.remove();
            updateTotals();
          });
        }
      }
      else {
        ProcessWire.alert($RP.data('last'));
      }
    }

    e.preventDefault();
  });
});

// template feature
$(function() {
  var $RP;

  // log function for debugging
  var log = function(...data) {
    if(!ProcessWire.config.debug) return;
    console.log(...data);
  }

  var saveTemplate = function(name) {
    var json = $RP.find('input.total').val();
    var url = $RP.data('url');
    var data = {
      name,
      action: 'save',
      json,
      field: $RP.data('fieldname'),
    }

    // send ajax request
    $.post(url, data, function(json) {
      // on success: update options
      UIkit.notification({
        message: json.msg,
        status: 'success',
        pos: 'top-right',
        timeout: 5000
      });
    }, 'json')
    .fail(function() {
      // on fail: show alert
      ProcessWire.alert($RP.data('tplsaveerror'));
    });
  }
  
  var deleteTemplate = function(name) {
    var url = $RP.data('url');
    var data = {
      name,
      action: 'trash',
      field: $RP.data('fieldname'),
    }

    // send ajax request
    $.post(url, data, function(json) {
      UIkit.notification({
        message: json.msg,
        status: 'success',
        pos: 'top-right',
        timeout: 5000
      });
      $RP.find('.tpl option[value='+name+']').remove();
      $RP.find('.tpl select').val();
    }, 'json')
    .fail(function() {
      ProcessWire.alert($RP.data('tpldeleteerror'));
    });
  }

  var setRows = function($RP, data) {
    var $row = $RP.find(".rp-row").first().clone();
    $RP.find(".rp-row").remove();

    var $rows = $RP.find('.rp-rows');
    $.each(data, function(i, row) {
      $r = $row.clone();
      $rows.append($r);
      $r.find('.tax input').val(row.tax);
      $r.find('.tax select').val(row.tax);
      $r.find('.gross input').val(row.gross);
      $r.find('.net input').val(row.net).change();
    });
  }

  // save template
  $(document).on('click', '.RockPrice button[name=save]', function(e) {
    e.preventDefault();
    $RP = $(e.target).closest('.RockPrice');
    var $input = $RP.find('.tpl input[name=tplname]');
    var val = $input.val().trim();
    if(!val) return ProcessWire.alert($RP.data('namealert'));
    saveTemplate(val);
  });
  
  // delete template
  $(document).on('click', '.RockPrice button[name=trash]', function(e) {
    e.preventDefault();
    $RP = $(e.target).closest('.RockPrice');
    var $select = $RP.find('.tpl select');
    var tpl = $select.val();
    if(!tpl) return ProcessWire.alert($RP.data('selecttpl'));
    ProcessWire.confirm($RP.data('confirmdeletetpl'), function() {
      deleteTemplate(tpl);
    });
  });

  // restore templates
  $(document).on('change', '.RockPrice .tpl select', function(e) {
    var $select = $(e.target);
    var tpl = $select.val();
    if(!tpl) return;

    var data = $select.find('option[value='+tpl+']').data('json');
    setRows($select.closest('.RockPrice'), data);
  });
});
