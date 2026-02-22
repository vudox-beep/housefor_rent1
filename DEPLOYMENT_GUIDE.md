# Image Persistence Fix - Deployment Guide

## Problem Identified
Your application was storing listing images in `public/uploads/listings/` which is **not persistent** on Laravel Forge. During deployments or cache clearing, these files were being deleted, leaving only placeholder images.

## Changes Made

### 1. **ListingController Updates**
   - Changed image storage from `public/uploads/listings/` to `storage/app/public/listings/`
   - Changed video storage from `public/uploads/videos/` to `storage/app/public/videos/`
   - Updated file deletion logic to work with new paths
   - Images now use URLs: `storage/listings/{filename}` (with symlink handling)

### 2. **Database Migration**
   - Created: `database/migrations/2025_02_22_000000_migrate_uploads_to_storage.php`
   - This migration updates all existing image paths in the database from `uploads/` to `storage/`

### 3. **View Templates Updated**
   - **listings/index.blade.php**: Improved fallback image handling with proper onerror handlers
   - **listings/show.blade.php**: Enhanced gallery with better fallback logic
   - **dealer/listings.blade.php**: Changed from hiding broken images to showing fallback images

## Deployment Steps

### Step 1: Run Migrations
```bash
php artisan migrate
```
This will update all existing image paths in your database.

### Step 2: Ensure Storage Symlink Exists
Laravel Forge already creates the symlink during deployment, but verify it:
```bash
php artisan storage:link
```

This creates: `public/storage` → `storage/app/public`

### Step 3: Move Existing Images (If Needed)
If you have existing images in `public/uploads/`, move them manually:
```bash
# Copy listings images
cp -r public/uploads/listings/* storage/app/public/listings/

# Copy video files
cp -r public/uploads/videos/* storage/app/public/videos/

# Then remove old directories
rm -rf public/uploads/
```

### Step 4: Verify Storage Directory Permissions
Ensure proper permissions:
```bash
chmod -R 755 storage/app/public/listings/
chmod -R 755 storage/app/public/videos/
```

### Step 5: Clear Cache
```bash
php artisan cache:clear
php artisan view:clear
```

## How It Works Now

### Storage Architecture
```
storage/app/public/
├── listings/
│   └── *.jpg, *.png, *.webp (listing images)
├── videos/
│   └── *.mp4, *.webm (listing videos)
├── agents/
│   └── *.jpg, *.png (agent photos - already working)
└── avatars/
    └── *.jpg, *.png (user avatars - already working)
```

### URL Format
- **Listings images**: `/storage/listings/{filename}` (via symlink)
- **Videos**: `/storage/videos/{filename}` (via symlink)
- **Agents photos**: `/storage/agents/{filename}` (already working)
- **User avatars**: `/storage/avatars/{filename}` (already working)

## Persistence Guarantee

### Why This Works on Laravel Forge
1. **Symlink Creation**: Forge automatically creates `public/storage` → `storage/app/public`
2. **Persistent Directory**: The `storage/` directory is preserved across deployments on Forge
3. **Database Tracking**: File paths are stored in the database with migration support
4. **Fallback Images**: If a file is missing, fallback Unsplash images display automatically

## Testing the Fix

### 1. Test New Listings
- Post a new listing with images
- Verify images display correctly
- Check browser console for no 404 errors

### 2. Test Dealer Panel
- View dealer listings
- Confirm images display (not hidden)
- Add a new agent with photo

### 3. Test Details Page
- Click on a listing to view details
- Verify gallery displays all images
- Check fallback images work on broken images

### 4. Test After Deployment
- Deploy your app
- Verify old images still display
- Create new listings to test new storage

## Troubleshooting

### Images Not Showing?
1. Run: `php artisan storage:link`
2. Check: `public/storage` symlink exists
3. Verify: Files in `storage/app/public/listings/`

### 404 on Old Images?
1. Run migration: `php artisan migrate`
2. Verify paths updated: Check database paths in `listings` table

### Permission Errors?
1. Run: `chmod -R 755 storage/app/public/`
2. Also ensure: `storage/app/public/listings/` writable

### Fallback Images Not Showing?
- This is normal when internet connection loads Unsplash
- Update `config/app.php`'s `APP_URL` if needed

## Files Modified
- `app/Http/Controllers/ListingController.php` - Storage path changes
- `resources/views/listings/index.blade.php` - Fallback handling
- `resources/views/listings/show.blade.php` - Gallery improvements
- `resources/views/dealer/listings.blade.php` - Image display fix
- `database/migrations/2025_02_22_000000_migrate_uploads_to_storage.php` - Path migration

## What Was NOT Changed
- Agent photo uploads (already using persistent storage)
- User avatar uploads (already using persistent storage)
- Video URL support (still works as before)

## Support Files
If needed to copy manually:
- Old path: `public/uploads/listings/`
- New path: `storage/app/public/listings/`
- Old path: `public/uploads/videos/`
- New path: `storage/app/public/videos/`

After deployment, all new images will automatically be stored in the persistent location!
