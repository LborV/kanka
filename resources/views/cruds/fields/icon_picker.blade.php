{{--
    Icon Picker Component
    Usage: @include('cruds.fields.icon_picker', ['target' => 'icon-field-name-or-id'])

    Provides a visual browser for FontAwesome and RPGAwesome icons that
    populates the target input field on click.
--}}
@php
$iconCategories = config('icons.categories', []);
$pickerId = 'icon-picker-' . ($target ?? 'icon');
@endphp

<div class="icon-picker mt-2" id="{{ $pickerId }}">
    <button type="button" class="btn2 btn-sm btn-ghost icon-picker-toggle" data-target="#{{ $pickerId }}-panel">
        <x-icon class="fa-solid fa-icons" />
        {{ __('icons.picker.browse') }}
    </button>

    <div class="icon-picker-panel hidden rounded border border-base-300 bg-base-100 mt-2 p-3" id="{{ $pickerId }}-panel">
        <div class="mb-2">
            <input type="text" class="w-full icon-picker-search" placeholder="{{ __('icons.picker.search') }}" />
        </div>

        <div class="icon-picker-categories flex flex-wrap gap-1 mb-2">
            <button type="button" class="btn2 btn-xs icon-picker-cat active" data-category="all">
                {{ __('icons.picker.all') }}
            </button>
            @foreach ($iconCategories as $key => $category)
                <button type="button" class="btn2 btn-xs icon-picker-cat" data-category="{{ $key }}">
                    {{ __($category['label']) }}
                </button>
            @endforeach
        </div>

        <div class="icon-picker-grid grid grid-cols-8 sm:grid-cols-10 gap-1 max-h-48 overflow-y-auto p-1">
            @foreach ($iconCategories as $catKey => $category)
                @foreach ($category['icons'] as $icon)
                    <button type="button"
                        class="icon-picker-btn p-2 rounded text-center cursor-pointer hover:bg-primary hover:text-primary-content transition-colors"
                        data-icon="{{ $icon }}"
                        data-category="{{ $catKey }}"
                        title="{{ $icon }}">
                        <i class="{{ $icon }}" aria-hidden="true"></i>
                    </button>
                @endforeach
            @endforeach
        </div>

        <div class="icon-picker-empty hidden text-center text-neutral-content py-4">
            {{ __('icons.picker.no_results') }}
        </div>
    </div>
</div>

<script>
(function() {
    const picker = document.getElementById('{{ $pickerId }}');
    if (!picker || picker.dataset.init === '1') return;
    picker.dataset.init = '1';

    const toggle = picker.querySelector('.icon-picker-toggle');
    const panel = picker.querySelector('.icon-picker-panel');
    const search = picker.querySelector('.icon-picker-search');
    const grid = picker.querySelector('.icon-picker-grid');
    const emptyMsg = picker.querySelector('.icon-picker-empty');
    const catButtons = picker.querySelectorAll('.icon-picker-cat');
    const iconButtons = grid.querySelectorAll('.icon-picker-btn');
    const targetInput = picker.closest('.field, x-forms\\.field, [class*="field"]')
        ?.querySelector('input[name="{{ $target }}"]')
        || document.querySelector('input[name="{{ $target }}"]');

    let activeCategory = 'all';

    // Toggle panel visibility
    toggle.addEventListener('click', function(e) {
        e.preventDefault();
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) {
            search.focus();
        }
    });

    // Category filtering
    catButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            activeCategory = btn.dataset.category;
            catButtons.forEach(b => b.classList.remove('active', 'btn-primary'));
            btn.classList.add('active', 'btn-primary');
            filterIcons();
        });
    });

    // Search filtering
    search.addEventListener('input', function() {
        filterIcons();
    });

    function filterIcons() {
        const query = search.value.toLowerCase().trim();
        let visibleCount = 0;

        iconButtons.forEach(function(btn) {
            const iconClass = btn.dataset.icon.toLowerCase();
            const matchesCat = activeCategory === 'all' || btn.dataset.category === activeCategory;
            const matchesSearch = !query || iconClass.includes(query);
            const visible = matchesCat && matchesSearch;

            btn.classList.toggle('hidden', !visible);
            if (visible) visibleCount++;
        });

        emptyMsg.classList.toggle('hidden', visibleCount > 0);
    }

    // Icon selection
    iconButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (targetInput) {
                targetInput.value = btn.dataset.icon;
                targetInput.dispatchEvent(new Event('change'));
            }

            // Visual feedback
            iconButtons.forEach(b => b.classList.remove('bg-primary', 'text-primary-content'));
            btn.classList.add('bg-primary', 'text-primary-content');

            // Close panel after short delay
            setTimeout(function() {
                panel.classList.add('hidden');
            }, 200);
        });
    });
})();
</script>
