# Hero Block Setup Guide

## ⚠️ CRITICAL: Required Plugin

Your hero-section block **REQUIRES** Advanced Custom Fields PRO plugin to work.

### Step-by-Step Setup:

#### 1. **Activate ACF Pro Plugin**
   - Go to WordPress Admin: `/wp-admin`
   - Navigate to: **Plugins > Installed Plugins**
   - Find: **Advanced Custom Fields PRO**
   - Status should show: **Active** (in blue)
   - If it says "Activate" (not active), **click Activate**

#### 2. **Enter License Key (if required)**
   - Go to: **Custom Fields > Updates**
   - Enter your ACF Pro license key
   - Click **Update License**
   - If you don't have a license, you have two options:
     - Purchase one from: https://www.advancedcustomfields.com/pro/
     - Or use the alternative solution below (no ACF required)

#### 3. **Verify Block is Available**
   - Go to: **Pages > Add New**
   - Click the **"+"** button (top left)
   - Look for category: **"Golden Template Components"**
   - You should see: **"Hero Section"** block
   - Also check for: **"Test Block"** in Common category

#### 4. **Clear Cache**
   - Hard refresh browser: **Ctrl+Shift+R** (Windows) or **Cmd+Shift+R** (Mac)
   - If using caching plugin, clear all cache

---

## 🔍 Troubleshooting

### Check Debug Information
1. Go to WordPress Admin Dashboard
2. Look for red notice: **"ACF Pro Debug Results"**
3. Check these values:
   - ✅ acf_register_block_type: Should be **YES**
   - ✅ ACF Pro plugin active: Should be **YES**
   - ✅ Hero block registered: Should be **YES**

### If ACF Pro shows "NO":
- Plugin is not activated
- Go to Plugins and activate it

### If Hero block shows "NO":
- There's a registration error
- Check `/wp-content/debug.log` for errors
- Make sure you're editing a **Page** (not Post)

---

## 🚨 Common Issues

### Issue 1: "Plugin not activated"
**Solution:** Go to Plugins > Installed Plugins > Activate ACF Pro

### Issue 2: "License key required"
**Solution:** Enter license in Custom Fields > Updates OR use alternative solution below

### Issue 3: "Block not showing in editor"
**Solution:** 
- Make sure you're editing a **Page** (not Post)
- Clear browser cache
- Check if ACF Pro is active

### Issue 4: "Update available warning"
**Solution:** Update ACF Pro to latest version

---

## 📋 Quick Checklist

- [ ] ACF Pro plugin is installed (✅ Already done)
- [ ] ACF Pro plugin is **ACTIVATED** (❌ Check this!)
- [ ] License key entered (if required)
- [ ] Editing a **Page** (not Post)
- [ ] Browser cache cleared
- [ ] Block appears in "Golden Template Components" category

---

## 🎯 Expected Result

When everything is working:
1. Go to **Pages > Add New**
2. Click **"+"** button
3. See **"Golden Template Components"** category
4. Click **"Hero Section"** block
5. Block appears with fields:
   - Background Image
   - Title
   - Content
   - Test Items (repeater)

---

## 📞 Still Not Working?

If you've followed all steps and it still doesn't work:

1. Check the debug notice in WordPress admin
2. Look for PHP errors in `/wp-content/debug.log`
3. Try deactivating other plugins temporarily
4. Switch to default WordPress theme temporarily to test

---

## Alternative: Use the solution below if you can't get ACF Pro working
See: HERO-BLOCK-NO-ACF.md (I'll create this next)
