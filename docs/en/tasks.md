---
title: Managing Tasks
group: Managing the platform
---

# Managing Tasks

Tasks are the main work items inside a **project**. Each task belongs to one **project**, one **board** (column), and can have assignees, a checklist, dates, attachments, and optional link to a **ticket**.

## Where to find tasks

With a workspace selected, open **Tasks** in the sidebar. You can list, create, view, and edit tasks. Tasks are scoped to the current workspace; you choose the project and board when creating or editing a task.

## Creating a task

1. Go to **Tasks** → **Create**.
2. **Step 1 — Task details**
   - **Project** — required; choose a project in the current workspace.
   - **Board** — required; choose a board (column) for this project. Options update based on the selected project.
   - **Priority** — Low, Medium, or High (default: Medium).
   - **Title** — required.
   - **Description** — optional.
   - **Checklist items** — optional list of sub-tasks (add/remove items).
   - **Screenshots / Attachments** — optional media (e.g. screenshots).
   - **Schedule** — optional start and end date/time (event period).
   - **Position** — optional number for ordering (default 0).
3. **Step 2 — Assigned users**
   - Add one or more **Assigned users** from the workspace.
   - For each, choose **User** and **Role**: Developer, Reviewer, or Lead.
   - You can reorder assignees.
4. Save.

## Viewing and editing a task

- **List** — **Tasks** shows tasks in the current workspace (filter by project/board as needed).
- **View** — open a task to see its full details (infolist), including comments if the app uses commentions.
- **Edit** — change project, board, priority, title, description, checklist, attachments, schedule, position, and assigned users.

## Task fields summary

| Field           | Purpose |
|----------------|---------|
| Project        | Which project the task belongs to. |
| Board          | Which column/stage (e.g. Pending, Ongoing, Review, Completed). |
| Priority       | Low, Medium, or High. |
| Title / Description | Main task content. |
| Checklist      | List of sub-items; progress can be calculated from completed items. |
| Schedule       | Start and end date/time. |
| Assigned users | Team members and their role (Developer, Reviewer, Lead). |
| Screenshots    | Attachments stored in the task’s media collection. |

## Moving tasks between boards

To move a task to another column (e.g. from “Pending” to “Ongoing”):

1. **Edit** the task.
2. Change **Board** to the desired board for the same project.
3. Save.

In a kanban-style view (if your app provides one), you may also drag and drop tasks between columns instead of editing the board field manually.

## Linking to tickets

Tasks can optionally be linked to a **ticket** (e.g. support or bug ticket). When editing a task, if a **Ticket** field is available, select the ticket to associate. This helps trace work back to reported issues or requests.
