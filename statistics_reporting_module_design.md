# Statistics and Reporting Module Design (Conceptual)

This document outlines the conceptual design for a Statistics and Reporting Module for the Fashion Customization Platform. Its purpose is to provide insights into sales, client activity, model performance, and basic financial metrics.

## 1. Key Reports and Metrics

The module will focus on providing the following reports and metrics:

### A. Sales Overview
*   **Total Revenue:**
    *   Overall lifetime revenue.
    *   Revenue for a selected period (e.g., Last 7 Days, Last 30 Days, Current Month, Current Year, Custom Date Range).
*   **Total Orders:**
    *   Overall lifetime count of completed/valid orders.
    *   Count of orders for a selected period.
*   **Average Order Value (AOV):**
    *   Overall AOV (Total Revenue / Total Orders).
    *   AOV for a selected period.

### B. Order-Based Reports
*   **Orders Over Time:**
    *   Number of orders per month (for the last 12 months or a selected year).
    *   Number of orders per year (for the last few years).
    *   *Presentation:* Tabular data, data suitable for a bar chart.
*   **Revenue Over Time:**
    *   Total revenue per month (for the last 12 months or a selected year).
    *   Total revenue per year (for the last few years).
    *   *Presentation:* Tabular data, data suitable for a bar chart.
*   **Order Status Distribution:**
    *   Count and percentage of orders in each status (e.g., Pending, In Production, Ready for Pickup, Completed, Cancelled).
    *   Filterable by a general period (e.g., all time, last 90 days).
    *   *Presentation:* Tabular data, data suitable for a pie or donut chart.

### C. Client-Based Reports
*   **Top N Clients:**
    *   List of top clients (e.g., top 10, top 20) based on:
        *   Total amount spent.
        *   Total number of orders placed.
    *   Filterable by period.
    *   *Presentation:* Tabular data.
*   **New Clients Acquired:**
    *   Number of new clients registered per month (for the last 12 months or a selected year).
    *   *Presentation:* Tabular data, data suitable for a line or bar chart.

### D. Model-Based Reports
*   **Most Popular Models (Best Sellers):**
    *   List of models ranked by:
        *   Quantity sold (number of times ordered).
        *   Total revenue generated.
    *   Filterable by period.
    *   *Presentation:* Tabular data.
*   **Sales per Model Category:**
    *   Total revenue and number of orders broken down by model category.
    *   Filterable by period.
    *   *Presentation:* Tabular data, data suitable for a bar or pie chart.

### E. Financial Reports (Simple)
*   **Payments Received:**
    *   Total amount of payments received within a selected period.
    *   Breakdown by payment method (if `PaymentMethodNotes` can be reliably parsed or if a structured `PaymentMethods` table is used and linked).
    *   *Presentation:* Tabular data.

## 2. Data Requirements and Conceptual SQL-like Logic

*(Note: SQL examples assume specific `OrderStatusID` for "Completed" or "Valid Sale". This ID needs to be defined, e.g., from an `OrderStatuses` table. Soft deletes (`IsDeleted = 0`) are assumed for relevant tables.)*

---
**A. Sales Overview**
*   **Total Revenue (Overall):**
    *   **Data Needed:** `Orders.FinalPrice`, `Orders.OrderStatusID`
    *   **Logic:** `SELECT SUM(FinalPrice) FROM Orders WHERE OrderStatusID = [CompletedStatusID] AND IsDeleted = 0;`
*   **Total Revenue (Period):**
    *   **Data Needed:** `Orders.FinalPrice`, `Orders.OrderDate`, `Orders.OrderStatusID`
    *   **Logic:** `SELECT SUM(FinalPrice) FROM Orders WHERE OrderStatusID = [CompletedStatusID] AND IsDeleted = 0 AND OrderDate BETWEEN :startDate AND :endDate;`
*   **Total Orders (Overall):**
    *   **Data Needed:** `Orders.OrderID`, `Orders.OrderStatusID`
    *   **Logic:** `SELECT COUNT(OrderID) FROM Orders WHERE OrderStatusID = [CompletedStatusID] AND IsDeleted = 0;`
*   **Total Orders (Period):**
    *   **Data Needed:** `Orders.OrderID`, `Orders.OrderDate`, `Orders.OrderStatusID`
    *   **Logic:** `SELECT COUNT(OrderID) FROM Orders WHERE OrderStatusID = [CompletedStatusID] AND IsDeleted = 0 AND OrderDate BETWEEN :startDate AND :endDate;`
*   **Average Order Value:**
    *   Calculated in application logic (Total Revenue / Total Orders for the respective period).

---
**B. Order-Based Reports**
*   **Orders Over Time (Monthly):**
    *   **Data Needed:** `Orders.OrderID`, `Orders.OrderDate`, `Orders.OrderStatusID`
    *   **Logic:** `SELECT strftime('%Y-%m', OrderDate) AS OrderMonth, COUNT(OrderID) AS OrderCount FROM Orders WHERE OrderStatusID = [CompletedStatusID] AND IsDeleted = 0 AND OrderDate >= :startYearMonth AND OrderDate < :endYearMonthPlusOne GROUP BY OrderMonth ORDER BY OrderMonth;`
*   **Revenue Over Time (Monthly):**
    *   **Data Needed:** `Orders.FinalPrice`, `Orders.OrderDate`, `Orders.OrderStatusID`
    *   **Logic:** `SELECT strftime('%Y-%m', OrderDate) AS OrderMonth, SUM(FinalPrice) AS MonthlyRevenue FROM Orders WHERE OrderStatusID = [CompletedStatusID] AND IsDeleted = 0 AND OrderDate >= :startYearMonth AND OrderDate < :endYearMonthPlusOne GROUP BY OrderMonth ORDER BY OrderMonth;`
*   **Order Status Distribution:**
    *   **Data Needed:** `Orders.OrderStatusID`, `OrderStatuses.StatusName`
    *   **Logic:** `SELECT os.StatusName, COUNT(o.OrderID) AS StatusCount FROM Orders o JOIN OrderStatuses os ON o.OrderStatusID = os.OrderStatusID WHERE o.IsDeleted = 0 (AND o.OrderDate BETWEEN :startDate AND :endDate - optional filter) GROUP BY os.StatusName ORDER BY StatusCount DESC;`

---
**C. Client-Based Reports**
*   **Top N Clients (by Total Spending):**
    *   **Data Needed:** `Orders.ClientID`, `Orders.FinalPrice`, `Orders.OrderStatusID`, `Clients.FullName`
    *   **Logic:** `SELECT o.ClientID, c.FullName, SUM(o.FinalPrice) AS TotalSpent FROM Orders o JOIN Clients c ON o.ClientID = c.ClientID WHERE o.OrderStatusID = [CompletedStatusID] AND o.IsDeleted = 0 (AND o.OrderDate BETWEEN :startDate AND :endDate - optional filter) GROUP BY o.ClientID, c.FullName ORDER BY TotalSpent DESC LIMIT :N;`
*   **Top N Clients (by Number of Orders):**
    *   **Data Needed:** `Orders.ClientID`, `Orders.OrderID`, `Orders.OrderStatusID`, `Clients.FullName`
    *   **Logic:** `SELECT o.ClientID, c.FullName, COUNT(o.OrderID) AS TotalOrders FROM Orders o JOIN Clients c ON o.ClientID = c.ClientID WHERE o.OrderStatusID = [CompletedStatusID] AND o.IsDeleted = 0 (AND o.OrderDate BETWEEN :startDate AND :endDate - optional filter) GROUP BY o.ClientID, c.FullName ORDER BY TotalOrders DESC LIMIT :N;`
*   **New Clients Acquired (Monthly):**
    *   **Data Needed:** `Clients.ClientID`, `Clients.CreatedAt` (assuming `CreatedAt` is the registration date)
    *   **Logic:** `SELECT strftime('%Y-%m', CreatedAt) AS JoinMonth, COUNT(ClientID) AS NewClientCount FROM Clients WHERE CreatedAt >= :startYearMonth AND CreatedAt < :endYearMonthPlusOne GROUP BY JoinMonth ORDER BY JoinMonth;`

---
**D. Model-Based Reports**
*   **Most Popular Models (by Quantity Sold):**
    *   **Data Needed:** `Orders.ModelID`, `Orders.OrderID`, `Orders.OrderStatusID`, `Models.Name`
    *   **Logic:** `SELECT o.ModelID, m.Name, COUNT(o.OrderID) AS QuantitySold FROM Orders o JOIN Models m ON o.ModelID = m.ModelID WHERE o.OrderStatusID = [CompletedStatusID] AND o.IsDeleted = 0 (AND o.OrderDate BETWEEN :startDate AND :endDate - optional filter) GROUP BY o.ModelID, m.Name ORDER BY QuantitySold DESC LIMIT :N;`
*   **Most Popular Models (by Revenue Generated):**
    *   **Data Needed:** `Orders.ModelID`, `Orders.FinalPrice` (or apportioned price if an order has multiple models - simpler if one model per order), `Orders.OrderStatusID`, `Models.Name`
    *   **Logic:** `SELECT o.ModelID, m.Name, SUM(o.FinalPrice) AS RevenueGenerated FROM Orders o JOIN Models m ON o.ModelID = m.ModelID WHERE o.OrderStatusID = [CompletedStatusID] AND o.IsDeleted = 0 (AND o.OrderDate BETWEEN :startDate AND :endDate - optional filter) GROUP BY o.ModelID, m.Name ORDER BY RevenueGenerated DESC LIMIT :N;`
*   **Sales per Model Category:**
    *   **Data Needed:** `Models.CategoryID`, `ModelCategories.CategoryName`, `Orders.FinalPrice`, `Orders.OrderID`, `Orders.OrderStatusID`
    *   **Logic:** `SELECT mc.CategoryName, COUNT(o.OrderID) AS TotalOrders, SUM(o.FinalPrice) AS TotalRevenue FROM Orders o JOIN Models m ON o.ModelID = m.ModelID JOIN ModelCategories mc ON m.CategoryID = mc.CategoryID WHERE o.OrderStatusID = [CompletedStatusID] AND o.IsDeleted = 0 (AND o.OrderDate BETWEEN :startDate AND :endDate - optional filter) GROUP BY mc.CategoryName ORDER BY TotalRevenue DESC;`

---
**E. Financial Reports (Simple)**
*   **Payments Received (Period):**
    *   **Data Needed:** `Payments.AmountPaid`, `Payments.PaymentDate`
    *   **Logic:** `SELECT SUM(AmountPaid) AS TotalPayments FROM Payments WHERE PaymentDate BETWEEN :startDate AND :endDate;`
*   **Payments Received (by Method - conceptual if notes are parsable or structured):**
    *   **Data Needed:** `Payments.AmountPaid`, `Payments.PaymentDate`, `Payments.PaymentMethodNotes`
    *   **Logic:** `SELECT PaymentMethodNotes, SUM(AmountPaid) AS TotalPayments FROM Payments WHERE PaymentDate BETWEEN :startDate AND :endDate GROUP BY PaymentMethodNotes ORDER BY TotalPayments DESC;` (This is very basic; a structured `PaymentMethods` table linked to `Payments` would be much better for this.)

---

## 3. Presentation Suggestions (Conceptual UI)

*   **Dashboard Page (`app/views/reports/index.php` or `app/views/dashboard/reports_summary.php`):**
    *   **Summary Cards:** Display key "at-a-glance" metrics:
        *   "Total Revenue (Last 30 Days)"
        *   "New Orders (Last 30 Days)"
        *   "Average Order Value (Last 30 Days)"
        *   "New Clients (Current Month)"
    *   **Links to Detailed Reports:** Sections or links like:
        *   "Sales Reports" (leading to pages for revenue/orders over time)
        *   "Client Reports" (leading to top clients, new client trends)
        *   "Model Reports" (leading to popular models, category sales)
        *   "Financial Summaries" (leading to payment reports)
    *   Maybe a small chart for "Revenue This Month vs. Last Month" or "Orders This Week".

*   **Individual Report Pages (e.g., `app/views/reports/monthly_sales.php`, `app/views/reports/top_clients.php`):**
    *   **Title:** Clearly state the report's purpose.
    *   **Filters:**
        *   Commonly: Date range pickers (e.g., "From Date", "To Date", or predefined like "Last 30 Days", "This Year").
        *   Specific filters (e.g., "Number of Top Clients: [input N]", "Select Model Category").
        *   "Apply Filters" button.
    *   **Data Display:**
        *   **Tables:** Cleanly formatted HTML tables (Bootstrap styled) for most data. Columns should be sortable where appropriate.
        *   **Charts:** For time-series data (sales/orders over time, new clients) or distribution data (order status), indicate where a chart would be beneficial.
            *   The PHP backend would provide a JSON endpoint for chart data.
            *   JavaScript charting libraries (e.g., Chart.js, ApexCharts, Google Charts) would be used in the view to render these charts.
            *   Example: A bar chart for "Monthly Sales" with months on X-axis and revenue on Y-axis.
            *   Example: A pie chart for "Order Status Distribution".
    *   **Export Options (Future Consideration):** Buttons to export table data as CSV or PDF.

## 4. Controller and Model Responsibilities (Conceptual)

*   **`app/controllers/ReportController.php`:**
    *   `index()`: Displays the main reporting dashboard/summary page.
    *   `salesOverTimeReport()`: Handles logic for displaying sales/orders over time (monthly, yearly). Fetches data via `ReportModel`, passes to a view.
    *   `orderStatusDistributionReport()`: Handles order status report.
    *   `topClientsReport()`: Handles top clients report.
    *   `newClientsReport()`: Handles new clients acquired report.
    *   `popularModelsReport()`: Handles most popular models report.
    *   `categorySalesReport()`: Handles sales per model category report.
    *   `paymentsReceivedReport()`: Handles payments received report.
    *   Each method would:
        *   Handle input parameters (e.g., date ranges from `$_GET` or `$_POST`).
        *   Validate parameters.
        *   Call appropriate methods in `ReportModel`.
        *   Load the corresponding view, passing the fetched data.
    *   `getSalesDataForChart()`: Example of a dedicated method that returns JSON data for AJAX calls from JS charting libraries.

*   **`app/models/ReportModel.php`:**
    *   `__construct(PDO $db)`: Constructor for PDO connection.
    *   `getTotalRevenue(string $startDate = null, string $endDate = null): float`
    *   `getTotalOrders(string $startDate = null, string $endDate = null): int`
    *   `getOrdersPerMonth(string $year): array`
    *   `getRevenuePerMonth(string $year): array`
    *   `getOrderStatusDistribution(string $startDate = null, string $endDate = null): array`
    *   `getTopNClientsBySpending(int $limit, string $startDate = null, string $endDate = null): array`
    *   `getTopNClientsByOrders(int $limit, string $startDate = null, string $endDate = null): array`
    *   `getNewClientsPerMonth(string $year): array`
    *   `getPopularModelsByQuantity(int $limit, string $startDate = null, string $endDate = null): array`
    *   `getPopularModelsByRevenue(int $limit, string $startDate = null, string $endDate = null): array`
    *   `getSalesPerModelCategory(string $startDate = null, string $endDate = null): array`
    *   `getTotalPaymentsReceived(string $startDate, string $endDate): float`
    *   Each method executes the specific SQL query (using prepared statements for dynamic parameters like dates) and returns the data in a structured array format.

## 5. Considerations for Implementation

*   **Performance:**
    *   Ensure all columns used in `WHERE` clauses, `JOIN` conditions, and `GROUP BY` clauses (especially date fields, IDs, status fields) are properly indexed in the database.
    *   For very large datasets, generating reports on-the-fly can be slow. Consider:
        *   Caching query results for short periods.
        *   Generating some reports during off-peak hours and storing results in summary tables (more advanced).
*   **Date Handling:**
    *   Use consistent date formats for input and database queries (e.g., `YYYY-MM-DD`).
    *   Be mindful of timezones if the application serves users across different timezones or if server/database timezones differ. Store dates in UTC in the database where possible and convert to user's timezone for display.
*   **Permissions:**
    *   Reporting data can be sensitive. Use the Staff/User Management module to restrict access to this module or specific reports (e.g., only 'Admin' role can view financial reports or detailed sales figures).
*   **Completed Order Definition:**
    *   Clearly define what constitutes a "completed" or "valid" order for financial calculations (e.g., `OrderStatusID` corresponding to 'Completed', 'Shipped', but not 'Cancelled' or 'Refunded'). This should be a configurable setting or a well-understood business rule.
*   **Data Integrity:** The accuracy of reports depends heavily on the integrity and consistency of data in the underlying operational tables.
*   **Chart Data Endpoints:** If using JS charts, create separate controller methods that return data in JSON format specifically for those charts, rather than trying to embed complex JSON into HTML views.

This conceptual design provides a roadmap for developing a useful Statistics and Reporting module. The initial implementation can start with a few key reports and expand over time.The conceptual design for the "Statistics and Reporting Module" has been outlined in `statistics_reporting_module_design.md`.

This document covers:
1.  **Key Reports and Metrics:** Sales overview, order-based reports, client-based reports, model-based reports, and simple financial reports.
2.  **Data Requirements and Conceptual SQL-like Logic:** For each report, it specifies the data needed and the conceptual SQL logic (aggregations, groupings, joins).
3.  **Presentation Suggestions (Conceptual UI):** Ideas for a dashboard page with summary cards and links to individual report pages with tables, filters, and notes on using JS charting libraries.
4.  **Controller and Model Responsibilities:** Suggests methods for `ReportController.php` and `ReportModel.php` to handle report generation and data fetching.
5.  **Considerations for Implementation:** Performance, date handling, permissions, definition of a completed order, data integrity, and chart data endpoints.

No actual PHP/HTML code was generated for views, as this was a conceptual design task. The structure for the controller and model methods was suggested.

This completes the subtask.
