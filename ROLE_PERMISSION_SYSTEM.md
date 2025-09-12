# Role and Permission Management System

## Overview

This cold storage management system now includes a comprehensive role-based access control (RBAC) system that allows you to manage user permissions and access levels throughout the application.

## Features

### 🔐 **Role Management**
- Create, read, update, and delete roles
- Assign permissions to roles
- Role hierarchy support
- Active/inactive role status

### 🛡️ **Permission Management**
- Granular permission control
- Module-based permission organization
- Permission assignment to roles
- Permission validation

### 👥 **User Role Assignment**
- Assign multiple roles to users
- Role synchronization
- User permission checking
- Role-based access control

## Database Structure

### Tables
- `roles` - Stores role information
- `permissions` - Stores permission definitions
- `role_permissions` - Many-to-many relationship between roles and permissions
- `user_roles` - Many-to-many relationship between users and roles

### Key Fields
- **Roles**: name, slug, description, is_active
- **Permissions**: name, slug, module, description, is_active
- **Relationships**: Foreign keys linking roles, permissions, and users

## Default Roles and Permissions

### Roles
1. **Super Admin** - Full system access
2. **Admin** - Administrative access to most features
3. **Manager** - Management access to operational features
4. **Staff** - Basic operational access
5. **Viewer** - Read-only access

### Permission Modules
- **customers** - Customer management
- **baskets** - Basket management
- **batches** - Batch management
- **invoices** - Invoice management
- **dispatches** - Dispatch management
- **storage** - Storage management
- **reports** - Reporting
- **users** - User management
- **system** - System administration

## API Endpoints

### Role Management
```
GET    /api/roles                    - List all roles
POST   /api/roles                    - Create new role
GET    /api/roles/{id}               - Get specific role
PUT    /api/roles/{id}               - Update role
DELETE /api/roles/{id}               - Delete role
GET    /api/roles/{id}/permissions   - Get role permissions
POST   /api/roles/{id}/permissions   - Assign permissions to role
DELETE /api/roles/{id}/permissions/{permission} - Revoke permission
```

### Permission Management
```
GET    /api/permissions              - List all permissions
POST   /api/permissions              - Create new permission
GET    /api/permissions/{id}         - Get specific permission
PUT    /api/permissions/{id}         - Update permission
DELETE /api/permissions/{id}         - Delete permission
```

### User Role Management
```
GET    /api/users                    - List users with roles
GET    /api/users/{id}/roles         - Get user roles
POST   /api/users/{id}/roles         - Assign role to user
DELETE /api/users/{id}/roles/{role}  - Remove role from user
POST   /api/users/{id}/roles/sync    - Sync user roles
```

## Usage Examples

### Backend (Laravel)

#### Check User Permissions
```php
// Check if user has specific permission
if ($user->hasPermission('customers.create')) {
    // User can create customers
}

// Check if user has any of multiple permissions
if ($user->hasAnyPermission(['customers.create', 'customers.edit'])) {
    // User can create or edit customers
}

// Check if user has specific role
if ($user->hasRole('admin')) {
    // User is an admin
}
```

#### Middleware Usage
```php
// Protect routes with role middleware
Route::middleware('role:admin,manager')->group(function () {
    // Routes accessible by admin or manager
});

// Protect routes with permission middleware
Route::middleware('permission:customers.create')->group(function () {
    // Routes accessible by users with customers.create permission
});
```

#### Controller Usage
```php
public function index()
{
    // Check permission in controller
    if (!auth()->user()->hasPermission('customers.view')) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    
    // Your logic here
}
```

### Frontend (Vue.js)

#### Check User Permissions
```javascript
// In Vue component
const user = authStore.user;

// Check if user has permission
if (user.hasPermission('customers.create')) {
    // Show create button
}

// Check if user has role
if (user.hasRole('admin')) {
    // Show admin features
}
```

## Security Features

### 🔒 **Access Control**
- All API endpoints protected with appropriate permissions
- Middleware-based permission checking
- Role-based route protection

### 🛡️ **Data Protection**
- Users can only access data they have permission for
- Soft delete protection for roles in use
- Permission validation before role assignment

### 🔐 **Authentication Integration**
- Seamless integration with Laravel Sanctum
- Token-based authentication with role information
- User roles included in authentication responses

## Frontend Components

### RoleManagement.vue
- Complete role and permission management interface
- Create, edit, and delete roles
- Assign permissions to roles
- Manage user role assignments
- Real-time permission checking

## Setup Instructions

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Seed Default Data**
   ```bash
   php artisan db:seed --class=RolePermissionSeeder
   ```

3. **Assign Roles to Users**
   ```php
   $user = User::find(1);
   $adminRole = Role::where('slug', 'admin')->first();
   $user->assignRole($adminRole);
   ```

## Testing

### Test Role Assignment
```bash
php artisan tinker
```

```php
// Assign admin role to first user
$user = User::first();
$adminRole = Role::where('slug', 'admin')->first();
$user->assignRole($adminRole);

// Check user permissions
$user->hasPermission('customers.create'); // Should return true
$user->hasRole('admin'); // Should return true
```

## Best Practices

1. **Principle of Least Privilege** - Give users only the permissions they need
2. **Role Hierarchy** - Use role hierarchy for easier management
3. **Permission Granularity** - Create specific permissions for each action
4. **Regular Audits** - Regularly review and update user permissions
5. **Documentation** - Keep permission documentation up to date

## Troubleshooting

### Common Issues

1. **Permission Denied Errors**
   - Check if user has the required permission
   - Verify role assignment
   - Check middleware configuration

2. **Role Not Found**
   - Ensure role exists in database
   - Check role slug spelling
   - Verify role is active

3. **Permission Not Working**
   - Check permission slug
   - Verify role has the permission
   - Check middleware registration

### Debug Commands
```bash
# Check user roles and permissions
php artisan tinker
$user = User::find(1);
$user->roles;
$user->getAllPermissions();
```

## Future Enhancements

- [ ] Role hierarchy with inheritance
- [ ] Time-based role assignments
- [ ] Permission groups
- [ ] Audit logging for role changes
- [ ] Bulk role management
- [ ] Role templates
- [ ] Advanced permission conditions

---

This role and permission system provides a solid foundation for managing access control in your cold storage management application. It's designed to be flexible, secure, and easy to use while maintaining the principle of least privilege.
