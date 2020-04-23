<?php
namespace App\Models;

/**
 * 検索ワード
 * 検索文字列をパースする
 */
class SearchCondition
{
    /**
     * 元の検索ワード
     * @var string
     */
    private $word;

    /**
     * 変換された検索条件配列
     * @var array
     */
    private $parsed;

    public function __construct($word = null)
    {
        $this->word = $this->clean($word);

        $this->parse();
    }

    /**
     * 検索条件に関わる文字を統一
     */
    private function clean($str)
    {
        $from = [' ', '　', '＆', '（', '）'];
        $to = ['|', '|', '&', '(', ')'];
        $str = str_replace($from, $to, $str);
        $str = mb_strtolower($str);
        $str = trim($str);
        return $str;
    }

    /**
     * 検索ワードをand,orで抽出する
     * (a b)&c d
     * [
     *  [type=>and, cond=> [
     *    [type=>or, cond=>[a,b],
     *      [type]
     *  ]
     * ]]
     * ]
     *
     *
     * @example hoge foo&bar -> [[hoge],[foo,bar]] -> hoge or (foo and bar)
     */
    private function parse()
    {
        $conds = $this->parseBlanket($this->word);
        dump($conds);

        $conds = $this->parseOperand($conds);

        dd($conds);
    }

    private function divide($str, $pos)
    {
        return array_filter([
            mb_substr($str, 0, $pos),
            mb_substr($str, $pos + 1),
        ]);
    }

    private function parseBlanket($str)
    {
        $len = mb_strlen($str);
        $begin_blanket = mb_stripos($str, '(');
        $end_blanket = mb_stripos($str, ')');

        // dump([$str, $len, $begin_blanket, $end_blanket]);

        if ($begin_blanket !== false) {
            return array_map(function ($str) {
                return $this->parseBlanket($str);
            }, $this->divide($str, $begin_blanket));
        }
        if ($end_blanket !== false) {
            return array_map(function ($str) {
                return $this->parseBlanket($str);
            }, $this->divide($str, $end_blanket));
        }

        return $str;

    }

    private function getFirstOperand($str)
    {
        $and_operand = mb_stripos($str, '&');
        $or_operand = mb_stripos($str, '|');

        if ($and_operand !== false && $or_operand !== false) {
            if ($and_operand < $or_operand) {
                return ['and', $and_operand];
            }
            return ['or', $or_operand];
        }
        if ($and_operand !== false) {
            return ['and', $and_operand];
        }
        if ($or_operand !== false) {
            return ['or', $or_operand];
        }
        return [null, false];
    }

    private function parseOperand($cond)
    {
        dump($cond);
        if (is_string($cond)) {
            return $cond;
        }
        $left = array_shift($cond);

        $len = mb_strlen($left);
        list($type, $pos) = $this->getFirstOperand($left);

        $cond = array_merge(
            $this->divide($left, $pos),
            $cond
        );

        return [
            'type' => $type,
            'conds' => array_map(function ($str) {
                return $this->parseOperand($str);
            }, $cond),
        ];
    }

    /**
     * return word collection
     */
    public function getWordsCollection()
    {
        return $this->words;
    }

    /**
     * return flatten words array
     */
    public function getFlattenWords()
    {
        return $this->words->flatten();
    }
}
