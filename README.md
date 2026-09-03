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