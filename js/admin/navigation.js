$(document).ready(function () {
  $('.nav-item-head').on('click', function () {
    $(this).siblings('.nav-item-submenu').slideToggle(300)
  })

// TABS
  $('.tabs-nav-item').on('click', function () {
    $('.tab, .content-panel').removeClass('active')
    $('.tabs-nav-item').removeClass('active')
    $(this).addClass('active')
    $('[data-tab="'+$(this).attr('data-tab-label')+'"]').addClass('active')

    if ($(this).hasClass('tabs-buttons-hide')) {
      $('.btn-row-bottom').hide()
    } else {
      $('.btn-row-bottom').show()
    }
  })

  $('.tabs-nav-item').each(function(){
    if($(this).hasClass('active') && $(this).hasClass('tabs-buttons-hide')){
      $('.btn-row-bottom').hide()
    }
  });
})
