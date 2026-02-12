<?php

namespace App\View\Components\Sidebar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Element extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $text,
        public ?string $icon = null,
        public ?string $url = null,
        public ?string $class = null,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->icon = $this->normalizeIcon($this->icon);

        $view = 'link';
        if (empty($this->url)) {
            $view = 'text';
        }

        return view('components.sidebar.element-' . $view);
    }

    protected function normalizeIcon(?string $icon): ?string
    {
        if (empty($icon)) {
            return $icon;
        }

        if (str_contains($icon, 'fa-duotone')) {
            return str_replace('fa-duotone', 'fa-solid', $icon);
        }

        return $icon;
    }
}
