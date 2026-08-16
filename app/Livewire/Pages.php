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
    #[Url]
    public string $keyword = '';

    #[Url]
    public int $page = 1;

    /**
     * @var array<string|int,bool>
     */
    #[Url]
    public array $paks = [
        PakSlug::Pak64->value => true,
        PakSlug::Pak128->value => true,
        PakSlug::Pak128Jp->value => true,
    ];

    /**
     * @var array<string,bool>
     */
    #[Url]
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

        // #[Url]経由でURLクエリ文字列から配列をハイドレートすると、値が
        // 真偽値ではなく文字列 '0'/'1' になる（PHP側のarray_filter等では
        // '0'はfalsy値として扱われるため検索フィルタ自体は正しく動作するが、
        // フロントに送られるJSON上は非空文字列としてtruthyに評価されてしまい、
        // チェックボックスの表示が実際の選択状態を反映しなくなる）。
        // 明示的にboolへキャストし直して補正する。
        $this->paks = array_map(fn (mixed $value): bool => (bool) $value, $this->paks);
        $this->sites = array_map(fn (mixed $value): bool => (bool) $value, $this->sites);
    }

    public function render(): View
    {
        return view('livewire.pages');
    }

    /**
     * ページネーションリンクは通常のURL遷移（`?page=N`）で行われ、`#[Url] $page` が
     * 「今何ページ目か」を表す唯一の状態源。検索条件（キーワード・pak・サイト）を
     * 変えたときは、ここで明示的に1ページ目へ戻す。
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
        $pages = (new SearchAction)([
            'keyword' => $this->keyword,
            'paks' => $this->selectedPaks(),
            'sites' => $this->selectedSites(),
            // #[Url]は型不一致(TypeError)は弾くが、0以下の値はintとして許してしまうため、
            // ?page=-1 のような不正なURLでもここで1に丸める。
            'page' => max(1, $this->page),
        ]);

        // ページネーションリンク（プレーンな<a href>によるURL遷移）に検索条件を
        // 引き継がせる。request()->query()に頼るwithQueryString()はLivewireの
        // AJAXリクエスト中は正しく機能しない（アドレスバーではなくAJAX
        // エンドポイント自体のクエリ文字列を見てしまう）ため、コンポーネントの
        // プロパティから明示的に構築する。
        return $pages->appends([
            'keyword' => $this->keyword,
            'paks' => $this->paks,
            'sites' => $this->sites,
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
