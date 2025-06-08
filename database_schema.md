# Data Management (Conceptual Database Structure)

This document outlines the conceptual database schema for the Fashion Customization Platform, based on the features defined in the previously designed modules.

## 1. Review of Implicit Table Definitions (from Modules)

Based on the module designs, the following entities and their key attributes were identified:

*   **Models:** ModelID, Name, Description, Category, BasePrice, PhotoURL(s), FileFormat, PolygonCount, TextureInfo, LicenseType, Tags.
*   **Clients:** ClientID, FullName, PhoneNumber, Email, Address (Street, City, State, PostalCode, Country), Notes.
*   **Measurements:** MeasurementID, ClientID, DateTaken/Label, Bust, Waist, Hips, and other specific measurement fields.
*   **Orders:** OrderID, ClientID, ModelID, OrderDate, EstimatedCompletionDate, CustomizationNotes, FinalPrice, OrderStatus, CustomizationCharges, Discount, Taxes, InternalOrderNotes.
*   **Payments:** PaymentID, OrderID, PaymentDate, AmountPaid, PaymentMethod, TransactionID, PaymentNotes.
*   **Expenses:** ExpenseID, Name/Description, Category, Amount, Date, LinkedModelID, LinkedOrderID, Vendor, ReceiptRef, ReceiptURL, Notes.

## 2. Refined Table Structures and New Tables

Below are the refined table structures, including Primary Keys (PK), Foreign Keys (FK), data types, audit fields, and new tables for normalization and consistency.

---

**Table: `Clients`**
Manages client information.

| Column Name     | Data Type     | Constraints        | Description                                  |
|-----------------|---------------|--------------------|----------------------------------------------|
| `ClientID`      | UUID          | PK                 | Unique identifier for the client             |
| `FullName`      | TEXT          | NOT NULL           | Client's full name                           |
| `Email`         | TEXT          | NOT NULL, UNIQUE   | Client's email address                       |
| `PhoneNumber`   | TEXT          | NOT NULL           | Client's phone number                        |
| `AddressLine1`  | TEXT          |                    |                                              |
| `AddressLine2`  | TEXT          |                    |                                              |
| `City`          | TEXT          |                    |                                              |
| `StateProvince` | TEXT          |                    |                                              |
| `PostalCode`    | TEXT          |                    |                                              |
| `Country`       | TEXT          |                    |                                              |
| `Notes`         | TEXT          |                    | General notes about the client               |
| `CreatedAt`     | DATETIME      | NOT NULL           | Timestamp of creation                        |
| `UpdatedAt`     | DATETIME      | NOT NULL           | Timestamp of last update                     |

---

**Table: `ModelCategories`** (New)
Stores categories for models.

| Column Name       | Data Type     | Constraints        | Description                          |
|-------------------|---------------|--------------------|--------------------------------------|
| `ModelCategoryID` | SERIAL        | PK                 | Unique identifier for model category |
| `CategoryName`    | TEXT          | NOT NULL, UNIQUE   | Name of the category (e.g., "Dresses") |
| `CreatedAt`       | DATETIME      | NOT NULL           | Timestamp of creation                |
| `UpdatedAt`       | DATETIME      | NOT NULL           | Timestamp of last update             |

---

**Table: `Models`**
Manages 3D models or customizable fashion items.

| Column Name         | Data Type     | Constraints        | Description                               |
|---------------------|---------------|--------------------|-------------------------------------------|
| `ModelID`           | UUID          | PK                 | Unique identifier for the model           |
| `ModelName`         | TEXT          | NOT NULL           | Name of the model                         |
| `Description`       | TEXT          |                    | Detailed description of the model         |
| `ModelCategoryID`   | INTEGER       | FK (ModelCategories) | Link to the model's category            |
| `BasePrice`         | REAL          | NOT NULL           | Starting price for the model              |
| `FileFormat`        | TEXT          |                    | e.g., .STL, .OBJ                          |
| `PolygonCount`      | INTEGER       |                    |                                           |
| `TextureInformation`| TEXT          |                    | Details about textures                    |
| `LicenseType`       | TEXT          |                    | e.g., "Standard Royalty-Free"             |
| `Tags`              | TEXT          |                    | Comma-separated tags or JSON array        |
| `IsArchived`        | BOOLEAN       | NOT NULL, DEFAULT FALSE | If the model is archived/discontinued |
| `Designer`          | TEXT          |                    | Designer/Artist of the model              |
| `ModelFileUrl`      | TEXT          |                    | Path/URL to the main 3D model file        |
| `CreatedAt`         | DATETIME      | NOT NULL           | Timestamp of creation                     |
| `UpdatedAt`         | DATETIME      | NOT NULL           | Timestamp of last update                  |

---

**Table: `ModelPhotos`** (New)
Stores multiple photos/renders for each model.

| Column Name     | Data Type     | Constraints        | Description                               |
|-----------------|---------------|--------------------|-------------------------------------------|
| `ModelPhotoID`  | SERIAL        | PK                 | Unique identifier for the model photo     |
| `ModelID`       | UUID          | FK (Models)        | Link to the model                         |
| `PhotoURL`      | TEXT          | NOT NULL           | URL or path to the image file             |
| `IsPrimary`     | BOOLEAN       | NOT NULL, DEFAULT FALSE | Indicates if this is the main preview image |
| `Caption`       | TEXT          |                    | Optional caption for the photo            |
| `UploadTimestamp`| DATETIME      | NOT NULL           | When the photo was uploaded               |
| `CreatedAt`     | DATETIME      | NOT NULL           | Timestamp of creation                     |
| `UpdatedAt`     | DATETIME      | NOT NULL           | Timestamp of last update                  |

---

**Table: `MeasurementSets`** (Refined from `Measurements`)
Stores sets of measurements for clients.

| Column Name        | Data Type     | Constraints        | Description                               |
|--------------------|---------------|--------------------|-------------------------------------------|
| `MeasurementSetID` | SERIAL        | PK                 | Unique ID for the measurement set         |
| `ClientID`         | UUID          | FK (Clients)       | Link to the client                        |
| `Label`            | TEXT          | NOT NULL           | User-defined label (e.g., "Summer Dress 2024") |
| `DateTaken`        | DATE          | NOT NULL           | Date measurements were taken/recorded     |
| `Notes`            | TEXT          |                    | Specific notes for this measurement set   |
| `CreatedAt`        | DATETIME      | NOT NULL           | Timestamp of creation                     |
| `UpdatedAt`        | DATETIME      | NOT NULL           | Timestamp of last update                  |
| `Bust`             | REAL          |                    | Measurement value                         |
| `Waist`            | REAL          |                    | Measurement value                         |
| `Hips`             | REAL          |                    | Measurement value                         |
| `ShoulderWidth`    | REAL          |                    | Measurement value                         |
| `SleeveLength`     | REAL          |                    | Measurement value                         |
| `Inseam`           | REAL          |                    | Measurement value                         |
| `Neck`             | REAL          |                    | ... (add other measurement fields as needed) |
| `Height`           | REAL          |                    |                                           |
| `Weight`           | REAL          |                    |                                           |
| `MeasurementUnit`  | TEXT          |                    | "cm" or "inches"                          |


---

**Table: `OrderStatuses`** (New)
Defines the possible statuses for an order.

| Column Name       | Data Type     | Constraints        | Description                                       |
|-------------------|---------------|--------------------|---------------------------------------------------|
| `OrderStatusID`   | SERIAL        | PK                 | Unique identifier for the order status            |
| `StatusName`      | TEXT          | NOT NULL, UNIQUE   | Name of the status (e.g., "In Progress")          |
| `Description`     | TEXT          |                    | Optional description of what the status means     |
| `SortOrder`       | INTEGER       |                    | For ordering statuses in UI if needed             |
| `IsSystemDefault` | BOOLEAN       | DEFAULT FALSE      | Indicates if this is a default status for new orders |
| `IsFinal`         | BOOLEAN       | DEFAULT FALSE      | Indicates if this is a terminal status (e.g. Completed, Cancelled) |


---

**Table: `Orders`**
Manages client orders.

| Column Name             | Data Type     | Constraints           | Description                                      |
|-------------------------|---------------|-----------------------|--------------------------------------------------|
| `OrderID`               | UUID          | PK                    | Unique identifier for the order                  |
| `ClientID`              | UUID          | FK (Clients)          | Link to the client placing the order             |
| `ModelID`               | UUID          | FK (Models)           | Link to the base model ordered                   |
| `MeasurementSetID`      | INTEGER       | FK (MeasurementSets), NULLABLE | Link to the specific measurements used for this order |
| `OrderDate`             | DATETIME      | NOT NULL              | Date and time the order was placed               |
| `EstimatedCompletionDate`| DATE         |                       |                                                  |
| `ActualCompletionDate`  | DATE          |                       |                                                  |
| `CustomizationNotes`    | TEXT          |                       | Specific requests from the client                |
| `InternalOrderNotes`    | TEXT          |                       | Notes for staff regarding the order              |
| `BaseModelPrice`        | REAL          | NOT NULL              | Price of the model at time of order              |
| `CustomizationCharges`  | REAL          | DEFAULT 0             | Additional charges for customization           |
| `DiscountAmount`        | REAL          | DEFAULT 0             | Any discount applied to the order                |
| `TaxAmount`             | REAL          | DEFAULT 0             | Taxes applied to the order                       |
| `FinalPrice`            | REAL          | NOT NULL              | Total price (Base + Customization - Discount + Tax) |
| `OrderStatusID`         | INTEGER       | FK (OrderStatuses)    | Current status of the order                      |
| `CreatedAt`             | DATETIME      | NOT NULL              | Timestamp of creation                            |
| `UpdatedAt`             | DATETIME      | NOT NULL              | Timestamp of last update                         |

---

**Table: `OrderCustomReferencePhotos`** (New)
Stores client-provided reference photos for order customizations.

| Column Name          | Data Type     | Constraints        | Description                               |
|----------------------|---------------|--------------------|-------------------------------------------|
| `ReferencePhotoID`   | SERIAL        | PK                 | Unique ID for the reference photo         |
| `OrderID`            | UUID          | FK (Orders)        | Link to the order                         |
| `PhotoURL`           | TEXT          | NOT NULL           | URL or path to the image file             |
| `Notes`              | TEXT          |                    | Optional notes about the reference image  |
| `UploadTimestamp`    | DATETIME      | NOT NULL           |                                           |
| `CreatedAt`          | DATETIME      | NOT NULL           | Timestamp of creation                     |

---

**Table: `PaymentMethods`** (New)
Defines accepted payment methods.

| Column Name        | Data Type     | Constraints        | Description                               |
|--------------------|---------------|--------------------|-------------------------------------------|
| `PaymentMethodID`  | SERIAL        | PK                 | Unique identifier for payment method      |
| `MethodName`       | TEXT          | NOT NULL, UNIQUE   | Name of method (e.g., "Credit Card", "Cash")|
| `IsEnabled`        | BOOLEAN       | NOT NULL, DEFAULT TRUE | If the method is currently active         |
| `CreatedAt`        | DATETIME      | NOT NULL           | Timestamp of creation                     |
| `UpdatedAt`        | DATETIME      | NOT NULL           | Timestamp of last update                  |

---

**Table: `Payments`**
Tracks payments made for orders.

| Column Name       | Data Type     | Constraints           | Description                               |
|-------------------|---------------|-----------------------|-------------------------------------------|
| `PaymentID`       | SERIAL        | PK                    | Unique identifier for the payment         |
| `OrderID`         | UUID          | FK (Orders)           | Link to the order this payment is for     |
| `PaymentDate`     | DATETIME      | NOT NULL              | Date and time of payment                  |
| `AmountPaid`      | REAL          | NOT NULL              | Amount paid in this transaction           |
| `PaymentMethodID` | INTEGER       | FK (PaymentMethods)   | How the payment was made                  |
| `TransactionID`   | TEXT          |                       | Optional reference for the transaction    |
| `PaymentNotes`    | TEXT          |                       | Any notes related to this payment         |
| `CreatedAt`       | DATETIME      | NOT NULL              | Timestamp of creation                     |
| `UpdatedAt`       | DATETIME      | NOT NULL              | Timestamp of last update                  |

---

**Table: `ExpenseCategories`** (New)
Stores categories for expenses.

| Column Name          | Data Type     | Constraints        | Description                             |
|----------------------|---------------|--------------------|-----------------------------------------|
| `ExpenseCategoryID`  | SERIAL        | PK                 | Unique identifier for expense category  |
| `CategoryName`       | TEXT          | NOT NULL, UNIQUE   | Name of the category (e.g., "Fabric")   |
| `CreatedAt`          | DATETIME      | NOT NULL           | Timestamp of creation                   |
| `UpdatedAt`          | DATETIME      | NOT NULL           | Timestamp of last update                |

---

**Table: `Expenses`**
Tracks business expenses.

| Column Name         | Data Type     | Constraints                | Description                               |
|---------------------|---------------|----------------------------|-------------------------------------------|
| `ExpenseID`         | SERIAL        | PK                         | Unique identifier for the expense         |
| `ExpenseName`       | TEXT          | NOT NULL                   | Short description of the expense          |
| `ExpenseCategoryID` | INTEGER       | FK (ExpenseCategories)     | Category of the expense                   |
| `Amount`            | REAL          | NOT NULL                   | Amount of the expense                     |
| `ExpenseDate`       | DATE          | NOT NULL                   | Date the expense was incurred             |
| `Vendor`            | TEXT          |                            | Supplier/Vendor                           |
| `LinkedModelID`     | UUID          | FK (Models), NULLABLE      | Optional link to a specific model         |
| `LinkedOrderID`     | UUID          | FK (Orders), NULLABLE      | Optional link to a specific client order  |
| `ReceiptReference`  | TEXT          |                            | Invoice or receipt number                 |
| `ReceiptURL`        | TEXT          |                            | Path/URL to scanned receipt/invoice       |
| `Notes`             | TEXT          |                            | Additional notes about the expense        |
| `CreatedAt`         | DATETIME      | NOT NULL                   | Timestamp of creation                     |
| `UpdatedAt`         | DATETIME      | NOT NULL                   | Timestamp of last update                  |

---

**Table: `Users` (for Staff/User Management Module - Conceptual)**
Manages staff/admin user accounts.

| Column Name        | Data Type     | Constraints        | Description                               |
|--------------------|---------------|--------------------|-------------------------------------------|
| `UserID`           | UUID          | PK                 | Unique identifier for the user            |
| `FullName`         | TEXT          | NOT NULL           | User's full name                          |
| `Email`            | TEXT          | NOT NULL, UNIQUE   | User's login email                        |
| `PasswordHash`     | TEXT          | NOT NULL           | Hashed password                           |
| `RoleID`           | INTEGER       | FK (Roles)         | Link to the user's role                   |
| `IsActive`         | BOOLEAN       | NOT NULL, DEFAULT TRUE | If the user account is active             |
| `LastLogin`        | DATETIME      |                    | Timestamp of last successful login        |
| `CreatedAt`        | DATETIME      | NOT NULL           | Timestamp of creation                     |
| `UpdatedAt`        | DATETIME      | NOT NULL           | Timestamp of last update                  |

---

**Table: `Roles` (for Staff/User Management Module - Conceptual)**
Defines user roles within the system.

| Column Name     | Data Type     | Constraints        | Description                               |
|-----------------|---------------|--------------------|-------------------------------------------|
| `RoleID`        | SERIAL        | PK                 | Unique identifier for the role            |
| `RoleName`      | TEXT          | NOT NULL, UNIQUE   | Name of the role (e.g., "Admin", "Tailor")|
| `Description`   | TEXT          |                    |                                           |
| `CreatedAt`     | DATETIME      | NOT NULL           | Timestamp of creation                     |
| `UpdatedAt`     | DATETIME      | NOT NULL           | Timestamp of last update                  |

---

**Table: `RolePermissions` (for Staff/User Management Module - Conceptual)**
Junction table for many-to-many relationship between Roles and Permissions.

| Column Name      | Data Type     | Constraints        | Description                               |
|------------------|---------------|--------------------|-------------------------------------------|
| `RoleID`         | INTEGER       | PK, FK (Roles)     |                                           |
| `PermissionID`   | INTEGER       | PK, FK (Permissions) |                                           |
| `CreatedAt`      | DATETIME      | NOT NULL           | Timestamp of creation                     |

---

**Table: `Permissions` (for Staff/User Management Module - Conceptual)**
Defines specific permissions in the system.

| Column Name        | Data Type     | Constraints        | Description                                     |
|--------------------|---------------|--------------------|-------------------------------------------------|
| `PermissionID`     | SERIAL        | PK                 | Unique identifier for the permission            |
| `PermissionName`   | TEXT          | NOT NULL, UNIQUE   | e.g., "CreateClient", "EditOrder", "ViewModelCosts" |
| `Description`      | TEXT          |                    |                                                 |
| `CreatedAt`        | DATETIME      | NOT NULL           | Timestamp of creation                           |
| `UpdatedAt`        | DATETIME      | NOT NULL           | Timestamp of last update                        |

---

## 3. Relationships

*   **Clients to MeasurementSets:** One-to-Many (A client can have many measurement sets).
*   **Clients to Orders:** One-to-Many (A client can have many orders).
*   **ModelCategories to Models:** One-to-Many (A category can have many models).
*   **Models to ModelPhotos:** One-to-Many (A model can have many photos).
*   **Models to Orders:** One-to-Many (A model can be part of many orders).
*   **MeasurementSets to Orders:** One-to-One (An order uses one specific measurement set; a measurement set could potentially be reused for multiple orders but an order instance points to one). This could also be Many-to-Many if a single measurement set instance is explicitly shared and uneditable once used in an order, but the current design implies a snapshot or specific selection for an order. For simplicity, `Orders.MeasurementSetID` is a direct FK.
*   **OrderStatuses to Orders:** One-to-Many (An order status can apply to many orders).
*   **Orders to Payments:** One-to-Many (An order can have multiple payments).
*   **Orders to OrderCustomReferencePhotos:** One-to-Many (An order can have multiple custom reference photos).
*   **PaymentMethods to Payments:** One-to-Many (A payment method can be used for many payments).
*   **ExpenseCategories to Expenses:** One-to-Many (An expense category can have many expenses).
*   **Models to Expenses:** One-to-Many (A model can be linked to many expenses). (Optional link)
*   **Orders to Expenses:** One-to-Many (An order can be linked to many expenses). (Optional link)
*   **Users to Roles:** Many-to-One (A user has one role). More complex setups could use a UserRoles junction table for multiple roles per user, but one role is simpler to start.
*   **Roles to Permissions:** Many-to-Many (via `RolePermissions` junction table). A role can have many permissions, and a permission can be assigned to many roles.


## 4. Summary of New/Refined Tables

*   **`ModelCategories`**: For model categorization.
*   **`ModelPhotos`**: For multiple model images.
*   **`MeasurementSets`**: Refined `Measurements` table, emphasizing sets.
*   **`OrderStatuses`**: For predefined order statuses.
*   **`OrderCustomReferencePhotos`**: For client-provided images for specific orders.
*   **`PaymentMethods`**: For predefined payment methods.
*   **`ExpenseCategories`**: For expense categorization.
*   **`Users`, `Roles`, `Permissions`, `RolePermissions`**: Added conceptually for the Staff/User Management module.

## 5. Key Considerations

*   **Data Types:** Chosen for general purpose (TEXT for strings, REAL for monetary values, INTEGER for IDs or counts, DATE/DATETIME for time, BOOLEAN for true/false). Specific database systems (PostgreSQL, MySQL, etc.) might have more specialized types (e.g., VARCHAR, DECIMAL, TIMESTAMP). UUIDs are chosen for globally unique IDs for key tables like Clients, Models, Orders, Users. SERIAL is used for auto-incrementing integer PKs in simpler lookup/category tables.
*   **Indexing:** Important fields for searching and joining (FKs, IDs, names, dates, statuses) should be indexed for performance.
*   **Constraints:** NOT NULL, UNIQUE, and FK constraints are vital for data integrity. Default values are used where appropriate.
*   **Custom Fields in Measurements:** The `MeasurementSets` table lists common fields. If extreme customization of measurement fields is needed per client/platform, a more complex Entity-Attribute-Value (EAV) model or JSON field might be considered for those specific measurements, but this adds complexity. The current fixed-field approach is simpler.
*   **Archiving vs. Deleting:** For critical data like Orders, Clients, Models (if linked to orders), a soft delete (e.g., `IsArchived` flag) is often preferred over hard deletion to maintain historical data and referential integrity. The `Models.IsArchived` field is an example. Similar flags could be added to other tables if needed.

This conceptual schema provides a solid foundation for the platform's database. It will need further refinement during detailed technical design and implementation.
