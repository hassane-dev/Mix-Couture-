-- SQL Schema for the Fashion Customization Platform
-- Target Database: MySQL

CREATE TABLE `Clients` (
    `ClientID` CHAR(36) PRIMARY KEY,
    `FullName` TEXT NOT NULL,
    `Email` VARCHAR(255) NOT NULL,
    `PhoneNumber` TEXT NOT NULL,
    `AddressLine1` TEXT NULL,
    `AddressLine2` TEXT NULL,
    `City` TEXT NULL,
    `StateProvince` TEXT NULL,
    `PostalCode` TEXT NULL,
    `Country` TEXT NULL,
    `Notes` TEXT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `UQ_Clients_Email` (`Email`)
);

CREATE TABLE `ModelCategories` (
    `ModelCategoryID` INT AUTO_INCREMENT PRIMARY KEY,
    `CategoryName` VARCHAR(255) NOT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `UQ_ModelCategories_CategoryName` (`CategoryName`)
);

CREATE TABLE `Roles` (
    `RoleID` INT AUTO_INCREMENT PRIMARY KEY,
    `RoleName` VARCHAR(255) NOT NULL,
    `Description` TEXT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `UQ_Roles_RoleName` (`RoleName`)
);

CREATE TABLE `Permissions` (
    `PermissionID` INT AUTO_INCREMENT PRIMARY KEY,
    `PermissionName` VARCHAR(255) NOT NULL,
    `Description` TEXT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `UQ_Permissions_PermissionName` (`PermissionName`)
);

CREATE TABLE `OrderStatuses` (
    `OrderStatusID` INT AUTO_INCREMENT PRIMARY KEY,
    `StatusName` VARCHAR(255) NOT NULL,
    `Description` TEXT NULL,
    `SortOrder` INT NULL,
    `IsSystemDefault` TINYINT(1) DEFAULT 0,
    `IsFinal` TINYINT(1) DEFAULT 0,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `UQ_OrderStatuses_StatusName` (`StatusName`)
);

CREATE TABLE `PaymentMethods` (
    `PaymentMethodID` INT AUTO_INCREMENT PRIMARY KEY,
    `MethodName` VARCHAR(255) NOT NULL,
    `IsEnabled` TINYINT(1) DEFAULT 1 NOT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `UQ_PaymentMethods_MethodName` (`MethodName`)
);

CREATE TABLE `ExpenseCategories` (
    `ExpenseCategoryID` INT AUTO_INCREMENT PRIMARY KEY,
    `CategoryName` VARCHAR(255) NOT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `UQ_ExpenseCategories_CategoryName` (`CategoryName`)
);

CREATE TABLE `Users` (
    `UserID` CHAR(36) PRIMARY KEY,
    `FullName` TEXT NOT NULL,
    `Email` VARCHAR(255) NOT NULL,
    `PasswordHash` TEXT NOT NULL,
    `RoleID` INT NOT NULL,
    `IsActive` TINYINT(1) DEFAULT 1 NOT NULL,
    `LastLogin` TIMESTAMP NULL DEFAULT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `UQ_Users_Email` (`Email`),
    CONSTRAINT `FK_Users_Roles` FOREIGN KEY (`RoleID`) REFERENCES `Roles` (`RoleID`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE `Models` (
    `ModelID` CHAR(36) PRIMARY KEY,
    `ModelName` TEXT NOT NULL,
    `Description` TEXT NULL,
    `ModelCategoryID` INT NOT NULL,
    `BasePrice` DECIMAL(10,2) NOT NULL,
    `FileFormat` TEXT NULL,
    `PolygonCount` INT NULL,
    `TextureInformation` TEXT NULL,
    `LicenseType` TEXT NULL,
    `Tags` TEXT NULL,
    `Designer` TEXT NULL,
    `ModelFileUrl` TEXT NULL,
    `IsArchived` TINYINT(1) DEFAULT 0 NOT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `FK_Models_ModelCategories` FOREIGN KEY (`ModelCategoryID`) REFERENCES `ModelCategories` (`ModelCategoryID`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE `ModelPhotos` (
    `ModelPhotoID` INT AUTO_INCREMENT PRIMARY KEY,
    `ModelID` CHAR(36) NOT NULL,
    `PhotoURL` TEXT NOT NULL,
    `IsPrimary` TINYINT(1) DEFAULT 0 NOT NULL,
    `Caption` TEXT NULL,
    `UploadTimestamp` TIMESTAMP NOT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `FK_ModelPhotos_Models` FOREIGN KEY (`ModelID`) REFERENCES `Models` (`ModelID`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE `MeasurementSets` (
    `MeasurementSetID` INT AUTO_INCREMENT PRIMARY KEY,
    `ClientID` CHAR(36) NOT NULL,
    `Label` TEXT NOT NULL,
    `DateTaken` DATE NOT NULL,
    `Notes` TEXT NULL,
    `Bust` DECIMAL(10,2) NULL,
    `Waist` DECIMAL(10,2) NULL,
    `Hips` DECIMAL(10,2) NULL,
    `ShoulderWidth` DECIMAL(10,2) NULL,
    `SleeveLength` DECIMAL(10,2) NULL,
    `Inseam` DECIMAL(10,2) NULL,
    `Neck` DECIMAL(10,2) NULL,
    `Height` DECIMAL(10,2) NULL,
    `Weight` DECIMAL(10,2) NULL,
    `MeasurementUnit` TEXT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `FK_MeasurementSets_Clients` FOREIGN KEY (`ClientID`) REFERENCES `Clients` (`ClientID`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE `Orders` (
    `OrderID` CHAR(36) PRIMARY KEY,
    `ClientID` CHAR(36) NOT NULL,
    `ModelID` CHAR(36) NOT NULL,
    `MeasurementSetID` INT NULL,
    `OrderDate` TIMESTAMP NOT NULL,
    `EstimatedCompletionDate` DATE NULL,
    `ActualCompletionDate` DATE NULL,
    `CustomizationNotes` TEXT NULL,
    `InternalOrderNotes` TEXT NULL,
    `BaseModelPrice` DECIMAL(10,2) NOT NULL,
    `CustomizationCharges` DECIMAL(10,2) DEFAULT 0.00,
    `DiscountAmount` DECIMAL(10,2) DEFAULT 0.00,
    `TaxAmount` DECIMAL(10,2) DEFAULT 0.00,
    `FinalPrice` DECIMAL(10,2) NOT NULL,
    `OrderStatusID` INT NOT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `FK_Orders_Clients` FOREIGN KEY (`ClientID`) REFERENCES `Clients` (`ClientID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `FK_Orders_Models` FOREIGN KEY (`ModelID`) REFERENCES `Models` (`ModelID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `FK_Orders_MeasurementSets` FOREIGN KEY (`MeasurementSetID`) REFERENCES `MeasurementSets` (`MeasurementSetID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `FK_Orders_OrderStatuses` FOREIGN KEY (`OrderStatusID`) REFERENCES `OrderStatuses` (`OrderStatusID`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE `OrderCustomReferencePhotos` (
    `ReferencePhotoID` INT AUTO_INCREMENT PRIMARY KEY,
    `OrderID` CHAR(36) NOT NULL,
    `PhotoURL` TEXT NOT NULL,
    `Notes` TEXT NULL,
    `UploadTimestamp` TIMESTAMP NOT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Added for consistency
    CONSTRAINT `FK_OrderCustomReferencePhotos_Orders` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`OrderID`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE `Payments` (
    `PaymentID` INT AUTO_INCREMENT PRIMARY KEY,
    `OrderID` CHAR(36) NOT NULL,
    `PaymentDate` TIMESTAMP NOT NULL,
    `AmountPaid` DECIMAL(10,2) NOT NULL,
    `PaymentMethodID` INT NOT NULL,
    `TransactionID` TEXT NULL,
    `PaymentNotes` TEXT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `FK_Payments_Orders` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`OrderID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `FK_Payments_PaymentMethods` FOREIGN KEY (`PaymentMethodID`) REFERENCES `PaymentMethods` (`PaymentMethodID`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE `Expenses` (
    `ExpenseID` INT AUTO_INCREMENT PRIMARY KEY,
    `ExpenseName` TEXT NOT NULL,
    `ExpenseCategoryID` INT NOT NULL,
    `Amount` DECIMAL(10,2) NOT NULL,
    `ExpenseDate` DATE NOT NULL,
    `Vendor` TEXT NULL,
    `LinkedModelID` CHAR(36) NULL,
    `LinkedOrderID` CHAR(36) NULL,
    `ReceiptReference` TEXT NULL,
    `ReceiptURL` TEXT NULL,
    `Notes` TEXT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `FK_Expenses_ExpenseCategories` FOREIGN KEY (`ExpenseCategoryID`) REFERENCES `ExpenseCategories` (`ExpenseCategoryID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `FK_Expenses_Models` FOREIGN KEY (`LinkedModelID`) REFERENCES `Models` (`ModelID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `FK_Expenses_Orders` FOREIGN KEY (`LinkedOrderID`) REFERENCES `Orders` (`OrderID`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE `RolePermissions` (
    `RoleID` INT NOT NULL,
    `PermissionID` INT NOT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`RoleID`, `PermissionID`),
    CONSTRAINT `FK_RolePermissions_Roles` FOREIGN KEY (`RoleID`) REFERENCES `Roles` (`RoleID`) ON DELETE CASCADE ON UPDATE CASCADE, -- Cascade delete for junction table
    CONSTRAINT `FK_RolePermissions_Permissions` FOREIGN KEY (`PermissionID`) REFERENCES `Permissions` (`PermissionID`) ON DELETE CASCADE ON UPDATE CASCADE -- Cascade delete for junction table
);
