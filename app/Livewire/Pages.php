<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\SearchPage\SearchAction;
use App\Enums\PakSlug;
use App\Enums\SiteName;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class Pages extends Component
{
    use WithPagination;

    public string $keyword = '';

    /**
     * @var int|string|null
     */
    #[Url]
    public $page = 1;

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

    public function render(SearchAction $searchAction): View
    {
        $this->resetPage();
        if (! is_numeric($this->page)) {
            $this->page = 1;
        }

        return view('livewire.pages', [
            'pages' => $searchAction([
                'keyword' => $this->keyword,
                'paks' => $this->selectedPaks(),
                'sites' => $this->selectedSites(),
                'page' => $this->page,
            ]),
        ]);
    }

    public function onConditionUpdate(SearchAction $searchAction): \Illuminate\Contracts\View\View
    {

        return $this->render($searchAction);
    }

    public function clear(): void
    {
        $this->resetPage();
        $this->reset('keyword', 'paks', 'sites');
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
