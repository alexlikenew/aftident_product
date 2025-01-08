$(document).ready(function () {
    $(window).click(function() {
        $('.autocomplete-container').hide()
        $('.form-search').removeClass('open')

        if (window.matchMedia('(min-width: 1199px)').matches) {
            setTimeout(function () {
                $('.nav-menu-wrapper').fadeIn()
            }, 300)
        }


    });

    $('.form-search-icon').on('click', function () {
        var thisEl = $(this)

        if (window.matchMedia('(min-width: 1199px)').matches) {
            $('.nav-menu-wrapper').fadeOut(300, function () {
                thisEl.parents('.form-search').addClass('open')
                thisEl.parents('.form-search').find('.form-search-input').focus()
            })
        } else {
            thisEl.parents('.form-search').addClass('open')
            thisEl.parents('.form-search').find('.form-search-input').focus()
        }

    })

    $('.form-search').on('click', function (e) {
        e.stopPropagation()
    })

    $(".form-search-input").on('keyup', function (event) {
        event.preventDefault();

        let keyword = $(this).val();
        let input = $(this).parent();

        if (keyword.length >= 2) {
            input.find('.autocomplete-items').html('');
            input.find('.autocomplete-container').show();

            // $('.search-form-btn').removeAttr('disabled')

            sendPostRequest('/szukaj?autocomplete=json', {
                keyword: keyword,
            }).then((response) => {
                input.find('.autocomplete-items').html('');

                response.map((product) => {

                    let productHtml = generateHtmlItem(product);

                    input.find('.autocomplete-items').append(productHtml);
                    input.find('.autocomplete-container').show();
                });
            });
        } else {
            input.find('.autocomplete-container').hide();
            $('.search-form-btn').attr('disabled', 'disabled')
        }
    });
});

function sendPostRequest(url, params) {
    return $.ajax({
        url: BASE_URL + url,
        method: "POST",
        data: params,
    });
}

function generateHtmlItem(product) {
    return `
    <a href="${product.url}" class="autocomplete-item">
        <div class="autocomplete-item-left">
            <div class="autocomplete-item__name">${product.title}</div>
            <div class="autocomplete-item__subtitle">${product.subtitle ? product.subtitle : ''}</div>
        </div>
    </a>
    `;
}

