<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
    <head>
        <title>Panel administracyjny | {$pageTitle|default:$CONF.page_title|default:''}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="keywords" content="{$pageKeywords|default:''}" />
        <meta name="description" content="{$pageDescription|default:''}" />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

        <link href="{$TPL_URL}/css/main.css?{$smarty.now|date_format:'%Y%m%d%H%M'}" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" type="text/css" href="{$TPL_URL}/css/vendor/lightpick.css">
        <link rel="stylesheet" type="text/css" href="{$TPL_URL}/css/vendor/cropper.min.css">
        <link rel="stylesheet" type="text/css" href="{$TPL_URL}/css/vendor/jquery.dropdown.min.css">
        <link rel="stylesheet" type="text/css" href="{$TPL_URL}/css/jquery.googlePreviewSnippet.css">
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <link href="{$BASE_URL}/js/jquery-ui/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="{$BASE_URL}/js/jcrop/jquery.Jcrop.css" rel="stylesheet" type="text/css" />
        <link href="{$BASE_URL}/js/uploadify/uploadify.css" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="{$TPL_URL}/img/favicon.ico" />

        <!--[if lte IE 6]>
                <link href="{$TPL_URL}/css/ie.css" rel="stylesheet" type="text/css" />
        <![endif]-->

        <script type="text/javascript">
            // <![CDATA[
            var BASE_URL = "{$BASE_URL}";
            var TPL_URL = "{$TPL_URL}";
            // ]]>
        </script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
        <script type="text/javascript" src="{$BASE_URL}/js/admin/vendor/lightpick.js"></script>



        <script type="text/javascript" src="{$BASE_URL}/js/vendor/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="{$BASE_URL}/js/admin/googlePreviewSnippet.js"></script>
        <script type="text/javascript" src="{$BASE_URL}/js/jcrop/jquery.Jcrop.min.js"></script>
        <script type="text/javascript" src="{$BASE_URL}/js/admin/tinymce.init.js"></script>

        <script type="text/javascript" src="{$BASE_URL}/js/admin/admin.js?{$smarty.now|date_format:'%Y%m%d%H%M'}"></script>
        <script type="text/javascript" src="{$BASE_URL}/js/admin/navigation.js?{$smarty.now|date_format:'%Y%m%d%H%M'}"></script>
        <script type="text/javascript" src="{$BASE_URL}/js/admin/vendor/tinymce/tinymce.min.js?{$smarty.now|date_format:'%Y%m%d%H%M'}"></script>
        <script type="text/javascript" src="{$BASE_URL}/js/admin/vendor/cropper.min.js?{$smarty.now|date_format:'%Y%m%d%H%M'}"></script>
        <script type="text/javascript" src="{$BASE_URL}/js/admin/vendor/sortable.min.js"></script>
        <script type="text/javascript" src="{$BASE_URL}/js/admin/vendor/jquery.dropdown.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr/dist/l10n/pl.js"></script>
    </head>
    <body id="app">
        {include file="navigation/nav-sidebar.html"}
        {if $error}
            <div class="error">{$error}</div>
        {/if}
        <div class="main">
            {include file="navigation/nav-top.html"}
            {if isset($body)}
            {include file=$body}
            {else}
                <h1>Dashboard</h1>
            {/if}
            <div id="module-response"></div>
        </div>
        <input type="hidden" name="csrf_token" id="csrf_token" value="{$csrf_token}">
        <script type="text/javascript" src="{$BASE_URL}/js/admin/images.js?{$smarty.now|date_format:'%Y%m%d%H%M'}"></script>
        <script type="text/javascript" src="{$BASE_URL}/js/admin/dropUpload.js?{$smarty.now|date_format:'%Y%m%d%H%M'}"></script>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    </body>
</html>