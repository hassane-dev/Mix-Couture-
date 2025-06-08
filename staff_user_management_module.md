# Staff/User Management Module Design

This document details the design of the Staff/User Management Module, which is responsible for authentication, authorization, and administration of user accounts for the Fashion Customization Platform's backend system.

## 1. User Roles

User roles define the level of access and control users have within the system. For this platform, we will focus on backend staff roles.

*   **`Admin` (Administrator):**
    *   **Purpose:** Has full and unrestricted control over the entire system. Responsible for system configuration, user management, and oversight of all data.
    *   **Permissions:** Can perform all actions (Create, Read, Update, Delete - CRUD) on all data modules (Models, Clients, Orders, Expenses). Can manage other staff accounts (create, edit, deactivate/activate, assign roles). Can access and modify system-level settings (e.g., defining expense categories, order statuses, payment methods, potentially customizing measurement fields if that feature is implemented).
    *   **Hierarchy:** Highest level role.

*   **`Staff` (Operational User):**
    *   **Purpose:** Represents day-to-day operational users of the platform who manage the core business processes.
    *   **Permissions:** Can perform most CRUD operations on the primary data modules: Models, Clients, Orders (including creating orders, updating status, managing payments), and Expenses. Typically cannot manage other user accounts (except possibly their own profile/password) or access critical system-wide settings.
    *   **Hierarchy:** Below Admin. Does not inherit Admin rights but has a broad set of operational permissions.

**Note on Customer Access:**
As per the prompt, this design focuses on backend staff (Admin, Staff). Direct customer login to this backend system is out of scope. If customers were to have access, it would likely be through a separate frontend application with its own user management, potentially federating identities or using API tokens if needed, and would have a very restricted "Customer" role (e.g., view own orders, browse models).

**Role Characteristics:**
*   Roles are distinct but can be seen as hierarchical in terms of authority (Admin > Staff).
*   A user is assigned one primary role.

## 2. Authentication

Authentication is the process of verifying a user's identity.

*   **Primary Method:**
    *   **Username/Email and Password Login:** Users will provide their registered email address (acting as username) and a password to access the system.
*   **Password Policies:**
    *   **Minimum Length:** e.g., 10 characters.
    *   **Complexity Requirements:** Enforced during password set/change. Must include:
        *   At least one uppercase letter.
        *   At least one lowercase letter.
        *   At least one number.
        *   At least one special symbol (e.g., !@#$%^&*).
    *   Policies should be clearly communicated to users during account creation or password changes.
*   **Secure Password Storage:**
    *   Passwords must **never** be stored in plain text.
    *   **Hashing:** Use a strong, modern, adaptive hashing algorithm (e.g., Argon2, scrypt, or bcrypt).
    *   **Salting:** A unique, randomly generated salt must be used for each user's password before hashing. The salt is stored alongside the hashed password in the `Users` table.
*   **Session Management:**
    *   **Login:** Upon successful authentication, a secure session is created.
    *   **Session Token:** A cryptographically strong, random session token is generated and stored by the server. This token is then sent to the client (e.g., via an HTTP-only, secure cookie).
    *   **Session Validation:** On subsequent requests, the client sends the session token, which the server validates.
    *   **Session Timeout:** Sessions should automatically expire after a period of inactivity (e.g., 30 minutes) or a maximum session duration (e.g., 8 hours).
    *   **Logout:** Users must have a clear way to log out, which invalidates the session token on the server-side and clears it from the client.
*   **Optional Advanced Features (Future Considerations):**
    *   **Password Reset Mechanism:** A secure "Forgot Password" flow involving email verification and a unique, time-limited reset link.
    *   **Two-Factor Authentication (2FA):** An additional layer of security requiring a second form of verification (e.g., TOTP app, SMS code).
    *   **Account Lockout:** Temporarily lock an account after a certain number of failed login attempts to prevent brute-force attacks.

## 3. Authorization (Permissions)

Authorization determines what actions an authenticated user is allowed to perform. Permissions are primarily derived from the user's assigned role.

| Module / Feature         | Action                     | `Admin` Role | `Staff` Role | Notes                                                                 |
|--------------------------|----------------------------|--------------|--------------|-----------------------------------------------------------------------|
| **Models Management**    | Create, Read, Update, Delete | Yes          | Yes          | Staff can fully manage model catalog.                                 |
|                          | Archive/Unarchive Model    | Yes          | Yes          |                                                                       |
| **Model Categories**     | Create, Read, Update, Delete | Yes          | No           | Managed by Admin as part of system setup. Staff use existing.         |
| **Clients Management**   | Create, Read, Update, Delete | Yes          | Yes          | Staff can fully manage client information.                            |
| **Measurements**         | Add, View, Edit, Delete (for a client) | Yes          | Yes          | Part of client/order management.                                      |
| **Orders Management**    | Create, Read, Update, Delete | Yes          | Yes          | Staff can fully manage orders. Delete might be restricted if audited. |
|                          | Update Order Status        | Yes          | Yes          |                                                                       |
|                          | Record Payments            | Yes          | Yes          |                                                                       |
| **Order Statuses**       | Create, Read, Update, Delete | Yes          | No           | Managed by Admin as part of system setup.                             |
| **Payment Methods**      | Create, Read, Update, Delete | Yes          | No           | Managed by Admin as part of system setup.                             |
| **Expenses Management**  | Create, Read, Update, Delete | Yes          | Yes          | Staff can manage operational expenses.                                |
| **Expense Categories**   | Create, Read, Update, Delete | Yes          | No           | Managed by Admin as part of system setup.                             |
| **Staff User Management**| Create New User            | Yes          | No           | Admin manages all user accounts.                                      |
|                          | View User List             | Yes          | No           |                                                                       |
|                          | Edit User (Role, Status)   | Yes          | No           |                                                                       |
|                          | Reset User Password        | Yes          | No           |                                                                       |
|                          | Deactivate/Activate User   | Yes          | No           |                                                                       |
|                          | Manage Roles/Permissions   | Yes          | No           | If granular permissions beyond hardcoded roles are implemented.       |
| **Own User Profile**     | View Own Profile           | Yes          | Yes          | All users can view their own details.                                 |
|                          | Change Own Password        | Yes          | Yes          | All users can change their own password.                              |
| **System Settings**      | View & Modify              | Yes          | No           | Any global system configurations.                                     |
| **Reporting**            | Access All Reports         | Yes          | Yes          | Staff may have access to operational reports. Sensitive financial reports might be Admin only. |

*This table provides a general guideline. Specific edge cases or more granular permissions might be identified during detailed implementation.*

## 4. User Administration (by Admin role)

Administrators require dedicated UI sections to manage staff accounts.

*   **UI for Creating New Staff Accounts:**
    *   Form with fields for:
        *   Full Name
        *   Email (will be used as username)
        *   Initial Password (system could generate a strong temporary password, or admin sets one, with a flag forcing user to change on first login)
        *   Assign Role (Dropdown: "Staff", "Admin")
    *   "Create User" button.
*   **UI for Viewing List of Users:**
    *   Table displaying all users with columns: Full Name, Email, Role, Status (Active/Inactive), Last Login Date.
    *   Search/filter capabilities by name, email, role, status.
*   **UI for Editing User Details (accessed from user list):**
    *   Form pre-filled with user's data.
    *   Editable fields: Full Name, Email (careful with username changes), Role.
    *   Actions:
        *   "Reset Password": Admin can trigger a password reset (e.g., generate a new temporary password).
        *   "Deactivate Account" / "Activate Account" toggle/button.
        *   "Save Changes" button.
*   **Deactivation vs. Deletion:**
    *   Prefer deactivating user accounts (`IsActive = false` in `Users` table) over permanent deletion.
    *   Deactivation preserves the user's history and audit trails (e.g., who created/updated which records).
    *   Re-activation is possible if the user returns.

## 5. Database Table Refinements for User Management

These tables were also outlined in the `database_schema.md`.

*   **`Users` Table:**
    *   `UserID` (UUID, PK): Unique identifier for the user.
    *   `FullName` (TEXT, NOT NULL): User's full name.
    *   `Email` (TEXT, NOT NULL, UNIQUE): User's login email.
    *   `PasswordHash` (TEXT, NOT NULL): Hashed password.
    *   `Salt` (TEXT, NOT NULL): Salt used for password hashing.
    *   `RoleID` (INTEGER, FK referencing `Roles.RoleID`): The role assigned to the user.
    *   `IsActive` (BOOLEAN, NOT NULL, DEFAULT TRUE): Whether the user account is active.
    *   `LastLogin` (DATETIME, NULLABLE): Timestamp of the last successful login.
    *   `PasswordLastChangedAt` (DATETIME, NULLABLE): Timestamp of last password change.
    *   `CreatedAt` (DATETIME, NOT NULL): Timestamp of account creation.
    *   `UpdatedAt` (DATETIME, NOT NULL): Timestamp of last account update.

*   **`Roles` Table:**
    *   `RoleID` (SERIAL, PK): Unique identifier for the role.
    *   `RoleName` (TEXT, NOT NULL, UNIQUE): Name of the role (e.g., "Admin", "Staff").
    *   `Description` (TEXT, NULLABLE): Brief description of the role.
    *   `CreatedAt` (DATETIME, NOT NULL): Timestamp of role creation.
    *   `UpdatedAt` (DATETIME, NOT NULL): Timestamp of last role update.

*   **Granular Permissions (Optional - for future scalability):**
    *   If permissions need to be more fine-grained than what's directly implied by "Admin" or "Staff" roles, the following tables (also in `database_schema.md`) would be fully implemented:
        *   **`Permissions` Table:** `PermissionID` (PK), `PermissionName` (e.g., "order:create", "model:delete"), `Description`.
        *   **`RolePermissions` Table:** `RoleID` (FK), `PermissionID` (FK) - Junction table.
    *   For the initial design, application logic can enforce permissions based on `RoleName` from the `Roles` table. If the platform grows in complexity with many nuanced access needs, activating this granular permission system would be the next step. For now, we assume permissions are primarily role-name driven.

## 6. Self-Service (for logged-in users)

Authenticated users should have some control over their own account.

*   **Change Own Password:**
    *   A dedicated screen accessible after login.
    *   Requires user to enter their current password, then the new password (twice for confirmation).
    *   New password must adhere to defined complexity policies.
    *   Updates `PasswordHash`, `Salt`, and `PasswordLastChangedAt` in the `Users` table.
*   **View Own Profile Details:**
    *   A screen displaying their Full Name, Email, Role.
    *   Typically read-only, except for password changes. Major changes like email or role would be admin-controlled.

This Staff/User Management module ensures that only authorized personnel can access and manipulate data within the Fashion Customization Platform, providing a secure and controlled environment.
