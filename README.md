WHATIS
======

PukiWikiの中から記法の変換に最低限必要なモノを抜き出しました。

HOWTO
=====

全部のファイルを同じディレクトリに入れてください。

    require 'INSTALL-DIRECTORY/pseudo-init.php';

してエラーが出なかったら pukiparser() を呼ぶだけです。呼び出し方は

    string pukiparser( mixed $source )

になります。HTML に整形された文字列が返ってきます。引数は文字列でも配列でも構いません。（いずれの場合も内部では配列として処理します。）

CONSTANTS
=========

利用している定数は以下の2つです。

 * DATA_HOME
 * CACHE_DIR

PukiWiki VERSION
================

PukiWiki 1.4.7 ( CVS HEAD at 2006-07-08 )

KNOWN BUGS
==========

ほとんどすべて PukiWiki のファイルをそのまま使っていることもあり、BracketName を利用した場合、自分自身への ?cmd=edit&page=HOGEHOGE などのリンクが自動的に生成されます。WikiName は無効になっているので通常の文章を書いている場合は問題にならないはずです。

あと、内部の文字コードが EUC-JP です。(UTF-8版が始まる前に抽出したものなので。）

LICENSE
=======

GPL ( same as PukiWiki )
