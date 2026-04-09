<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function summernoteUpload(Request $request)
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/content', 'public');
            
            // ❌ កូដចាស់: ប្រើ asset() ដែលជាប់ Domain 127.0.0.1
            // return response()->json(['url' => asset('storage/' . $path)]);
            
            // ✅ កូដថ្មី: ប្រើត្រឹម Root Path (វានឹងឆ្លាត ស្គាល់ Domain ដោយស្វ័យប្រវត្តិពេលយកទៅប្រើ)
            return response()->json(['url' => '/storage/' . $path]);
        }
        
        return response()->json(['error' => 'Upload failed'], 400);
    }
}