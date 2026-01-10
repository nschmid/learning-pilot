# LearningPilot Implementation Tasks

Use this file to track implementation progress. Check off items as they are completed.

## Phase 1: Foundation (Week 1)

### Project Setup
- [ ] Create Laravel 12 project
- [ ] Install Livewire 3
- [ ] Install Laravel Jetstream (Livewire flavor)
- [ ] Install Tailwind CSS 4
- [ ] Install additional packages (DomPDF, Scout, Meilisearch)
- [ ] Configure .env

### Authentication & Authorization
- [ ] Modify users migration (add role, avatar, is_active fields)
- [ ] Create UserRole enum
- [ ] Update User model with role casts and helper methods
- [ ] Create RoleMiddleware for route protection
- [ ] Create UserPolicy
- [ ] Setup role-based dashboard redirect

### Database Structure
- [ ] Create all 23 migrations (see docs/MIGRATIONS.md)
- [ ] Create all Enums (11 files)
- [ ] Create all Models (18 models) with:
  - [ ] Relationships
  - [ ] Casts
  - [ ] Scopes
  - [ ] Accessors/Mutators
- [ ] Create model factories
- [ ] Create seeders with test data

### Layouts & Views
- [ ] Create base app layout
- [ ] Create admin layout
- [ ] Create instructor layout
- [ ] Create learner layout
- [ ] Create navigation components
- [ ] Setup Tailwind with brand colors

## Phase 2: Learning Path Builder (Week 2-3)

### Instructor Dashboard
- [ ] Create Livewire\Instructor\Dashboard component
- [ ] Display my learning paths summary
- [ ] Show recent enrollments
- [ ] Display pending submission reviews
- [ ] Quick actions menu

### Learning Path Management
- [ ] Create Livewire\Instructor\LearningPaths\Index
  - [ ] List paths with filters (status, category)
  - [ ] Search functionality
  - [ ] Sorting options
  - [ ] Bulk actions
- [ ] Create Livewire\Instructor\LearningPaths\Create
  - [ ] Basic info form (title, description, category)
  - [ ] Difficulty selection
  - [ ] Tag input
  - [ ] Thumbnail upload
- [ ] Create Livewire\Instructor\LearningPaths\Edit
  - [ ] All create features
  - [ ] Version management
- [ ] Create Livewire\Instructor\LearningPaths\Builder
  - [ ] Module management (add/edit/delete/reorder)
  - [ ] Step management per module
  - [ ] Drag-and-drop ordering
  - [ ] Preview mode
  - [ ] Publish/unpublish toggle

### Module & Step Management
- [ ] Create Livewire\Instructor\Modules\ModuleManager
  - [ ] Add/edit module modal
  - [ ] Delete with confirmation
  - [ ] Reorder via drag-drop
  - [ ] Set unlock conditions
- [ ] Create Livewire\Instructor\Modules\StepEditor
  - [ ] Step type selection (material/task/assessment)
  - [ ] Points value setting
  - [ ] Estimated time
  - [ ] Required toggle

### Materials
- [ ] Create Livewire\Instructor\Materials\MaterialUploader
  - [ ] Multi-file upload
  - [ ] Progress indicator
  - [ ] File type validation
  - [ ] Preview generation
- [ ] Create Livewire\Instructor\Materials\MaterialLibrary
  - [ ] Browse uploaded files
  - [ ] Filter by type
  - [ ] Reuse in multiple steps
  - [ ] Delete with usage check

### Category & Tag Management
- [ ] Create admin interface for categories (hierarchical)
- [ ] Create admin interface for tags
- [ ] Wire up to learning path forms

## Phase 3: Learner Interface (Week 4-5)

### Catalog
- [ ] Create Livewire\Learner\Catalog\Browse
  - [ ] Grid/list view toggle
  - [ ] Category filter (sidebar)
  - [ ] Tag filter
  - [ ] Difficulty filter
  - [ ] Search with Meilisearch
  - [ ] Sorting (popularity, rating, newest)
  - [ ] Pagination
- [ ] Create Livewire\Learner\Catalog\PathDetail
  - [ ] Hero section with thumbnail
  - [ ] Description and objectives
  - [ ] Module/step outline
  - [ ] Instructor info
  - [ ] Reviews section
  - [ ] Enroll button / Continue learning button
  - [ ] Prerequisites check

### Learning Interface
- [ ] Create Livewire\Learner\Learning\PathProgress
  - [ ] Overall progress bar
  - [ ] Module list with completion status
  - [ ] Current step indicator
  - [ ] Time spent tracking
  - [ ] Points earned
- [ ] Create Livewire\Learner\Learning\StepViewer
  - [ ] Dynamic content based on step_type
  - [ ] Navigation (prev/next)
  - [ ] Mark as complete button
  - [ ] Time tracking (auto-track while viewing)
  - [ ] Bookmark toggle
  - [ ] Notes panel

### Content Viewers
- [ ] Create Livewire\Learner\Learning\MaterialViewer
  - [ ] Text/HTML renderer
  - [ ] Video player (native HTML5)
  - [ ] Audio player
  - [ ] PDF viewer (PDF.js or iframe)
  - [ ] Image viewer with zoom
  - [ ] External link handler
- [ ] Create VideoPlayer component with:
  - [ ] Play/pause
  - [ ] Progress tracking
  - [ ] Playback speed
  - [ ] Fullscreen
  - [ ] Auto-mark complete at X%
- [ ] Create PdfViewer component with:
  - [ ] Page navigation
  - [ ] Zoom controls
  - [ ] Download option

### Bookmarks & Notes
- [ ] Create Livewire\Learner\Notes component
  - [ ] Add/edit notes per step
  - [ ] View all notes for path
  - [ ] Search notes
  - [ ] Export notes
- [ ] Create bookmark functionality
  - [ ] Quick bookmark from step viewer
  - [ ] View all bookmarks
  - [ ] Remove bookmark

## Phase 4: Assessment System (Week 6)

### Assessment Builder (Instructor)
- [ ] Create Livewire\Instructor\Assessments\AssessmentBuilder
  - [ ] Assessment settings (time limit, passing score, attempts)
  - [ ] Question list with reorder
  - [ ] Add question modal
  - [ ] Preview mode
- [ ] Create Livewire\Instructor\Assessments\QuestionEditor
  - [ ] Question type selection
  - [ ] Question text (rich text)
  - [ ] Answer options (dynamic add/remove)
  - [ ] Correct answer marking
  - [ ] Points assignment
  - [ ] Explanation field
- [ ] Create Livewire\Instructor\Assessments\QuestionBank
  - [ ] Browse questions across assessments
  - [ ] Filter by type
  - [ ] Import into assessment
  - [ ] Bulk delete

### Assessment Taking (Learner)
- [ ] Create Livewire\Learner\Assessments\AssessmentTaker
  - [ ] Start assessment screen
  - [ ] Timer countdown (if time limit)
  - [ ] Question navigation
  - [ ] Answer selection/input
  - [ ] Submit confirmation
  - [ ] Auto-submit on timeout
- [ ] Create Livewire\Learner\Assessments\QuizQuestion
  - [ ] Single choice (radio)
  - [ ] Multiple choice (checkbox)
  - [ ] True/false
  - [ ] Text input
  - [ ] Matching (drag-drop)
- [ ] Create Livewire\Learner\Assessments\ResultsView
  - [ ] Score display
  - [ ] Pass/fail indicator
  - [ ] Question review with explanations
  - [ ] Points breakdown
  - [ ] Retry button (if attempts remain)

### Grading Service
- [ ] Create AssessmentGradingService
  - [ ] Grade answers by type
  - [ ] Calculate score percentage
  - [ ] Determine pass/fail
  - [ ] Update step progress
  - [ ] Award points

## Phase 5: Tasks & Certificates (Week 7)

### Task System
- [ ] Create Livewire\Instructor\Tasks\TaskEditor
  - [ ] Task type selection
  - [ ] Instructions (rich text)
  - [ ] Max points setting
  - [ ] Due date/days
  - [ ] Allowed file types
  - [ ] Rubric builder (optional)
- [ ] Create Livewire\Instructor\Tasks\SubmissionReview
  - [ ] List pending submissions
  - [ ] View submission content/files
  - [ ] Score input
  - [ ] Feedback field
  - [ ] Approve/request revision

### Task Submission (Learner)
- [ ] Create Livewire\Learner\Tasks\TaskView
  - [ ] Instructions display
  - [ ] Due date countdown
  - [ ] Previous submission status
- [ ] Create Livewire\Learner\Tasks\TaskSubmission
  - [ ] Text input area
  - [ ] File upload (with validation)
  - [ ] Preview before submit
  - [ ] Submit confirmation
  - [ ] Resubmit option

### Certificates
- [ ] Create CertificateGeneratorService
  - [ ] Generate unique certificate number
  - [ ] Create PDF with DomPDF
  - [ ] Include completion data
  - [ ] Store PDF file
- [ ] Create certificate PDF template
  - [ ] Professional design
  - [ ] User name
  - [ ] Path title
  - [ ] Completion date
  - [ ] Certificate number
  - [ ] QR code for verification
- [ ] Create Livewire\Learner\Certificates\Index
  - [ ] List earned certificates
  - [ ] Download PDF
  - [ ] Share link
- [ ] Create certificate verification page (public)
  - [ ] Verify by certificate number
  - [ ] Display validity

## Phase 6: Analytics & Polish (Week 8-9)

### Admin Dashboard
- [ ] Create Livewire\Admin\Dashboard
  - [ ] Total users (by role)
  - [ ] Total paths (published/draft)
  - [ ] Total enrollments
  - [ ] Completion rate
  - [ ] Revenue (if applicable)
- [ ] Create Livewire\Admin\UserManagement
  - [ ] User list with filters
  - [ ] Role management
  - [ ] Activate/deactivate
  - [ ] Impersonate user
- [ ] Create Livewire\Admin\Analytics
  - [ ] Enrollment trends (chart)
  - [ ] Popular paths
  - [ ] User activity
  - [ ] Assessment performance

### Instructor Reports
- [ ] Create Livewire\Instructor\Reports\EnrollmentStats
  - [ ] Enrollments per path
  - [ ] Completion rates
  - [ ] Drop-off points
- [ ] Create Livewire\Instructor\Reports\LearnerProgress
  - [ ] Per-learner progress
  - [ ] Time spent analysis
  - [ ] Assessment scores

### Learner Analytics
- [ ] Create Livewire\Learner\Progress\MyProgress
  - [ ] Overall statistics
  - [ ] Points/badges
  - [ ] Learning streak
  - [ ] Time spent this week
  - [ ] Achievements

### Search Enhancement
- [ ] Configure Laravel Scout
- [ ] Setup Meilisearch index
- [ ] Make LearningPath searchable
- [ ] Create search UI component
- [ ] Add search suggestions

### Performance & Testing
- [ ] Add database indexes for common queries
- [ ] Implement query caching
- [ ] Optimize Livewire polling
- [ ] Write feature tests for:
  - [ ] Authentication flows
  - [ ] Learning path CRUD
  - [ ] Enrollment process
  - [ ] Assessment taking
  - [ ] Task submission
  - [ ] Certificate generation
- [ ] Write unit tests for:
  - [ ] Services
  - [ ] Actions
  - [ ] Models
- [ ] Load testing

### Final Polish
- [ ] Review UI consistency
- [ ] Mobile responsiveness check
- [ ] Accessibility audit
- [ ] Error handling improvement
- [ ] User feedback integration
- [ ] Documentation update

---

## Quick Commands Reference

```bash
# Create a Livewire component
php artisan make:livewire Instructor/LearningPaths/Create

# Create a model with migration
php artisan make:model LearningPath -m

# Create a service
# (manual: app/Services/LearningPathService.php)

# Create a policy
php artisan make:policy LearningPathPolicy --model=LearningPath

# Create a form request
php artisan make:request StoreLearningPathRequest

# Create an event
php artisan make:event PathCompleted

# Run specific tests
php artisan test --filter=LearningPathTest
```

---

**Current Focus:** Phase 1 - Foundation

**Last Updated:** [DATE]
