# 🔑 Google Fonts API Key Setup Guide

## 📋 Quick Steps

### 1. Go to Google Cloud Console
👉 https://console.cloud.google.com/

### 2. Create or Select a Project
- Click on the project dropdown (top left)
- Click "New Project" or select existing
- Give it a name (e.g., "LaraShop Fonts")

### 3. Enable Web Fonts Developer API
1. Go to: **APIs & Services** → **Library**
2. Search for: **"Web Fonts Developer API"**
3. Click on it
4. Click **"Enable"**

### 4. Create API Key
1. Go to: **APIs & Services** → **Credentials**
2. Click **"+ CREATE CREDENTIALS"** (top)
3. Select **"API Key"**
4. Copy the generated key

### 5. (Optional) Restrict API Key
For security, you can restrict the key:
- Click on the created API key
- Under "API restrictions", select **"Restrict key"**
- Choose **"Web Fonts Developer API"**
- Click **"Save"**

### 6. Add to .env File
Open your `.env` file and add:
```
GOOGLE_FONTS_API_KEY=your_api_key_here
```

### 7. Clear Config Cache
```bash
php artisan config:clear
```

### 8. Test Connection
```bash
php artisan google-fonts:test
```

---

## ✅ Cost Information

- **FREE** - No billing required
- **No credit card** needed
- **Generous rate limits** (with cache, you'll never hit them)

---

## 🔍 Troubleshooting

### Error: "API key is invalid"
- Check if you copied the full key
- Make sure there are no extra spaces
- Verify the API is enabled in Google Cloud Console

### Error: "API is not enabled"
- Go to APIs & Services → Library
- Search for "Web Fonts Developer API"
- Click "Enable"

### Error: "403 Forbidden"
- Check API key restrictions
- Make sure "Web Fonts Developer API" is allowed
- Try creating a new API key without restrictions first

### Works without API key?
- Yes, but may have stricter rate limits
- Recommended to use API key for production

---

## 📚 Useful Links

- **Google Cloud Console**: https://console.cloud.google.com/
- **API Documentation**: https://developers.google.com/fonts/docs/developer_api
- **Get API Key**: https://console.cloud.google.com/apis/credentials

---

## 🧪 Manual Testing

### Test with curl (Windows CMD):
```cmd
curl "https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=YOUR_API_KEY"
```

### Test with PowerShell:
```powershell
Invoke-WebRequest -Uri "https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=YOUR_API_KEY" | Select-Object -ExpandProperty Content
```

### Test with Artisan:
```bash
php artisan google-fonts:test
php artisan google-fonts:test --key=YOUR_API_KEY
```
