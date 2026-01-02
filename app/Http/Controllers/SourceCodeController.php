<?php

namespace App\Http\Controllers;

use App\Models\SourceCode;
use Illuminate\Http\Request;

class SourceCodeController extends Controller
{
    /**
     * Hiển thị danh sách source code (frontend)
     */
    public function index()
    {
        $sourceCodes = SourceCode::orderBy('id', 'desc')->get();
        return view('pages.source-code', compact('sourceCodes'));
    }
}

