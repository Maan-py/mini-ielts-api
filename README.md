## Database Schema

Ada 2 tabel:

### questions

| Column | Type | Description |
|---|---|---|
| id | BIGINT | Primary key |
| part | INTEGER | IELTS Speaking part |
| topic | VARCHAR | Question topic |
| prompt | TEXT | Speaking question |

### attempts

| Column | Type | Description |
|---|---|---|
| id | BIGINT | Primary key |
| question_id | BIGINT | Foreign key referencing questions.id |
| answer_text | TEXT | Student's speaking answer |
| estimated_band | DECIMAL(2,1) | Estimated IELTS Speaking band |
| strengths | TEXT/JSON | Strengths identified by AI |
| areas_to_improve | TEXT/JSON | Areas for improvement identified by AI |
| created_at | TIMESTAMP | Attempt creation time |
| updated_at | TIMESTAMP | Last update time |

### Relationship

One `questions` record can have many `attempts`.

`attempts.question_id` references `questions.id`.

Ketika question dihapus, attempts yang berhubungan juga dihapus.

## instruksi singkat cara menjalankan project

# 1. Clone Repository
git clone <repository-url>
cd <project-folder>

# 2. Install Dependencies

Install PHP dependencies:

composer install

Install frontend dependencies:

npm install
# 3. Configure Environment

Copy the environment file:

cp .env.example .env

Generate Laravel application key:

php artisan key:generate

Set the required GEMINI_API_KEY in .env.

# 4. Setup Database

Create the SQLite database:

touch database/database.sqlite

Run migrations and seed the sample questions:

php artisan migrate
php artisan db:seed --class=QuestionSeeder

# 5. Run the Application

Start the Laravel server:

php artisan serve

In another terminal, start the Vite development server:

npm run dev

The application can then be accessed at:

http://localhost:8000

# 6. Run Tests
php artisan test