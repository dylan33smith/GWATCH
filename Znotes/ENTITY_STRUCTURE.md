# Entity Structure Documentation

## Overview

The entity structure has been reorganized to reflect the multi-database architecture:

- **SONGBIRD Database**: Contains user management, project tracking, and module metadata
- **Module Databases**: Contain actual GWAS data for each module

## Entity Organization

### `src/Entity/Gwatch/` - SONGBIRD Database Entities

Entities that belong to the main SONGBIRD database (`gwatch_db` - database name unchanged):

- **ModuleTracking.php** - Tracks available module databases
  - `id` - Primary key
  - `module_id` - Module identifier (e.g., "186", "187")
  - `owner_id` - User who created the module
  - `visible` - Whether module appears in Active Datasets
  - `created_at` - When module was registered

- **User.php** - User accounts
  - `user_id` - Primary key
  - `username` - Unique username
  - `password` - Hashed password
  - `mail` - Email address
  - `role` - User role
  - `created_at` - Account creation time
  - `image` - Profile image

- **Project.php** - Project management
  - `id` - Primary key
  - `owner_id` - User who owns the project
  - `title` - Project title
  - `description` - Project description
  - `created_at` - Project creation time

- **Sample.php** - Sample data management
  - Sample-related data for SONGBIRD system

### `src/Entity/Module/` - Module Database Entities

Entities that belong to individual module databases (`Module_186`, `Module_187`, etc.):

- **Chr.php** - Chromosome information
- **Ind.php** - SNP index data
- **Pos.php** - SNP positions
- **Pval.php** - P-values
- **Ratio.php** - Odds ratios
- **VInd.php** - Variable index data
- **Col.php** - Column/test definitions
- **Alias.php** - SNP aliases
- **Maf.php** - Minor allele frequencies
- **ChrSupp.php** - Chromosome support data
- **Polarization.php** - Polarization data
- **RadiusInd.php** - Radius index data
- **ReportTopHits.php** - Top hits for reports
- **RPval.php** - Ranked p-values
- **RRatio.php** - Ranked ratios

## Database Architecture

```
SONGBIRD Database (gwatch_db - database name unchanged)
├── module_tracking (tracks available modules)
├── users (user accounts)
├── project (project management)
└── sample (sample data)

Module Databases (Module_186, Module_187, etc.)
├── ind (SNP index)
├── pos (SNP positions)
├── chr (chromosome data)
├── pval (p-values)
├── ratio (odds ratios)
└── ... (other GWAS tables)
```

## Benefits of This Structure

1. **Clear Separation**: SONGBIRD management vs. GWAS data
2. **Scalability**: Easy to add new modules without code changes
3. **Maintainability**: Clear organization of entities by database context
4. **Security**: User management separate from data
5. **Flexibility**: Each module can have its own database schema

## Usage

- **SONGBIRD Entities**: Used for user management, project tracking, and module registration
- **Module Entities**: Used when working with specific GWAS data in module databases

The DatabaseManager handles switching between databases based on the module ID from the URL. 