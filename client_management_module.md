# Client Management Module Design

This document details the design of the Client Management Module, outlining its features and their interactions, specifically for a bespoke tailoring or 3D model customization service where client measurements are crucial.

## 1. Register New Client

This feature allows administrators or staff to register new clients.

### User Interface Elements:

*   **Input Fields:**
    *   **Full Name:** (Text input, mandatory)
    *   **Email Address:** (Email input, mandatory, unique)
    *   **Phone Number:** (Tel input, mandatory, may include country code dropdown)
    *   **Address Line 1:** (Text input, optional)
    *   **Address Line 2:** (Text input, optional)
    *   **City:** (Text input, optional)
    *   **State/Province:** (Text input, optional)
    *   **Postal/Zip Code:** (Text input, optional)
    *   **Country:** (Dropdown, optional)
    *   **Notes:** (Text area, optional, for any general client notes)
*   **Buttons:**
    *   **Save Client:** (Button, enabled only after all mandatory fields are valid)
    *   **Cancel:** (Button, discards changes and returns to the client list or dashboard)

### Data Validation:

*   **Full Name:**
    *   Cannot be empty.
    *   Maximum length (e.g., 255 characters).
    *   Should allow letters, spaces, and common punctuation in names.
*   **Email Address:**
    *   Cannot be empty.
    *   Must be a valid email format (e.g., `user@example.com`).
    *   Should be checked for uniqueness in the system.
*   **Phone Number:**
    *   Cannot be empty.
    *   Should be validated for a plausible phone number format (digits, parentheses, hyphens, plus sign). Specific regional format validation might be too complex initially but can be considered.
    *   Minimum/maximum length can be enforced.
*   **Address Fields (Line 1, City, State, Postal Code, Country):**
    *   Optional, but if provided, may have length limits.
    *   Postal code may have format validation based on the selected country (complex, could be a future enhancement).
*   **Notes:**
    *   Max length (e.g., 2000 characters).

### Unique Client ID Generation:

1.  When the "Save Client" button is clicked and all data is validated:
2.  The system generates a unique Client ID before saving the record to the database.
3.  **Generation Strategy:**
    *   **Option A (Sequential Numeric):** e.g., `CL00001`, `CL00002`. Requires a mechanism to get the last ID and increment it.
    *   **Option B (UUID):** e.g., `f47ac10b-58cc-4372-a567-0e02b2c3d479`. Globally unique, easy to generate without database lookups for the next sequence. Can be long for display but very robust.
    *   **Option C (Combination):** A prefix with a timestamp and a short random string, e.g., `CLT-20240315-A3F7`.
4.  The chosen Client ID is stored as a primary or unique key in the client's database record.
5.  This ID is primarily for internal system use but can also be displayed in the UI for reference.

**Recommended Strategy:** UUID (Option B) is generally preferred for its uniqueness and scalability, though it might be less "human-readable" than a sequential ID. If human readability is critical, Option A or C can be used, but ensure the generation mechanism is robust against race conditions if multiple users can create clients simultaneously. For this design, we'll assume **UUID**.

## 2. View/Search Clients

This feature allows administrators/staff to find and view a list of registered clients.

### UI for Listing Clients:

*   **Layout:** Typically a table/list view.
*   **Columns:**
    *   Client ID (shortened or first/last part of UUID if too long)
    *   Full Name
    *   Email Address
    *   Phone Number
    *   Date Registered
    *   Actions (e.g., "View Details", "Edit")
*   **Pagination:** If the number of clients is large, pagination controls will be necessary (e.g., "Previous", "Next", page numbers).
*   **Sorting:** Ability to sort the list by clicking on column headers (e.g., Full Name, Date Registered).

### Search Functionality:

*   A prominent search bar is available above the client list.
*   Users can type search queries.
*   **Searchable Fields:**
    *   Full Name (partial matches, case-insensitive)
    *   Phone Number (exact or partial match)
    *   Email Address (exact or partial match)
    *   Client ID (exact match)
*   As the user types, the list dynamically updates, or they press "Enter"/"Search" button.
*   Search results are displayed in the same list format.
*   A "Clear Search" button or functionality to reset the view to all clients.

### Summary Information Shown Per Client in List:

*   **Client ID:** (e.g., `f47ac10b...d479` or a more user-friendly internal ID if used)
*   **Full Name:**
*   **Email Address:**
*   **Phone Number:**
*   **Date Registered:**
*   (Optional) Number of orders or last order date as a quick indicator of activity.

## 3. View Client Details

This feature provides a comprehensive view of a single client's information.

### Screen Description:

*   Accessed by clicking "View Details" or the client's name from the client list.
*   The screen is typically divided into sections for clarity.
*   **Header Section:**
    *   Client's Full Name (prominently displayed)
    *   Client ID
    *   "Edit Client" button.
*   **Contact Information Section:**
    *   Full Name
    *   Email Address
    *   Phone Number
    *   Full Address (Line 1, Line 2, City, State, Postal Code, Country)
    *   Client Notes
*   **Order History Section:**
    *   A summary list or table of the client's past and current orders.
    *   Key information per order: Order ID, Order Date, Status (e.g., Pending, In Progress, Completed, Cancelled), Total Amount.
    *   Each order in the list should be clickable, linking to the full Order Details page (part of an Order Management Module).
    *   If there are no orders, a message like "No orders found for this client."
*   **Measurement Sets Section:**
    *   A list or set of cards displaying the client's stored measurement sets.
    *   Each set shows:
        *   Measurement Label/Name (e.g., "Summer Dress 2024", "Standard Suit - Jan 2023")
        *   Timestamp (Date created/updated)
        *   A few key measurements as a preview (e.g., Bust, Waist, Hips).
    *   "View/Edit Measurements" button for each set.
    *   "Add New Measurement Set" button.
    *   If no measurements are stored, a message like "No measurements recorded for this client."

## 4. Edit Client Information

Allows modification of a client's registered details.

### Selecting Client for Editing:

*   From the **Client List View**, an "Edit" button/icon is present for each client row.
*   From the **View Client Details** screen, an "Edit Client" button is available.
*   Clicking "Edit" navigates the user to the client editing form.

### Pre-filling Editing Form:

*   The editing form is identical in structure to the "Register New Client" form.
*   When the form loads, all fields are pre-filled with the current data of the selected client:
    *   Full Name, Email, Phone, Address fields, and Notes display the existing text.
*   The Client ID is displayed (typically as read-only).
*   **Email Uniqueness:** If the email is changed, validation must ensure the new email is not already in use by another client (unless it's the same client record).
*   Buttons: "Save Changes", "Cancel".

## 5. Client Measurement Management

This feature allows for storing and managing multiple sets of physical measurements for each client, essential for bespoke services.

### Storing Multiple Measurement Sets:

*   Each client can have one or more sets of measurements.
*   Each measurement set is a distinct record in the database, linked to the Client ID.
*   This allows tracking changes in measurements over time or for different garment types.
    *   Example: A client might have different measurements for a tight-fitting dress versus a loose-fitting coat.

### Typical Measurement Fields:

The system should provide a default list of measurement fields. This list should be **customizable** by the administrator if possible (e.g., adding new measurement types or hiding unused ones via a separate settings area – this customization feature itself is advanced).

**Default Fields (Examples):**

*   **Upper Body:**
    *   Neck Circumference
    *   Full Chest / Bust
    *   Under Bust (if applicable)
    *   Waist Circumference
    *   Hips Circumference
    *   Shoulder Width (Point to Point)
    *   Sleeve Length (Left/Right if different)
    *   Bicep Circumference (Left/Right)
    *   Wrist Circumference (Left/Right)
    *   Shirt Length / Torso Length
*   **Lower Body:**
    *   Thigh Circumference (Left/Right)
    *   Knee Circumference (Left/Right)
    *   Calf Circumference (Left/Right)
    *   Ankle Circumference (Left/Right)
    *   Inseam
    *   Outseam
    *   Crotch Depth / Rise
*   **Height & Other:**
    *   Overall Height
    *   Weight (optional, but can be useful)
    *   Specific notes for this measurement set (e.g., "Taken with shoes on," "Prefers a looser fit")

All fields would typically store numerical values (e.g., in cm or inches, unit preference should be a system setting or per measurement set).

### Timestamp and Label for Measurement Sets:

*   **Timestamp:**
    *   Each measurement set automatically records:
        *   `CreatedAt`: Timestamp when the set was first saved.
        *   `UpdatedAt`: Timestamp of the last modification.
    *   These are system-managed.
*   **Label/Name:**
    *   A user-defined text field (mandatory) for each measurement set.
    *   Examples: "Initial Consultation - Jan 2024", "Measurements for Wedding Dress", "Summer Collection Fit Profile", "Standard Body Scan - March 2023".
    *   This helps identify the purpose or context of the measurement set.

### UI for Adding/Viewing/Editing Measurements:

*   **Access:**
    *   From the "View Client Details" screen, in the "Measurement Sets" section.
    *   Clicking "Add New Measurement Set" opens a form.
    *   Clicking "View/Edit Measurements" on an existing set opens the form pre-filled with those measurements.
*   **"Add New Measurement Set" Form:**
    *   **Label/Name:** Text input (mandatory).
    *   Input fields for all defined measurement types (e.g., Bust, Waist, Hips, etc.).
    *   Unit selector (cm/inches) if not globally set.
    *   Notes specific to this measurement set.
    *   Buttons: "Save Measurements", "Cancel".
*   **"Edit Measurement Set" Form:**
    *   Same as the "Add" form, but pre-filled with the selected measurement set's data.
    *   The Label/Name is editable.
    *   Timestamp (CreatedAt, UpdatedAt) are displayed as read-only information.
    *   Buttons: "Save Changes", "Cancel", potentially "Delete Measurement Set" (with confirmation).
*   **Viewing Measurement Sets (within Client Details):**
    *   Listed clearly with Label, Date, and key measurements.
    *   Could be expandable cards to show all measurements without going to a separate edit screen for quick review.

This design provides a comprehensive framework for the Client Management Module, with a particular emphasis on robust measurement handling.
Further technical details (API design, database schema) would be defined during the implementation phase.
