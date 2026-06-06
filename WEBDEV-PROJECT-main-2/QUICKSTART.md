# Quick Start Guide - Estate Website

## 🏁 Setup in 5 Steps

### Step 1: Run Migrations
```bash
cd c:\Users\ASUS\Herd\Projectwebdevbaru
php artisan migrate
```

### Step 2: Create Property Types
Run the seeder if available or manually add through admin:
```bash
php artisan db:seed PropertyTypeSeeder
```

### Step 3: Create an Admin User
If not already present, create an admin user with the 'admin' or 'owner' role:
```bash
php artisan tinker
```

Then in tinker:
```php
$user = User::first(); // or create new user
$user->roles()->create(['role_name' => 'admin']);
```

### Step 4: Login and Create Agents
1. Navigate to `/login`
2. Login with your admin account
3. Click "Admin Panel" → "Add Agent"
4. Create at least one agent

### Step 5: Add Properties
1. Go to "Admin Panel" → "Add Property"
2. Fill in all property details
3. Select an agent
4. Mark as featured if desired
5. Submit

---

## 🔗 Key Pages

- **Home**: `/` - Featured properties & categories
- **Properties**: `/properties` - Browse all properties
- **Agents**: `/agents` - View all agents
- **Admin - Add Property**: `/properties/create-form`
- **Admin - Add Agent**: `/agents/create-form`

---

## 📱 Features Overview

✅ Property Listings with Filters
✅ Property Detail Pages  
✅ Agent Directory
✅ Agent Profiles
✅ Admin Management Panel
✅ Search Functionality
✅ Featured Properties
✅ Responsive Design

---

## 🚨 Troubleshooting

**Error: "route 'home' not defined"**
- Run `php artisan route:cache` (clear old routes)
- Check that PropertyController exists

**Error: "table 'properties' doesn't exist"**
- Run `php artisan migrate`
- Ensure migration file `2026_05_04_000001_refactor_to_properties.php` exists

**Admin Panel Missing**
- Ensure logged-in user has 'admin' or 'owner' role
- Check `app/Models/User.php` has `hasRole()` method

---

For more details, see `ESTATE_TRANSFORMATION.md`
