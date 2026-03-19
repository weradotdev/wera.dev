---
title: Managing Workspaces
group: Managing the platform
---

# Managing Workspaces

Workspaces are the top-level containers for your work. Each workspace has its own **projects**, **boards**, **tasks**, and **tickets**, and you can invite team members to collaborate within a workspace.

## Where to manage workspaces

- **All Workspaces** is available from the tenant (workspace) switcher in the sidebar — use it to see and switch between every workspace you belong to.
- Workspace management (create, edit, list) is in the admin panel under the **All Workspaces** view; it is not shown in the main navigation when you are inside a workspace.

## Creating a workspace

1. Go to **All Workspaces** (via the workspace menu in the sidebar).
2. Click **Create**.
3. Fill in:
   - **Name** — required; the display name of the workspace.
   - **Description** — optional.
   - **Image** — optional avatar/header image (stored under `avatars/workspaces`).
   - **Icon** — optional icon image (stored under `icons/workspaces`).
   - **Users** — select one or more users to add to the workspace (they can be given roles via workspace membership).
4. Save. A **slug** is generated automatically for the URL.

## Editing a workspace

1. Open **All Workspaces** and click the workspace you want to edit.
2. Click **Edit**.
3. Update name, description, image, icon, or **Users** as needed.
4. Save.

## Workspace contents

Within a workspace you can:

- **Projects** — create and manage projects (each project can have its own boards and tasks).
- **Boards** — define board types (e.g. Pending, Ongoing, Review, Completed) that projects in this workspace can use.
- **Tasks** — create and manage tasks linked to projects and boards.
- **Tickets** — manage tickets in this workspace.

Only users who are members of the workspace can see and use these resources. Switch workspaces from the tenant menu in the sidebar to work in a different workspace.
