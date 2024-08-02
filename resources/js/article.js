import * as bootstrap from 'bootstrap';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const articleList = document.getElementById('article-list');
    const pagination = document.getElementById('pagination');
    const articleModal = new bootstrap.Modal(document.getElementById('articleModal'));
    const articleForm = document.getElementById('article-form');
    const submitButton = document.getElementById('submit-button');
    const toastContainer = document.getElementById('toast-container');

    let currentPage = 1;

    // Fetch and display articles
    const fetchArticles = async (page = 1) => {
        try {
            const response = await axios.get(`/api/articles?page=${page}`);
            renderArticles(response.data.data); // Render articles
            renderPagination(response.data); // Render pagination controls
        } catch (error) {
            console.error('Error fetching articles:', error);
        }
    };

    // Render articles to the page
    const renderArticles = (articles) => {
        articleList.innerHTML = articles.map(article => `
        <li class="article-item mb-4">
           <div class="article-image-container">
                <a href="${article.image_name || '/images/default_thumbnail.png'}" class="image-link" target="_blank">
                    <img src="${article.image_name || '/images/default_thumbnail.png'}" alt="${article.title}" class="article-image">
                </a>
            </div>
            <div class="article-content">
                <h2 class="article-title">${article.title}</h2>
                <p class="article-description">${article.content}</p>
                <div class="article-buttons">
                    <button class="btn btn-secondary me-2" onclick="editArticle(${article.id})">Edit</button>
                    <button class="btn btn-danger" onclick="deleteArticle(${article.id})">Delete</button>
                </div>
            </div>
        </li>
        `).join('');
    };

    // Render pagination controls
    const renderPagination = (paginationData) => {
        const { current_page, last_page } = paginationData;
        let paginationHTML = '';

        if (last_page > 1) {
            paginationHTML += `<ul class="pagination justify-content-center">`;

            // Previous button
            if (current_page > 1) {
                paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${current_page - 1}">&laquo;</a></li>`;
            }

            // Page numbers
            for (let i = 1; i <= last_page; i++) {
                paginationHTML += `<li class="page-item ${i === current_page ? 'active' : ''}">
                                    <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }

            // Next button
            if (current_page < last_page) {
                paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${current_page + 1}">&raquo;</a></li>`;
            }

            paginationHTML += `</ul>`;
        }

        pagination.innerHTML = paginationHTML;

        // Add event listeners to pagination links
        document.querySelectorAll('.pagination .page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(link.getAttribute('data-page'));
                if (page && page !== currentPage) {
                    currentPage = page;
                    fetchArticles(currentPage);
                }
            });
        });
    };

    // Show Toast Message with fade-in effect and auto-hide after 10 seconds
    const showToast = (message, type) => {
        const toastHTML = `
            <div class="toast align-items-center text-bg-${type} border-0 show fade" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        // Append the toast HTML to the container
        toastContainer.innerHTML = toastHTML;

        // Initialize and show the toast
        const toastElement = toastContainer.querySelector('.toast');
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 10000 // Auto-hide after 10 seconds
        });
        toast.show();
    };

    // Function to handle POST request for creating a new article
const createArticle = async () => {
    const formData = new FormData(articleForm);

    try {
        const response = await axios.post('/api/articles', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.status === 201) {
            fetchArticles(currentPage); // Refresh article list
            articleModal.hide(); // Hide modal
            showToast('Article created successfully.', 'success'); // Show success toast
        } else {
            console.error('Error creating article:', response.data);
            showToast('Error creating article.', 'danger'); // Show error toast
        }
    } catch (error) {
        console.error('Error creating article:', error);
        showToast('Error creating article.', 'danger'); // Show error toast
    }
};

// Function to handle PUT request for updating an existing article
const updateArticle = async (id) => {
    const formData = new FormData();
    formData.append('title', document.getElementById('article-title').value);
    formData.append('content', document.getElementById('article-content').value);
    
    // Add image if present
    const imageFile = document.getElementById('article-image').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }

    try {
        const response = await axios.put(`/api/articles/${id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.status === 200) {
            fetchArticles(currentPage); // Refresh article list
            articleModal.hide(); // Hide modal
            showToast('Article updated successfully.', 'success'); // Show success toast
        } else {
            console.error('Error updating article:', response.data);
            showToast('Error updating article.', 'danger'); // Show error toast
        }
    } catch (error) {
        console.error('Error updating article:', error);
        showToast('Error updating article.', 'danger'); // Show error toast
    }
};


   // Handle form submission for create/update
articleForm.addEventListener('submit', async (event) => {
    event.preventDefault();

    const id = document.getElementById('article-id').value;

    if (id) {
        await updateArticle(id); // Update existing article
    } else {
        await createArticle(); // Create new article
    }
});


    // Open modal for creating a new article
    document.getElementById('create-article-button').addEventListener('click', () => {
        articleForm.reset();
        document.getElementById("article-id").setAttribute('value','');
        submitButton.textContent = 'Create Article'; // Set button text
        document.getElementById('articleModalLabel').textContent = 'Create Article'; // Set modal title
        articleModal.show(); // Show modal
    });

    // Edit an article
    window.editArticle = async (id) => {
        try {
            const response = await axios.get(`/api/articles/${id}`);
            const article = response.data;
            document.getElementById('article-id').value = article.id;
            document.getElementById('article-title').value = article.title;
            document.getElementById('article-content').value = article.content;
            document.getElementById('article-image').value = ''; // Reset file input
            submitButton.textContent = 'Update Article'; // Set button text
            document.getElementById('articleModalLabel').textContent = 'Edit Article'; // Set modal title
            articleModal.show(); // Show modal
        } catch (error) {
            console.error('Error fetching article:', error);
        }
    };


    // Delete an article
    window.deleteArticle = async (id) => {
        if (confirm('Are you sure you want to delete this article?')) {
            try {
                await axios.delete(`/api/articles/${id}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                fetchArticles(currentPage); // Refresh article list
                showToast('Article deleted successfully.', 'success'); // Show success toast
            } catch (error) {
                console.error('Error deleting article:', error);
                showToast('Error deleting article.', 'danger'); // Show error toast
            }
        }
    };

    // Initialize fetch
    fetchArticles(currentPage);
});
