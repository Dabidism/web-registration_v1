# Additional Driver Implementation

## Overview
Added functionality to allow vehicle owners to register an additional driver during the vehicle registration process.

## Changes Made

### 1. Database Schema Updates
- **File**: `add_additional_driver_columns.sql`
- **Changes**:
  - Added `additional_driver_name` VARCHAR(100) column to `vehicleowner` table
  - Added `additional_driver_relationship` VARCHAR(50) column to `vehicleowner` table
  - Added same columns to `applications` table for pending applications

### 2. Registration Form Updates
- **File**: `php/registration.php`
- **Changes**:
  - Added new section "Additional Driver (Optional)" with styled background
  - Added input field for additional driver name
  - Added dropdown for relationship selection with options:
    - Spouse, Child, Parent, Sibling, Other Relative, Friend, Employee, Other
  - Added JavaScript validation to ensure relationship is selected when name is provided

### 3. Backend Processing Updates
- **File**: `php/process_registration.php`
- **Changes**:
  - Added handling for `additionalDriverName` and `additionalDriverRelationship` form fields
  - Updated INSERT statements for both `applications` and `vehicleowner` tables
  - Modified approval process to transfer additional driver data from applications to vehicleowner table

## Database Migration
Run the SQL script to add the new columns:
```sql
-- Execute the contents of add_additional_driver_columns.sql
```

## Features
- **Optional Field**: Additional driver registration is completely optional
- **Validation**: If driver name is provided, relationship must be selected
- **Visual Design**: Section is styled with light background to distinguish it from required fields
- **Data Persistence**: Additional driver information is stored during application and transferred upon approval

## Usage
1. Users can optionally fill in additional driver information during vehicle registration
2. The additional driver name and relationship are stored with the application
3. Upon approval, the additional driver information is transferred to the vehicleowner table
4. The additional driver will be authorized to use the registered vehicle for campus entry

## Technical Notes
- Additional driver fields are nullable in the database
- Form validation ensures data consistency
- Backward compatibility maintained - existing registrations unaffected