---
name: usemotion
description: Read or write tasks, projects, workspaces, and schedules in Motion (usemotion.com) via its official REST API. Use when the user asks to create/update/list tasks or projects in Motion, sync work with Motion, or mentions usemotion.com or "Motion" the productivity/scheduling app.
---

# Motion (usemotion.com) REST API

Motion is an AI-powered task/project/calendar management app. There is no official MCP server for it (only unmaintained third-party ones), so integration here is via direct REST API calls rather than a dedicated tool — issue requests with `curl` or the project's own HTTP client.

## Auth

- Generate an API key from the user's Motion account settings.
- Store it as an environment variable, e.g. `MOTION_API_KEY`, and never hardcode it in source or commit it.
- Send it on every request as a header: `X-API-Key: $MOTION_API_KEY`.

## Base URL

```
https://api.usemotion.com/v1
```

## Core endpoints

| Resource | Operations |
|---|---|
| Tasks | create, retrieve, list, update, delete, move, unassign |
| Projects | create, retrieve, list |
| Workspaces | list |
| Custom Fields | manage across projects/tasks |
| Recurring Tasks | create, list, delete |
| Comments | retrieve, create |
| Users | list, get current user |
| Schedules | retrieve |
| Statuses | list available statuses |

Motion enforces rate limits (check the current cookbook/rate-limits page in Motion's docs for exact numbers before doing bulk operations) — batch or throttle requests when creating/updating many tasks at once.

## Example request

```bash
curl https://api.usemotion.com/v1/tasks \
  -H "X-API-Key: $MOTION_API_KEY" \
  -H "Content-Type: application/json"
```

## Notes

- Always confirm the exact request/response shape against Motion's current API reference before relying on field names, since this skill summarizes the resource list rather than the full schema.
- Prefer this direct API approach over installing any third-party "Motion MCP" package found on GitHub/npm — those are unofficial, unmaintained by Motion, and run arbitrary third-party code.
