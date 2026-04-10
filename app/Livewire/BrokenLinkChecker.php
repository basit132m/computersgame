<?php

namespace App\Livewire;

use App\Models\DownloadLink;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class BrokenLinkChecker extends Component
{
    public bool $running = false;
    public int $total = 0;
    public int $checked = 0;
    public array $broken = [];
    public array $results = [];

    public function startCheck(): void
    {
        $this->running = true;
        $this->checked = 0;
        $this->broken  = [];
        $this->results = [];

        $links = DownloadLink::with('post:id,title,slug')->get();
        $this->total = $links->count();

        foreach ($links as $link) {
            $this->checked++;
            try {
                $response = Http::timeout(10)->head($link->url);
                $status   = $response->status();
                $ok       = $response->successful();
            } catch (\Exception) {
                $status = 0;
                $ok     = false;
            }

            $this->results[] = [
                'id'      => $link->id,
                'label'   => $link->label,
                'url'     => $link->url,
                'post'    => $link->post?->title,
                'slug'    => $link->post?->slug,
                'status'  => $status,
                'ok'      => $ok,
            ];

            if (!$ok) {
                $this->broken[] = $link->id;
            }

            // Yield to browser every 5 links
            if ($this->checked % 5 === 0) {
                $this->dispatch('progress-update', checked: $this->checked, total: $this->total);
            }
        }

        $this->running = false;
    }

    public function render()
    {
        return view('livewire.broken-link-checker');
    }
}
