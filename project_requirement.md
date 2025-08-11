# Construction Management System - Project Requirements Document

## 1. Project Overview

Develop a comprehensive construction management system to manage all aspects of a construction company's operations including sites, personnel, finances, materials, and reporting.

## 2. System Architecture

### 2.1 Organizational Hierarchy

-   **Company Owner**: Top-level administrator with full system access
-   **Sites**: Multiple construction sites managed by the company
-   **Site Managers**: Multiple managers per site with site-specific responsibilities
-   **Workers**: Multiple workers per site with attendance and wage tracking

### 2.2 User Roles & Permissions

#### 2.2.1 Company Owner/Administrator

**Full System Access:**

-   Complete access to all modules and data
-   User management and role assignment
-   System configuration and settings
-   Financial data access across all entities
-   Report generation and export capabilities
-   Audit trail access and system monitoring

#### 2.2.2 Site Manager

**Site-Specific Access:**

-   Worker management for assigned site(s) - add, edit, delete workers
-   Attendance tracking and wage processing
-   Material inward/outward management
-   Equipment usage tracking for assigned site
-   Site-level financial reports (read-only)
-   Subcontractor coordination for assigned projects
-   Budget tracking for assigned site (read-only)

#### 2.2.3 Accountant/Finance Manager

**Financial Module Access:**

-   All ledger management (partners, vendors, workers, sites)
-   Payment processing and approvals
-   Financial report generation
-   Budget creation and monitoring
-   Cost estimation and variance analysis
-   Partner investment and return management
-   Tax and compliance reporting

#### 2.2.4 Procurement Manager

**Procurement & Vendor Access:**

-   Vendor management and registration
-   Material ordering and purchase orders
-   Inventory management across all sites
-   Vendor payment processing
-   Equipment procurement and management
-   Cost estimation for materials and equipment
-   Supplier performance tracking

#### 2.2.5 Project Manager

**Project & Contract Access:**

-   Contract management and creation
-   Project planning and timeline management
-   Resource allocation and scheduling
-   Budget planning and cost estimation
-   Subcontractor management and assignments
-   Progress tracking and milestone management
-   Quality control and compliance monitoring

#### 2.2.6 Worker/Employee

**Limited Access:**

-   Personal attendance history (read-only)
-   Personal wage and payment history
-   Personal advance request submission
-   Basic profile management
-   Work assignment notifications

## 3. Core Modules

### 3.1 Partner & Investment Management

-   **Partners**: Manage multiple company partners/investors
-   **Investment Tracking**: Record and track partner investments
-   **Returns Management**: Calculate and manage investment returns
-   **Partner Ledgers**: Maintain detailed financial records for each partner

### 3.2 Site & Personnel Management

-   **Site Management**: Create and manage multiple construction sites
-   **Site Manager Administration**: Assign and manage site managers
-   **Payment Processing**: Handle payments to site managers
-   **Site-level Ledgers**: Maintain financial records at site level
-   **Manager-level Ledgers**: Track individual manager payments and transactions

### 3.3 Vendor & Procurement Management

-   **Vendor Directory**: Manage multiple material suppliers/vendors
-   **Purchase Orders**: Create and track material orders
-   **Payment Processing**: Handle vendor payments
-   **Vendor Ledgers**: Maintain detailed financial records for each vendor

### 3.4 Material & Inventory Management

-   **Material Ordering**: Place orders with vendors
-   **Material Receipt**: Track incoming materials
-   **Site Transfer**: Transfer materials to specific construction sites
-   **Inter-Site Transfer**: Transfer inventory between different sites
-   **Inventory Management**: Track inward/outward material movement at site level
-   **Transfer Approvals**: Workflow for inter-site transfer approvals
-   **Material Ledgers**: Maintain detailed inventory and cost records

### 3.5 Worker Management System

-   **Worker Registration**: Store worker details (name, mobile number)
-   **Attendance Tracking**: Daily attendance management (full day/half day/absent)
-   **Wage Management**: Configure and track daily wages per worker
-   **Payment Processing**: Daily wage payments to workers
-   **Advance Management**: Handle advance payments to workers
-   **Worker Ledgers**: Individual financial records for each worker

### 3.6 Contract Management

-   **Contract Creation**: Create and manage client contracts
-   **Contract Terms**: Define project scope, timeline, and payment terms
-   **Milestone Tracking**: Track contract milestones and deliverables
-   **Contract Amendments**: Handle contract modifications and change orders
-   **Payment Schedules**: Manage contract-based payment schedules
-   **Contract Status**: Track contract progress (active, completed, terminated)
-   **Legal Documentation**: Store and manage contract documents and agreements

### 3.7 Equipment & Machinery Tracking

-   **Equipment Registry**: Maintain inventory of construction equipment
-   **Equipment Assignment**: Assign equipment to specific sites
-   **Usage Tracking**: Monitor equipment usage hours and efficiency
-   **Maintenance Management**: Schedule and track equipment maintenance
-   **Equipment Costs**: Track rental, purchase, and operational costs
-   **Equipment Availability**: Real-time equipment availability status
-   **Equipment Ledgers**: Financial tracking for each piece of equipment

### 3.8 Subcontractor Management

-   **Subcontractor Directory**: Maintain database of subcontractors
-   **Work Assignment**: Assign specific tasks to subcontractors
-   **Performance Tracking**: Monitor subcontractor work quality and timelines
-   **Payment Management**: Handle subcontractor payments and invoicing
-   **Contract Management**: Manage subcontractor agreements and terms
-   **Compliance Tracking**: Ensure subcontractor license and insurance compliance
-   **Subcontractor Ledgers**: Financial records for each subcontractor

### 3.9 Budget Planning & Cost Estimation

-   **Project Budgeting**: Create detailed project budgets
-   **Cost Estimation**: Estimate costs for materials, labor, and equipment
-   **Budget Allocation**: Allocate budgets across different project phases
-   **Cost Tracking**: Real-time tracking of actual vs budgeted costs
-   **Budget Variance Analysis**: Identify and analyze budget deviations
-   **Cost Forecasting**: Predict future costs based on current trends
-   **Budget Approvals**: Workflow for budget approval and modifications

### 3.10 Resource Allocation & Optimization

-   **Resource Planning**: Plan allocation of workers, equipment, and materials
-   **Capacity Management**: Monitor resource capacity across all sites
-   **Resource Scheduling**: Schedule resources based on project timelines
-   **Optimization Engine**: Automatically optimize resource allocation
-   **Resource Conflicts**: Identify and resolve resource scheduling conflicts
-   **Utilization Reports**: Track resource utilization efficiency
-   **Resource Forecasting**: Predict future resource requirements

## 4. Financial Management & Reporting

### 4.1 Ledger Management

-   Partner investment and return ledgers
-   Site-wise financial ledgers
-   Manager payment ledgers
-   Vendor payment and purchase ledgers
-   Material cost and inventory ledgers
-   Worker wage and advance ledgers

### 4.2 Financial Reporting

-   **Profit & Loss Statements**: Generate comprehensive P&L reports
-   **Ledger Reports**: Detailed financial reports for all entities
-   **Site-wise Financial Reports**: Individual site profitability analysis
-   **Cash Flow Reports**: Track money in/out across all operations

## 5. System Requirements

### 5.1 Functional Requirements

-   Multi-user access with role-based permissions
-   Real-time data synchronization across all modules
-   Automated calculation of wages, returns, and profits
-   Comprehensive audit trail for all transactions
-   Data backup and recovery capabilities

### 5.2 Data Validation Rules

#### 5.2.1 User Data Validation

-   **Mobile Numbers**: Must be 10-digit valid Indian mobile numbers
-   **Email Addresses**: Valid email format validation
-   **Names**: Minimum 2 characters, alphabets and spaces only
-   **Passwords**: Minimum 8 characters with special characters, numbers, and letters
-   **User Roles**: Must be from predefined role list

#### 5.2.2 Financial Data Validation

-   **Amounts**: Must be positive numbers with maximum 2 decimal places
-   **Payment Dates**: Cannot be future dates
-   **Investment Returns**: Cannot exceed 100% annually
-   **Budget Allocations**: Total allocation cannot exceed 100%
-   **Wage Rates**: Must be within minimum and maximum wage limits

#### 5.2.3 Operational Data Validation

-   **Attendance**: Only one entry per worker per day
-   **Material Quantities**: Must be positive numbers
-   **Equipment Hours**: Cannot exceed 24 hours per day
-   **Site Assignments**: Workers/equipment cannot be assigned to multiple sites simultaneously
-   **Contract Dates**: Start date must be before end date

#### 5.2.4 Business Rule Validation

-   **Payment Processing**: Sufficient balance check before payment approval
-   **Resource Allocation**: Resource availability check before assignment
-   **Material Transfer**: Stock availability check before transfer
-   **Advance Payments**: Cannot exceed 50% of monthly wages
-   **Budget Variance**: Alert when expenses exceed budget by 10%

### 5.3 Key Features

-   Dashboard with overview of all operations
-   Mobile-friendly interface for site managers
-   Automated report generation
-   Search and filter capabilities across all modules
-   Export functionality for reports and ledgers
-   Real-time notifications and alerts
-   Data import/export capabilities
-   Advanced filtering and sorting options

## 6. Success Criteria

The system should provide complete operational management for the construction company with accurate financial tracking, efficient workflow management, and comprehensive reporting capabilities to support business decision-making.
