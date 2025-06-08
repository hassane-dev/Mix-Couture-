# Order Management Module Design

This document details the design of the Order Management Module, outlining its features and their interactions. This module is central to the platform, connecting clients, models, and specific customizations.

## 1. Create New Order

This feature allows administrators or staff to create a new order for a client.

### Process Flow and UI Elements:

The process will likely be a multi-step form or a single page with clearly defined sections.

**Step 1: Select or Register Client**
*   **UI Elements:**
    *   **Search Existing Client:** A search bar to find clients by Name, Client ID, Email, or Phone Number. Search results appear in a list below.
    *   **Select Client Button:** Next to each client in the search results.
    *   **"Register New Client" Button:** If the client is not found, this button navigates to a condensed version of the client registration form (see Client Management Module) or opens it in a modal. Upon saving the new client, they are automatically selected for the current order.
*   **Selected Client Display:** Once a client is selected, their name and primary contact (e.g., email/phone) are displayed.

**Step 2: Select Model**
*   **UI Elements:**
    *   **Search/Browse Models:** A search bar to find models by Name or ID. A "Browse Models" button might open a modal window with a paginated grid/list view of models (similar to the "View/Browse Models" feature in the Models Management Module), allowing selection.
    *   **Model Details Preview:** Once a model is selected, its thumbnail, name, base price, and brief description are displayed.
*   **Selected Model Display:** Shows the chosen model's name and base price.

**Step 3: Input/Select Client's Measurements**
*   **UI Elements:**
    *   **"Use Stored Measurements" Dropdown/List:** Lists the client's existing measurement sets by their label and date (e.g., "Summer Dress 2024 - Jan 15, 2024"). Selecting one pre-fills the measurement fields.
    *   **"Enter New Measurements" Button/Option:** Reveals input fields for all relevant measurements (as defined in Client Management Module, e.g., Bust, Waist, Hips, etc.).
    *   **Measurement Fields:** Standard input fields for each measurement. These are pre-filled if a stored set is chosen.
    *   **"Save these measurements for future use?" Checkbox:** If new measurements are entered, this option allows saving them to the client's profile with a prompt for a label.
*   **Consideration:** The system should highlight or require specific measurements based on the selected model (e.g., a dress might require bust, waist, hips, while trousers require waist, inseam, hips). This is an advanced refinement.

**Step 4: Add Customization Notes**
*   **UI Elements:**
    *   **Customization Notes Text Area:** A large text field for detailing specific requests, fabric choices, alterations from the base model, color preferences, etc. (e.g., "Use silk lining, color navy blue. Add 2 inches to hem length. No embroidery on collar.")
    *   **File Upload for Reference Images (Optional):** Button to upload images provided by the client for specific design elements or inspiration.

**Step 5: Calculate Final Price**
*   **UI Elements (Display Only & Admin Input):**
    *   **Base Model Price:** Displayed (read-only, from selected model).
    *   **Customization Charges Input Field:** (Number input) For admin to add any extra charges due to complex customizations, special materials, or significant alterations. Default is 0.
    *   **Discount Input Field:** (Number input or percentage) For applying discounts. Default is 0.
    *   **Subtotal:** (Calculated: Base Price + Customization Charges)
    *   **Taxes (if applicable):** (Percentage input or calculated based on rules)
    *   **Total Price:** (Calculated: Subtotal - Discount + Taxes) Displayed clearly.

**Step 6: Set Estimated Completion Date & Finalize**
*   **UI Elements:**
    *   **Estimated Completion Date Picker:** A calendar input for selecting the expected date the order will be ready.
    *   **Order Notes (Internal):** Text area for any internal notes about the order (e.g., "Client needs this urgently," "Check material availability").
    *   **"Create Order" / "Save Order" Button:** Submits the new order.
    *   **"Cancel" Button:** Discards the new order form.

## 2. View Order Details

This screen provides a comprehensive overview of a specific order.

### Screen Description:

Accessed by clicking on an order from the "View Order History" list.

*   **Order Header:**
    *   **Order ID:** (e.g., `ORD-2024-00001`) Prominently displayed.
    *   **Order Status:** (e.g., "In Progress") Clearly visible, perhaps color-coded.
    *   "Update Status" Button (for admins).
    *   "Edit Order" Button (for admins, may be restricted based on status).
    *   "Print Invoice/Receipt" Button.
*   **Client Details Section:**
    *   Full Name
    *   Email Address
    *   Phone Number
    *   Shipping/Billing Address (if applicable)
    *   Link to the full Client Details page.
*   **Model Details Section:**
    *   Model Name
    *   Thumbnail Image of the model
    *   Key attributes (e.g., SKU, base price)
    *   Link to the full Model Details page.
*   **Measurements Used Section:**
    *   Displays the specific set of measurements that were recorded or selected for this order.
    *   Lists each measurement field and its value (e.g., Bust: 92cm, Waist: 75cm).
    *   Label of the measurement set if a stored one was used (e.g., "Measurements for Summer Dress 2024").
*   **Customization Notes Section:**
    *   Displays the full text of customization notes provided.
    *   Lists any uploaded reference files with links to view/download them.
*   **Pricing & Payment Section:**
    *   **Itemized Breakdown:**
        *   Base Model Price: $X
        *   Customization Charges: $Y
        *   Subtotal: $Z
        *   Discount (if any): -$D
        *   Taxes (if any): $T
    *   **Total Order Price:** $TOTAL
    *   **Payment Status:**
        *   Amount Paid: $P
        *   Payment Method(s) Used (e.g., "Credit Card ending 1234 on Mar 15, Cash on Mar 10")
        *   Balance Due: $(TOTAL - P)
    *   "Record Payment" Button (for admins).
*   **Key Dates Section:**
    *   Order Date: (Date the order was created)
    *   Estimated Completion Date: (As set during order creation or later updated)
    *   Actual Completion Date: (Filled when order status is "Completed")
    *   Last Updated Date: (Timestamp of the last modification to the order)
*   **Order Log/Activity (Optional, Advanced):**
    *   A chronological list of significant events for the order (e.g., "Order created," "Status changed to In Progress by Admin X," "Payment of $50 recorded").

## 3. Update Order Status

Allows administrators/staff to track the progress of an order through its lifecycle.

### Process:

1.  Admin accesses the "View Order Details" screen or a quick action from the order list.
2.  Clicks an "Update Status" button.
3.  A dropdown or modal appears with available statuses.
    *   **Standard Statuses:**
        *   `Pending Confirmation` (Newly created, awaiting review/payment)
        *   `Payment Pending` (Awaiting deposit or full payment)
        *   `Confirmed` / `In Production` (Payment received, work started)
        *   `On Hold` (Work paused, e.g., awaiting client feedback)
        *   `Alterations Required` (Post-production adjustments needed)
        *   `Quality Check`
        *   `Ready for Pickup/Shipping`
        *   `Completed` (Client received item, order fulfilled)
        *   `Cancelled` (Order terminated by client or admin)
        *   `Refunded`
    *   The list of statuses should be configurable by the system administrator.
4.  Admin selects the new status.
5.  Optionally, admin can add a note regarding the status change (e.g., "Materials arrived, moving to In Production").
6.  Clicks "Save" or "Update".
7.  The order's status is updated in the database, and a timestamp for this change is recorded.

### Notifications (Advanced Feature Consideration):

*   When an order status changes, the system could automatically trigger notifications:
    *   **To Client:** Via email or SMS (e.g., "Your order [Order ID] is now In Production," "Your order [Order ID] is Ready for Pickup").
    *   **To Staff:** For internal workflow (e.g., notify the tailoring team when an order is "Confirmed").
*   Notification templates would be managed in a separate settings area.

## 4. Manage Order Payments

Handles the financial aspects of an order.

### UI for Recording Payments:

*   Accessed from the "View Order Details" screen via a "Record Payment" button.
*   **Input Fields in "Record Payment" Modal/Form:**
    *   **Payment Amount:** (Number input, mandatory)
    *   **Payment Date:** (Date picker, defaults to today)
    *   **Payment Method:** (Dropdown: "Cash," "Credit Card," "Bank Transfer," "Online Payment Gateway," "Other" - list should be configurable)
    *   **Transaction ID/Reference:** (Text input, optional, e.g., for card transaction ID, check number)
    *   **Payment Notes:** (Text area, optional, e.g., "Advance payment for materials")
*   **Buttons:** "Save Payment," "Cancel."

### Handling Payments:

*   When a payment is saved:
    *   A new payment record is created in the database, linked to the order.
    *   The `Amount Paid` on the order is updated by adding the new payment amount.
    *   The `Balance Due` is recalculated (`Total Price` - `New Amount Paid`).
    *   If `Balance Due` is zero or less, the order might automatically be marked as "Paid in Full" (a payment status distinct from order processing status).
*   Multiple payments can be recorded against a single order (e.g., an initial deposit and a final payment).

### Tracking Outstanding Balance:

*   The "View Order Details" screen always displays:
    *   Total Order Price
    *   Total Amount Paid
    *   Current Balance Due
*   This information is updated in real-time as payments are recorded.

### Digital Invoice/Receipt Generation:

*   A "Print Invoice/Receipt" button on the "View Order Details" screen generates a simple, printable HTML page or PDF.
*   **Key Information to Include:**
    *   Your Company Name, Address, Contact Info, Logo
    *   Client Name, Address (if available), Contact Info
    *   Order ID
    *   Order Date
    *   Invoice Number (can be same as Order ID or a separate sequence)
    *   Invoice Date
    *   **Line Items:**
        *   Model Name/Description, Quantity (usually 1), Unit Price (Base Model Price), Total Price
        *   Customization Charges (as a separate line item or included in model price details)
    *   Subtotal
    *   Discount (if any)
    *   Taxes (if any, specifying type and rate)
    *   **Total Amount Due**
    *   Amount Paid
    *   **Balance Due**
    *   Payment terms (e.g., "Due upon receipt," "50% advance, 50% on completion")
    *   Estimated Completion Date (optional on invoice, but good for client record)
    *   Thank you note or other relevant business information (e.g., return policy summary).

## 5. View Order History

Allows users (admins, and potentially clients with a filtered view of their own orders) to see a list of all orders.

### UI for Listing Orders:

*   **Layout:** Table/list view.
*   **Columns (for Admin View):**
    *   Order ID
    *   Client Name
    *   Model Name (or primary item)
    *   Order Date
    *   Estimated Completion Date
    *   Total Price
    *   Amount Paid
    *   Balance Due
    *   Order Status
    *   Actions (e.g., "View Details", quick status update)
*   **Pagination:** Essential for handling a large number of orders.

### Filtering Options:

*   A dedicated filtering panel or dropdowns above the list.
*   **Filter By:**
    *   **Client:** Search/select client name or ID.
    *   **Order Status:** Multi-select checkboxes or dropdown (e.g., "Pending," "In Progress," "Completed").
    *   **Date Range:**
        *   Order Date (e.g., "Last 30 days," "Custom Range").
        *   Estimated Completion Date.
    *   **Payment Status:** (e.g., "Paid in Full," "Partial Payment," "Unpaid").
    *   **Model Name/ID (less common but possible).**
*   Filters should be combinable. "Clear Filters" button.

### Summary Information (Per Order in List):

*   Order ID
*   Client Name
*   Key Item/Model Name
*   Order Date
*   Total Price
*   Balance Due (or Payment Status icon, e.g., green for paid, orange for partial, red for unpaid)
*   Current Order Status

This design provides a comprehensive framework for managing orders from creation to completion, integrating various aspects of the platform. Implementation will involve careful database design to link orders with clients, models, measurements, and payments.
