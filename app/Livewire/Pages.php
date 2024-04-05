<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\PakSlug;
use App\Enums\SiteName;
use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Livewire\Component;
use Livewire\WithPagination;

class Pages extends Component
{
    use WithPagination;

    public string $keyword = '';

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
        return view('livewire.pages', [
            'pages' => $this->paginatePages(),
        ]);
    }

    public function clear(): void
    {
        $this->reset('keyword', 'paks', 'sites');
    }

    /**
     * @return LengthAwarePaginator<Page>
     */
    private function paginatePages(): LengthAwarePaginator
    {
        /**
         * @var LengthAwarePaginator<Page>
         */
        return Page::query()
            ->withWhereHas('paks', fn (Builder|BelongsToMany $builder) => $builder->whereIn('slug', $this->selectedPaks()))
            ->whereIn('site_name', $this->selectedSites())
            ->when($this->keyword, fn (Builder $builder) => $builder->where('text', 'like', sprintf('%%%s%%', $this->keyword)))
            ->orderBy('last_modified', 'desc')
            ->paginate(50);
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
