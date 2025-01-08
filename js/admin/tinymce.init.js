function initTinyMce(selector) {
    tinymce.init({
        selector: selector,
        language: 'pl',
        plugins: 'advlist autolink lists link image code charmap print preview hr anchor pagebreak wordcount searchreplace table',
        toolbar: 'undo redo | formatselect | ' +
            'bold italic backcolor | image | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist | ' +
            'removeformat searchreplace | code | wordcount | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
        toolbar_mode: 'floating',
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.onchange = function () {
                var file = this.files[0];

                var reader = new FileReader();
                reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    cb(blobInfo.blobUri(), { title: file.name });
                };
                reader.readAsDataURL(file);
            };

            input.click();
        },
        height : 400,
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px; }',
        browser_spellcheck: true,
        contextmenu: false
    });
}