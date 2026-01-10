# Enums Reference

All enums should be created in `app/Enums/`. Use PHP 8.1+ backed enums.

## Create Command

```bash
mkdir -p app/Enums
```

---

## UserRole.php

```php
<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Instructor = 'instructor';
    case Learner = 'learner';

    public function label(): string
    {
        return match($this) {
            self::Admin => 'Administrator',
            self::Instructor => 'Kursleiter',
            self::Learner => 'Lernender',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Admin => 'red',
            self::Instructor => 'blue',
            self::Learner => 'green',
        };
    }
}
```

---

## Difficulty.php

```php
<?php

namespace App\Enums;

enum Difficulty: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Expert = 'expert';

    public function label(): string
    {
        return match($this) {
            self::Beginner => 'Anfänger',
            self::Intermediate => 'Fortgeschritten',
            self::Advanced => 'Weit Fortgeschritten',
            self::Expert => 'Experte',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Beginner => 'green',
            self::Intermediate => 'blue',
            self::Advanced => 'orange',
            self::Expert => 'red',
        };
    }

    public function order(): int
    {
        return match($this) {
            self::Beginner => 1,
            self::Intermediate => 2,
            self::Advanced => 3,
            self::Expert => 4,
        };
    }
}
```

---

## StepType.php

```php
<?php

namespace App\Enums;

enum StepType: string
{
    case Material = 'material';
    case Task = 'task';
    case Assessment = 'assessment';

    public function label(): string
    {
        return match($this) {
            self::Material => 'Lernmaterial',
            self::Task => 'Aufgabe',
            self::Assessment => 'Prüfung',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Material => 'book-open',
            self::Task => 'clipboard-document-check',
            self::Assessment => 'academic-cap',
        };
    }
}
```

---

## MaterialType.php

```php
<?php

namespace App\Enums;

enum MaterialType: string
{
    case Text = 'text';
    case Video = 'video';
    case Audio = 'audio';
    case Pdf = 'pdf';
    case Image = 'image';
    case Link = 'link';
    case Interactive = 'interactive';

    public function label(): string
    {
        return match($this) {
            self::Text => 'Text',
            self::Video => 'Video',
            self::Audio => 'Audio',
            self::Pdf => 'PDF-Dokument',
            self::Image => 'Bild',
            self::Link => 'Externer Link',
            self::Interactive => 'Interaktiv',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Text => 'document-text',
            self::Video => 'video-camera',
            self::Audio => 'musical-note',
            self::Pdf => 'document',
            self::Image => 'photo',
            self::Link => 'link',
            self::Interactive => 'puzzle-piece',
        };
    }

    public function allowedExtensions(): array
    {
        return match($this) {
            self::Video => ['mp4', 'webm', 'mov'],
            self::Audio => ['mp3', 'wav', 'ogg'],
            self::Pdf => ['pdf'],
            self::Image => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            default => [],
        };
    }

    public function requiresFile(): bool
    {
        return in_array($this, [self::Video, self::Audio, self::Pdf, self::Image]);
    }
}
```

---

## TaskType.php

```php
<?php

namespace App\Enums;

enum TaskType: string
{
    case Submission = 'submission';
    case Project = 'project';
    case Discussion = 'discussion';

    public function label(): string
    {
        return match($this) {
            self::Submission => 'Abgabe',
            self::Project => 'Projekt',
            self::Discussion => 'Diskussion',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::Submission => 'Einreichung einer Datei oder eines Textes',
            self::Project => 'Umfangreiches Projekt mit mehreren Teilen',
            self::Discussion => 'Teilnahme an einer Diskussion',
        };
    }
}
```

---

## AssessmentType.php

```php
<?php

namespace App\Enums;

enum AssessmentType: string
{
    case Quiz = 'quiz';
    case Exam = 'exam';
    case Survey = 'survey';

    public function label(): string
    {
        return match($this) {
            self::Quiz => 'Quiz',
            self::Exam => 'Prüfung',
            self::Survey => 'Umfrage',
        };
    }

    public function isGraded(): bool
    {
        return $this !== self::Survey;
    }
}
```

---

## QuestionType.php

```php
<?php

namespace App\Enums;

enum QuestionType: string
{
    case SingleChoice = 'single_choice';
    case MultipleChoice = 'multiple_choice';
    case TrueFalse = 'true_false';
    case Text = 'text';
    case Matching = 'matching';

    public function label(): string
    {
        return match($this) {
            self::SingleChoice => 'Einzelauswahl',
            self::MultipleChoice => 'Mehrfachauswahl',
            self::TrueFalse => 'Richtig/Falsch',
            self::Text => 'Freitext',
            self::Matching => 'Zuordnung',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::SingleChoice => 'circle',
            self::MultipleChoice => 'squares-2x2',
            self::TrueFalse => 'check-circle',
            self::Text => 'pencil',
            self::Matching => 'arrows-right-left',
        };
    }

    public function isAutoGradable(): bool
    {
        return $this !== self::Text;
    }

    public function hasOptions(): bool
    {
        return in_array($this, [self::SingleChoice, self::MultipleChoice, self::TrueFalse]);
    }
}
```

---

## EnrollmentStatus.php

```php
<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Paused = 'paused';
    case Expired = 'expired';

    public function label(): string
    {
        return match($this) {
            self::Active => 'Aktiv',
            self::Completed => 'Abgeschlossen',
            self::Paused => 'Pausiert',
            self::Expired => 'Abgelaufen',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Active => 'green',
            self::Completed => 'blue',
            self::Paused => 'yellow',
            self::Expired => 'red',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Active => 'play',
            self::Completed => 'check',
            self::Paused => 'pause',
            self::Expired => 'clock',
        };
    }
}
```

---

## StepProgressStatus.php

```php
<?php

namespace App\Enums;

enum StepProgressStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match($this) {
            self::NotStarted => 'Nicht begonnen',
            self::InProgress => 'In Bearbeitung',
            self::Completed => 'Abgeschlossen',
            self::Skipped => 'Übersprungen',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NotStarted => 'gray',
            self::InProgress => 'yellow',
            self::Completed => 'green',
            self::Skipped => 'orange',
        };
    }

    public function isComplete(): bool
    {
        return in_array($this, [self::Completed, self::Skipped]);
    }
}
```

---

## SubmissionStatus.php

```php
<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Pending = 'pending';
    case Reviewed = 'reviewed';
    case RevisionRequested = 'revision_requested';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Ausstehend',
            self::Reviewed => 'Bewertet',
            self::RevisionRequested => 'Überarbeitung angefordert',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'yellow',
            self::Reviewed => 'green',
            self::RevisionRequested => 'orange',
        };
    }
}
```

---

## UnlockCondition.php

```php
<?php

namespace App\Enums;

enum UnlockCondition: string
{
    case Sequential = 'sequential';
    case CompletionPercent = 'completion_percent';
    case Manual = 'manual';
    case Date = 'date';

    public function label(): string
    {
        return match($this) {
            self::Sequential => 'Nach Abschluss des vorherigen Moduls',
            self::CompletionPercent => 'Bei X% Fortschritt',
            self::Manual => 'Manuell durch Kursleiter',
            self::Date => 'Ab bestimmtem Datum',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::Sequential => 'Das Modul wird freigeschaltet, sobald das vorherige Modul abgeschlossen ist',
            self::CompletionPercent => 'Das Modul wird bei einem bestimmten Fortschritt freigeschaltet',
            self::Manual => 'Der Kursleiter schaltet das Modul manuell frei',
            self::Date => 'Das Modul wird ab einem bestimmten Datum freigeschaltet',
        };
    }
}
```

---

## Quick Creation Script

Run this in your terminal to create all enum files:

```bash
mkdir -p app/Enums

# Copy each enum class from above into its respective file:
# - app/Enums/UserRole.php
# - app/Enums/Difficulty.php
# - app/Enums/StepType.php
# - app/Enums/MaterialType.php
# - app/Enums/TaskType.php
# - app/Enums/AssessmentType.php
# - app/Enums/QuestionType.php
# - app/Enums/EnrollmentStatus.php
# - app/Enums/StepProgressStatus.php
# - app/Enums/SubmissionStatus.php
# - app/Enums/UnlockCondition.php
```
