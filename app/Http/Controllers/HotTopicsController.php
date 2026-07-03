<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\HotTopic;
use Illuminate\View\View;

class HotTopicsController extends Controller
{
    public function index(): View
    {
        $hotTopics = HotTopic::orderBy('id')->get();
        $hotTopicsByCategory = $hotTopics->groupBy(fn ($item) => $item->category?->value);
        $categories = Category::cases();

        return view('ciso/hot-topics/index', compact('hotTopics', 'hotTopicsByCategory', 'categories'));
    }

    public function show(HotTopic $hotTopic): View
    {
        $hotTopic->load('resources');

        return view('ciso/hot-topics/show', compact('hotTopic'));
    }
}
