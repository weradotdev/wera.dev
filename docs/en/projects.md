---
title: Managing Projects
group: Managing the platform
---

# Managing Projects

Projects live inside a **workspace** and organize work around a goal or product. Each project has its own **boards** (columns for task stages), **tasks**, **tickets**, and optional integrations (GitHub, Slack, Telegram, WhatsApp).

## Where to find projects

After selecting a workspace, use **Projects** in the sidebar. You can list, create, view, and edit projects for the current workspace.

## Creating a project

1. Ensure you are in the correct **workspace** (use the workspace switcher in the sidebar if needed).
2. Go to **Projects** → **Create**.
3. Fill in the main fields:
   - **Name** — required.
   - **Color** — for visual identification (default black).
   - **Description** — optional.
   - **Icon**, **Image**, **Banner image** — optional; stored under `icons/projects`, `avatars/projects`, and `banners/projects`.
   - **Screenshots / Attachments** — optional media (e.g. mockups or references).
   - **Status** — Planning, Active, On Hold, or Completed (default: Active).
   - **Start date** and **End date** — optional.
4. Optionally expand **Integrations** and configure:
   - **GitHub** — connect a repo URL and optionally create a GitHub issue for each task.
   - **Notifications** — notify developers when assigned to a task; choose channels (Email, Slack, Telegram, WhatsApp).
   - **Slack** — webhook URL and channel for notifications.
   - **Telegram** — bot token.
   - **WhatsApp** — session (e.g. via “Connect WhatsApp” when editing), optional group name and JID for group notifications.
5. Save.

When you create a project, the platform automatically attaches the workspace’s **boards** to the project (e.g. Pending, Ongoing, Review, Completed) so you can start adding tasks to columns right away.

## Viewing and editing a project

- **List** — **Projects** shows all projects in the current workspace.
- **View** — open a project to see its details (infolist).
- **Edit** — change name, description, media, status, dates, and all integration settings.

## Integrations summary

| Integration   | Purpose |
|-------------|----------|
| GitHub      | Link a repository; optionally create an issue per task. |
| Notifications | Notify assignees per task; choose email and/or Slack, Telegram, WhatsApp. |
| Slack       | Send project/task notifications to a channel via webhook. |
| Telegram    | Send notifications via a bot token. |
| WhatsApp    | Notifications (and optional group) via connected session. |

All integration toggles and fields are in the **Integrations** section of the project form.
