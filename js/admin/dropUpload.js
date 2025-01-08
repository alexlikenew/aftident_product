$(document).ready(function () {
  var element = $('.drop-container')
  var input = $('input[type="file"]')
  var btnDelete = $('.image-preview-item__delete')

  element.on('drop', function (e) { dropHandler(e) })
  element.on('dragover', function (e) { dragOverHandler(e) })
  element.on('dragleave ', function (e) { dragLeaveHandler(e) })

  input.on('change', function () {
    if ($(this).prop('files').length > 1) {
      clonePreviewItem($(this).prop('files'))
    } else {
      let [file] = $(this).prop('files')
      let photo_type = $(this).prop('id');

      let preview = $(this).parents('.drop-container').next('.image-preview')
      let previewDelete = $(this).parents('.drop-container').next('.image-preview').find('.image-preview-item__delete')
      let filename = $(this).parents('.drop-container').find('.text')

      filename.html(file.name)
      preview.attr('style', 'display: flex')
      previewDelete.attr('style', 'display: block')
      preview.find('.image-preview-img').attr('src', URL.createObjectURL(file))

    }
  })

  function clonePreviewItem(files) {
    let preview = input.parents('.drop-container').next('.image-preview')
    let filename = input.parents('.drop-container').find('.text')
    let previewItem = $('.image-preview-item').first()

    $('.image-preview-item').not(':first')
    preview.attr('style', 'display: flex')

    for (let i = 0; i < files.length; i++) {
      let file = files[i]
      if (files[i].kind === 'file') file = files[i].getAsFile();

      filename.html(file.name)

      let clone = previewItem.clone()
      let cloneDelete = clone.find('.image-preview-item__delete')

      cloneDelete.attr('data-id', i)

      clone.find('.image-preview-img').attr('src', URL.createObjectURL(file))
      clone.appendTo(preview)

      cloneDelete.on('click', function () {
        removeFileFromFileList($(this).data('id'))

        $(this).parent().remove()
      })
    }

    previewItem.hide()
  }

  function removeFileFromFileList(index) {
    const dt = new DataTransfer()
    const files = input.prop('files')

    for (let i = 0; i < files.length; i++) {
      if (index !== i) dt.items.add(files[i])
    }

    clonePreviewItem(dt.files)
  }

  btnDelete.on('click', function () {
    input.prop('files', null)
    $(this).siblings('.image-preview-img').attr('src', '')

    $(this).parent().hide()
  })

  function dropHandler(e) {
    e.preventDefault();

    if (e.originalEvent.dataTransfer.items) {
      for (var i = 0; i < e.originalEvent.dataTransfer.items.length; i++) {
        clonePreviewItem(e.originalEvent.dataTransfer.files)
      }
    } else {
      clonePreviewItem(e.originalEvent.dataTransfer.files)
    }
  }

  function dragOverHandler(e) {
    e.preventDefault();

    element.addClass('dragover')
  }

  function dragLeaveHandler(e) {
    e.preventDefault();

    element.removeClass('dragover')
  }

})