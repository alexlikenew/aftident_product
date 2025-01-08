$(document).ready(function () {
  if ($('#promo_from').length > 0) {
    var picker = new Lightpick({
      field: document.getElementById('promo_from'),
      secondField: document.getElementById('promo_to'),
      singleDate: false,
      format: 'DD-MM-YYYY',
      onSelect: function(start, end){
        var str = '';
        str += start ? start.format('Do MMMM YYYY') + ' to ' : '';
        str += end ? end.format('Do MMMM YYYY') : '...';
        console.log(str)
      }
    });
  }

  if ($('#new_to').length > 0) {
    var pickerNew = new Lightpick({
      field: document.getElementById('new_to'),
      singleDate: true,
      format: 'DD-MM-YYYY',
      onSelect: function(start, end){
        console.log(start)
      }
    });
  }

  // PRODUCT PRICE
  var priceNettoInput = $('#price_netto')
  var priceBruttoInput = $('#price_brutto')
  var promoNettoInput = $('#price_promo_netto')
  var promoBruttoInput = $('#price_promo_brutto')
  var vatInput = $('#vat')
  var vat, vatPromo, priceNetto, priceBrutto, promoBrutto

  priceNettoInput.on('keyup', function () {
    vat = Number($(this).val()) * (Number(vatInput.val())/100)
    priceBrutto = (Number($(this).val()) + vat).toFixed(2)

    priceBruttoInput.val(priceBrutto)
  })

  priceBruttoInput.on('keyup', function () {
    vat = (Number(vatInput.val())/100) + 1
    priceNetto = (Number($(this).val() / vat)).toFixed(2)

    priceNettoInput.val(priceNetto)
  })

  vatInput.on('change', function () {
    vat = Number(priceNettoInput.val()) * (Number($(this).val())/100)
    vatPromo = Number(promoNettoInput.val()) * (Number($(this).val())/100)
    priceBrutto = (Number(priceNettoInput.val()) + vat).toFixed(2)
    promoBrutto = (Number(promoNettoInput.val()) + vatPromo).toFixed(2)

    priceBruttoInput.val(priceBrutto)
    promoBruttoInput.val(promoBrutto)
  })

  promoNettoInput.on('keyup', function () {
    vat = Number($(this).val()) * (Number(vatInput.val())/100)
    priceBrutto = (Number($(this).val()) + vat).toFixed(2)

    promoBruttoInput.val(priceBrutto)
  })

  promoBruttoInput.on('keyup', function () {
    vat = (Number(vatInput.val())/100) + 1
    priceNetto = (Number($(this).val() / vat)).toFixed(2)

    promoNettoInput.val(priceNetto)
  })

  $('.products-list-select').find('select').on('change', function () {
    if ($(this).val() !== '') {
      window.location.href = '/admin/produkty.php?cat=' + $(this).val()
    } else {
      window.location.href = '/admin/produkty.php'
    }
  })

  $('.spec-add').on('click', function () {
    var cloned = $(this).parent("td").parent("tr").clone(true)
    var valId = cloned.find('.spec_val')[0].id.split('_')[3];

    var newVal = Number(valId) + 1;

    cloned.find('.spec_name').attr("id", "spec_new_id_" + newVal);
    cloned.find('.spec_name').attr("name", "spec_new_id_" + newVal);

    cloned.find('.spec_val').attr("id", "spec_new_value_" + newVal);
    cloned.find('.spec_val').attr("name", "spec_new_value_" + newVal);

    cloned.find('.spec_margin').attr("id", "spec_new_margin_" + newVal);
    cloned.find('.spec_margin').attr("name", "spec_new_margin_" + newVal);

    $(this).parent("td").parent("tr").after(cloned);

    $(this).remove();
  })

  $('.spec-delete').on('click', function () {
    $(this).parent("td").parent("tr").remove()
  })

  $('.edit-color').on('click', function () {
    let id = $(this).data('id')
    let url = $(this).data('url')


    $.ajax({
      type: 'post',
      cache: false,
      url: url ? url : BASE_URL + '/admin/module.php?moduleName=produkty',
      data: {
        action: "edit_color",
        id: id
      },
      success: function (data) {
        $('.modal-color').addClass('open')
        $('.modal-color').find('.modal-dynamic').html(data)
      }
    })
  })

})