<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl" class="{if !$roomPage}overflow-hidden{/if}">
<head>
    <title>{$pageTitle}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="{$pageKeywords}" />
    <meta name="description" content="{$pageDescription}" />
    {if $CONF.verify_v1}
    {$CONF.verify_v1}
    {/if}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="{$TPL_URL}/css/global.css?v={$smarty.now|date_format:'%Y%m%d%H%M'}" rel="stylesheet" type="text/css" />
    <link href="{$TPL_URL}/css/vendor/swiper-bundle.min.css?v={$smarty.now|date_format:'%Y%m%d%H%M'}" rel="stylesheet" type="text/css" />
    <!--    <link href="{$TPL_URL}/css/vendor/aos.css?v={$smarty.now|date_format:'%Y%m%d%H%M'}" rel="stylesheet" type="text/css" />-->

    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
    <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
{literal}
    <script type="text/javascript">
      var BASE_URL = "{$BASE_URL}";
      var TPL_URL = "{$TPL_URL}";
      const analyticsConsent = localStorage.getItem("cookies_analytics");
      const marketingConsent = localStorage.getItem("cookies_marketing");
      const adPersonalization = localStorage.getItem("ad_personalization");
      const adUserData = localStorage.getItem("ad_user_data");
    </script>
    <script>
        //$(document).ready(function(){
        function gtag(){dataLayer.push(arguments);}
        window.dataLayer = window.dataLayer || [];

        if (!localStorage.getItem("policyChosen")) {
            gtag("consent", "default", {
                ad_personalization: "denied",
                ad_storage: "denied",
                ad_user_data: "denied",
                analytics_storage: "denied",
                functionality_storage: "denied",
                personalization_storage: "denied",
                security_storage: "granted",
                wait_for_update: 500
            });
            gtag("set", "ads_data_redaction", true);
        }
        else{
            gtag('consent', 'default', {
                ad_personalization: "denied",
                ad_storage: marketingConsent,
                ad_user_data: marketingConsent,
                analytics_storage: analyticsConsent,
                functionality_storage: adPersonalization,
                personalization_storage: adUserData,
                security_storage: 'granted',
                wait_for_update: 500
            });
        }
        //});
        function updateConsentStatus(adConsent, analyticsConsent, functionalityConsent, personalizationConsent) {
            gtag('consent', 'update', {
                'ad_storage': adConsent,
                'analytics_storage': analyticsConsent,
                'functionality_storage': functionalityConsent,
                'personalization_storage': personalizationConsent
            });
        }
        gtag('js', new Date());
    </script>

{/literal}
    <script src="{$BASE_URL}/js/vendor/jquery-3.6.0.min.js"></script>

    <!-- Google Analytics wersja uniwersalna -->
    {$CONF.google_analytics_universal}

    <meta property="og:title" content="{$pageTitle}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://{$smarty.server.SERVER_NAME}{$smarty.server.REQUEST_URI}" />
    {if $article.photo.source.photo}
    <meta property="og:image" content="https://{$smarty.server.SERVER_NAME}{$article.photo.source.photo}" />
    {else}
    <meta property="og:image" content="https://{$smarty.server.SERVER_NAME}/favicon/android-chrome-512x512.png" />
    {/if}
    <meta property="og:description" content="{$pageDescription}" />
</head>
<body class="{if !$roomPage}overflow-hidden{/if}">

{include file="misc/navigation.html"}
{include file=$body}
{include file="misc/contact.html"}
{* {include file="misc/our-brands.html"} *}
{include file="misc/footer.html"}
{*{include file="misc/cookies.html"}*}

<script type="text/javascript" src="{$BASE_URL}/js/vendor/js.cookie.min.js"></script>
<script type="text/javascript" src="{$BASE_URL}/js/search.js?v={$smarty.now|date_format:'%Y%m%d%H%M'}"></script>
<script type="text/javascript" src="{$BASE_URL}/js/main.js?v={$smarty.now|date_format:'%Y%m%d%H%M'}"></script>
<script type="text/javascript" src="{$BASE_URL}/js/vendor/swiper-bundle.min.js"></script>
<script type="text/javascript" src="{$BASE_URL}/js/swiper.init.js?v={$smarty.now|date_format:'%Y%m%d%H%M'}"></script>
<script type="text/javascript" src="{$BASE_URL}/js/vendor/fslightbox.js"></script>
<!--<script type="text/javascript" src="{$BASE_URL}/js/vendor/aos.js"></script>-->
<script type="text/javascript" src="{$BASE_URL}/js/checkCookies.js"></script>
<script>
  // AOS.init();
</script>

{$CONF.google_analytics_classic}
{include file="misc/modals/modal-cookies.html"}
</body>
</html>
