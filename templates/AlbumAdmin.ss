<!DOCTYPE html>
<html>
    <head>
        <title>Insert a Social Post</title>
        <script type="text/javascript" src="../../framework/thirdparty/jquery/jquery.js"></script>
        <script type="text/javascript" src="../../framework/thirdparty/tinymce/tiny_mce_popup.js"></script>
        <script type="text/javascript" src="/$ModuleDir/assets/build/js/popup.js"></script>
        <script type="text/javascript" src="/$ModuleDir/assets/build/css/main.css"></script>
    </head>
    <body class="popup">
        <form>
            <div>
                <label for="url"></label>
                <input type="hidden" name="AlbumID" id="AlbumID">
                <input type="text" name="q" id="q" placeholder="Start typing the name of the album" autocomplete="off">
                <ul id="options"></ul>
                <div id="preview"></div>
            </div>
            <button>OK</button>
        </form>
    </body>
</html>
