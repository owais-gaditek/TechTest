<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Articles</title>

    <!-- Compiled CSS -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <style>
        /* Custom styles for pagination */
        .pagination-container ul {
            display: flex;
            justify-content: center;
            padding: 0;
        }

        .pagination-container li {
            margin: 0 5px;
        }

        .pagination-container .page-item {
            cursor: pointer;
        }

        .pagination-container .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <header class="bg-primary text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h2 mb-0">We.Connect Articles</h1>
            <button id="create-article-button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#articleModal">
                Add New Article
            </button>
        </div>
    </header>

    <!-- Main Content Section -->
    <main class="container mt-4">
        <div id="article-list-container">
            <ul id="article-list" class="list-unstyled">
                <!-- Articles will be inserted here by JavaScript -->
            </ul>
        </div>
        <div id="pagination" class="pagination-container mt-4">
            <!-- Pagination controls will be inserted here by JavaScript -->
        </div>
    </main>

    <!-- Bootstrap Modal for Creating and Updating Articles -->
    <div class="modal fade" id="articleModal" tabindex="-1" aria-labelledby="articleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="articleModalLabel">Create Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="article-form" enctype="multipart/form-data">
                        <input type="hidden" id="article-id" name="id">
                        <div class="mb-3">
                            <label for="article-title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="article-title" name="title" placeholder="Enter article title" required>
                        </div>
                        <div class="mb-3">
                            <label for="article-content" class="form-label">Content</label>
                            <textarea class="form-control" id="article-content" name="content" rows="5" placeholder="Enter article content" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="article-image" class="form-label">Image (optional)</label>
                            <input type="file" class="form-control" id="article-image" name="image">
                        </div>
                        <button type="submit" id="submit-button" class="btn btn-primary">Create Article</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3">
        <!-- Toast notifications will be inserted here by JavaScript -->
    </div>

    <!-- Compiled JavaScript -->
    <script src="{{ mix('js/article.js') }}" defer></script>
</body>

</html>