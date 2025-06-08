# Models Management Module Design

This document details the design of the Models Management Module, outlining its features and their interactions.

## 1. Add New Model

This feature allows administrators to add new 3D models to the platform.

### User Interface Elements:

*   **Input Fields:**
    *   **Model Name:** (Text input, mandatory)
    *   **Description:** (Text area, optional)
    *   **Category:** (Dropdown or searchable tags, mandatory, pre-populated with existing categories, option to add new category)
    *   **Price:** (Number input, mandatory, currency symbol displayed)
    *   **Designer/Artist:** (Text input, optional)
    *   **File Format:** (Dropdown, e.g., .STL, .OBJ, .FBX, mandatory)
    *   **Polygon Count:** (Number input, optional)
    *   **Texture Information:** (Text area, optional, e.g., "Includes 4K PBR textures: Albedo, Normal, Roughness, Metallic")
    *   **License Type:** (Dropdown, e.g., "Standard Royalty-Free", "Extended License", mandatory)
    *   **Tags/Keywords:** (Text input with typeahead suggestions, allows multiple tags, optional)
*   **Buttons:**
    *   **Upload Model File:** (File input button, accepts relevant 3D model file types)
    *   **Upload Thumbnail/Preview Image:** (File input button, accepts .jpg, .png)
    *   **Upload Additional Images/Renders:** (File input button, allows multiple selections, accepts .jpg, .png)
    *   **Save Model:** (Button, enabled only after all mandatory fields are valid)
    *   **Cancel:** (Button, discards changes and returns to the model list)

### Data Validation:

*   **Model Name:**
    *   Cannot be empty.
    *   Maximum length (e.g., 255 characters).
    *   Alphanumeric, spaces, and common symbols allowed.
*   **Description:**
    *   No specific validation, but may have a maximum length (e.g., 5000 characters).
*   **Category:**
    *   Must be selected from the provided list or a new valid category name must be entered.
*   **Price:**
    *   Must be a positive number.
    *   Can have up to two decimal places.
    *   Cannot be empty.
*   **Designer/Artist:**
    *   Maximum length (e.g., 255 characters).
*   **File Format:**
    *   Must be selected from the predefined list of supported formats.
*   **Polygon Count:**
    *   Must be a positive integer if provided.
*   **License Type:**
    *   Must be selected.
*   **Tags/Keywords:**
    *   Each tag should have a minimum/maximum length if specified.
*   **Model File:**
    *   Must be uploaded.
    *   File type must match the selected "File Format".
    *   File size limit (e.g., 500MB).
*   **Thumbnail/Preview Image:**
    *   Must be uploaded.
    *   File type must be .jpg or .png.
    *   File size limit (e.g., 5MB).
    *   Recommended aspect ratio/dimensions might be suggested.
*   **Additional Images/Renders:**
    *   File type must be .jpg or .png.
    *   File size limit per image (e.g., 5MB).
    *   Limit on the number of additional images (e.g., 10).

### Photo/File Upload and Association:

1.  User clicks the "Upload Model File" button. A file dialog appears.
2.  User selects the 3D model file. Client-side validation checks file type and size.
3.  Upon selection, the file starts uploading (potentially in the background with a progress bar).
4.  The same process applies to "Upload Thumbnail/Preview Image" and "Upload Additional Images/Renders."
5.  Uploaded files are temporarily stored.
6.  When the user clicks "Save Model":
    *   All validated data, including references to the uploaded files (e.g., stored paths or unique IDs), are sent to the server.
    *   The server then moves the temporary files to a permanent storage location (e.g., a specific folder structure like `/models/{model_id}/files/` and `/models/{model_id}/images/`).
    *   Database entries are created linking the model record with its associated file paths. The primary thumbnail is flagged as such.

## 2. View/Browse Models

This feature allows users (both administrators and clients, potentially with different levels of detail shown) to browse and find models.

### Layout for Displaying Models:

*   **Grid View (Default):**
    *   Displays models as cards in a responsive grid.
    *   Each card shows:
        *   Thumbnail image.
        *   Model Name.
        *   Price.
        *   Category (optional, could be a filter selection).
        *   Designer/Artist (optional).
        *   Average rating (if applicable).
    *   Hovering over a card might reveal quick actions like "Add to Cart" or "View Details."
*   **List View (Alternative):**
    *   Displays models in a table-like format.
    *   Each row represents a model and shows:
        *   Small thumbnail image.
        *   Model Name.
        *   Category.
        *   Price.
        *   File Format.
        *   Date Added.
        *   Actions (e.g., "View", "Edit" for admins).
    *   Allows sorting by different columns (Name, Price, Date Added, etc.).
*   **Layout Switcher:** A button or toggle to switch between Grid and List view.

### Filtering and Searching:

*   **Filtering:**
    *   **By Category:** A sidebar or dropdown menu lists all available categories. Selecting a category updates the view to show only models from that category. Multiple categories might be selectable.
    *   **By Price Range:** Sliders or input fields to define a min/max price.
    *   **By File Format:** Checkboxes or a multi-select dropdown for file formats.
    *   **By License Type:** Dropdown to select a specific license.
    *   Filters are applied additively (e.g., Category "Sci-Fi" AND Price "$10-$50").
    *   A "Clear Filters" button resets all active filters.
*   **Searching:**
    *   A prominent search bar is available at the top of the page.
    *   Users can type keywords.
    *   Search queries will look for matches in:
        *   Model Name
        *   Description
        *   Tags/Keywords
        *   Designer/Artist
    *   Search results are displayed in the chosen layout (Grid/List).
    *   Search can work in conjunction with filters (e.g., search "robot" within the "Characters" category).

### Information Shown:

*   **Summary View (Grid Card / List Row):**
    *   Thumbnail Image
    *   Model Name
    *   Price
    *   Key identifier like Category or Designer (context-dependent)
    *   Possibly an average user rating.
*   **Detailed View (Accessed by clicking on a model in summary view):**
    *   **Primary Display:** Large preview of the thumbnail image, potentially a 3D model viewer if integrated.
    *   **Image Gallery:** Thumbnails of all additional images/renders, clickable to view larger versions or in a carousel.
    *   **Model Information Section:**
        *   Model Name (as a prominent heading)
        *   Full Description
        *   Price
        *   Category
        *   Designer/Artist
        *   File Format(s) available
        *   Polygon Count
        *   Texture Information
        *   License Type (with a link to details about the license)
        *   Date Added/Last Updated
        *   Tags/Keywords
        *   Statistics (e.g., number of views, downloads - if tracked)
    *   **Actions:**
        *   "Add to Cart" / "Purchase"
        *   "Add to Wishlist"
        *   Social sharing buttons
        *   For Admins: "Edit Model", "Delete Model" buttons.

## 3. Edit Model

This feature allows administrators to modify the details of existing models.

### Selecting Model for Editing:

*   From the **List View** of models, an "Edit" button or icon is present for each model row.
*   From the **Detailed View** of a model, an "Edit Model" button is available for administrators.
*   Clicking "Edit" navigates the administrator to the editing form.

### Pre-filling Editing Form:

*   The editing form is identical in structure to the "Add New Model" form.
*   When the form loads, all fields are pre-filled with the current data of the selected model.
    *   Text fields (Name, Description, etc.) display the existing text.
    *   Dropdowns (Category, File Format, License Type) have the current value selected.
    *   Price and Polygon Count show the current numerical values.
    *   Tags/Keywords are pre-populated.
*   **File Management:**
    *   **Model File:** Shows the name of the currently uploaded model file. A "Replace Model File" button allows uploading a new version. Optionally, a "Delete Model File" button might be present, but this would need careful handling if the model is live.
    *   **Thumbnail/Preview Image:** Displays the current thumbnail. "Replace Thumbnail" button allows uploading a new one.
    *   **Additional Images/Renders:** Displays current additional images. Each image has options to "Remove" or "Replace." An "Upload More Images" button is also present.

### Versioning/Change Tracking (Considerations):

*   **Simple Approach (Out of Scope for Basic App):** No explicit versioning. Edits overwrite the current data. A "Last Updated" timestamp is modified.
*   **Moderate Approach:**
    *   Keep a log of major changes (e.g., model file replacement, significant price changes). This could be a simple text log associated with the model.
    *   When a model file is replaced, the old file could be archived (e.g., renamed with a version suffix or moved to an archive directory) rather than immediately deleted. This allows for potential rollbacks, though the UI for this wouldn't be part of this initial module.
*   **Advanced Approach (Full Version Control):**
    *   Each significant save creates a new version of the model entry in the database.
    *   This is generally too complex for a standard e-commerce platform for 3D models unless specific requirements necessitate it (e.g., managing iterative versions for client projects).

For this module, we will assume the **Simple Approach** but log the "Last Updated" timestamp. Replacing the main model file should be handled with care, perhaps warning the admin if the model has active sales.

## 4. Delete Model

This feature allows administrators to remove models from the platform.

### Confirmation Process:

1.  Administrator clicks the "Delete Model" button (either from the list view action or the detailed model view).
2.  A confirmation dialog/modal appears.
3.  The dialog asks: "Are you sure you want to delete the model '[Model Name]'? This action cannot be undone."
4.  Buttons: "Confirm Delete" and "Cancel."
5.  If "Confirm Delete" is clicked, the deletion process proceeds. If "Cancel" is clicked, the dialog closes, and no action is taken.

### Handling Linked Client Orders:

This is a critical consideration to maintain data integrity and customer access to purchased models.

*   **Option 1: Disallow Deletion (Safest for Active Models):**
    *   If a model is linked to any existing client orders (i.e., it has been purchased at least once and the order is not refunded/cancelled):
        *   The system prevents outright deletion.
        *   The "Delete Model" button might be disabled, or upon clicking "Confirm Delete," the system shows a message: "This model cannot be deleted because it is linked to existing client orders. Consider archiving it instead."
*   **Option 2: Mark as "Archived" or "Inactive" (Recommended):**
    *   Instead of actual deletion from the database, the model is flagged as "inactive," "archived," or "discontinued."
    *   **Effects:**
        *   The model is no longer visible in public browsing/searching.
        *   It cannot be purchased by new clients.
        *   Existing clients who purchased the model **must** still have access to download it from their order history/dashboard. This is crucial.
        *   Administrators can still see archived models in a special section of the management interface, with options to potentially "Unarchive" or (if truly necessary and safe) perform a permanent delete later.
    *   The "Delete Model" button in the UI would effectively trigger this "archive" status change. The confirmation message might be adjusted: "Are you sure you want to archive the model '[Model Name]'? It will no longer be available for new purchases but will remain accessible to existing customers."
*   **Option 3: Soft Deletion with Grace Period (More Complex):**
    *   Model is marked for deletion, becomes inactive, but actual data removal is delayed (e.g., for 30 days). This allows for a recovery window. Still needs to handle existing order access.

**Chosen Approach for this Design:** **Option 2 (Mark as "Archived" or "Inactive")** is generally the best practice. This ensures customers retain access to their purchases while allowing administrators to manage the active catalog. The term "Delete" in the UI for an admin might translate to an "Archive" action in the backend if orders are linked. If no orders are linked, it could be a permanent deletion, or for consistency, always an archive action. For simplicity, let's assume "Delete" means "Archive" if linked, and "Permanent Delete" if not linked, with appropriate confirmation messages.

**If permanently deleting (model has no orders):**
*   Associated model files and images are deleted from storage.
*   Database entries related to the model are removed.

This design provides a comprehensive overview of the Models Management Module.
Further details can be elaborated for specific backend implementation choices (database schema, API endpoints, etc.) when development begins.
