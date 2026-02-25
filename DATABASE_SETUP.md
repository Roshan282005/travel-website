# 📊 TravelGo Database Tables - Setup Complete

## ✅ Created Tables (All 9 Tables)

### 1. **firebase_users** - User Accounts
```sql
- id (PRIMARY KEY)
- firebase_uid (Firebase Authentication ID)
- fullname
- email
- photo (Profile Picture)
- created_at
- reset_token (Password Reset)
- reset_expires
- updated_at
```
**Used by:** profile.php, login.php, signup.php, firebase_login.php

---

### 2. **users** - Extended User Profiles
```sql
- id (PRIMARY KEY)
- firebase_uid (Links to firebase_users)
- username
- email
- password
- fullname
- photo
- phone
- address, city, country
- bio
- language, currency, theme (User Preferences)
- two_factor_enabled
- created_at, updated_at, last_login
```
**Used by:** profile.php, settings.php

---

### 3. **bookings** - Trip Bookings
```sql
- id (PRIMARY KEY)
- user_id (Links to firebase_users)
- destination_id
- hotel_name
- check_in DATE
- check_out DATE
- guests, rooms
- total_price (DECIMAL)
- status (pending, confirmed, cancelled)
- payment_status (unpaid, paid, refunded)
- booking_date
- notes
```
**Used by:** mytrips.php, book.php, api/create_booking.php

---

### 4. **reviews** - Destination Reviews
```sql
- id (PRIMARY KEY)
- user_id (Links to firebase_users)
- destination_id
- title
- rating (1-5)
- review_text
- helpful_count
- created_at, updated_at
```
**Used by:** destination.php, api/submit_review.php

---

### 5. **wishlist** - Saved Destinations
```sql
- id (PRIMARY KEY)
- user_id (Links to firebase_users)
- destination_id
- added_date
- UNIQUE (user_id, destination_id) - Prevents duplicates
```
**Used by:** destination.php, wishlist.php, api/toggle_wishlist.php

---

### 6. **activity_log** - User Activity Tracking
```sql
- id (PRIMARY KEY)
- user_id (Links to firebase_users)
- action (TEXT - describes action)
- details (JSON - structured action data)
- created_at
```
**Used by:** profile.php (Recent Activity)

---

### 7. **notifications** - User Alerts
```sql
- id (PRIMARY KEY)
- user_id (Links to firebase_users)
- title
- message
- type (booking, review, wishlist, system)
- is_read (BOOLEAN)
- created_at
```
**Used by:** Future notification system

---

### 8. **user_preferences** - Settings & Preferences
```sql
- id (PRIMARY KEY)
- user_id (UNIQUE - one per user)
- language (Default: 'en')
- currency (Default: 'USD')
- theme (Default: 'light')
- notifications_enabled
- email_alerts, sms_alerts
- updated_at
```
**Used by:** settings.php, profile.php

---

### 9. **payment_transactions** - Payment Records
```sql
- id (PRIMARY KEY)
- user_id (Links to firebase_users)
- booking_id
- amount (DECIMAL)
- currency (USD, etc)
- payment_method (credit_card, paypal, stripe, wallet)
- transaction_id (UNIQUE)
- status (pending, completed, failed, refunded)
- created_at
```
**Used by:** api/create_booking.php, Payment processing

---

## 🔗 Database Relationships

```
firebase_users (Parent)
    ├── bookings (user_id FK)
    ├── reviews (user_id FK)
    ├── wishlist (user_id FK)
    ├── activity_log (user_id FK)
    ├── notifications (user_id FK)
    ├── user_preferences (user_id FK)
    ├── payment_transactions (user_id FK)
    └── users (Extended profile, firebase_uid link)
```

---

## 📋 Table Statistics

| Table | Purpose | Records |
|-------|---------|---------|
| firebase_users | User Accounts | 0 (auto-populate on signup) |
| users | Extended Profiles | 0 (optional) |
| bookings | Trip Bookings | 0 (auto-populate on booking) |
| reviews | Ratings/Reviews | 0 (auto-populate on review) |
| wishlist | Saved Destinations | 0 (auto-populate on save) |
| activity_log | Activity History | 0 (auto-populate on actions) |
| notifications | User Alerts | 0 (auto-populate on events) |
| user_preferences | Settings | Auto-create per user |
| payment_transactions | Payments | 0 (auto-populate on payment) |

---

## ✅ Database Features

### Foreign Key Constraints
- All child tables have CASCADE DELETE on firebase_users
- Ensures data integrity when user account is deleted

### Indexes
- `firebase_users` indexed on: firebase_uid, email
- `activity_log` indexed on: user_id, created_at
- `payment_transactions` indexed on: status
- Speeds up queries significantly

### Auto-Timestamps
- All tables have `created_at` timestamp
- Most have `updated_at` for modification tracking
- Enables audit trails and history

### Data Validation
- Email fields are UNIQUE
- Booking status is ENUM (controlled values)
- Payment status is ENUM (controlled values)
- Reviews rating uses CHECK constraint (1-5)

---

## 🚀 Usage Examples

### Create New User Account
```php
$conn->query("INSERT INTO firebase_users (firebase_uid, fullname, email, photo) VALUES ('uid123', 'John Doe', 'john@example.com', 'photo.jpg')");
```

### Create Booking
```php
$conn->query("INSERT INTO bookings (user_id, destination_id, hotel_name, check_in, check_out, guests, rooms, total_price) VALUES (1, 5, 'Hilton', '2025-12-31', '2026-01-05', 2, 1, 500.00)");
```

### Log User Activity
```php
$details = json_encode(['destination' => 'Paris', 'action' => 'viewed']);
$conn->query("INSERT INTO activity_log (user_id, action, details) VALUES (1, 'Viewed Destination', '$details')");
```

### Toggle Wishlist
```php
// Check if exists
$result = $conn->query("SELECT id FROM wishlist WHERE user_id = 1 AND destination_id = 5");
if ($result->num_rows > 0) {
    $conn->query("DELETE FROM wishlist WHERE user_id = 1 AND destination_id = 5");
} else {
    $conn->query("INSERT INTO wishlist (user_id, destination_id) VALUES (1, 5)");
}
```

---

## 🔧 Maintenance

### Backup Database
```bash
mysqldump -u root -p travel_db > backup.sql
```

### Check Table Sizes
```sql
SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb 
FROM information_schema.tables 
WHERE table_schema = 'travel_db';
```

### Optimize Tables
```sql
OPTIMIZE TABLE firebase_users, bookings, reviews, wishlist;
```

---

## 📝 Next Steps

1. ✅ **Database setup complete** - All tables created
2. ✅ **profile.php** - Now works without errors
3. ⏭️ **Create sample data** (optional) - Populate test records
4. ⏭️ **Setup backups** - Daily database backups recommended
5. ⏭️ **Monitor growth** - Track database size over time

---

## 🆘 Troubleshooting

### Error: "Table doesn't exist"
**Solution:** Run `php create_missing_tables.php` again

### Error: "Foreign key constraint fails"
**Solution:** Check that parent record exists before inserting child record

### Slow queries
**Solution:** Check if indexes are in place with `SHOW INDEXES FROM table_name;`

### Data integrity issues
**Solution:** Enable foreign key checks: `SET FOREIGN_KEY_CHECKS=1;`

---

**Created:** December 31, 2025
**Status:** ✅ All Systems Operational
**Next Review:** When adding new features
