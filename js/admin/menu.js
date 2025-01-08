$(document).ready(function () {

  $('[name="has_submenu"]').on('change', function() {
    $('#submenu-block').slideToggle(200)

    if ($(this).not(':checked')) {
      $('#submenu-generated').slideUp(200)
    }
  });

  $('[name="submenu_type"]').on('change', function() {
    if ($('[name="submenu_type"]:checked').val() === '1') {
      $('#submenu-generated').slideDown(200)
    } else {
      $('#submenu-generated').slideUp(200)
    }
  });

  $('[name="type"]').on('change', function() {
    let target = $('[name="type"]:checked').data('target')

    $('[data-type]').hide()
    $('[data-type="'+target+'"]').show()
  });

  $('.form-select').on('change', function () {
    let lang = $(this).parent().closest('div').data('lang')
    let value = $(this).val()

    console.log(lang)
    console.log(value)

    $('[name="target_id['+lang+']"]').val(value)
  })

  if ($('[name="recommended"]').is(':checked')) {
    $('.menu-search-product').show();
  }

  $('[name="recommended"]').change(function () {
    if ($(this).is(':checked')) {
      $('.menu-search-product').slideDown(200);
    } else {
      $('.menu-search-product').slideUp(200);
    }
  });

  /*
    $("#recommendedProduct").select2( {
      placeholder: "Wybierz produkt",
      allowClear: true
    } );
  */
});
