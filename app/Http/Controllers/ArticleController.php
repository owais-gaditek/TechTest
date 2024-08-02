<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Aws\Lambda\LambdaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    // Get all articles
    public function index()
    {
        $articles = Article::paginate(12); // 12 articles per page
        return response()->json($articles);
        // return response()->json(Article::all());
    }

    public function indexView()
    {
        // For web requests, return the view without JSON response
        return view('articles.index');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $article = new Article();
        $article->title = $validatedData['title'];
        $article->content = $validatedData['content'];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            Log::info('Attempting to upload file', ['filename' => $image->getClientOriginalName()]);

            try {
                // Store the file on S3
                $path = $image->store('articles', 's3');
                Log::info('File upload result', ['path' => $path]);

                if ($path) {
                    // Use the URL directly or construct it manually
                    $s3Url = env('AWS_URL');
                    $article->image_name = $s3Url ? $s3Url . '/' . $path : Storage::disk('s3')->url($path);

                    // Invoke Lambda function
                    $lambdaClient = new LambdaClient([
                        'version' => 'latest',
                        'region'  => 'us-east-2', // Update with your AWS region
                    ]);

                    $result = $lambdaClient->invoke([
                        'FunctionName' => 'ImageProcessingFunction', // Update with your Lambda function name
                        'InvocationType' => 'Event', // Use 'RequestResponse' if you need synchronous invocation
                        'Payload' => json_encode(['image_path' => $path]),
                    ]);

                    Log::info('Lambda invocation result', ['result' => $result]);
                } else {
                    Log::error('Image upload path is empty. Path: ' . $path);
                    return response()->json(['message' => 'Failed to upload image.'], 500);
                }
            } catch (\Exception $e) {
                Log::error('Exception during file upload', ['exception' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to upload image.'], 500);
            }
        } else {
            Log::error('No image file found in request.');
            return response()->json(['message' => 'No image file found.'], 400);
        }

        $article->save();

        return response()->json(['message' => 'Article created successfully!', 'article' => $article], 201);
    }


    // Get a specific article
    public function show($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        return response()->json($article, 200);
    }

    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        // Validate the request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update the article
        $article->title = $validatedData['title'];
        $article->content = $validatedData['content'];

        // Handle file upload
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($article->image && Storage::disk('s3')->exists($article->image)) {
                Storage::disk('s3')->delete($article->image);
            }

            // Store the new image
            $imagePath = $request->file('image')->store('articles', 's3');
            $article->image = $imagePath;
        }

        $article->save();

        // Generate the full URL for the image
        $article->image_url = Storage::disk('s3')->url($article->image);

        return response()->json($article, 200);
    }



    // Delete an article
    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        // Delete the image from S3
        if ($article->image_path) {
            Storage::disk('s3')->delete($article->image_path);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted successfully!']);

    }
}
