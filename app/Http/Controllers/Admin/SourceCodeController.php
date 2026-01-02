<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SourceCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SourceCodeController extends Controller
{
    /**
     * Display a listing of source code products
     */
    public function index()
    {
        $sourceCodes = SourceCode::orderBy('id', 'desc')->get();
        return view('admin.sourcecode.index', compact('sourceCodes'));
    }

    /**
     * Show the form for creating a new source code
     */
    public function create()
    {
        // Get available images from images folder
        $imagesPath = public_path('images');
        $availableImages = [];
        
        if (is_dir($imagesPath)) {
            $files = scandir($imagesPath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                    $availableImages[] = $file;
                }
            }
        }
        
        return view('admin.sourcecode.create', compact('availableImages'));
    }

    /**
     * Store a newly created source code in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'file' => 'nullable|file|mimes:zip,rar,tar,gz|max:102400', // Max 100MB
            'download_link' => 'nullable|url',
        ]);

        $filePath = null;
        
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            // Store in storage/app/public/source-code and save relative path
            $filePath = $file->storeAs('source-code', $fileName, 'public');
        }

        SourceCode::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'price' => $request->price,
            'image' => $request->image ? 'images/' . basename($request->image) : null,
            'file_path' => $filePath,
            'download_link' => $request->download_link,
            'time' => date('d/m/Y - H:i:s'),
        ]);

        return redirect()->route('admin.sourcecode.index')
            ->with('success', 'Source code đã được thêm thành công!');
    }

    /**
     * Show the form for editing the specified source code
     */
    public function edit($id)
    {
        $sourceCode = SourceCode::findOrFail($id);
        
        // Get available images from images folder
        $imagesPath = public_path('images');
        $availableImages = [];
        
        if (is_dir($imagesPath)) {
            $files = scandir($imagesPath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                    $availableImages[] = $file;
                }
            }
        }
        
        return view('admin.sourcecode.edit', compact('sourceCode', 'availableImages'));
    }

    /**
     * Update the specified source code in storage
     */
    public function update(Request $request, $id)
    {
        $sourceCode = SourceCode::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'file' => 'nullable|file|mimes:zip,rar,tar,gz|max:102400', // Max 100MB
            'download_link' => 'nullable|url',
        ]);

        $filePath = $sourceCode->file_path;
        
        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($sourceCode->file_path) {
                Storage::disk('public')->delete($sourceCode->file_path);
            }
            
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            // Store in storage/app/public/source-code and save relative path
            $filePath = $file->storeAs('source-code', $fileName, 'public');
        }

        $sourceCode->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'price' => $request->price,
            'image' => $request->image ? 'images/' . basename($request->image) : $sourceCode->image,
            'file_path' => $filePath,
            'download_link' => $request->download_link,
        ]);

        return redirect()->route('admin.sourcecode.index')
            ->with('success', 'Source code đã được cập nhật thành công!');
    }

    /**
     * Remove the specified source code from storage
     */
    public function destroy($id)
    {
        $sourceCode = SourceCode::findOrFail($id);
        
        // Delete file if exists
        if ($sourceCode->file_path) {
            Storage::disk('public')->delete($sourceCode->file_path);
        }
        
        $sourceCode->delete();

        return redirect()->route('admin.sourcecode.index')
            ->with('success', 'Source code đã được xóa thành công!');
    }
}
