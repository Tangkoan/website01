<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function summernoteUpload(Request $request)
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/content', 'public');
            return response()->json(['url' => '/storage/' . $path]);
        }
        
        return response()->json(['error' => 'Upload failed'], 400);
    }
}