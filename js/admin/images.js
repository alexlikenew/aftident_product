$(document).ready(function () {
  $('.edit-thumb').on('click', function () {
    let module = $(this).data('module')
    let id = $(this).data('id')
    let type = $(this).data('type')
    let url = $(this).data('url')
    let key_type = $(this).data('key')

    $.ajax({
      type: 'post',
      cache: false,
      url: url ? url : BASE_URL + '/admin/module.php?moduleName' + module,
      data: {
        action: "edit_thumb",
        id: id,
        type: type,
        key_type: key_type
      },
      success: function(data) {
        $('.modal-thumb').addClass('open')
        $('.modal-thumb').find('.modal-dynamic').html(data)

        initTinyMce('.text-editor')

        console.log('.text-editor')
      }
    });
  })

  $('.gallery-grid-item__check .checkbox-item input').on('change', function () {
    if ($('.gallery-grid-item__check .checkbox-item input:checked').length > 0) {
      $('#checked-count').parents('.gallery-grid-panel').addClass('show')
      $('#checked-count').html($('.gallery-grid-item__check .checkbox-item input:checked').length)
    } else {
      $('#checked-count').parents('.gallery-grid-panel').removeClass('show')
    }
  })
/*
  $('.gallery-item-delete').on('click', function () {

    let module = $(this).data('module')
    let id = $(this).data('id')
    let parent = $(this).data('parent')
    let name = $(this).data('name')
    let element = $(this).parents('.gallery-grid-item')

    url = BASE_URL + '/admin/module.php?moduleName=' + module + '&action=delete_photo&id='+id;
    confirmDelete(name, url);
    return false;

    if (confirmDelete(name, )) {
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
          return false;
          element.remove()
        }
      });
    }


  })
  */

})
