# 🗺️ Google Maps API Setup Guide

## Problem
The map shows: "This page didn't load Google Maps correctly. See the JavaScript console for technical details."

## Solution: Get Your Free Google Maps API Key

### Step 1: Create Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Sign in with your Google Account (create one if needed)
3. Create a new project:
   - Click "Select a Project" → "New Project"
   - Name it: `TravelGo` or any name
   - Click "Create"

### Step 2: Enable Required APIs
1. In the search bar, type `Maps JavaScript API` and click it
2. Click "Enable"
3. Repeat for these APIs:
   - **Places API** - for restaurant/hotel/POI search
   - **Directions API** - for route planning
   - **Geocoding API** - for address-to-coordinates
   - **Maps JavaScript API** - already enabled

### Step 3: Create API Key
1. Go to **Credentials** (left sidebar)
2. Click **"+ CREATE CREDENTIALS"** → **API Key**
3. Copy the generated key (looks like: `AIzaSy...`)

### Step 4: Set Up Billing (Google gives $200 free monthly!)
1. Go to **Billing** (left sidebar)
2. Click **"Link Billing Account"**
3. Enter your payment info (required for billing, but won't charge unless you exceed free tier)
4. Get $200 monthly free credit for Google Maps!

### Step 5: Add API Key to Your Website
1. Open `destination.php` in your editor
2. Find line 15-16:
   ```javascript
   const GOOGLE_MAPS_API_KEY = 'AIzaSyDemo_key_replace_with_yours';
   ```
   and
   ```html
   <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDemo_key_replace_with_yours&libraries=places,geometry,directions,marker" async defer></script>
   ```

3. Replace **both** `AIzaSyDemo_key_replace_with_yours` with your actual API key
4. Save the file

### Step 6: Optional - Secure Your API Key
For production, restrict your key:
1. In **Credentials**, click your API Key
2. Under **Application restrictions**, select **HTTP referrers (web sites)**
3. Add your domain (e.g., `localhost`, `yourdomain.com`)
4. Under **API restrictions**, select **Restrict key** and check only the 4 APIs you enabled

## Verification

After adding your API key:
1. Refresh `http://localhost/travel-website/destination.php`
2. You should see:
   - ✅ Interactive Google Map
   - 🔵 Blue marker at your location
   - 🔍 Search box working
   - 🍽️ Restaurant/hotel buttons functional

## Troubleshooting

### Still seeing error?
1. Open Browser DevTools (F12)
2. Go to **Console** tab
3. Look for error messages (they'll tell you exactly what's wrong)
4. Common issues:
   - **Invalid API Key**: Double-check copy-paste
   - **API not enabled**: Go to APIs list and enable missing ones
   - **Billing not set up**: Must enable billing even with free credit
   - **Key restriction**: Check if domain is whitelisted

### Free Tier Limits
- **Maps JavaScript API**: 28,000 requests/day free
- **Places API**: 25,000 requests/month free
- **Directions API**: 25,000 requests/day free
- **Geocoding API**: 5,000 requests/day free

For a travel website, you'll rarely hit these limits!

## Cost Breakdown (Worst Case)
| Feature | Free Limit | Cost/1000 |
|---------|-----------|----------|
| Map Loads | 28k/day | $7.00 |
| Place Search | 25k/month | $6.50 |
| Directions | 25k/day | $10.00 |
| Geocoding | 5k/day | $5.00 |

**Most small sites stay 100% free forever!**

## Questions?
- [Google Maps Documentation](https://developers.google.com/maps/documentation/javascript)
- [Pricing Calculator](https://cloud.google.com/products/calculator)
- Check console logs (F12) for specific errors
