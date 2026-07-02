<?php

namespace App\Http\Controllers;

use App\Models\HotTopic;
use Illuminate\View\View;

class HotTopicsController extends Controller
{
    public function index(): View
    {
        $hotTopics = HotTopic::orderBy('id')->get();

        return view('ciso/hot-topics/index', compact('hotTopics'));
    }

    public function show(HotTopic $hotTopic): View
    {
        $hotTopic->load('resources');

        return view('ciso/hot-topics/show', compact('hotTopic'));
    }
}
