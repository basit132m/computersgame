<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;

class GeminiController extends Controller
{
    public function generate(Request $request, GeminiService $gemini)
    {
        $request->validate([
            'action'   => 'required|string|in:meta_title,meta_description,meta_keywords,excerpt,readability',
            'title'    => 'nullable|string|max:500',
            'content'  => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'type'     => 'nullable|string|max:50',
        ]);

        try {
            $result = match ($request->action) {
                'meta_title'       => $gemini->generateMetaTitle($request->title, $request->category, $request->type),
                'meta_description' => $gemini->generateMetaDescription($request->title, $request->content, $request->type),
                'meta_keywords'    => $gemini->generateMetaKeywords($request->title, $request->content, $request->category),
                'excerpt'          => $gemini->generateExcerpt($request->content),
                'readability'      => $gemini->checkReadability($request->content),
                default            => throw new \InvalidArgumentException('إجراء غير معروف'),
            };

            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => 'حدث خطأ، يرجى المحاولة مرة أخرى',
            ], 500);
        }
    }
}
