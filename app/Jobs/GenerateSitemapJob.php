<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateSitemapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        try {
            $this->generateSitemapIndex();
            $this->pingSearchEngines();
        } catch (\Exception $e) {
            Log::error('Sitemap generation failed', ['error' => $e->getMessage()]);
        }
    }

    private function generateSitemapIndex(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $xml .= $this->sitemapEntry(url('/sitemap-posts.xml'));
        $xml .= $this->sitemapEntry(url('/sitemap-categories.xml'));
        $xml .= $this->sitemapEntry(url('/sitemap-pages.xml'));
        $xml .= '</sitemapindex>';

        file_put_contents(public_path('sitemap.xml'), $xml);
    }

    private function sitemapEntry(string $url): string
    {
        return "<sitemap>\n<loc>{$url}</loc>\n<lastmod>" . now()->toAtomString() . "</lastmod>\n</sitemap>\n";
    }

    private function pingSearchEngines(): void
    {
        $sitemapUrl = urlencode(url('/sitemap.xml'));
        try {
            Http::timeout(10)->get("https://www.google.com/ping?sitemap={$sitemapUrl}");
        } catch (\Exception $e) {
            Log::warning('Google ping failed: ' . $e->getMessage());
        }
    }
}
