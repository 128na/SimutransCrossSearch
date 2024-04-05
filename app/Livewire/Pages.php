<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\PakSlug;
use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Livewire\Component;
use Livewire\WithPagination;

class Pages extends Component
{
    use WithPagination;

    public $keyword = '';

    public $paks = [
        PakSlug::Pak64->value => true,
        PakSlug::Pak128->value => true,
        PakSlug::Pak128Jp->value => true,
    ];

    public function render()
    {
        return view('livewire.pages', [
            'pages' => $this->paginatePages(),
        ]);
    }

    public function clear(): void
    {
        $this->reset('keyword', 'paks');
    }

    private function paginatePages(): LengthAwarePaginator
    {
        return Page::query()
            ->withWhereHas('paks', fn (Builder|BelongsToMany $builder) => $builder->whereIn('slug', $this->selectedPaks()))
            ->when($this->keyword, fn (Builder $builder) => $builder->where('text', 'like', "%{$this->keyword}%"))
            ->orderBy('last_modified', 'desc')
            ->paginate(50);
    }

    /**
     * @return array<int,string>
     */
    private function selectedPaks(): array
    {
        return array_keys(array_filter($this->paks));
    }
}
