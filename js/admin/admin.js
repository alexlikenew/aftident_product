$(document).ready(function () {
  initTinyMce('.text-editor')

  $('.select-multiple').parent().dropdown({
    multipleMode:'label',
    input: '<input type="text" placeholder="Wybierz">',
  });

    // LIST
  $('[name="mark_all"]').on('change', function () {

    $(this).parents('.list-table').find('.list-table-row').each(function () {
      if ($('[name="mark_all"]').prop('checked')) {
        $(this).find('.list-check-item').children('input[type="checkbox"]').prop('checked', true).change()
      } else {
        $(this).find('.list-check-item').children('input[type="checkbox"]').prop('checked', false).change()
      }
    })
  })

  $('.list-check-item').on('change', function () {
    let length = $('.list-check-item input:checked').length
    if (length > 0) {
      $(this).parents('.list').find('.list-top-panel-count').addClass('show')
      $(this).parents('.list').find('.checked-count').html(length)
    } else {
      $(this).parents('.list').find('.list-top-panel-count').removeClass('show')

      $('[name="mark_all"]').prop('checked', false)
    }
  })

  let sortableList = document.getElementById('sortableList')

  if (sortableList) {
    Sortable.create(sortableList, {
      animation: 150,
      onEnd: function (event) {
        let item = event.item;
        let id = item.dataset.id;
        let url = item.parentNode.dataset.url;
        let action = item.parentNode.dataset.action;

        $.ajax({
          type: "POST",
          url: url,
          data: {
            action: action,
            id: id,
            order: event.newIndex + 1
          },
          success: function(res){ },
          error: function(err){ console.log(err) }
        });
      }
    });
  }

  let sortableList2 = document.getElementById('sortableList2')

  if (sortableList2) {
    Sortable.create(sortableList2, {
      animation: 150,
      onEnd: function (event) {
        let item = event.item;
        let id = item.dataset.id;
        let url = item.parentNode.dataset.url;
        let action = item.parentNode.dataset.action;

        $.ajax({
          type: "POST",
          url: url,
          data: {
            action: action,
            id: id,
            order: event.newIndex + 1
          },
          success: function(res){ },
          error: function(err){ console.log(err) }
        });
      }
    });
  }

  $('#question_type').on('change', function () {
    $('.answer-text').toggle()
  })

  if ($('#date_add').length > 0) {
    $("#date_add").flatpickr({
      enableTime: true,
      dateFormat: "Y-m-d H:i:s",
      locale: 'pl'
    });
  }
/*
  $('#type_id').change(function(){
    let type_id = $(this).val();
    let id = $(this).data('id')

  });

*/

  // FORM
  // var form = $('.form')
  //
  // form.on('submit', function (e) {
  //   tinymce.triggerSave();
  //
  //   var data = form.serialize()
  //   var url = form.attr('action')
  //
  //   $.ajax({
  //     type: "POST",
  //     url: url,
  //     data: data,
  //     success: function(res){
  //       console.log('success')
  //     },
  //     error: function(err){
  //       alert("Posting failed.");
  //     }
  //   });
  // })

// SWITCH ACTIVE
  $('.switch-active').on('change', function () {
    let id = $(this).data('id')
    let value_id = $(this).data('valueid')
    let url

    if ($(this).prop('checked')) {
      url = $(this).data('url') + '&action=set_active&id=' + id
    } else {
      url = $(this).data('url') + '&action=set_inactive&id=' + id
    }

    if(typeof value_id != 'undefined')
      url += '&value_id=' + value_id;

    $.ajax({
      type: "GET",
      url: url,
      success: function(res){ },
      error: function(err){ console.log(err) }
    });
  })

  // SWITCH COLOR ACTIVE
  $('.switch-color-active').on('change', function () {
    let id = $(this).data('id')
    let value_id = $(this).data('valueid')
    let url

    if ($(this).prop('checked')) {
      url = $(this).data('url') + '&action=set_color_active&id=' + id
    } else {
      url = $(this).data('url') + '&action=set_color_inactive&id=' + id
    }

    if(typeof value_id != 'undefined')
      url += '&value_id=' + value_id;

    $.ajax({
      type: "GET",
      url: url,
      success: function(res){ },
      error: function(err){ console.log(err) }
    });
  })

  // SWITCH Block ACTIVE
  $('.switch-block-active').on('change', function () {
    let id = $(this).data('id')
    let value_id = $(this).data('valueid')
    let url

    if ($(this).prop('checked')) {
      url = $(this).data('url') + '&action=set_block_active&id=' + id
    } else {
      url = $(this).data('url') + '&action=set_block_inactive&id=' + id
    }

    if(typeof value_id != 'undefined')
      url += '&value_id=' + value_id;

    $.ajax({
      type: "GET",
      url: url,
      success: function(res){ },
      error: function(err){ console.log(err) }
    });
  })

  // function confirmDelete(message) {
  //   message = message || '';
  //   message = message === '' ? 'Czy jesteś pewien, że chcesz skasować wybrany element?' : message;
  //   return confirmSubmit(message);
  // }
  //
  // function confirmSubmit(message) {
  //   message = message === '' ? 'Wprowadzone zmiany będą nieodwracalne. Czy na pewno wykonać?' : message;
  //   var agree = confirm(message);
  //   return agree;
  // }

  // MODAL
  var modal = $('.modal');
  var modalBtn = $('.modal-open')

  $('.modal-close, .modal-bg').on('click', function () {
    modal.removeClass('open');
  })

  modalBtn.on('click', function () {
    var modalName = $(this).attr('data-modal-name')
    $('[data-modal="'+modalName+'"]').addClass('open');
  })

  $('#question-response').on('click', function () {
    let module = $(this).data('module')
    let id = $(this).data('id')
    let type = $(this).data('type')
    let url = $(this).data('url')

    $.ajax({
      type: 'post',
      cache: false,
      url: url ? url : BASE_URL + '/admin/' + module + '.php',
      data: {
        action: "prepare_response",
        id: id
      },
      success: function(data) {
        $('.modal-response').addClass('open')
        $('.modal-response').find('.modal-dynamic').html(data)
      }
    });
  })

  $('.edit-item').on('click', function(){

      var id = $(this).attr('data-id')
      var module = $(this).attr('data-module')
      var token = $('#csrf_token').val();

      showEdit(module, id, token);
      return false;
  })

  $('.save_item').on('click', function(){
    tinymce.triggerSave();

    var module = $(this).attr('data-module')
    var token = $('#csrf_token').val();
    var data = $('#edit_form').serialize();

    updateItem(module, token, data);
    return false;
  })

  $('.create_item').on('click', function(){
    tinymce.triggerSave();

    var module = $(this).attr('data-module')
    var token = $('#csrf_token').val();
    var data = $('#create_form').serialize();

    createItem(module, token, data);
    return false;
  })

  $('.edit-file').on('click', function () {
    let id = $(this).data('id')
    let url = $(this).data('url')
    let module = $(this).data('module')

    $.ajax({
      type: 'post',
      cache: false,
      url: url ? url : BASE_URL + '/admin/module.php',
      data: {
        action: "edit_file",
        id: id,
        moduleName: module
      },
      success: function (data) {
        $('.modal-file').addClass('open')
        $('.modal-file').find('.modal-dynamic').html(data)
      }
    })
  })

  $('select[name="language"], select[name="language_2"]').on('change', function () {
    var element = $('[data-lang]')

    element.hide()

    $('[data-lang="'+$(this).val()+'"]').show()
  })

  $('.edit-block').on('click', function () {
    let id = $(this).data('id')
    let url = $(this).data('url')
    let module = $(this).data('module')

    $.ajax({
      type: 'post',
      cache: false,
      url: url ? url : BASE_URL + '/admin/module.php?moduleName=' + module,
      data: {
        action: "edit_block",
        id: id
      },
      success: function (data) {
        $('.modal-block').addClass('open')
        $('.modal-block').find('.modal-dynamic').html(data)

        $('select[name="language"]').on('change', function () {
          var element = $('[data-lang]')

          element.hide()

          $('[data-lang="'+$(this).val()+'"]').show()
        })

        initTinyMce('.text-editor')
      }
    })
  })
  $('#type_id').change(function(){
    var is_child = $(this).find(':selected').data('parent');

    if(is_child == 1){
      $('#parent_content').removeClass('hidden');
      $('#is_child').val(1);
    }
    else{
      $('#parent_content').addClass('hidden');
      $('#is_child').val(0);
    }

  });

  function validateInputs (form) {
    form.find('.input-required').each(function () {

      if (!$(this).val() && $(this).parent().is(":visible")) {
        $(this).addClass('input-error')
        $(this).siblings('.icon-error').show()
      } else {
        $(this).removeClass('input-error')
        $(this).siblings('.icon-error').hide()
      }
    })

    if (form.find('.check-required').not(':checked').length > 0) {
      form.find('.check-required').not(':checked').next().addClass('check-error')
    }

    form.find('.check-required').on('change', function () {
      $(this).next().removeClass('check-error')
    })

    return form.find('.input-error').length <= 0 && form.find('.check-error').not(':checked').length <= 0;
  }

  $('.input-required').on('change', function () {
    const form = $(this).parents('form:first')

    if (validateInputs(form)) {
      form.find('.btn').removeClass('disabled')
    } else {
      form.find('.btn').addClass('disabled')
    }
  })
})

function showEdit(module, id, token){

  $.ajax({
    type: 'POST',
    url: BASE_URL + '/admin/module',
    data: {
      moduleName: module,
      action: 'edit',
      id: id,
      token: token,
      ajax: true
    },
    beforeSend: function () {},
    success: function (data) {
      console.log(data)
      $('#module-response').html(data);
    },
  });
}

function updateItem(module, token, data){

  $.ajax({
    type: 'POST',
    url: BASE_URL + '/admin/module',
    data: {
      moduleName: module,
      action: 'Zapisz i kontynuuj edycję',
      formData: data,
      token: token,
      //cache : false,
      //dataType    : 'json',
      //processData : false,
      ajax: true
    },
    beforeSend: function () {},
    success: function (res) {

      var data = JSON.parse(res);
      console.log(data);
      // $('#module-response').show(0).delay(5000).hide(0);
      // $('#module-response').html(data.message);
      toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
      }

      if (data.status === 'success') {
        toastr.success(data.message)
      } else {
        toastr.error(data.message)
      }

    },
  });
  return false;
}

function createItem(module, token, data){

  $.ajax({
    type: 'POST',
    url: BASE_URL + '/admin/module',
    data: {
      moduleName: module,
      action: 'create',
      formData: data,
      token: token,
      //cache : false,
      //dataType    : 'json',
      //processData : false,
      ajax: true
    },
    beforeSend: function () {},
    success: function (res) {

      var data = JSON.parse(res);
      console.log(data);
      // $('#module-response').show(0).delay(5000).hide(0);
      // $('#module-response').html(data.message);
      toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
      }

      if (data.status === 'success') {
        toastr.success(data.message);

        setInterval(window.location.replace(BASE_URL + '/admin/module?moduleName=' + module), 4000);
      } else {
        toastr.error(data.message)
      }

    },
  });
  return false;
}

function confirmMassDelete(){
  var modalDelete = $('#modal-delete')
  modalDelete.addClass('open')
  modalDelete.find('.btn-row-text').attr('style', 'opacity: 1')
  modalDelete.find('.btn-red').addClass('disabled')
  $('#element-name').html(name)

  var secondsLabel = document.getElementById("deleteTimeout");
  var totalSeconds = 3;
  var timer = setInterval(setTime, 1000);

  function setTime() {
    if (totalSeconds > 0) {
      --totalSeconds;
      secondsLabel.innerHTML = totalSeconds.toString();
    } else {
      modalDelete.find('.btn-row-text').attr('style', 'opacity: 0')
      modalDelete.find('.btn-red').removeClass('disabled')

      clearInterval ( timer );
    }
  }

  function closeModal() {
    modalDelete.removeClass('open')
    totalSeconds = 3;
    secondsLabel.innerHTML = totalSeconds.toString();
    clearInterval ( timer );
  }

  modalDelete.find('.btn-white, .close, .modal-bg').on('click', function () {
    closeModal()
  })

  modalDelete.find('.btn-red').on('click', function () {
    $('#action').val('massDelete');
    $('#items_list').submit();
  })
}

function confirmDelete(name, url, element, photo = false) {
  var modalDelete = $('#modal-delete')
  modalDelete.addClass('open')
  modalDelete.find('.btn-row-text').attr('style', 'opacity: 1')
  modalDelete.find('.btn-red').addClass('disabled')
  $('#element-name').html(name)

  var secondsLabel = document.getElementById("deleteTimeout");
  var totalSeconds = 3;
  var timer = setInterval(setTime, 1000);

  function setTime() {
    if (totalSeconds > 0) {
      --totalSeconds;
      secondsLabel.innerHTML = totalSeconds.toString();
    } else {
      modalDelete.find('.btn-row-text').attr('style', 'opacity: 0')
      modalDelete.find('.btn-red').removeClass('disabled')

      clearInterval ( timer );
    }
  }

  function closeModal() {
    modalDelete.removeClass('open')
    totalSeconds = 3;
    secondsLabel.innerHTML = totalSeconds.toString();
    clearInterval ( timer );
  }

  modalDelete.find('.btn-white, .close, .modal-bg').on('click', function () {
    closeModal()
  })

  modalDelete.find('.btn-red').on('click', function () {
    if (photo) {
      deletePhoto(element)
      closeModal()
    } else {

      $.ajax({
        type: 'get',
        url: url,
        success: function (data) {
          window.location.reload();
        }
      })
    }

  })
}

function deletePhoto(el) {
  let module = el.data('module')
  let id = el.data('id')
  let parent = el.data('parent')
  let name = el.data('name')
  let element = el.parents('.gallery-grid-item')

  $.ajax({
    type: 'post',
    cache: false,
    url: BASE_URL + '/admin/module.php?moduleName=' + module,
    data: {
      action: "delete_photo",
      id: id,
      parent_id: parent,
      name: [name],
      new_foto: [id]
    },
    success: function(data) {
      element.remove()
    }
  });
}
$(init);

function init() {
  $("#snippet").serpSnippet({
    title: "Cupcake ipsum dolor sit amet powder pastry fruitcake chocolate",
    url: "www.example.com/example",
    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut",
    search: ""
  });
}
