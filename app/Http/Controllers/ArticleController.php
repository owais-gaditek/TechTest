<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Aws\Lambda\LambdaClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a paginated list of articles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Paginate articles with 12 articles per page
        $articles = Article::paginate(12);
        return response()->json($articles);
    }

    /**
     * Display the articles index view for web requests.
     *
     * @return \Illuminate\View\View
     */
    public function indexView()
    {
        // Return the view without JSON response
        return view('articles.index');
    }

    /**
     * Store a newly created article in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string|max:400',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Create a new article instance
        $article = new Article();
        $article->title = $validatedData['title'];
        $article->content = $validatedData['content'];

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            Log::info('Attempting to upload file', ['filename' => $image->getClientOriginalName()]);

            try {
                // Store the file on S3
                $path = $image->store('articles', 's3');
                Log::info('File upload result', ['path' => $path]);

                if ($path) {
                    // Construct the S3 URL for the uploaded image
                    $s3Url = env('AWS_URL');
                    $article->image_name = $s3Url ? $s3Url . '/' . $path : Storage::disk('s3')->url($path);

                    // Invoke Lambda function to process the image
                    $lambdaClient = new LambdaClient([
                        'version' => 'latest',
                        'region'  => env('AWS_DEFAULT_REGION'), // AWS region
                    ]);

                    $result = $lambdaClient->invoke([
                        'FunctionName' => 'ImageProcessingFunction',
                        'InvocationType' => 'Event',
                        'Payload' => json_encode(['image_path' => $path]),
                    ]);

                    Log::info('Lambda invocation result', ['result' => $result]);
                } else {
                    Log::error('Image upload path is empty. Path: ' . $path);
                    return response()->json(['message' => 'Failed to upload image.'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } catch (\Exception $e) {
                Log::error('Exception during file upload', ['exception' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to upload image.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            // No image file provided, no need to return an error
            $article->image_name = null; // Ensure that image_name is set to null
        }

        // Save the article and return a success response
        $article->save();
        return response()->json(['message' => 'Article created successfully!', 'article' => $article], response::HTTP_CREATED);
    }


    /**
     * Display a specific article by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($article, 200);
    }

    /**
     * Update the specified article in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        // Validate incoming request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string|max:400',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update article attributes
        $article->title = $validatedData['title'];
        $article->content = $validatedData['content'];

        // Handle file upload
        if ($request->hasFile('image')) {
            try {
                // Delete the old image if it exists
                if ($article->image && Storage::disk('s3')->exists($article->image)) {
                    Storage::disk('s3')->delete($article->image);
                }

                // Store the new image
                $imagePath = $request->file('image')->store('articles', 's3');

                // Construct the S3 URL for the new image
                $s3Url = env('AWS_URL');
                $article->image_name = $s3Url ? $s3Url . '/' . $imagePath : Storage::disk('s3')->url($imagePath);

                // Optionally invoke Lambda function if needed
                $lambdaClient = new LambdaClient([
                    'version' => 'latest',
                    'region'  => env('AWS_DEFAULT_REGION'), // AWS region
                ]);
                
                $result = $lambdaClient->invoke([
                    'FunctionName' => 'ImageProcessingFunction',
                    'InvocationType' => 'Event',
                    'Payload' => json_encode(['image_path' => $imagePath]),
                ]);
                
                Log::info('Lambda invocation result', ['result' => $result]);

            } catch (\Exception $e) {
                Log::error('Exception during file upload', ['exception' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to upload image.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        // Save the updated article
        $article->save();

        return response()->json(['message' => 'Article updated successfully!', 'article' => $article], Response::HTTP_OK);
    }

    /**
     * Remove the specified article from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        // Delete the image from S3
        if ($article->image) {
            Storage::disk('s3')->delete($article->image);
        }

        // Delete the article
        $article->delete();

        return response()->json(['message' => 'Article deleted successfully!']);
    }
}
