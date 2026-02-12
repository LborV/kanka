<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icon extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $class = null,
        public bool $tooltip = false,
        public ?string $title = null,
        public ?string $link = null,
        public ?string $size = null,
        public ?string $label = null,
        public ?string $show = null,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->class = $this->map($this->class);

        return view('components.icon');
    }

    /**
     * Map icon shortcuts into their fontawesome or other elements
     */
    protected function map(string $class): string
    {
        // Convert duotone to solid for self-hosted instances
        if (str_contains($class, 'fa-duotone')) {
            $class = str_replace('fa-duotone', 'fa-solid', $class);
        }

        return match ($class) {
            'map' => 'fa-solid fa-map',
            'check' => 'fa-solid-check',
            'trash' => 'fa-solid-trash-can',
            'plus' => 'fa-solid-plus',
            'question' => 'fa-solid-question-circle',
            'save' => 'fa-solid-save',
            'pencil' => 'fa-solid-pencil',
            'cog' => 'fa-solid-cog',
            'copy' => 'fa-solid-copy',
            'edit' => 'fa-solid-edit',
            'premium' => 'fa-solid-gem',
            'lock' => 'fa-solid-lock',
            'filter' => 'fa-solid-filter',
            'load' => 'fa-solid fa-spinner fa-spin',
            'arrow' => 'fa-solid-arrow-right',
            'permissions' => 'fa-solid-user-shield',
            'attributes' => 'fa-solid-rectangle-list',
            'link' => 'fa-solid-external-link',
            'sort' => 'fa-solid-grip-vertical',
            default => $class,
        };
    }
}
