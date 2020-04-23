<?php
namespace App\Models;

/**
 * 検索ワード
 * 検索文字列をパースする
 */
class SearchWords
{
    private $words;

    public function __construct($word = null)
    {
        $word = str_replace(['　'], ' ', $word);
        $this->words = collect(explode(' ', $word));
    }

    /**
     * return word collection
     */
    public function getWords()
    {
        return $this->words;
    }
}
