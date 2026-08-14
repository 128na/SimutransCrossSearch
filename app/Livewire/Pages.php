<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\SearchPage\SearchAction;
use App\Enums\PakSlug;
use App\Enums\SiteName;
use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\Paginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Livewire;

final class Pages extends Component
{
    #[Validate('string|max:20')]
    public string $keyword = '';

    #[Url]
    public int $page = 1;

    /**
     * @var array<string|int,bool>
     */
    public array $paks = [
        PakSlug::Pak64->value => true,
        PakSlug::Pak128->value => true,
        PakSlug::Pak128Jp->value => true,
    ];

    /**
     * @var array<string,bool>
     */
    public array $sites = [
        SiteName::Japan->value => true,
        SiteName::Twitrans->value => true,
        SiteName::Portal->value => true,
    ];

    /**
     * `WithPagination`トレイトを外した分、ページネーションリンクの生成元URLを
     * 実際のページURLに合わせる処理（本来はトレイトの`boot()`が担っていた）を
     * ここで肩代わりする。これが無いと、Livewireのアクション経由でページ送り
     * リンクを再生成した際にリンク先がLivewireの内部エンドポイント（POST専用）
     * になってしまい、クリック時に405が返る。
     */
    public function boot(): void
    {
        Paginator::currentPathResolver(fn (): string => Livewire::originalPath());
    }

    public function render(): View
    {
        return view('livewire.pages');
    }

    /**
     * ページネーションリンクは通常のURL遷移（`?page=N`）で行われ、`#[Url] $page` が
     * 「今何ページ目か」を表す唯一の状態源。検索条件（キーワード・pak・サイト）を
     * 変えたときは、ここで明示的に1ページ目へ戻す。
     *
     * 注意: `$keyword`/`$paks`/`$sites`は`#[Url]`化していないため、ページネーション
     * リンク（プレーンな`<a href>`によるページ遷移）をクリックすると検索条件はURLに
     * 乗らず、デフォルト状態でコンポーネントが再マウントされる（既知の制限。今回の
     * 修正はページ番号の二重管理を解消する範囲に限定しており、この制限自体はスコープ外）。
     */
    public function onConditionUpdate(): void
    {
        $this->page = 1;
    }

    public function clear(): void
    {
        $this->reset('keyword', 'paks', 'sites', 'page');
    }

    /**
     * @return LengthAwarePaginator<int, Page>
     */
    #[Computed]
    public function pages(): LengthAwarePaginator
    {
        return (new SearchAction)([
            'keyword' => $this->keyword,
            'paks' => $this->selectedPaks(),
            'sites' => $this->selectedSites(),
            // #[Url]は型不一致(TypeError)は弾くが、0以下の値はintとして許してしまうため、
            // ?page=-1 のような不正なURLでもここで1に丸める。
            'page' => max(1, $this->page),
        ]);
    }

    /**
     * @return array<int,int|string>
     */
    private function selectedPaks(): array
    {
        return array_keys(array_filter($this->paks));
    }

    /**
     * @return array<int,string>
     */
    private function selectedSites(): array
    {
        return array_keys(array_filter($this->sites));
    }
}
