<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\SearchPage\SearchAction;
use App\Enums\PakSlug;
use App\Enums\SiteName;
use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class Pages extends Component
{
    #[Validate('string|max:191')]
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

    public function render(): View
    {
        return view('livewire.pages');
    }

    /**
     * ページネーションリンクは通常のURL遷移（`?page=N`）で行われ、`#[Url] $page` が唯一の状態源。
     * 検索条件（キーワード・pak・サイト）を変えたときだけ、ここで明示的に1ページ目へ戻す。
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
            'page' => $this->page,
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
