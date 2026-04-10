<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', env('GEMINI_API_KEY'));
    }

    private function generate(string $prompt): string
    {
        $response = Http::timeout(30)->post("{$this->baseUrl}?key={$this->apiKey}", [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => [
                'temperature'     => 0.7,
                'maxOutputTokens' => 800,
            ],
        ]);

        if (!$response->successful()) {
            Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('خطأ في الاتصال بـ Gemini API');
        }

        return $response->json('candidates.0.content.parts.0.text', '');
    }

    public function generateMetaTitle(string $title, ?string $category = null, ?string $type = null): string
    {
        $prompt = "أجب باللغة العربية فقط. أنشئ عنوان meta SEO احترافي للصفحة التالية. يجب أن يكون العنوان بالعربية، لا يتجاوز 60 حرفاً، ويحتوي على كلمات البحث المهمة. أضف كلمة 'تحميل مجاني' عند الإمكان. لا تضف شرحاً، فقط العنوان المطلوب.\nعنوان الصفحة: {$title}\nالتصنيف: {$category}\nالنوع: {$type}";
        return trim($this->generate($prompt));
    }

    public function generateMetaDescription(string $title, ?string $excerpt = null, ?string $type = null): string
    {
        $text = $excerpt ? substr(strip_tags($excerpt), 0, 300) : '';
        $prompt = "أجب باللغة العربية فقط. أنشئ وصف meta SEO احترافي للصفحة التالية. يجب أن يكون الوصف بالعربية، لا يتجاوز 155 حرفاً، ويشجع على النقر ويحتوي على دعوة للتصرف مثل 'تحميل مجاني'. لا تضف شرحاً، فقط الوصف المطلوب.\nعنوان الصفحة: {$title}\nمحتوى مختصر: {$text}";
        return trim($this->generate($prompt));
    }

    public function generateMetaKeywords(string $title, ?string $content = null, ?string $category = null): string
    {
        $text = $content ? substr(strip_tags($content), 0, 500) : '';
        $prompt = "أجب باللغة العربية فقط. أنشئ قائمة من 8 إلى 10 كلمات مفتاحية SEO باللغة العربية مفصولة بفواصل للصفحة التالية. لا تضف شرحاً، فقط الكلمات المفتاحية.\nعنوان الصفحة: {$title}\nالتصنيف: {$category}\nمحتوى: {$text}";
        return trim($this->generate($prompt));
    }

    public function generateExcerpt(string $content): string
    {
        $text = substr(strip_tags($content), 0, 1000);
        $prompt = "أجب باللغة العربية فقط. أنشئ مقتطفاً مختصراً من جملتين باللغة العربية للمحتوى التالي. المقتطف يجب أن يكون جذاباً ويشجع على القراءة. لا تضف شرحاً.\nالمحتوى: {$text}";
        return trim($this->generate($prompt));
    }

    public function checkReadability(string $content): string
    {
        $text = substr(strip_tags($content), 0, 2000);
        $prompt = "أجب باللغة العربية فقط. قيّم مستوى سهولة قراءة النص العربي التالي وقدم اقتراحات للتحسين. أذكر نقاط القوة والضعف باختصار.\nالنص: {$text}";
        return trim($this->generate($prompt));
    }

    public function suggestInternalLinks(string $postTitle, array $recentPostTitles): string
    {
        $titles = implode("\n- ", $recentPostTitles);
        $prompt = "أجب باللغة العربية فقط. من قائمة المقالات التالية، اقترح أفضل 3-5 مقالات ذات صلة يمكن ربطها داخلياً بمقال بعنوان: '{$postTitle}'. أذكر فقط العناوين المقترحة دون شرح إضافي.\nقائمة المقالات:\n- {$titles}";
        return trim($this->generate($prompt));
    }
}
