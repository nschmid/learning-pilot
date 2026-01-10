# 📚 LearningPilot

A modern Learning Path Management System built with Laravel 12 and the TALL stack (Tailwind CSS, Alpine.js, Livewire).

## Features

- **Role-based Access**: Admin, Instructor, and Learner roles
- **Learning Path Builder**: Create structured learning paths with modules and steps
- **Multi-format Content**: Support for text, video, audio, PDF, images, and interactive content
- **Assessment System**: 5 question types with auto-grading and time limits
- **Task Management**: File submissions with instructor review workflow
- **Progress Tracking**: Detailed analytics on time spent, completion, and points
- **Certificates**: Auto-generated PDF certificates upon completion
- **Gamification**: Points system for engagement
- **Search**: Full-text search powered by Meilisearch
- **Multilingual**: German, English, and French language support

## Tech Stack

| Component | Technology | Version |
|-----------|------------|---------|
| Backend | Laravel | 12.x |
| CSS | Tailwind CSS | 4.x |
| JavaScript | Alpine.js | 3.x |
| Reactivity | Livewire | 3.x |
| Database | PostgreSQL | 16.x |
| Cache/Queue | Redis | 7.x |
| Search | Laravel Scout + Meilisearch | - |
| PDF Generation | DomPDF | - |

## Requirements

- PHP 8.4+
- Composer 2.x
- Node.js 20+
- PostgreSQL 16+
- Redis (optional, for cache/queue)

## Installation

```bash
# Clone the repository
git clone <repository-url> learning-pilot
cd learning-pilot

# Run setup script
chmod +x setup.sh
./setup.sh

# Or manual setup:
composer install
npm install
cp .env.example .env
php artisan key:generate

# Configure database in .env, then:
php artisan migrate
php artisan db:seed

# Build assets
npm run build
```

## Development

```bash
# Start development server
php artisan serve

# Watch for asset changes (separate terminal)
npm run dev

# Run tests
php artisan test

# Code formatting
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse
```

## Project Structure

```
app/
├── Actions/        # Single-purpose action classes
├── Enums/          # PHP 8.1+ backed enums
├── Events/         # Domain events
├── Http/           # Controllers, Middleware, Requests
├── Livewire/       # Livewire components (Admin/Instructor/Learner/Shared)
├── Models/         # Eloquent models
├── Policies/       # Authorization policies
├── Repositories/   # Data access layer
└── Services/       # Business logic layer
```

## User Roles

| Role | Description |
|------|-------------|
| **Admin** | Full system access, user management, analytics |
| **Instructor** | Create/manage learning paths, review submissions |
| **Learner** | Browse catalog, enroll in paths, track progress |

## Database Models (18 entities)

**Core**: User, LearningPath, Category, Tag, Module, LearningStep

**Content**: LearningMaterial, Task, Assessment, Question, AnswerOption

**Progress**: Enrollment, StepProgress, TaskSubmission, AssessmentAttempt, QuestionResponse

**Features**: Prerequisite, ModuleDependency, Certificate, UserNote, Bookmark, PathReview

## Configuration

Key settings in `config/lernpfad.php`:

```php
'defaults' => [
    'passing_score' => 70,
    'max_assessment_attempts' => 3,
    'certificate_validity_years' => 2,
],
'materials' => [
    'max_file_size' => 100 * 1024 * 1024, // 100MB
],
'gamification' => [
    'enabled' => true,
    'points' => [
        'step_completion' => 10,
        'assessment_pass' => 50,
        'path_completion' => 200,
    ],
],
```

## API Documentation

API routes are available under `/api/v1/` with authentication via Laravel Sanctum.

```
GET    /api/v1/learning-paths        # List published paths
GET    /api/v1/learning-paths/{id}   # Path details
POST   /api/v1/enrollments           # Enroll in a path
GET    /api/v1/progress              # User progress
```

## Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Write tests for new functionality
4. Submit a pull request

## License

[MIT License](LICENSE)

---

Built with ❤️ using Laravel 12 TALL Stack
