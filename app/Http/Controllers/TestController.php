<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{

    public function handle(Request $request)
    {
        // Return the request data for debugging
        return response()->json([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);
    }

    
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $path = $file->store('test-uploads', 's3');
                dd($path);

                return response()->json([
                    'message' => 'File uploaded successfully',
                    'path' => $path
                ]);
            } catch (\Exception $e) {
                Log::error('Upload failed: ' . $e->getMessage());
                return response()->json(['message' => 'Upload failed: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
