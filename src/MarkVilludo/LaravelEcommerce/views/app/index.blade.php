<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">


        <!--facebook -->

        <meta property="og:image" content="{{ request()->root() }}/images/logo/lg_fs_fb.png">
        <meta property="og:image:type" content="image/png">
        <meta property="og:image:width" content="700">
        <meta property="og:image:height" content="136">
        <meta property="og:url"/>
        <meta property="og:title" content="FS Features & Shades" />
        <meta property="og:description" content="FS Cosmetics is a Philippine makeup brand that had its beginnings in November 2003. The brand is owned by Mr. Edmon Ngo, the scion of a pioneer in the Philippine cosmetic industry, who launched his own company, Cosmetics Revelation Corporation, that honors integrity and excellence and that draws from the solid experience of his parents’ company." />


        <meta name='copyright' content='Fs Cosmetics'>

        <meta name='description' content='FS Cosmetics is a Philippine makeup brand that had its beginnings in November 2003. The brand is owned by Mr. Edmon Ngo, the scion of a pioneer in the Philippine cosmetic industry, who launched his own company, Cosmetics Revelation Corporation, that honors integrity and excellence and that draws from the solid experience of his parents’ company.'>
        <meta name='keywords' content='cosmetics, fashion, make-up, benefit cosmetics, cosmetics makeup, discontinued cosmetics, beauty cosmetics, cosmetics brands'>
        <title>Fs cosmetics</title>
        <link rel="stylesheet" href="{{ request()->root() }}{{ mix('css/app.css') }}">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>
    <body>

        <div style="opacity:0; z-index: -2; position: fixed;">
            <img src="{{ request()->root() }}/images/logo/lg_fs.png" alt="fs21 logo">
        </div>
        <div id="app">
            <router-view></router-view>
        </div>

    <script src="{{ request()->root() }}{{ mix('js/app.js') }}"></script>
    </body>
</html>
