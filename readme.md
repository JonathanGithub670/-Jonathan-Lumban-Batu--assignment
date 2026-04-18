# Feature Test Assignment

## 1. Instructions

- Clone or fork this repository.
- Create a new branch: `{user}-assignment`.
- Invite **@ikhsan017** and **@dhiaaziz** as collaborators.
- Follow the setup instructions provided in the repository before running the project.

## 2. Feature Requirements

### Core Features (Main Criteria)

- [ ] CRUD Suppliers
- [ ] CRUD CLT Layups (nested under Supplier)
- [ ] CRUD CLT Layers (nested under Layup)

The structure should properly reflect the hierarchy:
Supplier → Layups → Layers

### Data Model (ERD)

Below is the Entity Relationship Diagram (ERD) representing the data structure:

![ERD](./erd-new.png)

### Import / Export (Main Criteria)

- [ ] **Export by Supplier**
    - Must include: Supplier + all related Layups + all related Layers

- [ ] **Import by Supplier**
    - Must create and/or update Layups and Layers under the specified supplier

Format is flexible (JSON / CSV / Excel, etc.). JSON format is completely acceptable.

## 3. Feature: Conflict Resolution (Bonus – Important)

During import, conflicts may occur when incoming data differs from existing records.

### Conflict Detection Rules

#### 1. Layup-Level Conflict

If a layup with the same `name` already exists under the same supplier:

- Treat it as the same layup candidate.
- Do **not** automatically create a new layup.

#### 2. Layer-Level Conflict

If:

- A layer with the same `layer_order` exists within that layup,
- **AND** one or more fields differ (`thickness`, `width`, `angle`),

→ This must be treated as a conflict.

---

### Required Conflict Handling

You must implement a clearly defined conflict resolution strategy.

At minimum, support **one** of the following:

- **Overwrite Existing**  
  (Incoming data replaces current data)

- **Skip Conflict**  
  (Keep current data, ignore incoming change)

- **Duplicate Layup**  
  (Create a new layup with a suffix such as `name (imported)`)

- **Reject Entire Import**  
  (Abort and return a detailed conflict report)

---

### Advanced Conflict Resolution (UI-Based – Bonus)

For additional bonus points, implement a **manual conflict resolution interface** similar to GitHub merge conflict resolution.

Expected behavior:

- Display **Existing Version (Current Data)** and  
  **Incoming Version (Imported Data)** side-by-side
- Highlight field-level differences
- Allow the user to choose:
    - ✅ Keep Existing
    - ✅ Accept Incoming
- Support resolving conflicts one-by-one
- Provide navigation (e.g., “1 of 3 discrepancies”)

This may be implemented as:

- A modal, or
- A dedicated conflict resolution page.

## 4. Design Reference

A design reference is available in Figma:

[Figma Design File](https://www.figma.com/design/odWJ887r00aslmSFPIHMCx/SPEC-Toolbox---Feature-Test?node-id=11001-35&t=XUggOaUUi9p8jGFG-1)

> The design is for reference only. Exact visual matching is not required.

## 5. Evaluation Criteria

### Main Evaluation

- Correct implementation of the required features

### Bonus Evaluation

**Architecture & Design Patterns**

- Use Repository and/or Service pattern
- Bind interfaces via a Service Provider

**Laravel Best Practices**

- Form Request validation
- Policies or Gates for authorization
- Proper use of Route Model Binding
- Clean, maintainable code following Laravel conventions

**Automated Testing**

- Unit tests (validation, services, repositories)
- Feature tests (CRUD and import/export flows)

**Additional Improvements**

- Any meaningful enhancements will be considered positively

## 6. Submission

The deadline will be provided via email.  
Please ensure submission within the specified timeframe.


## 7. Demo

Include one of the following with your submission:

- A demo video (recommended), or
- A live project link

Ensure the demo clearly showcases:

- [x] CRUD functionality
- [x] Import / Export feature
- [x] Conflict resolution behavior

## 8. Technical Implementation (Bonus Evaluation Criteria)

This project has been implemented with high-standard architecture and Laravel best practices to exceed core requirements.

### Architecture & Design Patterns
- **Service Pattern**: Business logic for data import, export, and conflict resolution is encapsulated within `ImportExportService` and model-specific services (e.g., `SupplierService`).
- **Repository Pattern**: Data access is abstracted using Repositories (e.g., `SupplierRepository`), allowing for easier testing and future database flexibility.
- **Service Provider Binding**: Interfaces are bound to concrete implementations in `RepositoryServiceProvider`, following the **Dependency Inversion Principle**.

### Laravel Best Practices
- **Form Request Validation**: Every POST/PUT request uses dedicated Form Requests (e.g., `SupplierRequest`, `ImportRequest`) to ensure strict data validation before processing.
- **Policies & Authorization**: Access control is implemented via Laravel Policies (`SupplierPolicy`, etc.), ensuring secure data access.
- **Route Model Binding**: Utilizes Laravel's implicit binding for clean, readable controller methods.
- **Semantic HTML & Clean UI**: Built with a focus on modern aesthetics, using Tailwind CSS and Alpine.js for interactive elements.

### Automated Testing
- **Feature Tests**: Comprehensive tests for CRUD operations and the complex Import/Export flow (including conflict resolution scenarios).
- **Unit Tests**: Granular tests for services and repository logic.
- Run tests using: `php artisan test`

### Additional Enhancements
- **Advanced Side-by-Side Conflict Resolution**: A custom UI that mimics GitHub's merge conflict resolution, allowing users to resolve discrepancies layer-by-layer.
- **Hierarchical Breadcrumbs**: Dynamic breadcrumb system to navigate the nested `Supplier → Layup → Layer` hierarchy effortlessly.
- **Dashboard Analytics**: Informative dashboard showing system-wide statistics and recent activities.
- **Interactive Visualizer**: Schematic CLT assembly visualizer on the Layup detail page.
