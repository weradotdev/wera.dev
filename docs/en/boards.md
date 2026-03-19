---
title: Managing Boards
group: Managing the platform
---

# Managing Boards

Boards define the **stages** (columns) that tasks move through in a project — for example **Pending**, **Ongoing**, **Review**, and **Completed**. Boards belong to a **workspace** and are shared by all **projects** in that workspace.

## How boards work

- Boards are created at the **workspace** level.
- When you create a **project**, it automatically gets the workspace’s existing boards attached (with a default order). New projects use the same board set so all projects in the workspace follow the same workflow.
- Each **task** is assigned to one board (via “Board” in the task form), so moving a task to a different column means changing its board.

## Where to manage boards

1. Select the **workspace** you want to manage boards for.
2. Open **Boards** in the sidebar.
3. From here you can list, create, and edit boards for this workspace.

## Creating a board

1. Go to **Boards** → **Create**.
2. Fill in:
   - **Name** — required (e.g. “Pending”, “In progress”, “Done”).
   - **Color** — Gray, Red, Orange, Yellow, Green, Blue, Indigo, Purple, Pink, Brown, Black, or White (default: Gray).
   - **Description** — optional.
   - **Image** — optional; stored under `boards`.
3. Save. A **slug** is generated automatically.

New boards are available to all projects in the workspace. Existing projects may need to have the new board attached (or the system may attach it depending on your setup).

## Editing a board

1. Go to **Boards**, click the board you want to change.
2. Click **Edit**.
3. Update name, color, description, or image and save.

## Default boards for new projects

If the workspace has **no** boards when a project is created, the platform creates four default boards: **Pending**, **Ongoing**, **Review**, and **Completed**. If the workspace already has boards, the new project reuses those. You can add or edit boards at any time; structure boards to match how your team works (e.g. Backlog, To Do, In Progress, Review, Done).
