<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if ($request->file()) {
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
            $fileDomain = $request->getSchemeAndHttpHost();

            $file = new File;
            $file->file_name = $fileName;
            $file->file_path = '/storage/' . $filePath;
            $file->file_domain = $fileDomain;
            $file->save();

            return response()->json(['success' => 'File uploaded successfully.', 'file' => $file], 200);
        }

        return response()->json(['error' => 'No file was uploaded.'], 400);
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg, pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->file()) {
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
            $fileDomain = $request->getSchemeAndHttpHost(); // Get the protocol and domain

            $file = new File;
            $file->file_name = $fileName;
            $file->file_path = '/storage/' . $filePath;
            $file->file_domain = $fileDomain;
            $file->save();

            $fileUrl = $fileDomain . $file->file_path;

            return redirect()->back()->with('success', 'File uploaded successfully.')->with('fileUrl', $fileUrl);
        }

        return redirect()->back()->with('error', 'No file was uploaded.');
    }
}
