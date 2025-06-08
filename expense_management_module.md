# Expense Management Module Design

This document details the design of the Expense Management Module, outlining its features for tracking business operational costs and linking them to specific models or orders to calculate profitability.

## 1. Add Expense

This feature allows administrators or authorized staff to record business expenses.

### User Interface Elements:

*   **Input Fields:**
    *   **Expense Name/Description:** (Text input, mandatory, e.g., "Blue Silk Fabric Purchase," "Rent for March," "Tailor John Doe - Order 123 Labor")
    *   **Expense Category:** (Dropdown, mandatory, pre-populated and customizable list, e.g., "Raw Materials - Fabric," "Raw Materials - Threads," "Raw Materials - Buttons/Zippers," "Labor - Sewing," "Labor - Cutting," "Overheads - Rent," "Overheads - Utilities," "Marketing," "Shipping Supplies")
    *   **Amount:** (Number input, mandatory, currency symbol displayed)
    *   **Expense Date:** (Date picker, mandatory, defaults to today)
    *   **Vendor/Supplier (Optional):** (Text input, e.g., "Fabric Emporium," "Local Artisan Collective")
    *   **Link to Model (Optional):**
        *   **Model ID/Name Search:** A searchable dropdown or lookup field to find and select a model from the Models Management Module. (e.g., "Wedding Dress Style A")
    *   **Link to Client Order (Optional):**
        *   **Order ID Search:** A searchable dropdown or lookup field to find and select a client order from the Order Management Module. (e.g., "ORD-2024-00001")
    *   **Receipt/Invoice Attachment (Notional):**
        *   **Reference Number:** (Text input, e.g., Invoice #INV-7890)
        *   **File Upload (Optional):** A file input button ("Upload Receipt/Invoice") to attach a digital copy (e.g., .pdf, .jpg, .png). The actual storage mechanism will be defined during backend development (e.g., stored in a dedicated `expenses/{expense_id}/attachments/` path).
        *   **Uploaded File Display:** Shows the name of the uploaded file, with an option to remove/replace it before saving.
    *   **Notes (Optional):** (Text area for any additional details)
*   **Buttons:**
    *   **Save Expense:** (Button, enabled only after all mandatory fields are valid)
    *   **Cancel:** (Button, discards changes and returns to the expense list)

### Data Validation:

*   **Expense Name/Description:**
    *   Cannot be empty.
    *   Maximum length (e.g., 255 characters).
*   **Expense Category:**
    *   Must be selected from the provided list.
*   **Amount:**
    *   Must be a positive number.
    *   Can have up to two decimal places.
    *   Cannot be empty.
*   **Expense Date:**
    *   Must be a valid date.
    *   Should not be a future date (configurable, but typically expenses are for past or current events).
*   **Link to Model/Order:**
    *   If a value is entered, it must correspond to a valid Model ID or Order ID in the system.
*   **Receipt/Invoice Attachment:**
    *   If a file is uploaded, check for allowed file types and size limits (e.g., max 5MB).

### Linking Expense to Model/Order:

*   The purpose of linking is to allow for more granular cost analysis.
*   **Linking to Model:** Useful for direct costs associated with developing or producing a specific *type* of model (e.g., cost of unique materials for a new dress style being prototyped, or bulk purchase of embellishments used only on one model line).
*   **Linking to Client Order:** Useful for direct costs incurred for a *specific client's order* (e.g., cost of specific fabric purchased for that order, specific outsourced labor like embroidery for that order).
*   An expense can potentially be linked to both, or neither (for general overheads like rent).
*   The database schema will need foreign keys to `Models(ModelID)` and `Orders(OrderID)` which can be nullable.

### Receipt/Invoice Attachment:

*   This feature provides a way to keep a digital record of supporting documents.
*   The UI allows uploading a file. On saving the expense, this file is stored in a designated secure location, and its path or a reference ID is saved with the expense record.
*   This is "notional" in the design phase, meaning we acknowledge the need, but the full implementation of file storage and security is a backend task.

## 2. View Expenses

This feature allows users to browse, search, and filter recorded expenses.

### UI for Listing Expenses:

*   **Layout:** Table/list view.
*   **Columns:**
    *   Expense Date
    *   Expense Name/Description
    *   Category
    *   Amount
    *   Linked Model (Name or ID, if any)
    *   Linked Order (ID, if any)
    *   Vendor/Supplier (if any)
    *   Receipt (e.g., an icon indicating attachment, clickable to view/download)
    *   Actions (e.g., "Edit," "Delete")
*   **Pagination:** If the number of expenses is large.
*   **Sorting:** Ability to sort by columns like Date, Amount, Category.
*   **Summary Totals:** Display total expenses for the current view/filter at the bottom of the list.

### Filtering Options:

*   A dedicated filtering panel or dropdowns above the list.
*   **Filter By:**
    *   **Date Range:** (Start Date, End Date pickers)
    *   **Expense Category:** (Multi-select checkboxes or dropdown)
    *   **Linked Model:** (Search/select model name or ID)
    *   **Linked Client Order:** (Search/select Order ID)
    *   **Vendor/Supplier:** (Text search)
    *   **Amount Range:** (Min Amount, Max Amount inputs)
*   Filters should be combinable. "Clear Filters" button.

### Summary Information (Per Expense in List):

*   Expense Date
*   Expense Name/Description
*   Category
*   Amount
*   Indication of linked Model/Order (e.g., "Model: Wedding Dress A", "Order: ORD-2024-00015")
*   Icon for receipt attachment.

## 3. Calculate Model-Specific Costs (Reporting Feature)

This feature helps in understanding the direct costs associated with producing instances of a specific model, which informs pricing and profitability analysis.

### Functionality:

*   The system aggregates all expense entries that have been explicitly linked to a particular ModelID.
*   It can also (more complex) try to apportion costs from orders linked to that model type (e.g., if 5 orders for "Wedding Dress Style A" each had specific labor costs, these could be averaged or summed for "Wedding Dress Style A"). For simplicity, initial design might focus on directly linked expenses first.

### User Interface:

1.  **Selection Page/Area:**
    *   **"Model Cost Analysis"** section in a "Reports" area.
    *   **Select Model Dropdown/Search:** User chooses a model by name or ID.
    *   **Date Range (Optional):** User can specify a period for which to analyze costs (e.g., "analyze costs for Model X incurred in the last quarter"). Defaults to all-time.
    *   **"Generate Report" Button.**
2.  **Report Display View:**
    *   **Header:**
        *   Report Title: "Cost Analysis for [Model Name]"
        *   Selected Date Range (if any)
        *   Total Direct Costs for this Model: [Sum of all linked expenses]
    *   **Cost Breakdown Table:**
        *   Lists all individual expenses linked to this model within the specified date range.
        *   Columns: Expense Date, Expense Name/Description, Category, Amount, Linked Order (if the expense was also tied to a specific order of this model type).
    *   **Summary by Category (Optional but useful):**
        *   A pivot table or summary showing total expenses for this model, grouped by Expense Category (e.g., Raw Materials - Fabric: $X, Labor - Sewing: $Y).
    *   **Further Analysis (Advanced):**
        *   Compare with total revenue generated by this model (requires linking to Order Management's sales data).
        *   Calculate average cost per unit if multiple instances of the model have been produced and had costs logged.

This feature is primarily read-only for analysis.

## 4. Edit/Delete Expense

Allows for correction or removal of expense records.

### Selection for Editing/Deletion:

*   From the **View Expenses** list, each expense row has "Edit" and "Delete" buttons/icons.
*   Clicking "Edit" opens the "Add Expense" form pre-filled with the selected expense's data. All fields, including linked Model/Order and receipt attachment, are editable.
*   Clicking "Delete" initiates the deletion process.

### Confirmation Process for Deletion:

1.  User clicks the "Delete" button.
2.  A confirmation dialog/modal appears: "Are you sure you want to delete the expense '[Expense Name/Description]' amounting to [Amount]? This action cannot be undone."
3.  Buttons: "Confirm Delete," "Cancel."
4.  If "Confirm Delete" is clicked, the expense record is removed from the database. Any associated uploaded receipt file might also be deleted (or archived, depending on policy).

### Considerations for Finalized Reports:

*   **Concept of "Month-End Closing" or "Report Finalization":** Some businesses perform accounting closures (e.g., monthly or quarterly). Once an accounting period is "closed," expenses within that period might be locked to prevent changes that would affect finalized financial statements or profitability reports.
*   **Implementation Options:**
    *   **Simple:** No hard lock. Rely on user permissions and audit trails. A "Last Modified" timestamp on expenses is crucial.
    *   **Moderate:** Introduce a "Period Lock" feature. An administrator can mark a period (e.g., March 2024) as "Closed." Editing/deleting expenses within a closed period would then be disallowed or require special override permissions.
    *   **Advanced:** Full accounting integration with journaling, which is likely beyond the scope of this module unless it's a primary requirement.
*   For this design, we'll assume the **Simple approach** with good audit trails (created by, created date, last modified by, last modified date for each expense). Warnings could be displayed if editing an old expense.

This Expense Management Module provides essential tools for tracking costs and gaining insights into the financial aspects of the business, particularly when linked with models and orders.
