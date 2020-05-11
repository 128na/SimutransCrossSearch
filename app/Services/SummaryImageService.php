<?php

namespace App\Services;

use App\Models\Article;
use Intervention\Image\Facades\Image;

class SummaryImageService
{
    private $rows, $cols, $width, $height, $index;

    public function make($articles, $rows = 5, $cols = 5, $width = 320, $height = 180)
    {
        $this->rows = $rows;
        $this->cols = $cols;
        $this->width = $width;
        $this->height = $height;
        $this->index = 0;

        return $articles->reduce(
            function ($img, $article) {
                $thumb = $this->createImage($article);
                // オフセット位置
                $x = $this->width * ($this->index % $this->rows);
                $y = $this->height * floor($this->index / $this->rows);

                $this->index++;
                return $img->insert($thumb, 'top-left', $x, $y);
            },
            Image::canvas($this->width * $this->rows, $this->height * $this->cols)
        );
    }

    /**
     * 既定サイズの正方形にリサイズ・トリミングした画像を返す
     */
    private function createImage(Article $article)
    {
        try {
            $img = Image::make($article->thumbnail_url);
        } catch (\Throwable $e) {
            // 生成失敗時はブランク画像で埋める
            $img = Image::canvas($this->width, $this->height);
        }
        $raito = $this->width / $this->height;
        // 規定アス比でクロップ
        if ($img->width() / $img->height() > $raito) {
            // x = (w-hr)/2
            // w = hr
            $x = floor(($img->width() - $img->height() * $raito) / 2);
            $w = floor($img->height() * $raito);
            $img->crop($w, $img->height(), $x, 0);
        } else {
            // y = (h-w/r)/2
            // h = w/r
            $y = floor(($img->height() - $img->width() / $raito) / 2);
            $h = floor($img->width() / $raito);
            $img->crop($img->width(), $h, 0, $y);
        }
        // 既定サイズにリサイズ
        return $img->resize($this->width, $this->height);
    }
}
