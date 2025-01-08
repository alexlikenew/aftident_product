

$(function(){
   
    if ($('.admin_dropzone').length){
        Dropzone.autoDiscover = false;
        $('.admin_dropzone').each(function(){
            var _dropzone = $(this);
            var folder = _dropzone.data('folder');
            var files_name = _dropzone.data('files_name');
            var redirect = _dropzone.data('redirect');
            if(!files_name)
            {
                files_name = "pliki";
            }
            

            var maxFiles = 1;
            var uploadMultiple = false;
            if(_dropzone.hasClass("multi"))
            {
                maxFiles = 99;
                uploadMultiple = true;

            }
            _dropzone.dropzone({
                url: BASE_URL+'/js/uploadify/uploadify_admin.php?folder='+folder,
                paramName: "file",
                acceptedFiles: "image/*",
                maxFilesize: 5,
                createImageThumbnails: false,
                dictDefaultMessage: '<a href="javascript:void(0)" class="przycisk">Wybierz pliki</a>',
                maxFiles: maxFiles,
                parallelUploads: 1,
                uploadMultiple: uploadMultiple,
                autoProcessQueue: true,
                addRemoveLinks: false,
                headers: {"MyAppname-Service-Type": "Dropzone"},
                success: function(resp){
                    var slug = txtslug(resp.name);
                    _dropzone.next(".fileslist").append("<input type='hidden' class='input_pliki "+slug+"' name='"+files_name+"[]' value='"+resp.name+"'/>");
                },
                addRemoveLinks: true,
                dictRemoveFile: "",
                removedfile: function (file) {
                    var name = file.name;
                    var slug = txtslug(name);

                    _dropzone.next(".fileslist").find("input."+slug).remove();
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                },
                error: function(file, errorMessage){
                    Dropzone.forElement(_dropzone.get(0)).removeFile(file);
                    alert(errorMessage);
                },
                queuecomplete: function()
                {
                    if(redirect)
                    {
                        window.location.replace(redirect);
                    }
                },
                processing: function()
                {

                }
            });
            
        });
    }
    
});


/*----------------*/
function txtslug(text)
{
    var slug = text.replace(".", "_");
    slug = slug.replace("(", "_");
    slug = slug.replace(")", "_");
    slug = slug.replace(" ", "_");
    
    return slug;
}

function nl2br(str) {
    return str.replace(/\n/g, "<br />");
}

function strpos(haystack, needle, offset) {
    var i = (haystack + '').indexOf(needle, (offset ? offset : 0));
    return i === -1 ? false : i;
}

function coma2dot(wart) {
    if (strpos(wart, ",") !== false) {
        wart = wart.replace(/\,/g, ".");
    }
    return wart;
}

function dot2coma(wart) {
    if (strpos(wart, ".") !== false) {
        wart = wart.replace(/\./g, ",");
    }
    return wart;
}

function getCookieVal(offset) {
    var endstr = document.cookie.indexOf(";", offset);
    if (endstr == -1)
        endstr = document.cookie.length;
    return unescape(document.cookie.substring(offset, endstr));
}

function GetCookie(name) {
    var arg = name + "=";
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;
    while (i < clen) {
        var j = i + alen;
        if (document.cookie.substring(i, j) == arg)
            return getCookieVal(j);
        i = document.cookie.indexOf(" ", i) + 1;
        if (i == 0)
            break;
    }
    return null;
}

function SetCookie(name, value, days) {
    var expires = null;

    if (days) {
        expires = new Date();
        var theDay = expires.getDay();
        theDay = theDay + days;
        expires.setDate(theDay);
    }

    var argv = SetCookie.arguments;
    var argc = SetCookie.arguments.length;
    var path = (argc > 3) ? argv[3] : null;
    path = '/';
    //var domain = (argc > 4) ? argv[4] : null;
    var secure = (argc > 5) ? argv[5] : false;
    var expires_string = ((expires === null) ? "" : ("; expires=" + expires.toGMTString()));
    var path_string = ((path === null) ? "" : ("; path=" + path));
    //var domain_string = ((domain == null) ? "; domain=" + COOKIE_DOMAIN : ("; domain=" + domain))
    var secure_string = ((secure === true) ? "; secure" : "");
    document.cookie = name + "=" + escape(value) +
            expires_string +
            path_string +
            //domain_string +
            secure_string;
}

function in_array(needle, haystack, argStrict) {
    // Checks if the given value exists in the array
    //
    // version: 1006.1915
    // discuss at: http://phpjs.org/functions/in_array    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: vlado houba
    // +   input by: Billy
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);    // *     returns 1: true
    // *     example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
    // *     returns 2: false
    // *     example 3: in_array(1, ['1', '2', '3']);
    // *     returns 3: true    // *     example 3: in_array(1, ['1', '2', '3'], false);
    // *     returns 3: true
    // *     example 4: in_array(1, ['1', '2', '3'], true);
    // *     returns 4: false
    var key = '', strict = !!argStrict;
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }
    return false;
}

function confirmDelete(message) {

    message = message || '';
    message = (message == '') ? 'Czy jesteś pewien, że chcesz skasować wybrany element?' : message;
    return confirmSubmit(message);
}

function confirmSubmit(message) {
    message = (message == '') ? 'Wprowadzone zmiany będą nieodwracalne. Czy na pewno wykonać?' : message;
    var agree = confirm(message);
    if (agree) {
        return true;
    } else {
        return false;
    }
}

// funkcja zaznacza wszystkie checkboxy w formularzu - zapozyczone z phpmyadmina
function setCheckboxes(the_form, element_name, do_check) {
    var elts = document.forms[the_form].elements[element_name + '[]'];
    var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;

    if (elts_cnt) {
        for (var i = 0; i < elts_cnt; i++) {
            elts[i].checked = do_check;
        } // end for
    } else {
        elts.checked = do_check;
    }
    return false;
}

function showHide(item) {
    var o = document.all ? document.all[item] : document.getElementById(item);
    var tag_name = o.tagName;
    var type = '';
    switch (tag_name) {
        case 'tr':
        case 'TR':
            type = 'table-row';
            break;
        case 'td':
        case 'TD':
        case 'th':
        case 'TH':
            type = 'table-cell';
            break;
        default:
            type = 'block';
            break;
    }
    o.style.display = (o.style.display == type) ? 'none' : type;
    return false;
}

function hide(item) {
    var o = document.all ? document.all[item] : document.getElementById(item);
    o.style.display = 'none';
}

function show(item) {
    var o = document.all ? document.all[item] : document.getElementById(item);
    var tag_name = o.tagName;
    var type = '';
    switch (tag_name) {
        case 'tr':
        case 'TR':
            type = 'table-row';
            break;
        case 'td':
        case 'TD':
        case 'th':
        case 'TH':
            type = 'table-cell';
            break;
        default:
            type = 'block';
            break;
    }
    o.style.display = type;
}

function showOpcje(opcje, opcje1, opcje2) {
    opcje = typeof opcje !== 'undefined' ? opcje : 'opcje';
    opcje1 = typeof opcje2 !== 'undefined' ? opcje1 : 'opcje1';
    opcje2 = typeof opcje2 !== 'undefined' ? opcje2 : 'opcje2';
    show(opcje);
    hide(opcje1);
    show(opcje2);
    return false;
}

function hideOpcje(opcje, opcje1, opcje2) {
    opcje = typeof opcje !== 'undefined' ? opcje : 'opcje';
    opcje1 = typeof opcje2 !== 'undefined' ? opcje1 : 'opcje1';
    opcje2 = typeof opcje2 !== 'undefined' ? opcje2 : 'opcje2';
    hide(opcje);
    hide(opcje2);
    show(opcje1);
    return false;
}

function showButtons() {
    $('.main-buttons').stop(true, true).show();
    return true;
}

function hideButtons() {
    $('.main-buttons').stop(true, true).hide();
    return true;
}

function setOption(type, item, oValue) {
    var l = document.all ? document.all[item] : document.getElementById(item);
    var o = (type == 'page') ? l.url_page : l.url_module;
    for (var i = 0; i < o.options.length; i++) {
        if (o.options[i].value == oValue) {
            o.options[i].selected = true;
        } else {
            o.options[i].selected = false;
        }
    }
}

function checkForm(item) {
    var f = document.all ? document.all[item] : document.getElementById(item);
    if (!f.url.value) {
        f.url.value = f.url_addr.value;
    }
}
