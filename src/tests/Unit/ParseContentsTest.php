<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParseContentsTest extends TestCase
{
  private $text = "//cache
#navi(Addon128,prev,toc,next)

*(128 tile) Trains 19 / (アドオン128) 列車19 [#zb512453]
128x128ピクセル用の列車アドオンを掲載しています。
&size(10){運営スタッフは公開されているファイルの安全性について保証しません。};


**usage/使い方 [#jaeb0680]
アーカイブに収められている pak ファイルを simutrans/''pak128'' ディレクトリに配置してください。

&size(10){ここにアドオンを新たに掲載する方法については、こちらをご覧ください。 → [[このサイトについて]]};
&color(#ff0099){10-15件程度になったら、新規ページを作成してください。};

//更新メモは５件以上になったら古いのを消してください


RIGHT:
&size(10){''更新履歴''};
&size(10){EH800形を追加(纈纐検車) 2019/03/14};
&size(10){クモハ123-1を追加(纈纐検車) 2019/02/12};
&size(10){500系を追加(纈纐検車) 2019/02/04};



※[E]は電気機関車、[Emu]は電気動力分散方式（電車）[D]はディーゼル機関車、~
[Dmu]はディーゼル動力分散方式（気動車）、[S]は蒸気機関車、[T]は被牽引車（貨客車）の略号です。
|[Type]Title/題名|Author/作者|Date/投稿日|h
|Thumbnail/画像|Game Version/対応Ver.|Download/ダウンロード|h
|>|>|Comment/コメント|h
|[E/D/T]EH800形|纈纐検車|2019/03/14|
|&ref(JRF_EH800.png);|120.4.1&br;（旧描画位置）|&ref(JRF_EH800.zip);|
|>|>|EH500形(3次形)、EF210形(100番台後期型)、DF200形(50番台)とかも入っています。|
|[Emu]クモハ123-1|纈纐検車|2019/02/12|
|&ref(JRE_123-1.png);|120.4.1&br;（旧描画位置）|&ref(JRE_123-1.zip);|
|>|>|E127系100番台も入っています。|
|[Emu]500系|纈纐検車|2019/02/04|
|&ref(JRW_500image.png);|120.4.1&br;（旧描画位置）|&ref(JRW_500.zip);|
|>|>|700系レールスターも入っています。|

#navi(Addon128,prev,toc,next)
";

  /**
   * A basic test example.
   *
   * @return void
   */
  public function testBasicTest()
  {
    $this->parseContents();
    $this->assertTrue(true);
  }

  public function parseContents()
  {
    $lines = explode("\n", $this->text);

    $contents = [];

    $buffer = [];
    foreach ($lines as $line) {
      if($this->isNextContent($line)) {
        $contents[] = $buffer;
        $buffer = [];
      }
      $buffer[] = $line;
    }
    dd($contents);
  }
  public function isNextContent($line)
  {
    return preg_match('/\|\d{4}\/\d{1,2}\/\d{1,2}\|/u', $line) === 1;
  }
}
