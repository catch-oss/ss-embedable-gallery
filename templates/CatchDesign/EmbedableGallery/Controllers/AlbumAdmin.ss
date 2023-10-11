<!DOCTYPE html>
<html>
    <head>
        <title>Insert an Album</title>
        <script type="text/javascript" src="/resources/vendor/silverstripe/admin/thirdparty/tinymce/tinymce.js"></script>
        <script type="text/javascript" src="/resources/vendor/silverstripe/admin/client/dist/js/vendor.js"></script>
        <script type="text/javascript" src="/resources/vendor/silverstripe/admin/thirdparty/tinymce/plugins/compat3x/plugin.js"></script>
        <script type="text/javascript" src="/resources/vendor/silverstripe/admin/thirdparty/tinymce/plugins/compat3x/tiny_mce_popup.js"></script>
        <script type="text/javascript" src="/resources/vendor/catch/ss-embedable-gallery/assets/build/js/popup.js"></script>
        <link type="text/css" rel="stylesheet" href="/resources/vendor/catch/ss-embedable-gallery/assets/build/css/main.css">
    </head>
    <body class="popup">
        <form>
            <div>
                <label for="url"></label>
                <input type="hidden" name="AlbumID" id="AlbumID">
                <input type="text" name="q" id="q" placeholder="Start typing the name of the album" autocomplete="off">
                <button>OK</button>
                <ul id="options"></ul>
                <div id="preview"></div>
            </div>
        </form>
    </body>
</html>
