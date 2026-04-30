# n8n Workflows for Zforce Vortex

These workflows connect n8n to the Laravel Vortex API. All communication uses a shared secret (`X-Vortex-Key` header).

## Prerequisites

1. Your Laravel app is running at `https://zforce.army`
2. You have the `VORTEX_WEBHOOK_KEY` from your `.env`
3. n8n has HTTP Request and OpenAI (or HTTP Request to OpenRouter) nodes

---

## Workflow 1: Blog Generation

**Trigger**: Scheduled (daily) or HTTP Request (called by Laravel when arc completes)

**Purpose**: Generate blog posts from completed player story arcs.

### Node 1: HTTP Request — Fetch Completed Arcs

```javascript
// Method: GET
// URL: https://zforce.army/api/vortex/arcs/completed
// Authentication: Header Auth
// Header Name: X-Vortex-Key
// Header Value: {{ $env.VORTEX_WEBHOOK_KEY }}
```

**Response**:
```json
{
  "count": 2,
  "arcs": [
    {
      "user_id": 1,
      "temporal_id": "REC-2026-A1B2C3D4",
      "age": 9,
      "affinity": "mathematics",
      "completed_transmissions": ["first-contact", "war-briefing", "mission-001"],
      "narrative_beats": ["operator_recruited", "war_explained", "prime_sequence_solved"]
    }
  ]
}
```

### Node 2: Loop Over Arcs

Use n8n's **Split In Batches** or **Loop** node to process each arc.

### Node 3: OpenAI / HTTP Request — Generate Blog Post

Use **HTTP Request** node to call OpenRouter (single key, multiple models):

```javascript
// Method: POST
// URL: https://openrouter.ai/api/v1/chat/completions
// Authentication: Header Auth
// Header Name: Authorization
// Header Value: Bearer {{ $env.OPENROUTER_API_KEY }}
// Content-Type: application/json

// Body (JSON):
{
  "model": "anthropic/claude-3.5-haiku",
  "messages": [
    {
      "role": "system",
      "content": "You are an archivist from 2047. Write a blog post as a recovered transmission log. The tone is mysterious but inspiring. Target audience: parents and educators."
    },
    {
      "role": "user",
      "content": "Write a blog post from these narrative beats: {{ $json.narrative_beats.join(', ') }}. The operator is age {{ $json.age }} with affinity for {{ $json.affinity }}. Temporal ID: {{ $json.temporal_id }}."
    }
  ],
  "temperature": 0.8,
  "max_tokens": 1500
}
```

### Node 4: HTTP Request — Store Blog in Laravel

```javascript
// Method: POST
// URL: https://zforce.army/api/vortex/blog
// Authentication: Header Auth
// Header Name: X-Vortex-Key
// Header Value: {{ $env.VORTEX_WEBHOOK_KEY }}
// Content-Type: application/json

// Body (JSON):
{
  "user_id": {{ $json.user_id }},
  "title": "Archive Chronicle: The Journey of {{ $json.temporal_id }}",
  "content": "{{ $node['OpenRouter'].json.choices[0].message.content }}",
  "narrative_beats": {{ JSON.stringify($json.narrative_beats) }},
  "story_arc": "recruitment",
  "generated_by": "anthropic/claude-3.5-haiku"
}
```

---

## Workflow 2: Mission Generation

**Trigger**: Scheduled (hourly) — checks if Archive is running low

**Purpose**: Generate new missions when hand-crafted Archive entries are depleted.

### Node 1: HTTP Request — Check Archive Status

```javascript
// Method: GET
// URL: https://zforce.army/api/vortex/arcs/completed
// Authentication: Header Auth
// Header Name: X-Vortex-Key
// Header Value: {{ $env.VORTEX_WEBHOOK_KEY }}
```

### Node 2: If — Should Generate?

```javascript
// Condition: {{ $json.count < 5 }}
// True → Generate missions
// False → Do nothing
```

### Node 3: OpenAI / HTTP Request — Generate Mission

```javascript
// Method: POST
// URL: https://openrouter.ai/api/v1/chat/completions
// Authentication: Header Auth
// Header Name: Authorization
// Header Value: Bearer {{ $env.OPENROUTER_API_KEY }}
// Content-Type: application/json

// Body (JSON):
{
  "model": "qwen/qwen-2.5-7b-instruct",
  "messages": [
    {
      "role": "system",
      "content": "You generate training missions for the Zforce temporal interface. Respond with valid JSON only. No markdown, no explanation."
    },
    {
      "role": "user",
      "content": "Generate a mission for a {{ $json.age }}-year-old with {{ $json.affinity }} affinity. Phase: {{ $json.phase }}. Include: title, content (array of lines), choices (array of {key, label}), conditions (min_age, max_age, affinity), and narrative_beats. Make it feel like a transmission from 2047."
    }
  ],
  "response_format": { "type": "json_object" },
  "temperature": 0.9,
  "max_tokens": 800
}
```

### Node 4: HTTP Request — Store Mission in Archive

```javascript
// Method: POST
// URL: https://zforce.army/api/vortex/mission
// Authentication: Header Auth
// Header Name: X-Vortex-Key
// Header Value: {{ $env.VORTEX_WEBHOOK_KEY }}
// Content-Type: application/json

// Body (JSON) — parsed from OpenRouter response:
{
  "slug": "generated-mission-{{ Date.now() }}",
  "title": "{{ $node['OpenRouter'].json.choices[0].message.content.title }}",
  "content": {{ $node['OpenRouter'].json.choices[0].message.content.content }},
  "choices": {{ $node['OpenRouter'].json.choices[0].message.content.choices }},
  "conditions": {{ $node['OpenRouter'].json.choices[0].message.content.conditions }},
  "narrative_beats": {{ $node['OpenRouter'].json.choices[0].message.content.narrative_beats }},
  "generating_model": "qwen/qwen-2.5-7b-instruct"
}
```

---

## Workflow 3: Report Grading

**Trigger**: Scheduled (every 5 minutes) — polls for queued reports

**Purpose**: Grade player mission reports and award XP.

### Node 1: HTTP Request — Fetch Queued Reports

```javascript
// Method: GET
// URL: https://zforce.army/api/vortex/reports/queued
// Authentication: Header Auth
// Header Name: X-Vortex-Key
// Header Value: {{ $env.VORTEX_WEBHOOK_KEY }}
```

### Node 2: Loop Over Reports

Use **Split In Batches** with batch size 1.

### Node 3: OpenAI / HTTP Request — Grade Report

```javascript
// Method: POST
// URL: https://openrouter.ai/api/v1/chat/completions
// Authentication: Header Auth
// Header Name: Authorization
// Header Value: Bearer {{ $env.OPENROUTER_API_KEY }}
// Content-Type: application/json

// Body (JSON):
{
  "model": "anthropic/claude-3.5-haiku",
  "messages": [
    {
      "role": "system",
      "content": "You are ZETA-7 grading a training mission report. Respond with valid JSON only: {assessment: string, xp_awarded: int 0-100, commander_response: string, detected_struggles: string[], detected_strengths: string[]}. The operator is age {{ $json.profile.age_at_contact }}."
    },
    {
      "role": "user",
      "content": "Mission: {{ $json.mission_slug }}. Answers: {{ JSON.stringify($json.answers) }}"
    }
  ],
  "response_format": { "type": "json_object" },
  "temperature": 0.7,
  "max_tokens": 500
}
```

### Node 4: HTTP Request — Post Grade Back to Laravel

```javascript
// Method: POST
// URL: https://zforce.army/api/vortex/report
// Authentication: Header Auth
// Header Name: X-Vortex-Key
// Header Value: {{ $env.VORTEX_WEBHOOK_KEY }}
// Content-Type: application/json

// Body (JSON):
{
  "report_id": {{ $json.report_id }},
  "assessment": "{{ $node['OpenRouter'].json.choices[0].message.content.assessment }}",
  "xp_awarded": {{ $node['OpenRouter'].json.choices[0].message.content.xp_awarded }},
  "commander_response": "{{ $node['OpenRouter'].json.choices[0].message.content.commander_response }}",
  "detected_struggles": {{ JSON.stringify($node['OpenRouter'].json.choices[0].message.content.detected_struggles || []) }},
  "detected_strengths": {{ JSON.stringify($node['OpenRouter'].json.choices[0].message.content.detected_strengths || []) }}
}
```

---

## n8n Environment Variables

Set these in n8n (Settings → External Secrets or directly in credentials):

| Variable | Value | Where to Find |
|----------|-------|---------------|
| `VORTEX_WEBHOOK_KEY` | `zforce-temporal-key-...` | Your Laravel `.env` |
| `OPENROUTER_API_KEY` | `sk-or-v1-...` | OpenRouter dashboard |
| `ZFORCE_BASE_URL` | `https://zforce.army` | Your app URL |

---

## Cost Optimization (Adaptive Routing)

The Vortex already does adaptive model routing in Laravel. For n8n workflows, follow the same tier system:

| Workflow | Task | Recommended Model | Est. Cost |
|----------|------|-------------------|-----------|
| Blog Generation | Creative writing | `claude-3.5-haiku` | ~$0.008/post |
| Mission Generation | Structured JSON | `qwen/qwen-2.5-7b-instruct` | ~$0.002/mission |
| Report Grading | Assessment | `claude-3.5-haiku` | ~$0.005/report |

**Total estimated cost**: ~$0.015 per player per session (mostly Archive, occasional AI).

---

## Testing the Integration

From your Laravel app:

```bash
# Test blog endpoint
curl -X POST https://zforce.army/api/vortex/blog \
  -H "X-Vortex-Key: YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "title": "Test Chronicle",
    "content": "This is a test blog post.",
    "story_arc": "recruitment"
  }'

# Test mission endpoint
curl -X POST https://zforce.army/api/vortex/mission \
  -H "X-Vortex-Key: YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "slug": "test-mission-001",
    "title": "Test Mission",
    "content": ["Line 1", "Line 2"],
    "choices": [{"key": "1", "label": "Option A"}]
  }'
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| `401 Unauthorized` | Check `X-Vortex-Key` header matches `.env` |
| `422 Validation Error` | Check required fields in payload |
| OpenRouter returns errors | Verify API key and model name |
| n8n can't reach Laravel | Check firewall, HTTPS certificate, URL |
| Mission slug already exists | Use unique slug with timestamp |
