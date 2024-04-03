<?php

namespace Tests;

/**
 * 動作確認用のHTML構造
 */
class MockHTML
{
    public static function japan($title, $include_text = 'include text', $exclude_text = 'exclude text')
    {
        return <<<EOT
<?xml version="1.0" encoding="EUC-JP" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">

<head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=EUC-JP" />
    <meta http-equiv="content-style-type" content="text/css" />
    <title>{$title} - Simutrans日本語化･解説</title>
</head>

<body>
    <div id="header">{$exclude_text}</div>
    <hr class="full_hr" />
    <table border="0" style="width:100%">
        <tr>
        <td class="menubar">{$exclude_text}</td>
        <td class="main" valign="top">
            <div id="body">{$include_text}</div>
        </td>
        </tr>
    </table>
    <div id="attach">{$exclude_text}</div>
    <div id="lastmodified">Last-modified: 2020-01-01 (金) 00:00:00</div>
    <div id="footer">{$exclude_text}</div>
</body>
</html>
EOT;
    }

    public static function twitrans($title, $include_text = 'include text', $exclude_text = 'exclude text')
    {
        return <<<EOT
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$title} - Simutrans的な実験室 Wiki*</title>
</head>

<body>
    <div class="container-wrapper" id="backframe">
    {$exclude_text}
    <div id="contents" class="columns-container container clearfix has-left-column">
        <div class="columns-container-center-right clearfix">
        <div class="columns-container-center clearfix">
            <div class="column-center clearfix">
            <div id="body">
                <div id="title">
                    <h1 class="title" id="header">{$exclude_text}</h1>
                        <div id="lastmodified">Last-modified: 2020-01-01 (金) 00:00:00</div>
                </div>
                <div id="content">{$include_text}</div>
                <div id="prframe" style="height:280px;float:left;margin:50px 8px 10px 0px;">{$exclude_text}</div>
                <div id="prframe" style="height:280px;float:left;margin:50px 0px 50px 0px;">{$exclude_text}</div>
                <div id="ad-image-area-rectangle"></div>
            </div>
            </div>
        </div>
        </div>
        {$exclude_text}
    </div>
    <div id="footer">{$exclude_text}</div>
    </div>
</body>
</html>
EOT;
    }
}
