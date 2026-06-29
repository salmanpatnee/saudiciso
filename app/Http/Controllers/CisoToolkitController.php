<?php

namespace App\Http\Controllers;

use App\Models\CisoToolkit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CisoToolkitController extends Controller
{
    public function index(): View
    {
        $toolkits = CisoToolkit::latest()->paginate(20);

        return view('process/ciso-toolkit/index', compact('toolkits'));
    }

    public function create(): View
    {
        $item = null;

        return view('process/ciso-toolkit/create', compact('item'));
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|extensions:pdf,doc,docx,txt|max:20480',
        ]);

        $file = $request->file('file');

        CisoToolkit::create([
            'title' => $attributes['title'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $file->store('ciso-toolkit', 'public'),
            'file_type' => $file->getClientMimeType(),
        ]);

        return redirect(route('admin.ciso-toolkit.index'))
            ->with('success', 'Toolkit saved successfully.');
    }

    public function edit(CisoToolkit $cisoToolkit): View
    {
        $item = $cisoToolkit;

        return view('process/ciso-toolkit/create', compact('item'));
    }

    public function update(Request $request, CisoToolkit $cisoToolkit): RedirectResponse
    {
        $attributes = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|extensions:pdf,doc,docx,txt|max:20480',
        ]);

        $data = ['title' => $attributes['title']];

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($cisoToolkit->file_path);

            $file = $request->file('file');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_path'] = $file->store('ciso-toolkit', 'public');
            $data['file_type'] = $file->getClientMimeType();
        }

        $cisoToolkit->update($data);

        return redirect(route('admin.ciso-toolkit.index'))
            ->with('success', 'Toolkit saved successfully.');
    }

    public function destroy(CisoToolkit $cisoToolkit): RedirectResponse
    {
        Storage::disk('public')->delete($cisoToolkit->file_path);
        $cisoToolkit->delete();

        return redirect(route('admin.ciso-toolkit.index'))
            ->with('success', 'Toolkit deleted successfully.');
    }
}
