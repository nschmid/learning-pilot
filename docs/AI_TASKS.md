# AI Feature Implementation Tasks

## Overview

This document contains the implementation checklist for the AI Content Feature. These tasks should be completed **after** the core LearningPilot (Phases 1-6) is functional.

---

## Phase AI-1: Foundation (3 days)

### Database Schema
```
[ ] Create migration: ai_user_quotas
    - user_id (FK), monthly_token_limit, monthly_tokens_used
    - daily_request_limit, daily_requests_used
    - feature flags (tutor_enabled, practice_gen_enabled, advanced_explanations)
    - reset tracking dates

[ ] Create migration: ai_generated_contents
    - Polymorphic (contentable_type, contentable_id)
    - content_type enum, content text
    - content_metadata JSON, context_snapshot JSON
    - rating, was_helpful, user_feedback
    - cache_key, expires_at, version

[ ] Create migration: ai_tutor_conversations
    - user_id FK, optional path/module/step FKs
    - title, status enum
    - system_context JSON, total_messages, total_tokens_used

[ ] Create migration: ai_tutor_messages
    - conversation_id FK, role enum, content
    - model, tokens_input, tokens_output, latency_ms
    - references JSON

[ ] Create migration: ai_practice_sessions
    - user_id FK, optional path/module/step FKs
    - difficulty enum, question_count, focus_areas JSON
    - progress counters, status enum

[ ] Create migration: ai_practice_questions
    - session_id FK, question_type enum
    - question_text, options JSON, correct_answer
    - explanation, difficulty enum, topics JSON
    - user_answer, is_correct, ai_feedback

[ ] Create migration: ai_usage_logs
    - user_id FK, service_type enum, model
    - tokens_input, tokens_output, tokens_total (generated)
    - cost_credits, latency_ms, cache_hit
    - context_type, context_id

[ ] Create migration: ai_feedback_reports
    - user_id FK, ai_generated_content_id FK
    - feedback_type enum, description
    - status enum, admin_notes, resolved_by
```

### Enums
```
[ ] Create app/Enums/AIContentType.php
    - Values: explanation, hint, summary, practice_question, feedback, recommendation, flashcard, concept_breakdown
    - Methods: label(), icon(), cacheDuration()

[ ] Create app/Enums/AIServiceType.php
    - Values: explanation, hint, summary, practice_gen, feedback, tutor_chat, recommendation
    - Methods: label(), defaultModel(), maxTokens(), rateLimit()

[ ] Create app/Enums/AIPracticeDifficulty.php
    - Values: beginner, intermediate, advanced, adaptive
    - Methods: label(), description()

[ ] Create app/Enums/AIFeedbackType.php
    - Values: inaccurate, unhelpful, too_complex, too_simple, off_topic, inappropriate, other
    - Methods: label()
```

### Models
```
[ ] Create app/Models/AIUserQuota.php
    - belongsTo: User
    - Methods: checkAndResetDaily(), checkAndResetMonthly()
    - Methods: canMakeRequest(), hasTokensAvailable(), incrementUsage()
    - Methods: getRemainingTokens(), getUsagePercent()

[ ] Create app/Models/AIGeneratedContent.php
    - morphTo: contentable (QuestionResponse, StepProgress, Module, etc.)
    - belongsTo: User
    - Methods: isExpired()
    - Scopes: valid(), byType()

[ ] Create app/Models/AITutorConversation.php
    - belongsTo: User, LearningPath (nullable), Module (nullable), LearningStep (nullable)
    - hasMany: AITutorMessage
    - Methods: getContextScope()
    - Scopes: active()

[ ] Create app/Models/AITutorMessage.php
    - belongsTo: AITutorConversation
    - Methods: isAssistant(), isUser(), getTotalTokens()
    - No timestamps (only created_at)

[ ] Create app/Models/AIPracticeSession.php
    - belongsTo: User, LearningPath (nullable), Module (nullable), LearningStep (nullable)
    - hasMany: AIPracticeQuestion
    - Methods: getAccuracyPercent(), isComplete()

[ ] Create app/Models/AIPracticeQuestion.php
    - belongsTo: AIPracticeSession
    - Methods: isAnswered()
    - Casts: question_type, difficulty, options (array), topics (array)

[ ] Create app/Models/AIUsageLog.php
    - belongsTo: User
    - Casts: service_type

[ ] Create app/Models/AIFeedbackReport.php
    - belongsTo: User, AIGeneratedContent (nullable), resolvedBy User (nullable)
    - Casts: feedback_type, status
```

### Update Existing Models
```
[ ] Update app/Models/User.php
    - Add hasOne: AIUserQuota
    - Add hasMany: AITutorConversation
    - Add hasMany: AIPracticeSession
    - Add hasMany: AIUsageLog

[ ] Update app/Models/QuestionResponse.php
    - Add morphMany: AIGeneratedContent

[ ] Update app/Models/StepProgress.php
    - Add morphMany: AIGeneratedContent

[ ] Update app/Models/Module.php
    - Add morphMany: AIGeneratedContent
```

---

## Phase AI-2: Core Services (4 days)

### API Client
```
[ ] Create app/Services/AI/AIClientService.php
    - Constructor with API key injection
    - Method: createMessage(serviceType, systemPrompt, messages, model?, maxTokens?)
    - HTTP client with Anthropic headers
    - Error handling and logging
    - Return: content, model, tokens_input, tokens_output, latency_ms
```

### Context Builder
```
[ ] Create app/Services/AI/AIContextBuilder.php
    - Method: buildQuestionContext(QuestionResponse) → array
        - Question details, user answer, correct answer
        - Assessment and learning path context
    
    - Method: buildStepContext(StepProgress) → array
        - Step materials, progress info
        - Previous materials for context
    
    - Method: buildTutorContext(User, Path?, Module?, Step?) → array
        - User info, learning context
        - Enrollment progress, weak areas
    
    - Method: buildPracticeContext(User, Path?, Module?, Step?) → array
        - Scope determination
        - Materials for question generation
        - Performance data
    
    - Method: buildSummaryContext(Module) → array
        - All steps and materials
        - Path difficulty info
    
    - Helper methods: getCorrectAnswer(), getPreviousMaterials()
    - Helper methods: identifyWeakAreas(), calculateOverallAccuracy()
    - Helper methods: truncateContent()
```

### Usage Service
```
[ ] Create app/Services/AI/AIUsageService.php
    - Method: checkQuota(User, AIServiceType)
        - Check feature access
        - Check daily request limit
        - Check monthly token limit
        - Throw AIQuotaExceededException if exceeded
    
    - Method: logUsage(User, AIServiceType, result, context?)
        - Create AIUsageLog entry
        - Increment user quota
    
    - Method: getUsageStats(User) → array
        - Today's usage by service
        - Monthly usage by service
        - Remaining quota
        - Feature access flags
    
    - Helper: getOrCreateQuota(User)
    - Helper: isFeatureEnabled(AIUserQuota, AIServiceType)
    - Helper: calculateCost(model, inputTokens, outputTokens)
```

### Explanation Service
```
[ ] Create app/Services/AI/AIExplanationService.php
    - Constructor: AIClientService, AIContextBuilder, AIUsageService
    
    - Method: explainWrongAnswer(QuestionResponse, User) → AIGeneratedContent
        - Check cache first
        - Check quota
        - Build context
        - Create prompt for explanation
        - Call API
        - Log usage
        - Store and cache result
    
    - Method: generateHint(StepProgress, User, hintLevel) → AIGeneratedContent
        - Check quota
        - Build context with hint level
        - Create progressive hint prompt
        - Call API
        - Log and store result
    
    - Protected: buildExplanationPrompt() → string
    - Protected: buildHintPrompt(hintLevel) → string
```

### Tutor Service
```
[ ] Create app/Services/AI/AITutorService.php
    - Constructor: AIClientService, AIContextBuilder, AIUsageService
    
    - Method: startConversation(User, Path?, Module?, Step?) → AITutorConversation
        - Build initial context
        - Create conversation record
    
    - Method: sendMessage(AITutorConversation, message, User) → AITutorMessage
        - Check quota
        - Store user message
        - Build messages array from history
        - Create system prompt with context
        - Call API
        - Store AI response
        - Update conversation stats
        - Log usage
    
    - Method: getConversation(conversationId) → ?AITutorConversation
    - Method: archiveConversation(AITutorConversation)
    
    - Protected: buildMessagesArray(AITutorConversation) → array
    - Protected: buildTutorSystemPrompt(AITutorConversation) → string
    - Protected: generateTitle(Path?, Module?, Step?) → string
```

### Practice Generator Service
```
[ ] Create app/Services/AI/AIPracticeGeneratorService.php
    - Constructor: AIClientService, AIContextBuilder, AIUsageService
    
    - Method: startSession(User, count, difficulty, Path?, Module?, Step?, focusAreas) → AIPracticeSession
        - Create session
        - Generate first batch of questions
    
    - Method: generateQuestions(AIPracticeSession, User, count)
        - Check quota
        - Build practice context
        - Determine difficulty (adaptive logic)
        - Create generation prompt
        - Call API
        - Parse and store questions
        - Log usage
    
    - Method: submitAnswer(AIPracticeQuestion, answer, User) → array
        - Evaluate answer
        - Update question and session stats
        - Generate feedback for wrong answers
        - Check if more questions needed
        - Check session completion
    
    - Protected: determineDifficulty(AIPracticeSession) → Difficulty
    - Protected: evaluateAnswer(AIPracticeQuestion, answer) → bool
    - Protected: evaluateChoiceAnswer(question, answer) → bool
    - Protected: evaluateMultipleChoiceAnswer(question, answer) → bool
    - Protected: generateAnswerFeedback(question, answer, User) → string
    - Protected: getCorrectAnswerDisplay(question) → string
    - Protected: parseGeneratedQuestions(content) → array
    - Protected: buildGeneratorPrompt() → string
```

### Summary Service
```
[ ] Create app/Services/AI/AISummaryService.php
    - Constructor: AIClientService, AIContextBuilder, AIUsageService
    
    - Method: generateModuleSummary(Module, User) → AIGeneratedContent
        - Check cache
        - Check quota
        - Build summary context
        - Create prompt
        - Call API
        - Log and cache result
    
    - Method: generateFlashcards(Module|LearningStep, User, count) → AIGeneratedContent
        - Check quota
        - Build context
        - Create flashcard prompt
        - Call API
        - Store result
    
    - Protected: buildSummaryPrompt(scope) → string
    - Protected: buildFlashcardPrompt() → string
```

### Exceptions
```
[ ] Create app/Exceptions/AIQuotaExceededException.php
    - Extend Exception
    - German-friendly error messages

[ ] Create app/Exceptions/AIServiceException.php
    - Extend Exception
    - For API and parsing errors
```

### Service Registration
```
[ ] Update app/Providers/AppServiceProvider.php
    - Register AIClientService as singleton with API key from config
    - Register other AI services
```

---

## Phase AI-3: Livewire Components (4 days)

### Tutor Chat Component
```
[ ] Create app/Livewire/Learner/AI/TutorChat.php
    - Properties: conversationId, message, isLoading, error
    - Properties: pathId, moduleId, stepId (context)
    
    - Method: sendMessage()
        - Validate message
        - Get or create conversation
        - Send via AITutorService
        - Handle quota exceptions
        - Dispatch message-sent event
    
    - Method: getOrCreateConversation() → AITutorConversation
    - Method: render() with conversation data

[ ] Create resources/views/livewire/learner/ai/tutor-chat.blade.php
    - Chat message list (user/assistant styling)
    - Message input with send button
    - Loading indicator
    - Error display
    - Remaining requests counter
```

### Practice Session Component
```
[ ] Create app/Livewire/Learner/AI/PracticeSession.php
    - Properties: sessionId, pathId, moduleId, stepId
    - Properties: questionCount, difficulty
    - Properties: currentQuestionIndex, selectedAnswer, result, isLoading
    
    - Method: startSession()
    - Method: submitAnswer()
    - Method: nextQuestion()
    - Method: render() with session and current question

[ ] Create resources/views/livewire/learner/ai/practice-session.blade.php
    - Setup screen (count, difficulty selection)
    - Question display with options
    - Answer feedback with explanation
    - Progress indicator
    - Results summary
```

### Explanation Modal Component
```
[ ] Create app/Livewire/Learner/AI/ExplanationModal.php
    - Properties: show, responseId, explanation, isLoading, error
    - Listener: showExplanation(responseId)
    
    - Method: loadExplanation()
    - Method: submitFeedback(wasHelpful)
    - Method: close()
    - Method: render()

[ ] Create resources/views/livewire/learner/ai/explanation-modal.blade.php
    - Modal overlay
    - Loading spinner
    - Markdown-rendered explanation
    - Helpful/Not helpful buttons
    - Close button
```

### Hint Button Component
```
[ ] Create app/Livewire/Learner/AI/HintButton.php
    - Properties: stepProgressId, currentHintLevel, hints, isLoading
    
    - Method: getHint()
        - Increment hint level
        - Call AIExplanationService::generateHint()
        - Store hint
    
    - Method: render()

[ ] Create resources/views/livewire/learner/ai/hint-button.blade.php
    - Button with hint icon
    - Hint level indicator (1/3, 2/3, 3/3)
    - Hint display area
```

### Summary Panel Component
```
[ ] Create app/Livewire/Learner/AI/SummaryPanel.php
    - Properties: moduleId, summary, isLoading, error
    
    - Method: generateSummary()
    - Method: render()

[ ] Create resources/views/livewire/learner/ai/summary-panel.blade.php
    - Generate button
    - Loading state
    - Markdown-rendered summary
    - Print/Copy buttons
```

### Flashcard Viewer Component
```
[ ] Create app/Livewire/Learner/AI/FlashcardViewer.php
    - Properties: contentId, flashcards, currentIndex, showAnswer
    
    - Method: loadFlashcards(moduleId|stepId)
    - Method: nextCard()
    - Method: previousCard()
    - Method: flipCard()
    - Method: render()

[ ] Create resources/views/livewire/learner/ai/flashcard-viewer.blade.php
    - Card display (front/back flip animation)
    - Navigation buttons
    - Progress indicator
    - Shuffle button
```

### Usage Stats Component (Learner)
```
[ ] Create app/Livewire/Learner/AI/UsageStats.php
    - Properties: stats (loaded from AIUsageService)
    
    - Method: mount() - load stats
    - Method: render()

[ ] Create resources/views/livewire/learner/ai/usage-stats.blade.php
    - Token usage progress bar
    - Daily requests remaining
    - Feature access indicators
```

---

## Phase AI-4: Integration (2 days)

### Integrate into Existing Components
```
[ ] Update StepViewer component
    - Add floating "KI-Tutor" button
    - Pass step context to TutorChat

[ ] Update AssessmentResults component
    - Add "Erklärung anzeigen" button next to wrong answers
    - Integrate ExplanationModal
    - Add "Mehr üben" button linking to PracticeSession

[ ] Update ModuleComplete component
    - Add "Zusammenfassung generieren" button
    - Add "Lernkarten erstellen" button

[ ] Update MyProgress (Learner Dashboard)
    - Add "Schwächen üben" section
    - Show AI usage stats widget
    - Recent tutor conversations list
```

### Admin Components
```
[ ] Create app/Livewire/Admin/AI/UsageDashboard.php
    - Total tokens used (daily/monthly)
    - Usage by service type chart
    - Top users by usage
    - Cost estimation

[ ] Create app/Livewire/Admin/AI/QuotaManager.php
    - Search/filter users
    - Edit user quotas
    - Enable/disable features per user
    - Bulk quota updates

[ ] Create app/Livewire/Admin/AI/FeedbackReview.php
    - List pending feedback reports
    - Review and resolve interface
    - Filter by type/status
```

### Routes
```
[ ] Update routes/learner.php
    - GET /learn/ai/tutor → TutorChat
    - GET /learn/ai/tutor/{conversation} → TutorChat (existing)
    - GET /learn/ai/practice → PracticeSession setup
    - GET /learn/ai/practice/{session} → PracticeSession (active)
    - GET /learn/ai/summary/{module} → SummaryPanel
    - GET /learn/ai/flashcards/{module} → FlashcardViewer

[ ] Update routes/admin.php
    - GET /admin/ai/usage → UsageDashboard
    - GET /admin/ai/quotas → QuotaManager
    - GET /admin/ai/feedback → FeedbackReview
```

### Navigation Updates
```
[ ] Update learner navigation
    - Add "KI-Assistent" menu item
    - Sub-items: Tutor, Übungen, Meine Nutzung

[ ] Update admin navigation
    - Add "KI-System" menu item
    - Sub-items: Nutzung, Kontingente, Feedback
```

---

## Phase AI-5: Testing & Polish (2 days)

### Unit Tests
```
[ ] Test AIContextBuilder
    - Test buildQuestionContext with various question types
    - Test buildStepContext with and without previous materials
    - Test buildTutorContext with different scopes
    - Test weak area identification
    - Test content truncation

[ ] Test AIUsageService
    - Test quota checking and enforcement
    - Test daily/monthly reset logic
    - Test usage logging
    - Test cost calculation

[ ] Test Answer Evaluation
    - Test single choice evaluation
    - Test multiple choice evaluation
    - Test true/false evaluation
```

### Feature Tests
```
[ ] Test TutorChat flow
    - Start new conversation
    - Send message and receive response
    - Continue existing conversation
    - Handle quota exceeded

[ ] Test PracticeSession flow
    - Start session with different configs
    - Answer questions
    - Verify adaptive difficulty
    - Complete session

[ ] Test ExplanationModal
    - Request explanation for wrong answer
    - Verify caching works
    - Submit feedback

[ ] Test Quota Enforcement
    - Daily limit reached
    - Monthly limit reached
    - Feature disabled
```

### Integration Tests
```
[ ] Test API integration
    - Mock Anthropic API responses
    - Test error handling
    - Test timeout handling

[ ] Test caching
    - Verify cached explanations are returned
    - Verify cache expiration works
```

### Performance
```
[ ] Optimize context building
    - Lazy load relationships
    - Limit material content length
    - Cache frequently used data

[ ] Consider response streaming
    - Evaluate SSE for tutor chat
    - Implement if beneficial

[ ] Cache warming
    - Generate summaries for popular modules
    - Pre-cache common explanations
```

### Documentation
```
[ ] Update README.md with AI features
[ ] Document AI configuration options
[ ] Add usage examples
[ ] Create admin guide for quota management
```

---

## Configuration Checklist

```
[ ] Update config/lernpfad.php
    - Add 'ai' section with all settings

[ ] Update .env.example
    - AI_PROVIDER
    - ANTHROPIC_API_KEY
    - AI_MODEL_* settings
    - AI_DEFAULT_* limits
    - AI_FEATURE_* flags
    - AI_CACHE_ENABLED

[ ] Create AIServiceProvider (optional)
    - Register AI services
    - Set up rate limiting
```

---

## Post-Implementation

```
[ ] Monitor API costs
[ ] Analyze usage patterns
[ ] Gather user feedback
[ ] Iterate on prompts based on feedback
[ ] Consider adding more AI features:
    - Voice input for tutor
    - Image-based questions
    - Code execution for programming courses
    - Peer comparison insights
```
