# Tourist Profile and Travel Diary Feature - Setup Guide

## Overview
This feature adds two main functionalities:
1. **Tourist Profile** - Allows tourists to view and edit their profile information
2. **Travel Diary** - Allows tourists to create, edit, and share travel diary entries with photos

## Database Setup

Run the SQL script to create the necessary tables:

```sql
-- Run this in your MySQL database (ceylon_go)
-- File location: database/create_diary_tables.sql
```

Or manually execute:
1. Open phpMyAdmin
2. Select the `ceylon_go` database
3. Go to SQL tab
4. Copy and paste the contents of `database/create_diary_tables.sql`
5. Click "Go"

## Features Created

### 1. Tourist Profile
- **Route**: `/CeylonGo/public/tourist/profile`
- **Features**:
  - View profile information
  - Edit first name, last name, email, contact number
  - Change password (optional)
  - Profile updates sync with authentication system

### 2. Travel Diary

#### My Diary (Own Entries)
- **Route**: `/CeylonGo/public/tourist/my-diary`
- View all your diary entries
- Edit, delete, or view individual entries
- See which entries are public or private

#### Add Diary Entry
- **Route**: `/CeylonGo/public/tourist/add-diary-entry`
- Create new diary entries with:
  - Title
  - Location
  - Date
  - Story/content
  - Multiple photos
  - Public/private visibility option

#### Edit Diary Entry
- **Route**: `/CeylonGo/public/tourist/edit-diary-entry/{id}`
- Edit existing entries
- Add more photos
- Change visibility

#### View Diary Entry
- **Route**: `/CeylonGo/public/tourist/view-diary-entry/{id}`
- View full diary entry with photos
- Shows author information for public entries

#### Public Diaries
- **Route**: `/CeylonGo/public/tourist/public-diaries`
- Browse all public diary entries from all tourists
- Anyone can view (no login required)
- See stories and experiences from other travelers

## Files Created

### Models
- `models/TripDiary.php` - Handles diary entry database operations
- Updated `models/Tourist.php` - Added `updateProfile()` method

### Controllers
- Updated `controllers/TouristController.php` - Added profile and diary methods

### Views
- `views/tourist/profile.php` - Profile page
- `views/tourist/my_diary.php` - List of own diary entries
- `views/tourist/add_diary_entry.php` - Form to add new entry
- `views/tourist/edit_diary_entry.php` - Form to edit entry
- `views/tourist/view_diary_entry.php` - View single entry
- `views/tourist/public_diaries.php` - Browse public entries

### CSS
- `public/css/tourist/profile.css` - Profile page styles
- `public/css/tourist/diary.css` - Diary pages styles

### Database
- `database/create_diary_tables.sql` - SQL script to create tables

### Updated Files
- `public/index.php` - Added routes for profile and diary
- `views/tourist/header.php` - Added navigation links

## Database Tables

### trip_diary_entries
- Stores diary entry information
- Fields: id, tourist_id, title, content, location, entry_date, is_public, created_at, updated_at

### diary_images
- Stores images associated with diary entries
- Fields: id, entry_id, image_path, created_at

## Image Upload

- Images are uploaded to: `uploads/diary/`
- Supported formats: JPG, JPEG, PNG, GIF, WEBP
- Multiple images can be uploaded per entry
- Images are automatically deleted when entry is deleted

## Navigation

The header navigation has been updated to include:
- **Travel Diaries** - Link to public diaries (visible to all)
- **My Diary** - Link to own diary (visible when logged in)
- **Profile** - Link to profile page (visible when logged in)

## Usage

1. **For Tourists**:
   - Login to your account
   - Click "Profile" to manage your profile
   - Click "My Diary" to create and manage diary entries
   - Make entries public to share with others

2. **For Visitors**:
   - Click "Travel Diaries" to browse public entries
   - View stories and photos from other travelers
   - No login required to view public diaries

## Notes

- All diary entries are private by default
- Tourists can choose to make entries public
- Only the owner can edit or delete their entries
- Public entries are visible to everyone (logged in or not)
- Profile updates require authentication
- Password changes are optional


