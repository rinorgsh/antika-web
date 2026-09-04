{{--
    Écran « carte ». Styles portés par ce fichier plutôt que par des classes
    utilitaires : la feuille Tailwind du panneau est précompilée par Filament
    et n'inclut pas ce gabarit, des classes arbitraires n'y survivraient pas.
--}}
<x-filament-panels::page>
    <style>
        .mb-tabs{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.5rem}
        .mb-tab{
            padding:.5rem 1rem;border-radius:.6rem;border:1px solid rgb(228 228 231 / .8);
            background:#fff;font-size:.875rem;font-weight:600;color:#52525b;cursor:pointer;
        }
        .mb-tab:hover{background:#fafafa}
        .mb-tab[data-on="1"]{background:#18181b;color:#fff;border-color:#18181b}
        .dark .mb-tab{background:rgb(39 39 42);border-color:rgb(63 63 70);color:#d4d4d8}
        .dark .mb-tab[data-on="1"]{background:#f4f4f5;color:#18181b;border-color:#f4f4f5}

        .mb-cat{
            border:1px solid rgb(228 228 231 / .9);border-radius:.85rem;background:#fff;
            margin-bottom:1rem;overflow:hidden;
        }
        .dark .mb-cat{background:rgb(24 24 27);border-color:rgb(63 63 70)}
        .mb-cat-head{
            display:flex;align-items:center;gap:.75rem;padding:.85rem 1rem;
            background:rgb(250 250 250);border-bottom:1px solid rgb(228 228 231 / .9);
        }
        .dark .mb-cat-head{background:rgb(39 39 42);border-color:rgb(63 63 70)}
        .mb-cat-title{font-weight:700;font-size:1rem;letter-spacing:.01em}
        .mb-count{font-size:.75rem;color:#71717a}
        .mb-grip{cursor:grab;color:#a1a1aa;font-size:1.1rem;line-height:1;user-select:none;touch-action:none}
        .mb-grip:active{cursor:grabbing}

        .mb-row{
            display:flex;align-items:center;gap:.75rem;padding:.6rem 1rem;
            border-bottom:1px solid rgb(244 244 245);
        }
        .dark .mb-row{border-color:rgb(39 39 42)}
        .mb-row:last-of-type{border-bottom:0}
        .mb-row[data-off="1"]{opacity:.45}
        .mb-thumb{
            width:44px;height:44px;border-radius:.5rem;object-fit:cover;flex:0 0 auto;
            background:rgb(244 244 245);
        }
        .mb-thumb.logo{object-fit:contain;background:#fff;padding:3px;border:1px solid rgb(228 228 231)}
        .mb-noimg{
            width:44px;height:44px;border-radius:.5rem;flex:0 0 auto;
            background:rgb(244 244 245);display:flex;align-items:center;justify-content:center;
            color:#a1a1aa;font-size:.7rem;
        }
        .dark .mb-noimg{background:rgb(39 39 42)}
        .mb-name{flex:1 1 auto;min-width:0;font-weight:600;text-decoration:none;color:inherit}
        .mb-name:hover{text-decoration:underline}
        .mb-name .sub{display:block;font-weight:400;font-size:.75rem;color:#71717a;
            overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .mb-head-tag{
            font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
            color:#a16207;background:#fef9c3;padding:.15rem .5rem;border-radius:.35rem;
        }
        .mb-price{width:88px;flex:0 0 auto}
        .mb-price input{
            width:100%;text-align:right;padding:.3rem .5rem;border-radius:.45rem;
            border:1px solid rgb(228 228 231);font-size:.875rem;background:#fff;color:inherit;
        }
        .dark .mb-price input{background:rgb(39 39 42);border-color:rgb(63 63 70)}
        .mb-sw{
            width:38px;height:22px;border-radius:999px;border:0;cursor:pointer;flex:0 0 auto;
            background:rgb(212 212 216);position:relative;transition:background .15s;
        }
        .mb-sw[data-on="1"]{background:#16a34a}
        .mb-sw::after{
            content:"";position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;
            background:#fff;transition:transform .15s;
        }
        .mb-sw[data-on="1"]::after{transform:translateX(16px)}
        .mb-del{border:0;background:none;color:#a1a1aa;cursor:pointer;font-size:1rem;padding:.25rem}
        .mb-del:hover{color:#dc2626}
        .mb-add{
            display:block;width:100%;padding:.6rem;border:0;background:none;cursor:pointer;
            color:#71717a;font-size:.8rem;font-weight:600;
        }
        .mb-add:hover{background:rgb(250 250 250);color:#18181b}
        .dark .mb-add:hover{background:rgb(39 39 42);color:#fafafa}
        .mb-addcat{
            display:block;width:100%;padding:.9rem;border:1px dashed rgb(212 212 216);
            border-radius:.85rem;background:none;cursor:pointer;color:#71717a;font-weight:600;
        }
        .mb-addcat:hover{border-color:#18181b;color:#18181b}
        .mb-empty{padding:1rem;color:#a1a1aa;font-size:.85rem;text-align:center}
    </style>

    @if (count($this->surfaces()) > 1)
        <div class="mb-tabs">
            @foreach ($this->surfaces() as $code => $libelle)
                <button type="button" class="mb-tab" data-on="{{ $surface === $code ? '1' : '0' }}"
                        wire:click="setSurface('{{ $code }}')">{{ $libelle }}</button>
            @endforeach
        </div>
    @endif

    <div
        x-sortable
        x-on:end.stop="$wire.reorderCategories($event.target.sortable.toArray())"
    >
        @forelse ($this->categories as $cat)
            <div class="mb-cat" wire:key="cat-{{ $cat->id }}" x-sortable-item="{{ $cat->id }}">
                <div class="mb-cat-head">
                    <span class="mb-grip" x-sortable-handle title="Glisser pour réordonner">⠿</span>
                    <a class="mb-cat-title mb-name" href="{{ $this->categoryUrl($cat->id) }}">
                        {{ $this->label($cat->title) }}
                    </a>
                    <span class="mb-count">{{ $cat->items->count() }} {{ Str::plural('ligne', $cat->items->count()) }}</span>
                </div>

                <div
                    x-sortable
                    x-on:end.stop="$wire.reorderItems($event.target.sortable.toArray())"
                >
                    @foreach ($cat->items as $item)
                        @php $thumb = $this->thumbUrl($item->photo); @endphp
                        <div class="mb-row" wire:key="it-{{ $item->id }}" x-sortable-item="{{ $item->id }}"
                             data-off="{{ $item->is_active || $item->is_subheader ? '0' : '1' }}">
                            <span class="mb-grip" x-sortable-handle>⠿</span>

                            @if ($item->is_subheader)
                                <span class="mb-noimg">—</span>
                            @elseif ($thumb)
                                <img class="mb-thumb {{ str_contains((string) $item->photo, 'logos/') ? 'logo' : '' }}"
                                     src="{{ $thumb }}" alt="" loading="lazy">
                            @else
                                <span class="mb-noimg">photo</span>
                            @endif

                            <a class="mb-name" href="{{ $this->itemUrl($item->id) }}">
                                {{ $item->is_subheader
                                    ? ($item->default_name ?: $this->label($item->name))
                                    : ($this->label($item->name) !== '—' ? $this->label($item->name) : ($item->default_name ?: $item->slug)) }}
                                @if ($item->is_subheader)
                                    <span class="mb-head-tag">sous-titre</span>
                                @elseif ($d = $this->label($item->description, ''))
                                    <span class="sub">{{ $d }}</span>
                                @endif
                            </a>

                            @unless ($item->is_subheader)
                                <span class="mb-price">
                                    <input type="text" value="{{ $item->price }}" placeholder="—"
                                           wire:change="savePrice({{ $item->id }}, $event.target.value)">
                                </span>
                                <button type="button" class="mb-sw" data-on="{{ $item->is_active ? '1' : '0' }}"
                                        wire:click="toggleItem({{ $item->id }})"
                                        title="{{ $item->is_active ? 'Visible sur la carte' : 'Masqué' }}"></button>
                            @endunless

                            <button type="button" class="mb-del"
                                    wire:click="deleteItem({{ $item->id }})"
                                    wire:confirm="Supprimer définitivement cette ligne ?">✕</button>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="mb-add" wire:click="addItem({{ $cat->id }})">
                    + ajouter un plat
                </button>
            </div>
        @empty
            <div class="mb-empty">Aucune catégorie pour l'instant.</div>
        @endforelse
    </div>

    <button type="button" class="mb-addcat" wire:click="addCategory">+ ajouter une catégorie</button>
</x-filament-panels::page>
