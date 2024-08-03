**Technical Documentation for "TechTest"**

**Installation:**
composer install
npm install
npm run dev

**Overview**
This documentation contains a comprehensive guide for the "TechTest" project as given by We
Connect, including backend development with Laravel, frontend development with JavaScript
and CSS preprocessors, AWS integration, algorithm complexity analysis, and PL/SQL script
implementation. The goal is to implement a complete solution that involves a RESTful API, a
web page for displaying articles, AWS services integration, and optimized algorithms.

**Tools and Technologies Used:**
I have used all the latest stable versions as instructed in the requirements
• PHP 8
• Laravel 9
• JavaScript
• MySQL
• LESS and CSS preprocessors for front end
• AWS Services (EC2, S3, Lambda, RDS)
• GIT

**Project Access and Repository:**
• Website URL: [TechTest Live Site](http://3.138.67.50/articles)

**DIRECT LIVE URL’s:**
http://3.138.67.50/articles

**Detailed Documentation with Steps:***
**1. Backend Development**
**1.1. Laravel Project Setup**
**1. Create a Laravel Project:**
composer create-project --prefer-dist laravel/laravel TechTest "9.*"

**2. Set Up Environment:**
 Configured the .env file with database credentials and other environment settings.

**1.2. Implement RESTful API**
**1. Migration for Articles Table:**

php artisan migrate

**2. Seed the Articles Table:**
php artisan db:seed –-class=ArticlesTableSeeder
Seeder File (database/seeders/ArticlesTableSeeder.php):

**3. CRUD Operations:**
**Controller:**
There is an ArticleController which has all the CRUD operations as required

**Registered Routes (routes/api.php):**
Route::apiResource('articles', ArticleController::class);

**2. Frontend Development**
**2.1. Create Web Page**
**1. HTML Template (resources/views/articles/index.blade.php):**
There is an index.blade.php file that includes the front-end html of the articles
There is also an fibonacci.blade.php file that can be checked from the front end as well

**2. JavaScript For Articles (resources/js/article.js):**
It contains the JavaScript file for the article operations and utilizing the API’s operations

**3. SCSS Styling (resources/sass/app.scss):**
It contains the scss preprocessor file for all the related styling used

**3. AWS Integration**
**3.1. Configure AWS Services**
**1. AWS EC2:**
• Created EC2 instance and deployed the project for “TechTest”.
• Set up security groups to allow HTTP/HTTPS traffic and others as needed.

**2. AWS S3:**
• Created an S3 bucket for storing article images.
• Configure Laravel to use S3 for file storage on the Application side.

**3. AWS Lambda:**
• Set up Lambda functions for serverless backend logic that is doing the
Optimization of the file uploading on S3 bucket.

**4. AWS RDS:**
• Create an RDS instance for the database.
• Provide security permissions as needed.
• The .env file contains all the credentials needed in application to use RDS for the
database connection.

**4. Algorithm Complexity**
**4.1. Fibonacci Sequence Function**
**1. PHP Function:**
function fibonacci($n) {
 $fib = [0, 1];
 for ($i = 2; $i <= $n; $i++) {
 $fib[$i] = $fib[$i - 1] + $fib[$i - 2];
 }
 return $fib;
}

**2. Time Complexity:**
o The time complexity of this implementation is O(n), where n is the input number.

**5. PL/SQL**
**5.1. Stored Procedure**
**1. PL/SQL Script:**
CREATE OR REPLACE PROCEDURE GetArticleById (p_id IN NUMBER, p_article
OUT SYS_REFCURSOR) AS
BEGIN
 OPEN p_article FOR
 SELECT * FROM articles WHERE id = p_id;
END GetArticleById;

**6. Documentation and Communication**
**6.1. Summary of Approach**
• Backend Development: Created a Laravel project with a RESTful API for managing
articles. Implemented CRUD operations and seeded the database with sample data.
• Frontend Development: Built a simple web page to display articles, using JavaScript to
fetch data from the API and SCSS for styling.
• AWS Integration: Configured AWS services including EC2 for hosting, S3 for storage,
Lambda for serverless functions, and RDS for the database backend.
• Algorithm Complexity: Implemented a Fibonacci sequence function with O(n) time
complexity.
• PL/SQL: Created a stored procedure to fetch articles by ID.

**6.2. Challenges and Solutions**
• Challenge: Ensuring the API and frontend were correctly integrated.
• Solution: Implemented comprehensive testing and debugging to verify data flow
between the frontend and backend.
